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
            $object['type'] = strtolower(end($tmp));
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

                // Async and inline together is not possible, inline wins
                if ($object['inline'] && $object['async']) {
                    $object['async'] = false;
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
