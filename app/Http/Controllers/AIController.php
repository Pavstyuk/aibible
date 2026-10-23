<?php

namespace App\Http\Controllers;

use App\Exceptions\AiProvidersFailedException;
use App\Mail\SendAiComment;
use App\Models\Key;
use App\Services\AiProviderChain;
use App\Services\LmStudioClient;
use App\Support\HtmlSanitizer;
use App\Traits\AILog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class AIController extends Controller
{
    use AILog;

    private string $key = '';

    public string $addon_base = "Ты евангельский христианин богослов. Дай комментарий к отрывку из Библии с христианской точки зрения в контекст все главы и книги. Следуй строгой экзегез. Отметь основную мысль, которая поможет человеку больше познать Бога Творца или Сына Иисуса Христа или Духа Святого. Не придумывай смыслы, которые автор не закладывал в отрывок. В заголовке напиши к какому стиху относится этот комментарий. Ответ должен быть около 1000 символов. Вот сам отрывок: ";
    public string $addon_lexical = "Ты евангельский христианин богослов. Сделай лексическое литературное и стилистическое исследование отрывка из Библии. Проанализируй язык оригинала текста. Пропиши транслитерацию оригинальных слов на русский язык. Укажи номер Стронга для каждого слова. Если у слов есть несколько значений отметь это. Постарайся быть предельно точным и грамотным. В заголовке укажи, что это лексический анализ. Ответ должен быть около 1000 символов. Вот сам отрывок: ";
    public string $addon_questions = "Ты организатор христианской встречи для обсуждения Библии. Подготовь три вопроса для обсуждения отрывка из Библии в небольшой домашней атмосфере. Постарайся чтобы вопросы помогли лучше понять замысел автора текста, лучше познать Бога и укрепиться в вере в Иисуса Христа. А также понять применимость текста в жизни современного человека. Напиши заголовок: Вопросы для обсуждения отрывка и вставь координаты отрывка. Не пиши ничего лишнего только заголовок и вопросы. Вопросы должны быть достаточно простыми и понятными как верующим так и не верующим людям. Вот сам отрывок: ";
    public string $addon_sermon = 'Ты современный христианский проповедник с хорошим чувством юмора и достаточно остроумный. Подготовь небольшую текстуальную проповедь по отрывку из Библии. В проповеди должно быть вступление три пункта и вывод. Проповедь должны быть увлекательной и актуальной, строго следовать христианской традиции и соблюдать принципы экзегезы. При подготовки используй инструменты описанные в книге Джеймса Брага "Как подготовить библейскую проповедь", но при этом не нужно его цитировать. При необходимости можно процитировать великих учителей Церкви первых столетий или известных личностей, если это будет уместно, но обязательно с точной ссылкой на цитату. Постарайся сделать проповедь практичной и применимой к реальной жизни современного человека. Не нужно использовать emoji или другую графику, только текст. Вот сам отрывок на основе которого нужно сделать проповедь: ';
    public string $addon_postfix = " Проверь свой результат на предмет точности, верности, если есть ошибки - исправь.";

    public function chooseCommentAddon($comm_type)
    {
        return match ($comm_type) {
            'base', 'comment' => $this->addon_base,
            'lexical' => $this->addon_lexical,
            'questions' => $this->addon_questions,
            'sermon' => $this->addon_sermon,
            default => $this->addon_base,
        };
    }

    /**
     * Backwards-compatible spam guard used by the AI form.
     * Real abuse protection is the auth + throttle:ai route middleware.
     */
    public function checkKeys(string $antispam_key = '', string $hmtx_key = ''): bool
    {
        $verification_key = 'aibible_form_key_' . substr((string) time(), 0, 8);

        return $antispam_key === $verification_key && $hmtx_key === 'htmx-post';
    }

    public function askAi(Request $request, AiProviderChain $chain)
    {
        return $this->respondWithProvider(
            $request,
            'fallback',
            fn (string $question) => $chain->ask($question)
        );
    }

    public function askLMStudio(Request $request, LmStudioClient $lmStudio)
    {
        return $this->respondWithProvider(
            $request,
            'lmstudio',
            fn (string $question) => $lmStudio->ask($question)
        );
    }

    public function saveComment(Request $request)
    {
        $validated = $request->validate([
            'verse_id' => ['required', 'integer', 'min:1'],
            'translation' => ['required', 'string'],
            'ai_comment' => ['required', 'string', 'max:60000'],
            'model' => ['nullable', 'string', 'max:255'],
        ]);

        $translation = $this->translationOrFail($validated['translation']);
        $verse_id = (int) $validated['verse_id'];

        if (! DB::table($translation)->where('id', $verse_id)->exists()) {
            return response('Стих не найден', 422);
        }

        $safeComment = HtmlSanitizer::clean($validated['ai_comment']);

        if ($safeComment === '') {
            return response('Комментарий пуст', 422);
        }

        $model = Str::limit((string) ($validated['model'] ?? '--'), 255, '');
        $user = $request->user();

        $id = DB::table('ai_comments_' . $translation)->insertGetId([
            'translation' => $translation,
            'verse_id' => $verse_id,
            'ai_comment' => htmlspecialchars($safeComment, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            'model' => $model,
            'user_id' => (int) $user->id,
        ]);

        return view('partials.ai-save', [
            'id' => $id,
            'ai_comment' => $safeComment,
        ]);
    }

    public function getCommentsByVerseID(string $translation, int $verse_id)
    {
        if (! in_array($translation, config('bible.translations', []), true)) {
            return collect();
        }

        return DB::table('ai_comments_' . $translation)
            ->where('verse_id', $verse_id)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function deleteCommentById(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'min:1'],
            'translation' => ['required', 'string'],
        ]);

        $translation = $this->translationOrFail($validated['translation']);
        $id = (int) $validated['id'];
        $comment = DB::table('ai_comments_' . $translation)->where('id', $id)->first();

        if (! $comment) {
            return response('Комментарий не найден', 404);
        }

        $user = $request->user();

        if ((int) $comment->user_id !== (int) $user->id && ! (bool) $user->is_admin) {
            return response('Нет прав на удаление', 403);
        }

        $deleted = DB::table('ai_comments_' . $translation)->where('id', $id)->delete();

        return $deleted
            ? response('<span class="text-color-success">Удалено!</span>')
            : response('Ошибка удаления', 500);
    }

    public function getCommentsByUserId(Request $request)
    {
        $user_id = (int) $request->user()->id;
        $comments = $this->commentsForUser($user_id);

        if ($comments === []) {
            return response('Нет комментариев');
        }

        return view('partials.ai-comments', ['comments' => $comments]);
    }

    public function displayCommentsByUserId(Request $request, int $id = 0)
    {
        $user_id = (int) $request->user()->id;

        if ($user_id !== $id) {
            return abort(404);
        }

        $comments = $this->commentsForUser($user_id);

        $seo_title = 'Сохраненные сгенерированные ИИ комментарии к отрывкам из Библии';
        $seo_description = 'Сохраненные сгенерированные ИИ комментарии к отрывкам из Библии';

        $data = [
            'comments' => $comments,
            'user_id' => $user_id,
            'book_num' => $request->cookie('book_num'),
            'chapter_num' => $request->cookie('chapter_num'),
            'seo_title' => $seo_title,
            'seo_description' => $seo_description,
            'theme' => $_COOKIE['apptheme'] ?? '',
        ];

        return view('ai.comments', $data);
    }

    public function getNewestComments(int $number = 3): array
    {
        $number = max(1, $number);
        $comments = [];

        foreach (config('bible.translations', []) as $translation) {
            $current = DB::table('ai_comments_' . $translation)
                ->latest()
                ->take($number)
                ->get()
                ->all();

            $comments = array_merge($comments, $current);
        }

        usort($comments, function ($a, $b) {
            return strcmp((string) $b->created_at, (string) $a->created_at)
                ?: ((int) $b->id <=> (int) $a->id);
        });

        return array_slice($comments, 0, $number);
    }

    protected function validateAiRequest(Request $request): array
    {
        $validated = $request->validate([
            'verse' => ['required', 'string', 'max:20000'],
            'verse_id' => ['required', 'integer', 'min:1'],
            'translation' => ['required', 'string'],
            'type' => ['nullable', 'string', 'in:comment,base,lexical,questions,sermon'],
        ]);

        $validated['translation'] = $this->translationOrFail($validated['translation']);
        $validated['verse_id'] = (int) $validated['verse_id'];
        $validated['type'] = (string) ($validated['type'] ?? 'base');

        if (! DB::table($validated['translation'])->where('id', $validated['verse_id'])->exists()) {
            abort(422, 'Стих не найден');
        }

        return $validated;
    }

    protected function translationOrFail(string $translation): string
    {
        $translation = strtolower((string) preg_replace('/[^a-z]/', '', $translation));

        if (! in_array($translation, config('bible.translations', []), true)) {
            abort(404);
        }

        return $translation;
    }

    protected function respondWithProvider(Request $request, string $provider, callable $generate): \Illuminate\View\View
    {
        $validated = $this->validateAiRequest($request);
        $translation = $validated['translation'];
        $verseId = (int) $validated['verse_id'];
        $verse = urldecode($validated['verse']);
        $question = $this->chooseCommentAddon($validated['type']) . $verse . $this->addon_postfix;

        try {
            $result = $generate($question);
        } catch (Throwable $exception) {
            report($exception);

            $this->logToFile([
                'provider' => $provider,
                'error' => $exception->getMessage(),
                'failures' => $exception instanceof AiProvidersFailedException ? $exception->errors : null,
            ]);

            return $this->aiErrorView($translation, $verseId, 'Не удалось получить ответ ИИ. Попробуйте позже.');
        }

        $this->logToFile([
            'provider' => (string) ($result['provider'] ?? $provider),
            'http_code' => $result['http_code'] ?? null,
            'model' => $result['model'] ?? null,
            'usage' => $result['usage'] ?? null,
            'fallback_errors' => $result['fallback_errors'] ?? null,
        ]);

        return $this->aiCommentView(
            $request,
            $translation,
            $verseId,
            (string) ($result['answer'] ?? ''),
            (string) ($result['model'] ?? '--')
        );
    }

    protected function aiCommentView(Request $request, string $translation, int $verse_id, string $answer, string $model): \Illuminate\View\View
    {
        $comment = $this->safeMarkdown($answer);
        $model = Str::limit($model, 255, '');

        $this->sendAiCommentNotification($request, $translation, $verse_id, $comment, $model);

        return view('partials.ai-comment', [
            'translation' => $translation,
            'verse_id' => $verse_id,
            'comment' => $comment,
            'model' => $model,
        ]);
    }

    protected function sendAiCommentNotification(
        Request $request,
        string $translation,
        int $verse_id,
        string $commentHtml,
        string $model
    ): void {
        if (! (bool) config('ai.email_notifications', true)) {
            return;
        }

        $user = $request->user();

        if (! $user || ! is_string($user->email) || trim($user->email) === '') {
            return;
        }

        try {
            $verse = DB::table($translation)->find($verse_id);

            if (! $verse) {
                return;
            }

            $bookName = trim((string) ($verse->book_name ?? ''));
            $chapter = (int) ($verse->chapter_num ?? 0);
            $verseNumber = (int) ($verse->verse_num ?? 0);
            $reference = $bookName !== ''
                ? "{$bookName} {$chapter}:{$verseNumber}"
                : "Стих {$verse_id}";

            $verseUrl = route('single', [
                'translation' => $translation,
                'book_num' => (int) ($verse->book_num ?? 0),
                'chapter' => $chapter,
                'verse' => $verseNumber,
            ]);

            Mail::to($user->email)->send(new SendAiComment(
                userName: (string) ($user->name ?? ''),
                verseReference: $reference,
                translation: $translation,
                verseUrl: $verseUrl,
                commentHtml: $commentHtml,
                model: $model,
            ));
        } catch (Throwable $exception) {
            // Email is a notification, not part of the AI response. Never let
            // an SMTP/template failure break the generated comment on the site.
            report($exception);
        }
    }

    protected function aiErrorView(string $translation, int $verse_id, string $message): \Illuminate\View\View
    {
        return view('partials.ai-comment-error', [
            'translation' => $translation,
            'verse_id' => $verse_id,
            'comment' => '<p class="message message-error">' . e($message) . '</p>',
            'model' => '--',
        ]);
    }

    protected function safeMarkdown(string $markdown): string
    {
        return HtmlSanitizer::clean(Str::markdown($markdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]));
    }

    protected function commentsForUser(int $user_id): array
    {
        $comments = [];

        foreach (config('bible.translations', []) as $translation) {
            $current = DB::table('ai_comments_' . $translation)
                ->where('user_id', $user_id)
                ->orderByDesc('id')
                ->get()
                ->all();

            $comments = array_merge($comments, $current);
        }

        usort($comments, fn($a, $b) => (int) $b->id <=> (int) $a->id);

        return $comments;
    }
}
