<?php

namespace Charcoal\Support\View;

/**
 * Implementation, as trait, of the {@see \Charcoal\View\HtmlableInterface}.
 */
trait HtmlableTrait
{
    /**
     * Generate a string of HTML attributes
     *
     * Note: Adapted from `html_build_attributes()`. Added $prefix parameter.
     *
     * @see https://gist.github.com/mcaskill/0177f151e39b94ee2629f06d72c4b65b
     *
     * @param   array          $attr     Associative array of attribute names and values.
     * @param   callable|null  $callback Callback function to escape values for HTML attributes.
     *     Defaults to `htmlspecialchars()`.
     * @param   string|boolean $prefix   Prepend the returned string with a space.
     * @return  string  Returns a string of HTML attributes.
     */
    public function htmlAttributes(array $attr, callable $callback = null, $prefix = true)
    {
        if (!count($attr)) {
            return '';
        }

        $html = array_map(
            function ($val, $key) use ($callback) {
                if (is_bool($val)) {
                    return ($val) ? $key : '';
                } elseif (isset($val)) {
                    if ($val instanceof \Closure) {
                        $val = $val();
                    } elseif ($val instanceof \JsonSerializable) {
                        $val = json_encode(
                            $val->jsonSerialize(),
                            (JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)
                        );
                    } elseif (is_callable([ $val, 'toArray' ])) {
                        $val = $val->toArray();
                    } elseif (is_callable([ $val, '__toString' ])) {
                        $val = strval($val);
                    }

                    if (is_array($val)) {
                        $val = implode(' ', $val);
                    }

                    if (is_callable($callback)) {
                        $val = call_user_func($callback, $val);
                    } else {
                        $val = htmlspecialchars($val, ENT_QUOTES);
                    }

                    if (is_string($val)) {
                        return sprintf('%1$s="%2$s"', $key, $val);
                    }
                }
            },
            $attr,
            array_keys($attr)
        );

        $html = implode(' ', $html);

        if (is_bool($prefix)) {
            $prefix = ($prefix) ? ' ' : '';
        } elseif (!is_string($prefix)) {
            $prefix = '';
        }

        return $prefix.$html;
    }
}
