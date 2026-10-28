<?php

namespace App\Console\Commands;

use App\Services\Blog\BlogWorkflow;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * Flips scheduled blog posts whose publication time has passed to "published".
 * Idempotent; intended to run every minute from the scheduler. (Scheduled
 * posts are already publicly visible once due, so this keeps statuses tidy
 * and fires model observers such as revisions and the audit log.)
 */
#[Signature('softec:publish-scheduled')]
#[Description('Publish scheduled blog posts whose publication date has passed')]
class PublishScheduledPosts extends Command
{
    public function handle(BlogWorkflow $workflow): int
    {
        $count = $workflow->publishDueScheduledPosts();

        $this->components->info($count === 0 ? 'No scheduled posts are due.' : "Published {$count} scheduled post(s).");

        return self::SUCCESS;
    }
}
