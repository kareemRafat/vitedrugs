@props([
    /**
     * Body systems keyed by canonical name, each `['system' => BodySystem,
     * 'attachments' => Collection, 'diseases' => int, 'organs' => int]`.
     */
    'systems' => [],
    'selected' => null,
    'label' => '',
])

{{--
    A schematic hen, drawn here rather than imported.

    No illustration was licensed for this page and none exists in the repository,
    so both the bird and the organ regions are drawn inline. Marker positions are
    indicative rather than anatomical and the page says so. Drawing it inline
    means no image request, no pinch-zoom interception and no licensing question,
    and when the anatomy needs to move it moves here rather than in a design file
    nobody can diff.

    Hotspots are body systems drawn as organs, not dots. A dot cannot point at a
    thyroid gland, a lymph node or a spinal cord, and those are three of the
    eleven systems — which is the whole reason this is shapes rather than markers.

    `$shapes` holds the inner markup of each region, colour-free and built on
    `currentColor` so one class on the wrapping `<g>` drives every shape in it.
    A system with no entry here is still listed, still selectable and still
    readable in the panel; it simply has no artwork on the bird. Adding a
    thirteenth system to the table does not draw itself.

    The hen faces start, and the SVG is never mirrored for RTL: anatomy should not
    reverse under an Arabic locale.
--}}
@php
    /**
     * The bird: tail plumes, body, head furniture, wing, legs.
     *
     * Held back so the organs read on top, but it still has to read as a bird —
     * the first pass dropped the beak, eye and legs and it became a blob.
     */
    $bird = <<<'SVG'
    <g fill="#cbd5e1">
      <path d="M466 252 Q566 178 652 118 Q588 198 492 266 Z"/>
      <path d="M476 280 Q578 232 656 184 Q600 264 500 290 Z"/>
      <path d="M484 306 Q570 282 628 246 Q578 314 502 316 Z"/>
      <path d="M492 330 Q554 326 582 300 Q548 346 506 342 Z"/>
      <path d="
        M 100 182
        C 116 176, 130 172, 142 170
        C 140 138, 156 118, 184 118
        C 212 118, 232 138, 234 168
        C 235 188, 233 202, 229 214
        C 240 214, 252 218, 266 222
        C 310 214, 366 214, 418 226
        C 454 234, 476 246, 488 262
        C 504 290, 502 324, 484 350
        C 454 384, 404 402, 346 406
        C 302 408, 258 398, 230 374
        C 200 348, 184 320, 180 286
        C 177 260, 180 242, 190 226
        C 182 236, 168 232, 162 216
        C 152 208, 140 202, 126 196
        C 114 190, 104 186, 100 182
        Z"/>

      <path fill="#f59e0b" d="M 118 180 L 62 190 L 118 198 Z"/>
      <path fill="#d97706" d="M 118 187 L 62 190 L 118 194 Z"/>
      <path fill="#dc2626" d="
        M 140 132
        C 140 104, 148 86, 158 82
        C 166 66, 180 62, 186 78
        C 194 60, 210 60, 214 80
        C 222 68, 236 72, 236 90
        C 240 102, 236 118, 230 128
        C 206 118, 168 120, 140 132
        Z"/>
      <path fill="#dc2626" d="M 146 194 C 138 204, 138 226, 148 232 C 158 238, 166 226, 164 208 C 162 196, 154 190, 146 194 Z"/>
      <circle cx="172" cy="158" r="7" fill="#334155"/>
      <circle cx="170" cy="156" r="2.4" fill="#ffffff"/>

      <path d="
        M 256 258
        C 310 240, 384 242, 442 260
        C 470 270, 486 284, 488 298
        C 456 312, 394 318, 334 314
        C 288 310, 260 288, 256 258
        Z"/>
      <g fill="none" stroke="#94a3b8" stroke-width="3" stroke-linecap="round">
        <path d="M270 280 C324 294 396 294 462 284"/>
        <path d="M274 296 C328 308 400 306 466 294"/>
      </g>

      <g fill="#dde4ed">
        <ellipse cx="302" cy="394" rx="30" ry="26"/>
        <ellipse cx="356" cy="394" rx="30" ry="26"/>
      </g>
      <g stroke="#94a3b8" stroke-width="14" stroke-linecap="round" fill="none">
        <path d="M302 402 L308 440 L302 466"/>
        <path d="M356 402 L360 440 L354 466"/>
      </g>
      <g stroke="#94a3b8" stroke-width="9" stroke-linecap="round" fill="none">
        <path d="M302 466 L268 484"/>
        <path d="M302 466 L302 492"/>
        <path d="M302 466 L336 482"/>
        <path d="M302 466 L330 472"/>
        <path d="M354 466 L320 484"/>
        <path d="M354 466 L354 492"/>
        <path d="M354 466 L388 482"/>
        <path d="M354 466 L382 472"/>
      </g>
    </g>
    SVG;

    /**
     * One entry per body system. Authored here, not data-driven: the anatomy is
     * a drawing, and a drawing does not come out of a join.
     */
    $shapes = [
        'digestive' => <<<'SVG'
        <g fill="currentColor" stroke="currentColor">
            <path d="M158 182 C180 186 196 196 208 206" fill="none" stroke-width="9" stroke-linecap="round"/>
            <ellipse cx="222" cy="212" rx="19" ry="13" stroke="none"/>
            <path d="M232 220 C252 236 268 250 282 268" fill="none" stroke-width="7" stroke-linecap="round"/>
            <path d="M316 292 C310 310 328 326 346 322 C364 318 368 298 354 286 C342 276 322 278 316 292 Z" stroke="none"/>
            <path d="M348 332 C366 324 392 332 396 350 C400 366 384 378 366 374 C350 370 342 354 350 342
                     C356 334 370 334 376 342 C382 350 374 358 366 354" fill="none"
                stroke-width="8" stroke-linecap="round"/>
            <ellipse cx="452" cy="348" rx="10" ry="9" stroke="none"/>
        </g>
        SVG,

        'respiratory' => <<<'SVG'
        <g fill="currentColor" stroke="currentColor">
            <path d="M200 196 C222 214 242 232 268 246" fill="none" stroke-width="7" stroke-linecap="round"/>
            <path d="M286 264 C296 250 330 248 342 260 C350 270 342 284 328 288 C308 292 288 280 286 264 Z" stroke="none"/>
            <path d="M346 262 C356 250 384 250 392 262 C398 272 390 284 374 286 C358 288 346 276 346 262 Z" stroke="none"/>
            <ellipse cx="398" cy="316" rx="30" ry="22" stroke="none"/>
            <ellipse cx="444" cy="298" rx="24" ry="18" stroke="none"/>
            <ellipse cx="450" cy="338" rx="20" ry="16" stroke="none"/>
            <ellipse cx="368" cy="352" rx="20" ry="15" stroke="none"/>
        </g>
        SVG,

        'cardiovascular' => <<<'SVG'
        <g fill="currentColor" stroke="currentColor">
            <path d="M280 292 C272 278 284 266 298 270 C312 274 312 294 298 306 C286 314 276 302 280 292 Z" stroke="none"/>
            <path d="M288 268 C284 250 280 236 276 222" fill="none" stroke-width="6" stroke-linecap="round"/>
            <path d="M288 254 C318 248 350 252 374 262" fill="none" stroke-width="5" stroke-linecap="round"/>
        </g>
        SVG,

        'urinary' => <<<'SVG'
        <g fill="currentColor">
            <path d="M396 262 C420 252 452 254 466 264 C472 272 464 278 450 278 C424 276 400 272 396 262 Z"/>
            <path d="M398 288 C422 280 452 284 464 292 C470 300 462 306 448 306 C424 304 402 298 398 288 Z"/>
        </g>
        SVG,

        'reproductive' => <<<'SVG'
        <g fill="currentColor" stroke="currentColor">
            <circle cx="344" cy="302" r="8" stroke="none"/><circle cx="358" cy="297" r="7" stroke="none"/>
            <circle cx="352" cy="312" r="7" stroke="none"/><circle cx="366" cy="309" r="6" stroke="none"/>
            <path d="M372 320 C390 314 406 324 400 338 C394 350 374 350 370 338 C367 330 380 328 386 334"
                fill="none" stroke-width="6" stroke-linecap="round"/>
        </g>
        SVG,

        'immune' => <<<'SVG'
        <g fill="currentColor">
            <circle cx="244" cy="230" r="7"/>
            <circle cx="258" cy="240" r="6"/>
            <circle cx="271" cy="246" r="5"/>
            <ellipse cx="328" cy="330" rx="16" ry="11"/>
            <ellipse cx="438" cy="352" rx="12" ry="16"/>
        </g>
        SVG,

        'nervous' => <<<'SVG'
        <g fill="currentColor" stroke="currentColor">
            <ellipse cx="198" cy="164" rx="20" ry="17" stroke="none"/>
            <path d="M198 194 C222 210 238 224 266 230 C306 236 356 234 402 246" fill="none"
                stroke-width="8" stroke-linecap="round"/>
        </g>
        SVG,

        'lymphatic_system' => <<<'SVG'
        <g fill="currentColor">
            <circle cx="286" cy="252" r="6"/>
            <circle cx="304" cy="260" r="5"/>
            <circle cx="296" cy="388" r="6"/>
            <circle cx="316" cy="392" r="5"/>
        </g>
        SVG,

        'endocrine' => <<<'SVG'
        <g fill="currentColor">
            <ellipse cx="210" cy="204" rx="10" ry="7"/>
            <ellipse cx="228" cy="210" rx="9" ry="6"/>
        </g>
        SVG,

        'musculoskeletal' => <<<'SVG'
        <g fill="none" stroke="currentColor" stroke-width="8" stroke-linecap="round">
            <path d="M302 398 L308 438 L302 464"/>
            <path d="M354 398 L360 438 L354 464"/>
            <path d="M284 298 C336 308 404 306 444 294"/>
            <path d="M302 300 L302 352"/>
        </g>
        SVG,

        'integumentary' => <<<'SVG'
        <g fill="currentColor" stroke="currentColor">
            <path d="
                M 140 132
                C 140 104, 148 86, 158 82
                C 166 66, 180 62, 186 78
                C 194 60, 210 60, 214 80
                C 222 68, 236 72, 236 90
                C 240 102, 236 118, 230 128
                C 206 118, 168 120, 140 132
                Z" stroke="none"/>
            <ellipse cx="154" cy="212" rx="9" ry="13" stroke="none"/>
            <path d="M272 230 C316 220 372 220 416 232 C434 237 444 245 450 254" fill="none"
                stroke-width="10" stroke-linecap="round"/>
            <path d="M196 300 C210 330 232 356 266 374" fill="none" stroke-width="10" stroke-linecap="round"/>
        </g>
        SVG,
    ];
@endphp

<svg viewBox="0 0 700 520" class="w-full h-auto select-none" role="group" aria-label="{{ $label }}"
    xmlns="http://www.w3.org/2000/svg">

    {{-- The bird, held back so the organs read on top of it. --}}
    <g class="opacity-70 dark:opacity-45">{!! $bird !!}</g>

    @foreach ($systems as $key => $entry)
        @if (isset($shapes[$key]))
            @php $isSelected = $selected === $key; @endphp

            <g role="button" tabindex="0" aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                aria-label="{{ $entry['system']->localized_display_name }} — {{ $entry['diseases'] }}"
                wire:key="region-{{ $key }}" wire:click="selectSystem('{{ $key }}')"
                wire:keydown.enter.prevent="selectSystem('{{ $key }}')"
                wire:keydown.space.prevent="selectSystem('{{ $key }}')"
                @class([
                    'cursor-pointer outline-none transition-colors duration-200',
                    'text-brand' => $isSelected,
                    'text-brand/70 hover:text-brand' => ! $isSelected && ! $selected,
                    // With one region lit the rest drop back, or eleven systems
                    // overlap and the selection is lost in the noise.
                    'text-brand/25' => ! $isSelected && $selected,
                    'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
                ])>
                <title>{{ $entry['system']->localized_display_name }} — {{ $entry['diseases'] }}</title>
                {!! $shapes[$key] !!}
            </g>
        @endif
    @endforeach
</svg>