<section id="comments" class="mt-14 border-t border-border pt-10" aria-labelledby="comments-heading">
    <h2 id="comments-heading" class="font-display text-2xl font-bold">Comments ({{ $comments->count() }})</h2>

    @if (session('comment_status'))
        <p class="alert alert-success mt-6" role="status"><x-heroicon-o-check-circle class="size-5 shrink-0" aria-hidden="true" />{{ session('comment_status') }}</p>
    @endif

    <ul class="mt-8 space-y-6">
        @foreach ($comments as $comment)
            <li class="rounded-card border border-border p-5">
                <p class="text-sm font-semibold">{{ $comment->name }} <span class="font-normal text-muted">· <time datetime="{{ $comment->created_at->toIso8601String() }}">{{ $comment->created_at->diffForHumans() }}</time></span></p>
                <p class="mt-2 whitespace-pre-line text-[0.9375rem] text-muted">{{ $comment->body }}</p>
                @foreach ($comment->replies as $reply)
                    <div class="mt-4 border-l-2 border-primary/30 pl-4">
                        <p class="text-sm font-semibold">{{ $reply->name }}</p>
                        <p class="mt-1 whitespace-pre-line text-sm text-muted">{{ $reply->body }}</p>
                    </div>
                @endforeach
            </li>
        @endforeach
    </ul>

    <form method="POST" action="{{ route('blog.comments.store', $post->slug) }}" class="mt-10 space-y-5 rounded-panel border border-border bg-surface p-6" x-data="formState" x-on:submit="submit" novalidate>
        @csrf
        <x-forms.spam-protection />
        <h3 class="font-display text-lg font-bold">Leave a comment</h3>
        <p class="text-sm text-muted">Comments are moderated before they appear. Your email is never published.</p>
        <x-forms.form-errors />
        <div class="grid gap-5 sm:grid-cols-2">
            <x-forms.field name="name" label="Name" required><input id="field-name" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name" class="form-control" @error('name') aria-invalid="true" @enderror></x-forms.field>
            <x-forms.field name="email" label="Email" required><input id="field-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="form-control" @error('email') aria-invalid="true" @enderror></x-forms.field>
        </div>
        <x-forms.field name="body" label="Comment" required><textarea id="field-body" name="body" rows="4" required maxlength="2000" class="form-control" @error('body') aria-invalid="true" @enderror>{{ old('body') }}</textarea></x-forms.field>
        <x-forms.consent />
        <x-forms.submit label="Submit comment" />
    </form>
</section>
