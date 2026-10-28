<?php

namespace App\Support\Forms;

use App\Enums\ConsentType;
use App\Models\ConsentRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Stores an auditable consent trail (type, decision, policy version, time).
 * IP addresses are stored as keyed hashes only.
 */
class ConsentRecorder
{
    /**
     * @param  array<string, mixed>|null  $details
     */
    public function record(ConsentType $type, bool $granted, ?Model $subject = null, ?Request $request = null, ?array $details = null): ConsentRecord
    {
        $request ??= request();

        return ConsentRecord::query()->create([
            'consentable_type' => $subject?->getMorphClass(),
            'consentable_id' => $subject?->getKey(),
            'consent_type' => $type,
            'granted' => $granted,
            'policy_version' => (string) settings('privacy.consent_version'),
            'details' => $details,
            'ip_hash' => SpamGuard::hashIp($request->ip()),
            'user_agent' => Str::limit((string) $request->userAgent(), 250, ''),
            'created_at' => now(),
        ]);
    }
}
