@props([
    /**
     * An App\Models\LargeAnimals\MedicalArticle, plus its `disease` relation
     * already eager loaded. Passed whole for the same reason as x-part.post-card:
     * the Arabic columns are the model's business, not the card's.
     *
     * @var \App\Models\LargeAnimals\MedicalArticle $article
     */
    'article',
])

@php
    /*
     * The chip is the article type, read from the same map the full index view
     * uses, so a teaser cannot disagree with its own page about what kind of
     * article it is.
     */
    $chip = $article->article_type
        ? __('large-animals.medical_articles.types.'.$article->article_type)
        : null;

    /*
     * `localized_summary_or_content` already handles the case where a row has a
     * summary in neither language and falls back to a length-limited strip of
     * the body, so this cannot render an empty excerpt.
     */
    $excerpt = $article->localized_summary_or_content;

    $disease = $article->disease?->localized_name;
@endphp

{{--
    A medical article teaser.

    Same card as the /drugs blog band — both wrap x-part.content-card — so a
    visitor moving between the two parts does not have to re-learn the shape.

    There is no publication date to show: `medical_articles` has no
    `published_at`, only `is_published`. Putting `created_at` there would imply
    a publication date that does not exist, so the footer carries the two facts
    that are real instead — the species it covers and the disease it sits under.
--}}
<x-part.content-card
    :href="route('large-animals.medical-articles.show', ['slug' => $article->slug])"
    :chip="$chip"
    :title="$article->localized_title"
    :excerpt="$excerpt"
    :meta-start="$article->localized_species"
    :meta-end="$disease"
/>