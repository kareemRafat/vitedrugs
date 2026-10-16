{{--
    The loading spinner shared by the body map's clickable controls.

    Three controls on this page swap an icon for this while their request is in
    flight — the "show all" footer, the system chips under the bird, and the
    organ markers on the bird itself. They have to look like one set of controls,
    and an inline copy in each place is how they stop looking like one.

    This is a ring that pulses, not the arc that rotates. The arc was three
    quarters of a circle, so its ink sat to one side at almost every frame and
    read as off-centre — measurably exact at offset [0, 0], but the eye weighs
    the bright part, and the bright part was an off-centre arc. A full ring is the
    same shape at every frame, so there is nothing left to read.

    `motion-reduce:animate-none` is not decoration: a spinner is the one thing on
    the page that animates continuously, so it is exactly what a reader who has
    asked the OS for reduced motion should not be shown moving.

    If a rotating spinner is wanted back, two arcs 180° apart on an `r="8"` circle
    (`stroke-dasharray="12.57 12.57"`, circumference 50.27) rotate as a balanced
    pair — better than one arc, but the silhouette still turns, so it is not
    pixel-identical the way this is.

    @param string $size Tailwind width/height classes.
--}}
<svg class="animate-pulse motion-reduce:animate-none {{ $size ?? 'w-3.5 h-3.5' }}" aria-hidden="true"
    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
    <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="3" stroke-opacity=".3"></circle>
    <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="3" stroke-linecap="round"></circle>
</svg>
