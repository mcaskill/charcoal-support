<?php

namespace Charcoal\Support\View\Mustache;

use Mustache_LambdaHelper as LambdaHelper;
use Charcoal\View\Mustache\HelpersInterface;

/**
 * Mustache String Helpers
 */
class StringHelpers implements HelpersInterface
{
    /**
     * Wrap the string helpers.
     *
     * @return array
     */
    public function toArray()
    {
        return [ 'str' => $this->filters() ];
    }

    /**
     * Retrieve the collection of helpers.
     *
     * @return array
     */
    public function filters()
    {
        return [
            /**
             * Convert the given string to title case.
             *
             * @return string
             */
            'title' => function ($text, LambdaHelper $helper) {
                $text = $helper->render($text);

                return mb_convert_case($text, MB_CASE_TITLE, 'UTF-8');
            },
            /**
             * Convert the given string to upper-case.
             *
             * @return string
             */
            'upper' => function ($text, LambdaHelper $helper) {
                $text = $helper->render($text);

                return mb_strtoupper($text, 'UTF-8');
            },
            /**
             * Convert the given string to lower-case.
             *
             * @return string
             */
            'lower' => function ($text, LambdaHelper $helper) {
                $text = $helper->render($text);

                return mb_strtolower($text, 'UTF-8');
            },
            /**
             * Make a string's first character uppercase.
             *
             * @return string
             */
            'ucfirst' => function ($text, LambdaHelper $helper) {
                $text = $helper->render($text);

                return mb_strtoupper(mb_substr($text, 0, 1)).mb_substr($text, 1, null, 'UTF-8');
            },
            /**
             * URL-encodes a string.
             *
             * @return string
             */
            'urlencode' => function ($text, LambdaHelper $helper) {
                $text = $helper->render($text);

                return urlencode($text);
            },
            /**
             * URL-encodes a string according to RFC 3986.
             *
             * @return string
             */
            'rawurlencode' => function ($text, LambdaHelper $helper) {
                $text = $helper->render($text);

                return rawurlencode($text);
            }
        ];
    }
}
