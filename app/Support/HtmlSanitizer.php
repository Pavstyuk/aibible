<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Small, dependency-free HTML sanitiser for user/AI generated comments.
 *
 * The sanitiser keeps only a conservative set of formatting tags and strips
 * every attribute except safe links. It is intentionally applied both when a
 * comment is stored and when it is rendered (defence in depth for old rows).
 */
class HtmlSanitizer
{
    /**
     * Tags that are allowed in comments. Values are allowed attributes.
     *
     * @var array<string, list<string>>
     */
    private const ALLOWED_TAGS = [
        'a' => ['href', 'title'],
        'b' => [],
        'blockquote' => [],
        'br' => [],
        'code' => [],
        'del' => [],
        'div' => [],
        'em' => [],
        'h2' => [],
        'h3' => [],
        'h4' => [],
        'h5' => [],
        'h6' => [],
        'hr' => [],
        'i' => [],
        'li' => [],
        'ol' => [],
        'p' => [],
        'pre' => [],
        's' => [],
        'span' => [],
        'strong' => [],
        'sub' => [],
        'sup' => [],
        'table' => [],
        'tbody' => [],
        'td' => [],
        'th' => [],
        'thead' => [],
        'tr' => [],
        'u' => [],
        'ul' => [],
    ];

    /**
     * Tags that must be removed together with their contents.
     *
     * @var list<string>
     */
    private const DROP_TAGS = [
        'audio',
        'base',
        'button',
        'canvas',
        'embed',
        'form',
        'iframe',
        'img',
        'input',
        'link',
        'math',
        'meta',
        'noscript',
        'object',
        'option',
        'script',
        'select',
        'source',
        'style',
        'svg',
        'template',
        'textarea',
        'track',
        'video',
    ];

    public static function clean(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        if (!class_exists(DOMDocument::class)) {
            return htmlspecialchars(
                strip_tags($html, '<p><br><strong><b><em><i><u><s><ul><ol><li><blockquote><code><pre><hr>'),
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );
        }

        $previousErrorHandling = libxml_use_internal_errors(true);

        $document = new DOMDocument('1.0', 'UTF-8');
        $loaded = $document->loadHTML(
            '<!DOCTYPE html><html><head><meta charset="utf-8"></head><body><div id="html-sanitizer-root">'
            . $html
            . '</div></body></html>',
            LIBXML_HTML_NODEFDTD | LIBXML_NONET
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previousErrorHandling);

        if (!$loaded) {
            return '';
        }

        $root = self::findRoot($document);

        if (!$root) {
            return '';
        }

        self::sanitizeChildren($root);

        $output = '';

        foreach ($root->childNodes as $child) {
            $output .= $document->saveHTML($child);
        }

        return trim($output);
    }

    private static function findRoot(DOMDocument $document): ?DOMElement
    {
        foreach ($document->getElementsByTagName('div') as $element) {
            if ($element->getAttribute('id') === 'html-sanitizer-root') {
                return $element;
            }
        }

        return null;
    }

    private static function sanitizeChildren(DOMNode $node): void
    {
        $children = [];

        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            if ($child instanceof \DOMComment || $child instanceof \DOMCdataSection) {
                $node->removeChild($child);
                continue;
            }

            if (! $child instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);

            if (in_array($tag, self::DROP_TAGS, true)) {
                $node->removeChild($child);
                continue;
            }

            if (! array_key_exists($tag, self::ALLOWED_TAGS)) {
                // Unknown formatting tags are unwrapped, but their children are
                // cleaned first so that nothing dangerous can move upwards.
                self::sanitizeChildren($child);

                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }

                $node->removeChild($child);
                continue;
            }

            self::sanitizeAttributes($child, $tag);
            self::sanitizeChildren($child);
        }
    }

    private static function sanitizeAttributes(DOMElement $element, string $tag): void
    {
        $allowedAttributes = self::ALLOWED_TAGS[$tag];
        $attributes = [];

        foreach ($element->attributes as $attribute) {
            $attributes[] = $attribute->name;
        }

        foreach ($attributes as $attributeName) {
            if (! in_array(strtolower($attributeName), $allowedAttributes, true)) {
                $element->removeAttribute($attributeName);
            }
        }

        if ($tag !== 'a') {
            return;
        }

        $href = trim($element->getAttribute('href'));

        if ($href === '' || ! self::isSafeUrl($href)) {
            $element->removeAttribute('href');
            return;
        }

        $element->setAttribute('rel', 'noopener noreferrer nofollow');
    }

    private static function isSafeUrl(string $url): bool
    {
        $url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if ($url === '') {
            return false;
        }

        if (str_starts_with($url, '#')) {
            return true;
        }

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return true;
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);

        return is_string($scheme)
            && in_array(strtolower($scheme), ['http', 'https', 'mailto'], true);
    }
}
