<section id="{{ $section->anchor ?: 'section-'.$section->id }}" class="dark stage section" aria-labelledby="section-heading-{{ $section->id }}">
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(90%_120%_at_50%_0%,#1a1442_0%,#050816_60%)]" aria-hidden="true"></div>
    <div class="aurora ambient animate-aurora -top-40 left-1/4 size-[32rem] bg-[#4f46e5]/35" aria-hidden="true"></div>
    <div class="aurora ambient animate-aurora -right-40 -bottom-48 size-[30rem] bg-[#06b6d4]/25 [animation-delay:-9s]" aria-hidden="true"></div>
    <div class="bg-grid-lines absolute inset-0 -z-10 opacity-50" aria-hidden="true"></div>
    <div class="container-site">
        <x-ui.section-heading :eyebrow="$section->eyebrow" :title="$section->title" :subtitle="$section->subtitle" id="section-heading-{{ $section->id }}" />
        <dl class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-{{ min(4, max(2, $records->count())) }}">
            @foreach ($records as $statistic)
                <div class="group relative flex flex-col gap-2 overflow-hidden rounded-panel border border-white/10 bg-white/[0.04] p-8 text-center backdrop-blur transition duration-500 hover:-translate-y-1 hover:border-white/20 hover:bg-white/[0.07]" data-reveal data-spotlight style="--i:{{ $loop->index }}">
                    <dt class="order-2 text-sm font-medium text-white/65">{{ $statistic->label }}</dt>
                    <dd class="order-1 font-display text-5xl font-extrabold tracking-tight sm:text-6xl">
                        <span class="tabular-nums-fixed text-gradient" data-counter="{{ (float) $statistic->value }}" data-decimals="{{ $statistic->decimals }}" data-prefix="{{ $statistic->prefix }}" data-suffix="{{ $statistic->suffix }}">{{ $statistic->formattedValue() }}</span>
                    </dd>
                    @if ($statistic->is_demo)
                        <dd class="order-3"><x-ui.demo-badge :show="true" /></dd>
                    @elseif ($statistic->description)
                        <dd class="order-3 text-xs text-white/55">{{ $statistic->description }}</dd>
                    @endif
                </div>
            @endforeach
        </dl>
    </div>
</section>
