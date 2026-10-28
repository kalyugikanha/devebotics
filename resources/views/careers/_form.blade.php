@if (session('application_reference'))
    <div class="alert alert-success mb-6" role="status" tabindex="-1" x-data x-init="$el.focus()">
        <x-heroicon-o-check-circle class="size-6 shrink-0" aria-hidden="true" />
        <div><p class="font-semibold">Application received — thank you!</p><p class="mt-1">Your reference is <strong class="font-mono">{{ session('application_reference') }}</strong>. We will be in touch if your profile matches.</p></div>
    </div>
@endif
<form method="POST" action="{{ route('careers.apply', $job?->slug) }}" enctype="multipart/form-data" class="space-y-5" x-data="formState" x-on:submit="submit" novalidate>
    @csrf
    <x-forms.spam-protection />
    <x-forms.form-errors />
    <div class="grid gap-5 sm:grid-cols-2">
        <x-forms.field name="name" label="Full name" required><input id="field-name" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name" class="form-control" @error('name') aria-invalid="true" @enderror></x-forms.field>
        <x-forms.field name="email" label="Email" required><input id="field-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="form-control" @error('email') aria-invalid="true" @enderror></x-forms.field>
        <x-forms.field name="phone" label="Phone"><input id="field-phone" type="tel" name="phone" value="{{ old('phone') }}" maxlength="40" autocomplete="tel" class="form-control" @error('phone') aria-invalid="true" @enderror></x-forms.field>
        <x-forms.field name="location" label="Location"><input id="field-location" name="location" value="{{ old('location') }}" maxlength="120" autocomplete="address-level2" class="form-control"></x-forms.field>
        <x-forms.field name="linkedin_url" label="LinkedIn profile"><input id="field-linkedin_url" type="url" name="linkedin_url" value="{{ old('linkedin_url') }}" placeholder="https://www.linkedin.com/in/…" class="form-control" @error('linkedin_url') aria-invalid="true" @enderror></x-forms.field>
        <x-forms.field name="portfolio_url" label="Portfolio / GitHub"><input id="field-portfolio_url" type="url" name="portfolio_url" value="{{ old('portfolio_url') }}" placeholder="https://" class="form-control" @error('portfolio_url') aria-invalid="true" @enderror></x-forms.field>
    </div>
    <div>
        <p class="form-label">Résumé / CV <span class="text-highlight" aria-hidden="true">*</span></p>
        <x-forms.file name="resume" accept=".pdf,.doc,.docx" required help="PDF, DOC or DOCX up to {{ (int) (config('softec.uploads.max_attachment_kb') / 1024) }} MB. Stored privately." />
        @error('resume')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <x-forms.field name="cover_letter" label="Why would you like to join us?"><textarea id="field-cover_letter" name="cover_letter" rows="5" maxlength="5000" class="form-control">{{ old('cover_letter') }}</textarea></x-forms.field>
    <x-forms.consent />
    <x-forms.submit label="Submit application" />
</form>
