{{-- Interactive hero scene: orbit system around the brand mark with floating product-UI cards (illustrative, not data). --}}
<div class="relative mx-auto aspect-square w-full max-w-[30rem] lg:max-w-[33rem] xl:max-w-[36rem]" data-parallax-scene aria-hidden="true">
    <div class="absolute inset-0" data-tilt="5">
        {{-- Glow --}}
        <div class="absolute inset-[16%] rounded-full bg-[radial-gradient(circle,rgba(139,92,246,.55),rgba(217,70,239,.22)_45%,transparent_70%)] blur-2xl"></div>

        {{-- Orbit rings --}}
        <svg viewBox="0 0 500 500" class="ambient absolute inset-0 h-full w-full animate-orbit" data-depth="6">
            <defs>
                <linearGradient id="hero-scene-a" x1="0" x2="1" y1="0" y2="1">
                    <stop offset="0" stop-color="#8b5cf6" stop-opacity="0" />
                    <stop offset=".45" stop-color="#818cf8" />
                    <stop offset=".75" stop-color="#22d3ee" />
                    <stop offset="1" stop-color="#a78bfa" stop-opacity=".25" />
                </linearGradient>
                <linearGradient id="hero-scene-b" x1="1" x2="0">
                    <stop offset="0" stop-color="#22d3ee" stop-opacity="0" />
                    <stop offset=".5" stop-color="#22d3ee" />
                    <stop offset="1" stop-color="#34d399" stop-opacity=".15" />
                </linearGradient>
            </defs>
            <circle cx="250" cy="250" r="238" fill="none" stroke="#ffffff" stroke-opacity=".12" stroke-dasharray="2 10" />
            <circle cx="250" cy="250" r="196" fill="none" stroke="url(#hero-scene-a)" stroke-width="2" />
            <circle cx="250" cy="54" r="7" fill="#22d3ee" />
            <circle cx="250" cy="54" r="15" fill="none" stroke="#22d3ee" stroke-opacity=".35" />
        </svg>
        <svg viewBox="0 0 500 500" class="ambient absolute inset-0 h-full w-full animate-orbit-reverse" data-depth="10">
            <ellipse cx="250" cy="250" rx="232" ry="94" fill="none" stroke="url(#hero-scene-b)" stroke-width="3" transform="rotate(-24 250 250)" />
            <circle cx="444" cy="166" r="10" fill="url(#hero-scene-a)" />
            <circle cx="92" cy="322" r="5" fill="#22d3ee" />
        </svg>

        {{-- Pixel cluster --}}
        <div class="ambient absolute top-[8%] left-[5%] grid animate-float grid-cols-3 gap-2 [animation-delay:-2s]" data-depth="22">
            <span class="size-5 rounded-md bg-brand-cyan/85"></span><span class="size-6 rounded-lg bg-brand-amber"></span><span></span>
            <span class="size-4 rounded-md bg-brand-mint/85"></span><span class="size-6 rounded-lg bg-brand-pink/85"></span><span class="size-4 rounded-md bg-brand-violet"></span>
            <span></span><span class="size-6 rounded-lg bg-brand-violet/70"></span><span></span>
        </div>

        {{-- Brand core --}}
        <div class="absolute inset-[26%] grid place-items-center" data-depth="14">
            <div class="relative grid size-full place-items-center rounded-full border border-white/15 bg-white/[0.06] shadow-[inset_0_1px_0_rgba(255,255,255,.15),0_30px_80px_-20px_rgba(79,70,229,.6)] backdrop-blur-xl">
                <span class="ambient absolute inset-0 animate-pulse-ring rounded-full border border-[#818cf8]/40"></span>
                <span class="absolute inset-3 rounded-full bg-[conic-gradient(from_180deg,rgba(139,92,246,.18),rgba(217,70,239,.08),rgba(34,211,238,.18),rgba(139,92,246,.18))]"></span>
                <img src="{{ \App\Support\Branding::markUrl() }}" alt="Softec AI Core Technology Platform" width="240" height="194" class="relative w-[64%] drop-shadow-[0_22px_40px_rgba(79,70,229,.55)]">
            </div>
        </div>

        {{-- AI assistant card --}}
        <div class="ambient absolute top-[9%] right-0 lg:right-[-1%] xl:right-[-2%] w-52 animate-float rounded-2xl border border-white/12 bg-[#0f1530]/80 p-3.5 text-left shadow-2xl backdrop-blur-xl sm:w-60" data-depth="30">
            <div class="flex items-center gap-2.5">
                <span class="grid size-8 place-items-center rounded-lg bg-[linear-gradient(135deg,#6d28d9,#4f46e5)] text-white"><x-heroicon-s-sparkles class="size-4" /></span>
                <div class="min-w-0">
                    <p class="truncate text-xs font-bold text-white">AI assistant</p>
                    <p class="flex items-center gap-1 text-[0.6875rem] text-white/60">Thinking<span class="inline-flex gap-0.5"><span class="size-1 animate-pulse rounded-full bg-white/70"></span><span class="size-1 animate-pulse rounded-full bg-white/70 [animation-delay:.2s]"></span><span class="size-1 animate-pulse rounded-full bg-white/70 [animation-delay:.4s]"></span></span></p>
                </div>
            </div>
            <div class="mt-3 space-y-1.5">
                <div class="h-1.5 w-full rounded-full bg-white/10"></div>
                <div class="h-1.5 w-4/5 rounded-full bg-white/10"></div>
                <div class="h-1.5 w-2/3 overflow-hidden rounded-full bg-white/10"><div class="h-full w-full animate-shimmer bg-[linear-gradient(90deg,#8b5cf6,#6366f1,#22d3ee,#8b5cf6)] bg-[length:200%_100%]"></div></div>
            </div>
        </div>

        {{-- Analytics card --}}
        <div class="ambient absolute top-[46%] right-0 lg:right-[-2%] xl:right-[-3%] hidden w-44 animate-float rounded-2xl border border-white/12 bg-[#0f1530]/80 p-3.5 shadow-2xl backdrop-blur-xl [animation-delay:-4s] sm:block" data-depth="26">
            <p class="text-[0.6875rem] font-semibold text-white/60">Performance</p>
            <svg viewBox="0 0 140 48" class="mt-2 h-12 w-full">
                <defs><linearGradient id="hero-spark" x1="0" x2="0" y1="0" y2="1"><stop offset="0" stop-color="#22d3ee" stop-opacity=".45" /><stop offset="1" stop-color="#22d3ee" stop-opacity="0" /></linearGradient></defs>
                <path d="M0 40 L18 34 L34 36 L52 24 L70 28 L88 16 L106 19 L124 8 L140 10 L140 48 L0 48 Z" fill="url(#hero-spark)" />
                <path d="M0 40 L18 34 L34 36 L52 24 L70 28 L88 16 L106 19 L124 8 L140 10" fill="none" stroke="#22d3ee" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="ill-draw" style="--ill-length:190" />
            </svg>
        </div>

        {{-- Deploy pipeline card --}}
        <div class="ambient absolute bottom-[10%] left-0 lg:left-[-1%] xl:left-[-3%] w-56 animate-float rounded-2xl border border-white/12 bg-[#0f1530]/80 p-3.5 shadow-2xl backdrop-blur-xl [animation-delay:-3s] sm:w-60" data-depth="34">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-white">Deploy pipeline</p>
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-400/15 px-2 py-0.5 text-[0.625rem] font-bold text-emerald-300"><span class="size-1.5 rounded-full bg-emerald-400"></span>Passed</span>
            </div>
            <div class="mt-3 flex items-center gap-1.5">
                @foreach (['Build', 'Test', 'Scan', 'Ship'] as $step)
                    <div class="flex-1">
                        <div class="h-1.5 rounded-full bg-[linear-gradient(90deg,#22d3ee,#34d399)]"></div>
                        <p class="mt-1 text-[0.5625rem] font-medium text-white/55">{{ $step }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Code card --}}
        <div class="ambient decorative-desktop absolute right-[4%] bottom-[2%] hidden animate-float rounded-2xl border border-white/12 bg-[#0f1530]/85 px-4 py-3 font-mono text-[0.6875rem] leading-relaxed shadow-2xl backdrop-blur-xl [animation-delay:-5s] md:block" data-depth="18">
            <p><span class="text-[#c4b5fd]">await</span> <span class="text-white">agent</span>.<span class="text-[#67e8f9]">plan</span>(<span class="text-[#fcd34d]">goals</span>)</p>
            <p class="text-white/45">// grounded in your data<span class="ml-0.5 inline-block h-3 w-1.5 translate-y-0.5 bg-white/70 ill-blink"></span></p>
        </div>
    </div>
</div>
