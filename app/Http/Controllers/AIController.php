<?php

namespace App\Http\Controllers;

use App\Traits\AILog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AIController extends Controller
{
    use AILog;

    public array $translations = ["rbo", "rst", "nasb"];
    private string $key;
    public string $addon_base = "Ты евангельский христианин богослов. Дай комментарий к отрывку из Библии с христианской точки зрения в контекст все главы и книги. Следуй строгой экзегез. Отметь основную мысль, которая поможет человеку больше познать Бога Творца или Сына Иисуса Христа или Духа Святого. Не придумывай смыслы, которые автор не закладывал в отрывок. В заголовке напиши к какому стиху относится этот комментарий. Ответ должен быть около 1000 символов. Вот сам отрывок: ";
    public string $addon_lexical = "Ты евангельский христианин богослов. Сделай лексическое литературное и стилистическое исследование отрывка из Библии. Проанализируй язык оригинала текста. Пропиши транслитерацию оригинальных слов на русский язык. Укажи номер Стронга для каждого слова. Если у слов есть несколько значений отметь это. Постарайся быть предельно точным и грамотным. В заголовке укажи, что это лексический анализ. Ответ должен быть около 1000 символов. Вот сам отрывок: ";
    public string $addon_questions = "Ты организатор христианской встречи для обсуждения Библии. Подготовь три вопроса для обсуждения отрывка из Библии в небольшой домашней атмосфере. Постарайся чтобы вопросы помогли лучше понять замысел автора текста, лучше познать Бога и укрепиться в вере в Иисуса Христа. А также понять применимость текста в жизни современного человека. Напиши заголовок: Вопросы для обсуждения отрывка и вставь координаты отрывка. Не пиши ничего лишнего только заголовок и вопросы. Вопросы должны быть достаточно простыми и понятными как верующим так и не верующим людям. Вот сам отрывок: ";
    public string $addon_sermon = 'Ты современный христианский проповедник с хорошим чувством юмора и достаточно остроумный. Подготовь небольшую текстуальную проповедь по отрывку из Библии. В проповеди должно быть вступление три пункта и вывод. Проповедь должны быть увлекательной и актуальной, строго следовать христианской традиции и соблюдать принципы экзегезы. При подготовки используй инструменты описанные в книге Джеймса Брага "Как подготовить библейскую проповедь", но при этом не нужно его цитировать. При необходимости можно процитировать великих учителей Церкви первых столетий или известных личностей, если это будет уместно, но обязательно с точной ссылкой на цитату. Постарайся сделать проповедь практичной и применимой к реальной жизни современного человека. Не нужно использовать emoji или другую графику, только текст. Вот сам отрывок на основе которого нужно сделать проповедь: ';
    public string $addon_postfix = " Проверь свой результат на предмет точности, верности, если есть ошибки - исправь.";

    public function chooseCommentAddon($comm_type)
    {
        return match ($comm_type) {
            "base" => $this->addon_base,
            "lexical" => $this->addon_lexical,
            "questions" => $this->addon_questions,
            "sermon" => $this->addon_sermon,
            default => $this->addon_base,
        };
    }

    public function askGemini(Request $request)
    {
        $url = "https://gemini-proxy-ten-chi.vercel.app/api/v1beta/interactions";
        $this->key = config("app.gemini_key");
        // $model = "gemini-3.5-flash-lite";
        $model = "gemini-3.5-flash";
        $verse = urldecode($request->input("verse"));
        $verse_id = htmlspecialchars($request->input("verse_id"));
        $translation = htmlspecialchars($request->input("translation"));
        $comm_type = htmlspecialchars($request->input("type"));

        $addon = $this->chooseCommentAddon($comm_type);

        $question = $addon . $verse . $this->addon_postfix;


        $data = [
            'model' => $model,
            'input' => $question,
        ];

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'x-goog-api-key: ' . $this->key
            ],
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            die("cURL Error: $error\n");
        }

        if ($httpCode === 200) {
            $result = json_decode($response, true);
        } else {
            $result = json_decode($response, true);
        }

        if (!empty($result['steps'][1]['content'][0]['text'])) {
            $answer = $result['steps'][1]['content'][0]['text'];
            $model = $result["model"];
        } else {
            $answer = 'Ошибка';
            $model = $result["model"];
        }

        $this->logToFile($result);

        $answer_html = Str::markdown($answer);

        $data = [
            "translation" => $translation,
            "verse_id" => $verse_id,
            "comment" => $answer_html,
            "model" => $model ?? '--',
        ];

        $template = "partials.ai-comment";

        return view($template, $data);
    }

    public function askMistral(Request $request)
    {
        $url = "https://api.mistral.ai/v1/chat/completions";
        $this->key = config("app.mistral_key");
        $temp = "0.7";
        $verse = urldecode($request->input("verse"));
        $verse_id = htmlspecialchars($request->input("verse_id"));
        $translation = htmlspecialchars($request->input("translation"));
        $comm_type = htmlspecialchars($request->input("type"));

        $addon = $this->chooseCommentAddon($comm_type);

        $question = $addon . $verse . $this->addon_postfix;

        $data = [
            "model" => "mistral-small-latest",
            "temperature" => $temp,
            "messages" => [
                [
                    "role" => "user",
                    "content" => $question,
                ],
            ],
        ];

        $options = [
            "http" => [
                "header" => [
                    "Content-Type: application/json",
                    "Accept: application/json",
                    "Authorization: Bearer $this->key",
                ],
                "method" => "POST",
                "content" => json_encode($data),
            ],
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        $response_object = json_decode($response, true);

        $this->logToFile($response_object);

        $answer = $response_object["choices"][0]["message"]["content"];
        $model = $response_object["model"];

        $answer_html = Str::markdown($answer);

        $data = [
            "translation" => $translation,
            "verse_id" => $verse_id,
            "comment" => $answer_html,
            "model" => $model,
        ];

        $template = "partials.ai-comment";

        return view($template, $data);
    }

    public function askVercelProxy(Request $request)
    {
        $apiKey = config("app.open_router_key_1");
        $url = "https://openrouter-proxy-plum.vercel.app/api/v1/chat/completions";

        $model_gpt_oss = "nvidia/nemotron-3-super-120b-a12b:free";
        $verse = urldecode($request->input("verse"));
        $verse_id = htmlspecialchars($request->input("verse_id"));
        $translation = htmlspecialchars($request->input("translation"));
        $comm_type = htmlspecialchars($request->input("type"));

        $addon = $this->chooseCommentAddon($comm_type);

        $question = $addon . $verse . $this->addon_postfix;

        $data = [
            "model" => $model_gpt_oss,
            "messages" => [
                [
                    "role" => "user",
                    "content" => $question,
                ],
            ],
        ];

        $options = [
            "http" => [
                "method" => "POST",
                "header" => [
                    "Content-Type: application/json",
                    "Accept: application/json",
                    "Authorization: Bearer " . $apiKey,
                    "HTTP-Referer: http://localhost",
                    "X-Title: AI Bible App",
                ],
                "content" => json_encode($data),
            ],
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $response_object = json_decode($response, true);

        $this->logToFile($response_object);

        $answer = $response_object["choices"][0]["message"]["content"];
        $model = $response_object["model"];

        $answer_html = Str::markdown($answer);

        $data = [
            "translation" => $translation,
            "verse_id" => $verse_id,
            "comment" => $answer_html,
            "model" => $model,
        ];

        $template = "partials.ai-comment";

        return view($template, $data);
    }

    public function askLMStudio(Request $request)
    {
        set_time_limit(0);

        $url = "http://192.168.1.108:1234/v1/chat/completions";
        $model_gemma = "google/gemma-4-e4b";

        $verse = urldecode($request->input("verse"));
        $verse_id = htmlspecialchars($request->input("verse_id"));
        $translation = htmlspecialchars($request->input("translation"));

        $comm_type = htmlspecialchars($request->input("comment_type"));

        $addon = $this->chooseCommentAddon($comm_type);

        $question = $addon . $verse . $this->addon_postfix;

        $data = [
            "model" => $model_gemma,
            "messages" => [
                [
                    "role" => "user",
                    "content" => $question,
                ],
            ],
            "temperature" => 0.7,
            "stream" => false,
        ];

        $options = [
            "http" => [
                "method" => "POST",
                "header" => [
                    "Content-Type: application/json",
                    "Accept: application/json",
                ],
                "content" => json_encode($data),
            ],
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $response_object = json_decode($response, true);

        $this->logToFile($response_object);

        $answer = $response_object["choices"][0]["message"]["content"];
        $model = $response_object["model"];

        $answer_html = Str::markdown($answer);

        $data = [
            "translation" => $translation,
            "verse_id" => $verse_id,
            "comment" => $answer_html,
            "model" => $model,
        ];

        $template = "partials.ai-comment";

        return view($template, $data);
    }

    function saveComment(Request $request)
    {
        $verse_id = (int) htmlspecialchars($request->input("verse_id"));
        $translation = htmlspecialchars($request->input("translation"));
        $ai_comment = htmlspecialchars($request->input("ai_comment"));
        $model = htmlspecialchars($request->input("model"));
        $user_id = htmlspecialchars($request->input("user_id"));

        // $now = date();

        $db_name = "ai_comments_" . $translation;

        $result = DB::table($db_name)->insertGetId([
            "translation" => $translation,
            "verse_id" => $verse_id,
            "ai_comment" => $ai_comment,
            "model" => $model,
            "user_id" => $user_id,
        ]);

        $data = [
            "id" => $result,
            "ai_comment" => $ai_comment,
        ];

        $template = "partials.ai-save";

        return view($template, $data);
    }

    function getCommentsByVerseID(string $translation, int $verse_id)
    {
        $db_name = "ai_comments_" . $translation;

        $comments = DB::table($db_name)
            ->where("verse_id", $verse_id)
            ->orderBy("id", "desc")
            ->get();

        return $comments;
    }

    function deleteCommentById(Request $request)
    {
        $id = htmlspecialchars($request->input("id"));
        $translation = htmlspecialchars($request->input("translation"));
        $db_name = "ai_comments_" . $translation;
        $res = DB::table($db_name)->where("id", $id)->delete();
        if ($res) {
            echo '<span class="text-color-success">Удалено!</span>';
        } else {
            echo "Ошибка удаления";
        }
    }

    function getCommentsByUserId(Request $request)
    {
        $user_id = $request->input("user_id", 0);

        if ($user_id === 0) {
            echo "Нет данных";
        }

        $translations = $this->translations;

        $comments = [];
        $count = 0;

        foreach ($translations as $translation) {
            $db_name = "ai_comments_" . $translation;
            $current_comments = DB::table($db_name)
                ->where("user_id", $user_id)
                ->orderBy("id", "desc")
                ->get();

            if ($current_comments) {
                $count += array_push($comments, $current_comments);
            }
        }

        // $comments = DB::table('ai_comments')
        //     ->where('user_id', $user_id)
        //     ->orderBy('id', 'desc')
        //     ->get();

        // if ($comments->count() === 0) {
        //     echo "Нет комментариев";
        //     exit;
        // }

        if ($count === 0) {
            echo "Нет комментариев";
            exit();
        }

        $data = [
            "comments" => $comments,
        ];

        $template = "partials.ai-comments";

        return view($template, $data);
    }

    /**
     *
     * Нужно переделать дальнейшие методы под новые таблицы ai комментариев
     */

    function displayCommentsByUserId(Request $request, int $id = 0)
    {
        if (!empty($request->user()->id)) {
            $user_id = (int) $request->user()->id;
        } else {
            return abort(404);
        }

        if ($user_id !== $id) {
            return abort(404);
        }

        $translations = $this->translations;

        $comments = [];

        foreach ($translations as $translation) {
            $db_name = "ai_comments_" . $translation;
            $current_comments = DB::table($db_name)
                ->where("user_id", $user_id)
                ->orderBy("id", "desc")
                ->get()
                ->all();

            $comments += $current_comments;
        }

        // dd($comments);

        // $comments = DB::table('ai_comments')
        //     ->where('user_id', $user_id)
        //     ->orderBy('id', 'desc')
        //     ->get();

        $seo_title =
            "Сохраненные сгенерированные ИИ комментарии к отрывкам из Библии";
        $seo_description =
            "Сохраненные сгенерированные ИИ комментарии к отрывкам из Библии";

        $data = [
            "comments" => $comments,
            "user_id" => $user_id,
            "book_num" => $request->cookie("book_num"),
            "chapter_num" => $request->cookie("chapter_num"),
            "seo_title" => $seo_title,
            "seo_description" => $seo_description,
            "theme" => $_COOKIE["apptheme"] ?? "",
        ];

        $template = "ai.comments";

        return view($template, $data);
    }

    function getNewestComments(int $number = 3)
    {
        $translations = $this->translations;

        $comments = [];

        foreach ($translations as $translation) {
            $db_name = "ai_comments_" . $translation;
            $current_comments = DB::table($db_name)
                ->latest()
                ->take($number)
                ->get()
                ->all();

            $comments += $current_comments;
        }

        // $comments = DB::table('ai_comments')
        //     ->latest()
        //     ->take($number)
        //     ->get();

        return $comments;
    }
}
