@props(['faq', 'index' => 0])
<details class="accordion-item group" data-reveal style="--i:{{ $index }}">
    <summary>
        <span>{{ $faq->question }}</span>
        <span class="accordion-icon" aria-hidden="true"><x-heroicon-m-plus class="size-4" /></span>
    </summary>
    <div class="accordion-body prose-brand text-[0.9375rem]">{!! $faq->answer !!}</div>
</details>
