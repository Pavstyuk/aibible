 <article id="ai-comment-{{ $comment->id }}" class="the-card-small border-thin border-color-gray border-radius-norm">
     {!! htmlspecialchars_decode($comment->ai_comment) !!}
     <p class="text-right font-size-tiny mb-0"><i>{{ __('Автор:') }} {{ ' ' . $comment->model }}</i>
     </p>
     <p class="text-right font-size-tiny mb-0">
         @php
             $user_name = DB::table('users')->where('id', $comment->user_id)->value('name') ?? '';
         @endphp
         <i>{{ __('Пользователь: ') }} {{ $user_name }}</i>
     </p>
     <p class="text-right font-size-tiny mb-0"><i>{{ explode(' ', $comment->created_at)[0] }}</i></p>
     @if ((int) $comment->user_id === (int) Auth::id() || (Auth::user()->is_admin ?? false))
         <div class="text-right mt-1">
             <button hx-delete="{{ route('delete-ai') }}"
                 hx-trigger="click[confirm('Уверены? Комментарий #{{ $comment->id }} будет удален!')]"
                 hx-swap="innerHTML"
                 hx-on::after-request="document.getElementById('ai-comment-{{ $comment->id }}').remove()"
                 hx-vals='{"id": "{{ $comment->id }}", "translation": "{{ $comment->translation }}", "_token": "{{ csrf_token() }}"}'
                 class="button button-small button-delete htmx-button">{{ __('Удалить') }}</button>
         </div>
     @endif
 </article>
