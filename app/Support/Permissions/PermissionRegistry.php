<?php

namespace App\Support\Permissions;

/**
 * Single source of truth for permissions and default roles.
 *
 * Seeders, the role editor, policies and tests all read from here. Adding a
 * permission means adding it below and re-running the PermissionSeeder
 * (`php artisan db:seed --class=PermissionSeeder`), which is idempotent.
 */
final class PermissionRegistry
{
    public const SUPER_ADMIN = 'Super Administrator';

    public const ADMIN = 'Administrator';

    /**
     * Permissions grouped for display in the role editor.
     *
     * @return array<string, array<string, string>>
     */
    public static function groups(): array
    {
        return [
            'Dashboard' => [
                'dashboard.view' => 'Access the admin panel and dashboard',
            ],
            'Users & roles' => [
                'users.view' => 'View users',
                'users.create' => 'Create / invite users',
                'users.update' => 'Edit users, activate and deactivate accounts',
                'users.delete' => 'Delete users',
                'roles.manage' => 'Manage roles and permissions',
            ],
            'Settings' => [
                'settings.manage' => 'Company, contact, social, forms and privacy settings',
                'appearance.manage' => 'Theme colours, modes and animation settings',
                'integrations.manage' => 'Analytics, maps, CAPTCHA and other integrations',
                'email_templates.manage' => 'Email templates and notification recipients',
                'system.manage' => 'Cache, queues, backups and maintenance tools',
            ],
            'Audit & security' => [
                'audit_logs.view' => 'View the administrative audit log',
                'security_logs.view' => 'View login and security activity',
            ],
            'Website content' => [
                'pages.view' => 'View pages and homepage sections',
                'pages.manage' => 'Create and edit pages, sections and announcements',
                'navigation.manage' => 'Manage header and footer navigation',
                'media.view' => 'Browse the media library',
                'media.manage' => 'Upload and edit media',
                'media.delete' => 'Delete media files',
            ],
            'Services & products' => [
                'services.view' => 'View services, categories and technologies',
                'services.manage' => 'Manage services, categories and technologies',
                'products.view' => 'View products',
                'products.manage' => 'Manage products, plans and product categories',
                'industries.view' => 'View industries',
                'industries.manage' => 'Manage industries',
                'portfolio.view' => 'View case studies',
                'portfolio.manage' => 'Manage case studies and portfolio categories',
            ],
            'Company' => [
                'company.view' => 'View team, milestones, achievements, statistics, FAQs',
                'company.manage' => 'Manage team, milestones, achievements, statistics, FAQs',
                'clients.manage' => 'Manage clients and logo approvals',
                'partners.manage' => 'Manage partners and alliances',
            ],
            'Blog' => [
                'blogs.view' => 'View all blog posts',
                'blogs.create' => 'Write posts (own drafts only)',
                'blogs.edit' => 'Edit posts written by others',
                'blogs.review' => 'Review submitted posts and leave feedback',
                'blogs.publish' => 'Publish, schedule and unpublish posts',
                'blogs.delete' => 'Delete posts',
                'blog_taxonomies.manage' => 'Manage blog categories, tags and authors',
                'comments.moderate' => 'Moderate blog comments',
            ],
            'SEO' => [
                'seo.manage' => 'Edit SEO metadata, global SEO settings and robots rules',
                'redirects.manage' => 'Manage URL redirects',
            ],
            'Reviews' => [
                'testimonials.view' => 'View client feedback',
                'testimonials.moderate' => 'Approve, reject, feature and reply to feedback',
                'testimonials.delete' => 'Delete feedback',
            ],
            'Query desk (leads & CRM)' => [
                'inquiries.view' => 'View inquiries assigned to me',
                'inquiries.view_team' => 'View and pick up inquiries routed to my teams',
                'inquiries.view_all' => 'View every inquiry (including unrouted ones) and move inquiries between teams',
                'inquiries.assign' => 'Assign inquiries (team leads: within their team)',
                'inquiries.update' => 'Update status, priority, notes and follow-ups',
                'inquiries.export' => 'Export inquiries to CSV',
                'inquiries.delete' => 'Delete inquiries',
                'teams.manage' => 'Manage query desk teams, members and routing rules',
                'newsletter.manage' => 'Manage newsletter subscribers',
            ],
            'Careers' => [
                'careers.view' => 'View job openings',
                'careers.manage' => 'Manage job openings',
                'applications.view' => 'View job applications and résumés',
                'applications.update' => 'Update application status and notes',
                'applications.delete' => 'Delete job applications',
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return array_keys(array_merge(...array_values(self::groups())));
    }

    public static function description(string $permission): string
    {
        return array_merge(...array_values(self::groups()))[$permission] ?? $permission;
    }

    /**
     * Default roles. `['*']` means every permission; Super Administrator is
     * additionally granted everything through a Gate::before hook.
     *
     * @return array<string, array<int, string>>
     */
    public static function roles(): array
    {
        $contentView = ['pages.view', 'services.view', 'products.view', 'industries.view', 'portfolio.view', 'company.view', 'blogs.view', 'media.view'];

        return [
            self::SUPER_ADMIN => ['*'],
            self::ADMIN => array_values(array_diff(self::all(), ['system.manage'])),
            'Content Manager' => [
                'dashboard.view', ...$contentView, 'pages.manage', 'navigation.manage', 'media.manage', 'media.delete',
                'services.manage', 'products.manage', 'industries.manage', 'portfolio.manage', 'company.manage',
                'clients.manage', 'partners.manage', 'blogs.create', 'blogs.edit', 'blogs.review', 'blogs.publish',
                'blog_taxonomies.manage', 'comments.moderate', 'testimonials.view', 'careers.view',
            ],
            'SEO Manager' => ['dashboard.view', ...$contentView, 'careers.view', 'seo.manage', 'redirects.manage'],
            'Blog Editor' => [
                'dashboard.view', 'blogs.view', 'blogs.create', 'blogs.edit', 'blogs.review', 'blogs.publish',
                'blog_taxonomies.manage', 'comments.moderate', 'media.view', 'media.manage',
            ],
            'Blog Author' => ['dashboard.view', 'blogs.create', 'media.view', 'media.manage'],
            'Product Manager' => ['dashboard.view', 'products.view', 'products.manage', 'services.view', 'industries.view', 'portfolio.view', 'media.view', 'media.manage'],
            'Service Manager' => [
                'dashboard.view', 'services.view', 'services.manage', 'industries.view', 'industries.manage',
                'portfolio.view', 'portfolio.manage', 'products.view', 'media.view', 'media.manage',
            ],
            'Sales Manager' => [
                'dashboard.view', 'inquiries.view', 'inquiries.view_team', 'inquiries.view_all', 'inquiries.assign',
                'inquiries.update', 'inquiries.export', 'teams.manage', 'newsletter.manage', 'testimonials.view',
                'services.view', 'products.view',
            ],
            'Team Lead' => [
                'dashboard.view', 'inquiries.view', 'inquiries.view_team', 'inquiries.assign', 'inquiries.update',
                'inquiries.export', 'testimonials.view',
            ],
            'Support Agent' => ['dashboard.view', 'inquiries.view', 'inquiries.view_team', 'inquiries.update', 'testimonials.view'],
            'HR Manager' => [
                'dashboard.view', 'careers.view', 'careers.manage', 'applications.view', 'applications.update',
                'applications.delete', 'company.view', 'media.view', 'media.manage', 'inquiries.view',
                'inquiries.view_team', 'inquiries.update',
            ],
            'Reviewer / Moderator' => ['dashboard.view', 'testimonials.view', 'testimonials.moderate', 'comments.moderate', 'blogs.view', 'blogs.review'],
            'Analyst (read-only)' => [
                'dashboard.view', ...$contentView, 'careers.view', 'testimonials.view', 'inquiries.view', 'inquiries.view_all',
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function permissionsForRole(string $role): array
    {
        $permissions = self::roles()[$role] ?? [];

        return $permissions === ['*'] ? self::all() : array_values(array_unique($permissions));
    }
}
