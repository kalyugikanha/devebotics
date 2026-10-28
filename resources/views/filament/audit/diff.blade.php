@php
    $record = $getRecord();
    $old = (array) ($record->old_values ?? []);
    $new = (array) ($record->new_values ?? []);
    $keys = collect(array_keys($old))->merge(array_keys($new))->unique()->values();
    $format = fn ($value) => is_scalar($value) || $value === null
        ? (is_bool($value) ? ($value ? 'true' : 'false') : (string) ($value ?? '—'))
        : json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
@endphp
<div class="overflow-x-auto rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10">
    <table class="w-full text-start text-sm">
        <thead class="bg-gray-50 dark:bg-white/5">
            <tr>
                <th class="px-4 py-2 text-start font-semibold text-gray-950 dark:text-white">Field</th>
                <th class="px-4 py-2 text-start font-semibold text-gray-950 dark:text-white">Before</th>
                <th class="px-4 py-2 text-start font-semibold text-gray-950 dark:text-white">After</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-white/10">
            @foreach ($keys as $key)
                <tr>
                    <td class="px-4 py-2 align-top font-mono text-xs text-gray-700 dark:text-gray-300">{{ $key }}</td>
                    <td class="px-4 py-2 align-top"><pre class="whitespace-pre-wrap break-all font-mono text-xs text-danger-600 dark:text-danger-400">{{ array_key_exists($key, $old) ? $format($old[$key]) : '—' }}</pre></td>
                    <td class="px-4 py-2 align-top"><pre class="whitespace-pre-wrap break-all font-mono text-xs text-success-600 dark:text-success-400">{{ array_key_exists($key, $new) ? $format($new[$key]) : '—' }}</pre></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
