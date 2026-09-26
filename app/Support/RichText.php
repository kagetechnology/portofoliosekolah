<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

class RichText
{
    private const ALLOWED_TAGS = ['div', 'p', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li', 'blockquote', 'a', 'img', 'figure', 'figcaption', 'table', 'thead', 'tbody', 'tr', 'th', 'td', 'iframe'];

    private const ALLOWED_ATTRIBUTES = [
        'a' => ['href', 'target', 'rel'],
        'img' => ['src', 'alt'],
        'th' => ['colspan', 'rowspan'],
        'td' => ['colspan', 'rowspan'],
        'iframe' => ['src', 'width', 'height', 'allow', 'allowfullscreen', 'frameborder'],
    ];

    public static function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return null;
        }

        if ($html === strip_tags($html)) {
            return nl2br(e($html), false);
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?><div>'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $root = $dom->getElementsByTagName('div')->item(0);
        if (! $root) {
            return null;
        }

        self::cleanNode($root);

        $clean = '';
        foreach ($root->childNodes as $child) {
            $clean .= $dom->saveHTML($child);
        }

        return trim($clean) !== '' ? trim($clean) : null;
    }

    private static function cleanNode(DOMNode $node): void
    {
        for ($child = $node->firstChild; $child; $child = $next) {
            $next = $child->nextSibling;

            if ($child instanceof DOMElement) {
                $tag = strtolower($child->tagName);
                if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                    if (in_array($tag, ['script', 'style'], true)) {
                        $child->parentNode?->removeChild($child);
                        continue;
                    }
                    self::unwrap($child);
                    continue;
                }

                self::cleanAttributes($child, $tag);
            }

            self::cleanNode($child);
        }
    }

    private static function cleanAttributes(DOMElement $element, string $tag): void
    {
        for ($i = $element->attributes->length - 1; $i >= 0; $i--) {
            $attr = $element->attributes->item($i);
            $name = strtolower($attr->name);
            if (! in_array($name, self::ALLOWED_ATTRIBUTES[$tag] ?? [], true)) {
                $element->removeAttribute($attr->name);
            }
        }

        if ($tag === 'a') {
            $href = $element->getAttribute('href');
            if (! self::isSafeUrl($href)) {
                $element->removeAttribute('href');
            }
            $element->setAttribute('target', '_blank');
            $element->setAttribute('rel', 'noopener noreferrer');
        }

        if ($tag === 'img' && ! self::isSafeUrl($element->getAttribute('src'))) {
            self::unwrap($element);
        }

        if ($tag === 'iframe') {
            if (! self::isSafeEmbedUrl($element->getAttribute('src'))) {
                self::unwrap($element);
                return;
            }
            $element->setAttribute('loading', 'lazy');
            $element->setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
        }
    }

    private static function isSafeUrl(string $url): bool
    {
        return (bool) preg_match('/^https?:\/\//i', trim($url));
    }

    private static function isSafeEmbedUrl(string $url): bool
    {
        return (bool) preg_match('/^https:\/\/(www\.)?(youtube\.com\/embed\/|youtube-nocookie\.com\/embed\/|player\.vimeo\.com\/video\/)/i', trim($url));
    }

    private static function unwrap(DOMElement $element): void
    {
        $parent = $element->parentNode;
        if (! $parent) {
            return;
        }

        while ($element->firstChild) {
            $parent->insertBefore($element->firstChild, $parent->isSameNode($element) ? null : $element);
        }
        if ($element->parentNode) {
            $parent->removeChild($element);
        }
    }
}
