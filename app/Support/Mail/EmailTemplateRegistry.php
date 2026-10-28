<?php

namespace App\Support\Mail;

/**
 * Default content and allowed placeholders for every transactional email.
 * Administrators can edit subject/heading/body in the admin panel; only the
 * placeholders listed here are substituted (values are always escaped).
 */
final class EmailTemplateRegistry
{
    /**
     * @return array<string, array{name: string, subject: string, heading: string, body: string, action_label: ?string, variables: array<int, string>}>
     */
    public static function all(): array
    {
        return [
            'inquiry_acknowledgement' => [
                'name' => 'Contact form — acknowledgement to visitor',
                'subject' => 'We received your message ({{ reference }})',
                'heading' => 'Thanks for reaching out, {{ name }}',
                'body' => "We have received your inquiry and a member of our team will get back to you shortly, usually within one business day.\n\n**Your reference:** {{ reference }}\n\n**Subject:** {{ subject }}\n\nIf you need to add anything, simply reply to this email and quote your reference.",
                'action_label' => 'Visit our website',
                'variables' => ['name', 'reference', 'subject', 'company_name'],
            ],
            'inquiry_notification' => [
                'name' => 'Contact form — new inquiry alert (internal)',
                'subject' => 'New {{ type }}: {{ reference }} from {{ name }}',
                'heading' => 'New inquiry received',
                'body' => "**Reference:** {{ reference }}\n\n**From:** {{ name }} ({{ company }})\n\n**Service of interest:** {{ service }}\n\n**Budget:** {{ budget }} · **Timeline:** {{ timeline }}\n\n**Subject:** {{ subject }}\n\n{{ message_excerpt }}\n\nOpen the inquiry in the admin panel to view full details, attachments and assign it.",
                'action_label' => 'Open inquiry',
                'variables' => ['reference', 'type', 'name', 'company', 'service', 'budget', 'timeline', 'subject', 'message_excerpt'],
            ],
            'inquiry_assigned' => [
                'name' => 'Query desk — inquiry assigned to you (internal)',
                'subject' => 'Assigned to you: {{ reference }} from {{ name }}',
                'heading' => 'An inquiry is waiting for you',
                'body' => "Hi {{ assignee_name }},\n\n**{{ reference }}** has been assigned to you.\n\n**Team:** {{ team }}\n\n**Topic:** {{ topic }}\n\n**From:** {{ name }} ({{ company }})\n\n**Subject:** {{ subject }}\n\n{{ message_excerpt }}\n\n**Please respond by:** {{ response_due }}",
                'action_label' => 'Open inquiry',
                'variables' => ['assignee_name', 'reference', 'team', 'topic', 'name', 'company', 'subject', 'message_excerpt', 'response_due'],
            ],
            'feedback_notification' => [
                'name' => 'Client feedback — new submission alert (internal)',
                'subject' => 'New client feedback ({{ rating }}/5) awaiting moderation',
                'heading' => 'New feedback awaiting moderation',
                'body' => "**Client:** {{ client_name }} ({{ company }})\n\n**Overall rating:** {{ rating }}/5\n\n**Permission to publish:** {{ publish_permission }}\n\n{{ feedback_excerpt }}\n\nReview it in the admin panel before anything is published.",
                'action_label' => 'Moderate feedback',
                'variables' => ['client_name', 'company', 'rating', 'publish_permission', 'feedback_excerpt'],
            ],
            'feedback_verification' => [
                'name' => 'Client feedback — confirm email address',
                'subject' => 'Please confirm your feedback for {{ company_name }}',
                'heading' => 'Confirm your feedback',
                'body' => "Hi {{ client_name }},\n\nThank you for sharing your experience with {{ company_name }}. Please confirm your email address so we can review your feedback.\n\nIf you did not submit feedback, you can ignore this email.",
                'action_label' => 'Confirm my feedback',
                'variables' => ['client_name', 'company_name'],
            ],
            'testimonial_approved' => [
                'name' => 'Client feedback — published notice',
                'subject' => 'Your feedback is now live',
                'heading' => 'Thank you, {{ client_name }}',
                'body' => 'Your feedback has been approved and published on our website. We truly appreciate you taking the time to share your experience with {{ company_name }}.',
                'action_label' => 'View reviews',
                'variables' => ['client_name', 'company_name'],
            ],
            'testimonial_reply' => [
                'name' => 'Client feedback — private reply',
                'subject' => 'A reply to your feedback',
                'heading' => 'Hi {{ client_name }}',
                'body' => "{{ reply }}\n\n— The {{ company_name }} team",
                'action_label' => null,
                'variables' => ['client_name', 'reply', 'company_name'],
            ],
            'job_application_acknowledgement' => [
                'name' => 'Careers — application acknowledgement',
                'subject' => 'We received your application ({{ reference }})',
                'heading' => 'Thanks for applying, {{ name }}',
                'body' => "We have received your application for **{{ position }}**. Our team reviews every application carefully and will contact you if your profile matches the role.\n\n**Your reference:** {{ reference }}",
                'action_label' => 'See other openings',
                'variables' => ['name', 'position', 'reference'],
            ],
            'job_application_notification' => [
                'name' => 'Careers — new application alert (internal)',
                'subject' => 'New application for {{ position }}: {{ name }}',
                'heading' => 'New job application',
                'body' => "**Reference:** {{ reference }}\n\n**Candidate:** {{ name }}\n\n**Position:** {{ position }}\n\nOpen the application in the admin panel to review the résumé.",
                'action_label' => 'Open application',
                'variables' => ['reference', 'name', 'position'],
            ],
            'newsletter_confirmation' => [
                'name' => 'Newsletter — confirm subscription',
                'subject' => 'Confirm your subscription to {{ company_name }} insights',
                'heading' => 'One more step',
                'body' => "Please confirm that you would like to receive occasional insights and updates from {{ company_name }}. You can unsubscribe at any time.\n\nIf you did not request this, you can ignore this email.",
                'action_label' => 'Confirm subscription',
                'variables' => ['company_name'],
            ],
            'user_invitation' => [
                'name' => 'Admin — account invitation',
                'subject' => 'You have been invited to the {{ company_name }} admin panel',
                'heading' => 'Welcome, {{ name }}',
                'body' => "An administrator has created an account for you with the role **{{ roles }}**.\n\nUse the button below to choose your password. The link expires in 60 minutes; you can request a new one from the sign-in page.",
                'action_label' => 'Set my password',
                'variables' => ['name', 'roles', 'company_name'],
            ],
            'security_alert' => [
                'name' => 'Admin — security notification',
                'subject' => 'Security notice for your {{ company_name }} account',
                'heading' => 'Security notice',
                'body' => "Hi {{ name }},\n\n{{ event }}\n\n**When:** {{ time }}\n\nIf this was not you, reset your password immediately and contact your administrator.",
                'action_label' => 'Open admin panel',
                'variables' => ['name', 'event', 'time', 'company_name'],
            ],
            'test_email' => [
                'name' => 'System — test email',
                'subject' => 'Test email from {{ company_name }}',
                'heading' => 'Email delivery works',
                'body' => "This is a test message sent by {{ name }} from the admin panel at {{ time }}.\n\nIf you can read this, outgoing email is configured correctly.",
                'action_label' => null,
                'variables' => ['name', 'time', 'company_name'],
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function variables(string $key): array
    {
        return self::all()[$key]['variables'] ?? [];
    }
}
