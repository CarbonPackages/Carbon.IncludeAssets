<?php

namespace Carbon\IncludeAssets\Eel\Helper;

use Neos\Eel\ProtectedContextAwareInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Utility\PositionalArraySorter;
use Traversable;

/**
 * Helpers for Eel contexts
 *
 * @Flow\Proxy(false)
 */
class IncludeAssetsHelper implements ProtectedContextAwareInterface
{
    /**
     * Sort an array by key and position
     *
     * @param mixed $array The array to sort
     * @return array|null
     */
    public function sort(mixed $array): ?array
    {
        if ($array instanceof Traversable) {
            $array = iterator_to_array($array);
        }
        if (!is_array($array)) {
            return null;
        }
        $array = array_filter($array);
        if (empty($array)) {
            return null;
        }

        // Sort the array by key
        \ksort($array, SORT_NATURAL | SORT_FLAG_CASE);

        // Sort array by position key
        $sorter = new PositionalArraySorter($array);
        return $sorter->toArray();
    }

    /**
     * Check if a string is of a specific type
     *
     * @param string $type metaTop || asyncJs || cssWithImport || syncJs || syncCss || asyncCss || preload || deferJs || rest
     * @param string|null $value The string to check
     * @return bool
     */
    public function isType(string $type, ?string $value): bool
    {
        $value = $value ? trim($value) : $value;
        if (empty($value)) {
            return false;
        }

        $isRest = $type === 'rest';

        // Meta tags on top
        $metaTop =
            preg_match(
                '/<meta\s+[^>]*(?:charset|viewport|http-equiv)[^>]*>/i',
                $value,
            ) === 1;
        if ($type === 'metaTop') {
            return $metaTop;
        }
        if ($isRest && $metaTop) {
            return false;
        }

        // Preload tags
        $preload =
            str_starts_with($value, '<link rel="preload" href="') ||
            str_starts_with($value, '<link rel="modulepreload" href="');
        if ($type === 'preload') {
            return $preload;
        }
        if ($isRest && $preload) {
            return false;
        }

        // Javascript
        $asyncJs = preg_match('/<script\s+[^>]*async[^>]*>/i', $value) === 1;
        if ($type === 'asyncJs') {
            return $asyncJs;
        }
        if ($isRest && $asyncJs) {
            return false;
        }
        $deferJs = preg_match('/<script\s+[^>]*defer[^>]*>/i', $value) === 1;
        if ($type === 'deferJs') {
            return $deferJs;
        }
        if ($isRest && $deferJs) {
            return false;
        }
        $syncJs = !$asyncJs && !$deferJs && str_starts_with($value, '<script');
        if ($type === 'syncJs') {
            return $syncJs;
        }
        if ($isRest && $syncJs) {
            return false;
        }

        // CSS
        $asyncCss = str_starts_with(
            $value,
            '<link rel="preload" as="style" onload="this.onload=null;',
        );
        if ($type === 'asyncCss') {
            return $asyncCss;
        }
        if ($isRest && $asyncCss) {
            return false;
        }
        $cssWithImport = false;
        $syncCss = str_starts_with($value, '<style');
        if (
            !$asyncCss &&
            !$syncCss &&
            preg_match('/<link[^>]*rel=(["\'])stylesheet\1[^>]*>/i', $value)
        ) {
            $hasImports = false;
            if (preg_match('/href=(["\'])([^"\']+)\1/i', $value, $matches)) {
                $cssUrl = $matches[2];
                try {
                    $cssContent = file_get_contents($cssUrl);
                    if ($cssContent !== false) {
                        $hasImports =
                            preg_match('/@import\s+/i', $cssContent) === 1;
                    }
                } catch (Exception $e) {
                }
            }
            if ($hasImports) {
                $cssWithImport = true;
            } else {
                $syncCss = true;
            }
        }

        if ($type === 'cssWithImport') {
            return $cssWithImport;
        }
        if ($isRest && $cssWithImport) {
            return false;
        }
        if ($type === 'syncCss') {
            return $syncCss;
        }
        if ($isRest && $syncCss) {
            return false;
        }

        if ($isRest) {
            return true;
        }

        return false;
    }

    /**
     * Split HTML into an array of tags
     *
     * @param string $type
     * @param mixed $html The HTML to split
     * @return string The html filtered html tags
     */
    public function filterTypeFromHtml(string $type, mixed $html = null): string
    {
        $html = is_string($html) ? trim($html) : '';
        if (empty($html)) {
            return '';
        }

        // Regex to find HTML tags (opening, closing, and self-closing tags)
        preg_match_all('/<[^>]+>/', $html, $matches);
        $result = '';
        if (!empty($matches[0])) {
            foreach ($matches[0] as $tag) {
                $trimmedTag = trim($tag);
                if (!empty($trimmedTag) && $this->isType($type, $trimmedTag)) {
                    $result .= $trimmedTag;
                }
            }
        }

        return $result;
    }

    /**
     * Check if a file is an HTML file based on its extension
     *
     * @param string $filename The path to the file to check
     * @return bool True if the file is an HTML file, false otherwise
     */
    public function isHtmlFile(string $item): bool
    {
        $item = trim(strtolower($item));
        if (str_ends_with($item, '.html') || str_ends_with($item, '.htm') || str_ends_with($item, '(html)')) {
            return true;
        }

        return false;
    }

    /**
     * Parse a filename and return an array with all the properties
     *
     * @param string $string The string to parse
     * @return array|null
     */

    public function parseFilename(string $string): ?array
    {
        // The string is a plain html tag
        if (str_starts_with($string, '<') && str_ends_with($string, '>')) {
            return [
                'type' => 'PLAIN',
                'markup' => $string,
            ];
        }

        $types = [
            'html',
            'js',
            'css',
            'mjs',
            'resourcehint',
            'preloadasset',
            'preloadcss',
            'preloadscript',
            'modulepreload',
        ];
        // 1 => Filename
        // 2 => Search string
        // 3 => Attributes
        // 4 => Specific type
        $regularExpression =
            '/^([^\[\(\?]+)(\?[^\[\(]*)?(?:\[?([^\]]*)\])?(?:\((' .
            implode('|', $types) .
            ')\))?$/i';
        preg_match($regularExpression, $string, $match);

        // We need a filename
        if (!array_key_exists(1, $match)) {
            return null;
        }

        $object = [
            'filename' => $match[1],
            'search' => array_key_exists(2, $match) ? $match[2] : '',
            'type' => null,
            'attributes' => '',
            'async' => false,
            'defer' => false,
            'inline' => false,
            'path' => strpos($match[1], 'resource://') === 0,
            'external' => strpos($match[1], '//') === false ? false : true,
        ];

        if ($object['path']) {
            $object['external'] = false;
        }

        // We have an specific type
        if (array_key_exists(4, $match)) {
            $object['type'] = strtolower($match[4]);
        } else {
            $tmp = explode('.', $object['filename']);
            $tmp = strtolower(end($tmp));
            if ($tmp === 'htm') {
                $tmp = 'html';
            }
            $object['type'] = $tmp;
        }

        if (!in_array($object['type'], $types)) {
            return null;
        }

        if ($object['type'] === 'html') {
            $object['inline'] = true;
        }

        // We got attributes (ModulePreload don't need these)
        if (array_key_exists(3, $match) && $object['type'] != 'modulepreload') {
            $array = preg_split('/[\s,]+/', $match[3], -1, PREG_SPLIT_NO_EMPTY);
            foreach ($array as $value) {
                $split = preg_split('/=/', $value, 2);
                $key = trim($split[0]);
                $value = array_key_exists(1, $split)
                    ? '=' . trim($split[1])
                    : '';

                switch ($key) {
                    case 'async':
                        $object['async'] = true;
                        break;
                    case 'defer':
                        $object['defer'] = true;
                        break;
                    case 'inline':
                        if (!$object['external']) {
                            $object['inline'] = true;
                        }
                        break;
                    default:
                        if (in_array($object['type'], ['js', 'mjs'])) {
                            // Javascript files
                            if ($key != 'src') {
                                $object['attributes'] .= ' ' . $key . $value;
                            }
                        } elseif ($key != 'href' && $key != 'rel') {
                            // CSS and Preload files
                            $object['attributes'] .= ' ' . $key . $value;
                        }
                        break;
                }

                // Async/defer and inline together is not possible, inline wins
                if (
                    $object['inline'] &&
                    ($object['async'] || $object['defer'])
                ) {
                    $object['async'] = false;
                    $object['defer'] = false;
                }
                // Most modern browsers prioritize async over defer
                if ($object['async'] && $object['defer']) {
                    $object['defer'] = false;
                }
            }
        }

        // Add type to javascript modules
        if (
            $object['type'] === 'mjs' &&
            strpos($object['attributes'], ' type=') === false
        ) {
            $object['attributes'] .= ' type="module"';
        }

        // Add as="type" to preload CSS and JS
        if (strpos($object['attributes'], ' as=') === false) {
            switch ($object['type']) {
                case 'preloadcss':
                    $object['attributes'] .= ' as="style"';
                    break;
                case 'preloadscript':
                    $object['attributes'] .= ' as="script"';
                    break;
            }
        }

        // Set type to uppercase for paths
        $object['type'] = strtoupper($object['type']);

        return $object;
    }

    /**
     * All methods are considered safe, i.e. can be executed from within Eel
     *
     * @param string $methodName
     * @return boolean
     */
    public function allowsCallOfMethod($methodName)
    {
        return true;
    }
}
