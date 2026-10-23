<?php

namespace App\Support;

class PublicMetadata
{
    /** @return array{title: string, description: string, canonical: string, noindex: bool, socialImage: ?string} */
    public static function make(string $title, string $description, string $canonical, bool $indexable, ?string $image = null): array
    {
        return ['title' => $title, 'description' => $description, 'canonical' => $canonical, 'noindex' => ! $indexable, 'socialImage' => $image];
    }

    /**
     * @param  list<array{label: string, url: ?string}>  $items
     * @return array<string, mixed>
     */
    public static function breadcrumbs(array $items, string $currentUrl): array
    {
        return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => array_map(
            fn (array $item, int $index): array => ['@type' => 'ListItem', 'position' => $index + 1, 'name' => $item['label'], 'item' => $item['url'] ?? $currentUrl],
            $items, array_keys($items),
        )];
    }
}
