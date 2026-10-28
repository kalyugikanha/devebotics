<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Read-only view of the queue's failed_jobs table for the System health page.
 */
class FailedJob extends Model
{
    public $timestamps = false;

    protected $table = 'failed_jobs';

    protected function casts(): array
    {
        return [
            'failed_at' => 'datetime',
        ];
    }

    public function displayName(): string
    {
        $payload = json_decode((string) $this->payload, true);

        return (string) ($payload['displayName'] ?? 'Unknown job');
    }

    public function exceptionSummary(): string
    {
        return strtok((string) $this->exception, "\n") ?: '';
    }
}
