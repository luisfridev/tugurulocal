<?php

namespace App\Support;

use App\Actions\Listings\SubmitListingReport;
use App\Models\Listing;

class ListingPageData
{
    public function make(Listing $listing, bool $preview): array
    {
        $canonical = route('listings.show', ['listing' => $listing->slug]);
        $title = $listing->seo_title ?: $listing->name.' · Tu Gurú Local';
        $description = $listing->seo_description ?: ($listing->summary ?: $listing->name.' en '.$listing->city->name);
        $approved = $preview || $listing->hasAuthorizedMedia();
        $logo = ($preview || $listing->hasAuthorizedLogo()) ? $listing->getFirstMedia('logo') : null;
        $cover = $approved ? $listing->getFirstMedia('cover') : null;
        $gallery = $approved ? $listing->getMedia('gallery') : collect();
        $document = $approved ? $listing->getFirstMedia('document') : null;
        $contacts = [];
        foreach (['maps' => ['Cómo llegar', $listing->maps_url], 'phone' => ['Llamar', filled($listing->phone) ? 'tel:'.preg_replace('/[^+0-9]/', '', $listing->phone) : null], 'whatsapp' => ['WhatsApp', filled($listing->whatsapp) ? 'https://wa.me/'.$listing->whatsapp : null], 'website' => ['Sitio web', $listing->website_url], 'facebook' => ['Facebook', $listing->facebook_url], 'instagram' => ['Instagram', $listing->instagram_url]] as $kind => [$label,$url]) {
            if (filled($url)) {
                $contacts[] = ['label' => $label, 'url' => $preview ? $url : route('listings.contact', ['listing' => $listing->slug, 'kind' => $kind])];
            }
        }
        $structured = ['@context' => 'https://schema.org', '@type' => 'LocalBusiness', 'name' => $listing->name, 'description' => $description, 'url' => $canonical];
        if ($listing->address) {
            $structured['address'] = ['@type' => 'PostalAddress', 'streetAddress' => $listing->address, 'addressLocality' => $listing->city->name];
        }
        if ($listing->phone) {
            $structured['telephone'] = $listing->phone;
        }
        if ($socialImage = $cover ?? $logo) {
            $structured['image'] = $socialImage->getUrl($socialImage->hasGeneratedConversion('card') ? 'card' : '');
        }
        if ($logo) {
            $structured['logo'] = $logo->getUrl();
        }
        $jsonLd = [$structured];
        if (filled($listing->faqs)) {
            $jsonLd[] = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array_map(fn (array $faq): array => ['@type' => 'Question', 'name' => $faq['question'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']]], $listing->faqs)];
        }

        return compact('canonical', 'title', 'description', 'logo', 'cover', 'gallery', 'document', 'contacts', 'jsonLd') + ['reportsAvailable' => ! $preview && SubmitListingReport::available()];
    }
}
