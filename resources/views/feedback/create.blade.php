<x-layout :seo="$seo">
    <x-site.page-hero :seo="$seo" eyebrow="Feedback" title="Tell us about [your experience]"
        intro="Your feedback helps us improve. With your permission we may publish it as a testimonial — always after review, and only with the details you allow." />

    <section class="section pt-12">
        <div class="container-site grid gap-12 lg:grid-cols-[1.4fr_1fr]">
            <div>
                @if (session('feedback_submitted'))
                    <div class="alert alert-success" role="status" tabindex="-1" x-data x-init="$el.focus()">
                        <x-heroicon-o-check-circle class="size-6 shrink-0" aria-hidden="true" />
                        <div>
                            <p class="font-semibold">Thank you for your feedback!</p>
                            <p class="mt-1">Our team will review it shortly.@if (settings('forms.feedback_email_verification')) Please check your inbox to confirm your email address.@endif</p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('feedback.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6" x-data="formState" x-on:submit="submit" novalidate>
                    @csrf
                    <x-forms.spam-protection />
                    <x-forms.form-errors />

                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-forms.field name="client_name" label="Your name" required><input id="field-client_name" name="client_name" value="{{ old('client_name') }}" required maxlength="120" autocomplete="name" class="form-control" @error('client_name') aria-invalid="true" aria-describedby="field-client_name-error" @enderror></x-forms.field>
                        <x-forms.field name="email" label="Email address" required help="Never published. Used only if we need to follow up."><input id="field-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="form-control" aria-describedby="field-email-help" @error('email') aria-invalid="true" @enderror></x-forms.field>
                        <x-forms.field name="company_name" label="Company"><input id="field-company_name" name="company_name" value="{{ old('company_name') }}" maxlength="160" autocomplete="organization" class="form-control"></x-forms.field>
                        <x-forms.field name="job_title" label="Job title"><input id="field-job_title" name="job_title" value="{{ old('job_title') }}" maxlength="120" autocomplete="organization-title" class="form-control"></x-forms.field>
                        <x-forms.field name="service_id" label="Service used">
                            <select id="field-service_id" name="service_id" class="form-control">
                                <option value="">Select a service</option>
                                @foreach ($services as $id => $title)<option value="{{ $id }}" @selected((int) old('service_id') === $id)>{{ $title }}</option>@endforeach
                            </select>
                        </x-forms.field>
                        <x-forms.field name="project_name" label="Project name"><input id="field-project_name" name="project_name" value="{{ old('project_name') }}" maxlength="160" class="form-control"></x-forms.field>
                    </div>

                    <div class="rounded-panel border border-border bg-surface p-6">
                        <x-forms.star-rating name="rating" label="Overall rating" required />
                        <div class="mt-6 grid gap-6 sm:grid-cols-3">
                            <x-forms.star-rating name="rating_communication" label="Communication" size="sm" />
                            <x-forms.star-rating name="rating_quality" label="Quality" size="sm" />
                            <x-forms.star-rating name="rating_delivery" label="Delivery" size="sm" />
                        </div>
                    </div>

                    <x-forms.field name="feedback" label="Your feedback" required help="At least 20 characters.">
                        <textarea id="field-feedback" name="feedback" rows="6" required minlength="20" maxlength="3000" class="form-control" aria-describedby="field-feedback-help" @error('feedback') aria-invalid="true" @enderror>{{ old('feedback') }}</textarea>
                    </x-forms.field>

                    <fieldset class="space-y-4 rounded-panel border border-border p-6">
                        <legend class="px-2 text-sm font-semibold">Publishing permissions</legend>
                        <label class="flex items-start gap-3 text-sm"><input type="checkbox" name="publish_permission" value="1" class="form-check" @checked(old('publish_permission'))><span><span class="font-semibold text-foreground">You may publish my feedback</span><span class="block text-muted">After review, as a testimonial on the website.</span></span></label>
                        <label class="flex items-start gap-3 text-sm"><input type="checkbox" name="display_identity_consent" value="1" class="form-check" @checked(old('display_identity_consent'))><span><span class="font-semibold text-foreground">You may show my name, company and photo</span><span class="block text-muted">Otherwise it is shown as “Verified client”.</span></span></label>
                        <div>
                            <p class="form-label">Photo or company logo <span class="optional">(optional)</span></p>
                            <x-forms.file name="photo" accept="image/jpeg,image/png,image/webp" help="JPG, PNG or WebP, up to 2 MB. Only used with the permission above." />
                            @error('photo')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                    </fieldset>

                    <x-forms.consent />
                    <x-forms.submit label="Submit feedback" />
                </form>
            </div>

            <aside class="space-y-6 lg:sticky lg:top-28 lg:self-start">
                <div class="card card-muted">
                    <span class="icon-tile-soft"><x-heroicon-o-shield-check aria-hidden="true" /></span>
                    <h2 class="heading-card mt-4">How we handle feedback</h2>
                    <ul class="mt-4 space-y-3 text-sm text-muted">
                        <li class="flex gap-2"><x-heroicon-s-check-circle class="mt-0.5 size-4 shrink-0 text-brand-mint" aria-hidden="true" />Every submission is reviewed by a moderator.</li>
                        <li class="flex gap-2"><x-heroicon-s-check-circle class="mt-0.5 size-4 shrink-0 text-brand-mint" aria-hidden="true" />Nothing is published without your permission.</li>
                        <li class="flex gap-2"><x-heroicon-s-check-circle class="mt-0.5 size-4 shrink-0 text-brand-mint" aria-hidden="true" />Your email address is never shown publicly.</li>
                        <li class="flex gap-2"><x-heroicon-s-check-circle class="mt-0.5 size-4 shrink-0 text-brand-mint" aria-hidden="true" />You can ask us to remove your testimonial at any time.</li>
                    </ul>
                </div>
            </aside>
        </div>
    </section>
</x-layout>
