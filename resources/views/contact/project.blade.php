<x-layout :seo="$seo">
    <x-site.page-hero :seo="$seo" illustration="strategy" eyebrow="Start a project" title="Tell us about [your project]"
        intro="Share as much as you can — goals, scope, budget and timeline. We will come back with questions, ideas and next steps." />

    <section class="section pt-12" id="form">
        <div class="container-site grid gap-12 lg:grid-cols-[1.5fr_1fr]">
            <div class="rounded-panel border border-border bg-background p-6 shadow-sm sm:p-10">
                @include('contact._success')
                <form method="POST" action="{{ route('contact.store') }}" enctype="multipart/form-data" class="mt-2 space-y-8" x-data="formState" x-on:submit="submit" novalidate>
                    @csrf
                    <input type="hidden" name="type" value="project">
                    <x-forms.spam-protection />
                    <x-forms.form-errors />

                    <fieldset class="space-y-5">
                        <legend class="font-display text-lg font-bold">1. About you</legend>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-forms.field name="name" label="Full name" required><input id="field-name" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name" class="form-control" @error('name') aria-invalid="true" @enderror></x-forms.field>
                            <x-forms.field name="email" label="Business email" required><input id="field-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="form-control" @error('email') aria-invalid="true" @enderror></x-forms.field>
                            <x-forms.field name="phone" label="Phone"><input id="field-phone" type="tel" name="phone" value="{{ old('phone') }}" maxlength="40" autocomplete="tel" class="form-control" @error('phone') aria-invalid="true" @enderror></x-forms.field>
                            <x-forms.field name="company" label="Company"><input id="field-company" name="company" value="{{ old('company') }}" maxlength="160" autocomplete="organization" class="form-control"></x-forms.field>
                        </div>
                    </fieldset>

                    <fieldset class="space-y-5">
                        <legend class="font-display text-lg font-bold">2. Your project</legend>
                        <x-forms.field name="service_id" label="Service of interest">
                            <select id="field-service_id" name="service_id" class="form-control">
                                <option value="">Select a service</option>
                                @foreach ($services as $id => $title)<option value="{{ $id }}" @selected((int) old('service_id', $selectedService) === $id)>{{ $title }}</option>@endforeach
                            </select>
                        </x-forms.field>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-forms.field name="budget_range" label="Budget range">
                                <select id="field-budget_range" name="budget_range" class="form-control">
                                    <option value="">Select a range</option>
                                    @foreach ($budgets as $budget)<option value="{{ $budget }}" @selected(old('budget_range') === $budget)>{{ $budget }}</option>@endforeach
                                </select>
                            </x-forms.field>
                            <x-forms.field name="timeline" label="Timeline">
                                <select id="field-timeline" name="timeline" class="form-control">
                                    <option value="">Select a timeline</option>
                                    @foreach ($timelines as $timeline)<option value="{{ $timeline }}" @selected(old('timeline') === $timeline)>{{ $timeline }}</option>@endforeach
                                </select>
                            </x-forms.field>
                        </div>
                        <x-forms.field name="subject" label="Project title" required><input id="field-subject" name="subject" value="{{ old('subject') }}" required maxlength="200" class="form-control" placeholder="e.g. Customer portal with AI assistant" @error('subject') aria-invalid="true" @enderror></x-forms.field>
                        <x-forms.field name="message" label="Project details" required help="Goals, users, key features, existing systems and anything else that helps.">
                            <textarea id="field-message" name="message" rows="8" required minlength="20" maxlength="5000" class="form-control" aria-describedby="field-message-help" @error('message') aria-invalid="true" @enderror>{{ old('message') }}</textarea>
                        </x-forms.field>
                        @if ($attachmentsEnabled)
                            <div>
                                <p class="form-label">Attachment <span class="optional">(optional)</span></p>
                                <x-forms.file name="attachment" accept=".pdf,.doc,.docx,.txt,.png,.jpg,.jpeg,.zip" help="Brief, wireframes or RFP — PDF, DOC(X), TXT, PNG, JPG or ZIP up to {{ (int) (config('softec.uploads.max_attachment_kb') / 1024) }} MB. Stored privately." />
                                @error('attachment')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                        @endif
                    </fieldset>

                    <x-forms.consent />
                    <x-forms.submit label="Send project inquiry" />
                </form>
            </div>

            <aside class="space-y-6 lg:sticky lg:top-28 lg:self-start">
                <div class="relative overflow-hidden rounded-panel bg-ink p-7 text-white">
                    <div class="orb -top-14 -right-14 size-44 bg-brand-violet" aria-hidden="true"></div>
                    <h2 class="relative font-display text-xl font-bold">What happens next?</h2>
                    <ol class="relative mt-5 space-y-4 text-sm text-white/80">
                        @foreach (['We review your brief and reply, usually within one business day.', 'A short discovery call to understand goals and constraints.', 'You receive a proposal with approach, team, timeline and estimate.'] as $step)
                            <li class="flex gap-3"><span class="grid size-6 shrink-0 place-items-center rounded-full bg-white/15 text-xs font-bold">{{ $loop->iteration }}</span>{{ $step }}</li>
                        @endforeach
                    </ol>
                </div>
                @include('contact._details')
            </aside>
        </div>
    </section>
</x-layout>
