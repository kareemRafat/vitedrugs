@props([
    /**
     * One entry of the browse hub's chapter data: index, route, image, width,
     * height, image_alt, title, body, link. Built at
     * resources/views/app/landing/sections/chapters.blade.php.
     *
     * The intrinsic width/height ride along with the card rather than being passed
     * as their own attribute: Blade mis-compiles an array lookup such as
     * `:dimensions="$sizes[$card['image']]"` into unbalanced brackets.
     */
    'chapter' => [],
    /**
     * How the card takes its height. This is the only thing that differs between
     * the two columns: the two start-column cards split the column between them,
     * while the end-column card is stretched to match both.
     */
    'sizing' => 'flex-1 min-h-[260px] lg:min-h-[280px]',
])

{{--
    Full-bleed photograph with the copy stacked at the bottom over a scrim. The
    scrim runs almost to opaque at the foot because the copy sits on the darkest
    part of it - the reference's horizontal split has no room in a half-width card.
--}}
<article {{ $attributes->class([
        'group relative flex flex-col justify-end overflow-hidden rounded-2xl lg:rounded-3xl bg-slate-950 border border-slate-800/80 shadow-xl transition-all duration-500 hover:border-slate-700 hover:shadow-2xl hover:shadow-black/40',
        $sizing,
    ]) }}>
    <div class="absolute inset-0 z-0 overflow-hidden">
        <picture>
            <source srcset="{{ asset("images/landing/{$chapter['image']}.webp") }}" type="image/webp">
            <img src="{{ asset("images/landing/{$chapter['image']}.jpg") }}"
                alt="{{ $chapter['image_alt'] }}" width="{{ $chapter['width'] }}" height="{{ $chapter['height'] }}"
                loading="lazy" decoding="async"
                class="w-full h-full object-cover object-center filter brightness-[0.85] contrast-[1.1] transition-transform duration-700 ease-out group-hover:scale-105 group-hover:brightness-95">
        </picture>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/75 to-slate-950/20"></div>
    </div>

    <div class="relative z-10 p-6 sm:p-8">
        <span class="text-xs font-mono font-bold tracking-widest text-amber-400 uppercase mb-2">/ {{ $chapter['index'] }}</span>
        <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white uppercase mb-2 font-sans">{{ $chapter['title'] }}</h3>
        <p class="text-xs sm:text-sm text-slate-300 font-normal leading-relaxed max-w-md mb-4">{{ $chapter['body'] }}</p>
        <a href="{{ route($chapter['route']) }}"
            class="inline-flex items-center gap-2 text-xs font-bold tracking-wider uppercase text-amber-400 hover:text-white transition-colors">
            <span>{{ $chapter['link'] }}</span>
            <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M5 12h14m0 0-6-6m6 6-6 6" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </a>
    </div>
</article>