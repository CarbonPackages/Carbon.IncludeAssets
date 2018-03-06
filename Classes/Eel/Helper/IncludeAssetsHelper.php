<?php

namespace Carbon\IncludeAssets\Eel\Helper;

use Neos\Flow\Annotations as Flow;
use Neos\Eel\EvaluationException;
use Neos\Eel\ProtectedContextAwareInterface;

/**
 * Helpers for Eel contexts
 *
 * @Flow\Proxy(false)
 */
class IncludeAssetsHelper implements ProtectedContextAwareInterface
{
    public function parseFilename($string)
    {
        // 1 => Filename
        // 2 => Search string
        // 3 => Attributes
        // 4 => Specific type
        $regularExpression = '/^([^\[\(\?]+)(\?[^\[\(]*)?(?:\[?([^\]]*)\])?(?:\((js|css)\))?$/i';
        preg_match($regularExpression, $string, $match);

        // We need a filename
        if (!array_key_exists(1, $match)) {
            return null;
        }

        $object = [
            'filename' => $match[1],
            'search' => array_key_exists(2, $match) ? $match[2] : '',
            'type' =>  null,
            'attributes' => '',
            'async' =>  false,
            'inline' => false,
            'path' => strpos($match[1], 'resource://') === 0,
            'external' => strpos($match[1], '//') === false ? false : true
        ];

        if ($object['path']) {
            $object['external'] = false;
        }

        // We have an specific type
        if (array_key_exists(4, $match)) {
            $object['type'] = strtoupper($match[4]);
        } else {
            $tmp = explode('.', $object['filename']);
            $object['type'] = strtoupper(end($tmp));
        }

        if ($object['type'] != 'JS' && $object['type'] != 'CSS') {
            return null;
        }

        // We got attributes
        if (array_key_exists(3, $match)) {
            $array = explode(' ', $match[3]);
            foreach ($array as $value) {
                $split = explode('=', $value);
                $key = trim($split[0]);
                $value = array_key_exists(1, $split) ? '=' . trim($split[1]) : '';

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
                        if (($object['type'] == 'JS' && $key != 'src') || ($object['type'] == 'CSS' && $key != 'href' && $key != 'rel')) {
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

        // Add type to javascript
        if ($object['type'] == 'JS' && strpos($object['attributes'], ' type=') === false) {
            $object['attributes'] .= ' type="text/javascript"';
        }

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
