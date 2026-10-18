@props([
    /**
     * An App\Models\Blog. Passed whole rather than as a bag of fields so the
     * Arabic fallbacks live in one place — there are three separate `_ar`
     * columns on this model and picking the wrong one silently renders English
     * copy on an Arabic page.
     *
     * @var \App\Models\Blog $post
     */
    'post',
])

@php
    $isArabic = app()->getLocale() === 'ar';

    $category = $post->category
        ? ($isArabic && $post->category->name_ar ? $post->category->name_ar : $post->category->name)
        : null;
@endphp

{{--
    A blog teaser. Maps Blog onto the shared card so the /drugs blog band and
    the /large-animals article band cannot drift apart visually.
--}}
<x-part.content-card
    :href="route('blog.show', $post)"
    :chip="$category"
    :title="$isArabic && $post->title_ar ? $post->title_ar : $post->title"
    :excerpt="$isArabic && $post->excerpt_ar ? $post->excerpt_ar : $post->excerpt"
    :meta-start="$post->published_at->translatedFormat('M d, Y')"
    :meta-start-iso="$post->published_at->toDateString()"
    :meta-end="__('messages.blog.read_time', ['minutes' => $post->read_time])"
/>