<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class TaxonomyContent
{
    public static function sanitize(?string $html): string
    {
        $config = new HtmlSanitizerConfig;
        foreach (['p', 'br', 'h2', 'h3', 'strong', 'em', 'ul', 'ol', 'li', 'blockquote'] as $element) {
            $config = $config->allowElement($element);
        }
        $config = $config->allowElement('a', ['href', 'title'])->allowLinkSchemes(['http', 'https', 'mailto'])
            ->dropElement('script')->dropElement('style')->dropElement('iframe')->dropElement('object');

        return (new HtmlSanitizer($config))->sanitize($html ?? '');
    }
}
