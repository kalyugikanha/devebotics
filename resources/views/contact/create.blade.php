<x-layout :seo="$seo">
    <x-site.page-hero :seo="$seo" illustration="chat" eyebrow="Contact" title="Let’s talk about [your goals]"
        intro="Questions, ideas or a project in mind? Send us a message and the right person on our team will get back to you.">
        <a href="{{ route('project.create') }}" class="btn btn-secondary btn-lg">Have a detailed brief? Start a project</a>
    </x-site.page-hero>

    <section class="section pt-12" id="form">
        <div class="container-site grid gap-12 lg:grid-cols-[1.4fr_1fr]">
            <div class="rounded-panel border border-border bg-background p-6 shadow-sm sm:p-10">
                <h2 class="font-display text-2xl font-bold">Send us a message</h2>
                <p class="mt-2 text-muted">Fields marked * are required.</p>
                <div class="mt-6">@include('contact._success')</div>

                <form method="POST" action="{{ route('contact.store') }}" class="mt-6 space-y-5" x-data="formState" x-on:submit="submit" novalidate>
                    @csrf
                    <input type="hidden" name="type" value="quick">
                    <x-forms.spam-protection />
                    <x-forms.form-errors />
                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-forms.field name="name" label="Full name" required><input id="field-name" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name" class="form-control" @error('name') aria-invalid="true" aria-describedby="field-name-error" @enderror></x-forms.field>
                        <x-forms.field name="email" label="Business email" required><input id="field-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="form-control" @error('email') aria-invalid="true" aria-describedby="field-email-error" @enderror></x-forms.field>
                        <x-forms.field name="phone" label="Phone"><input id="field-phone" type="tel" name="phone" value="{{ old('phone') }}" maxlength="40" autocomplete="tel" class="form-control" @error('phone') aria-invalid="true" @enderror></x-forms.field>
                        <x-forms.field name="company" label="Company"><input id="field-company" name="company" value="{{ old('company') }}" maxlength="160" autocomplete="organization" class="form-control"></x-forms.field>
                    </div>
                    <x-forms.field name="topic" label="What is this about?" required>
                        <select id="field-topic" name="topic" required class="form-control" @error('topic') aria-invalid="true" aria-describedby="field-topic-error" @enderror>
                            <option value="">Choose a topic</option>
                            @foreach (\App\Enums\InquiryTopic::cases() as $topic)<option value="{{ $topic->value }}" @selected(old('topic') === $topic->value)>{{ $topic->getLabel() }}</option>@endforeach
                        </select>
                    </x-forms.field>
                    <x-forms.field name="service_id" label="Service of interest">
                        <select id="field-service_id" name="service_id" class="form-control">
                            <option value="">Select a service</option>
                            @foreach ($services as $id => $title)<option value="{{ $id }}" @selected((int) old('service_id') === $id)>{{ $title }}</option>@endforeach
                        </select>
                    </x-forms.field>
                    <x-forms.field name="subject" label="Subject"><input id="field-subject" name="subject" value="{{ old('subject') }}" maxlength="200" class="form-control"></x-forms.field>
                    <x-forms.field name="message" label="Message" required help="At least 20 characters.">
                        <textarea id="field-message" name="message" rows="6" required minlength="20" maxlength="5000" class="form-control" aria-describedby="field-message-help" @error('message') aria-invalid="true" @enderror>{{ old('message') }}</textarea>
                    </x-forms.field>
                    <x-forms.consent />
                    <x-forms.submit label="Send message" />
                </form>
            </div>

            <aside class="space-y-8">
                @include('contact._details')
                @if ($faqs->isNotEmpty())
                    <div>
                        <h2 class="font-display text-lg font-bold">Common questions</h2>
                        <div class="mt-4 space-y-3">@foreach ($faqs as $faq)<x-site.faq-item :faq="$faq" :index="$loop->index" />@endforeach</div>
                    </div>
                @endif
            </aside>
        </div>
    </section>
</x-layout>
