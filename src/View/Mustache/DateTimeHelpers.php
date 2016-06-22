<?php

namespace Charcoal\Support\View\Mustache;

use DateTime;
use DateTimeZone;
use DateTimeImmutable;
use Pimple\Container;
use Mustache_LambdaHelper as LambdaHelper;
use Charcoal\View\Mustache\Helpers\AbstractHelpers;

/**
 * Mustache Date/Time Helpers
 */
class DateTimeHelpers extends AbstractHelpers
{
    /**
     * A string concatenation of inline `<script>` elements.
     *
     * @var string $js
     */
    private static $now;

    /**
     * Inject dependencies from a DI Container.
     *
     * @param Container $container A dependencies container instance.
     * @return void
     */
    public function setDependencies(Container $container)
    {
        $timezone = $container['config']['timezone'];
        if (is_string($timezone)) {
            $timezone = new DateTimeZone($timezone);
        }

        static::$now = new DateTimeImmutable('now', $timezone);
    }

    /**
     * Retrieve the collection of helpers.
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge($this->now(), $this->date());
    }

    /**
     * Date formatter for current local time
     *
     * @return array Returns an associative array of values and Closures for outputting date formats.
     */
    public function now()
    {
        $now = static::$now;

        $formats = [
            'year'      => $now->format('Y'),
            'atom'      => $now->format(DateTime::ATOM),
            'sqlFull'   => $now->format('Y-m-d H:i:s'),
            'sqlDate'   => $now->format('Y-m-d'),
            'sqlTime'   => $now->format('H:i:s'),
            'timestamp' => $now->getTimestamp(),
            'seconds'   => ( $now->getTimestamp() - time() )
        ];

        return $formats;
    }

    /**
     * Date formatter for current local time
     *
     * @return callable[] Returns an associative array of values and Closures for outputting date formats.
     */
    public function date()
    {
        /**
         * Parse the Mustache tag into a DateTime instance.
         *
         * @var callable
         */
        $parse = function ($time, LambdaHelper $helper) {
            $time = $helper->render($time);
            $time = new DateTime($time);

            return $time;
        };

        /**
         * Retrieves the singular or plural form based on the supplied number.
         *
         * @var callable
         */
        $plural = function ($singular, $plural, $number) {
            return ($number === 1 ? $singular : $plural);
        };

        if ($this instanceof CatalogAwareInterface) {
            $catalog = $this->catalog();
        } else {
            $catalog = null;
        }

        $formats = [
            'year' => function ($time, LambdaHelper $helper) use ($parse) {
                $time = $parse($time, $helper);

                return $time->format('Y');
            },
            'atom' => function ($time, LambdaHelper $helper) use ($parse) {
                $time = $parse($time, $helper);

                return $time->format(DateTime::ATOM);
            },
            'sqlFull' => function ($time, LambdaHelper $helper) use ($parse) {
                $time = $parse($time, $helper);

                return $time->format('Y-m-d H:i:s');
            },
            'sqlDate' => function ($time, LambdaHelper $helper) use ($parse) {
                $time = $parse($time, $helper);

                return $time->format('Y-m-d');
            },
            'sqlTime' => function ($time, LambdaHelper $helper) use ($parse) {
                $time = $parse($time, $helper);

                return $time->format('H:i:s');
            },
            'timestamp' => function ($time, LambdaHelper $helper) use ($parse) {
                $time = $parse($time, $helper);

                return $time->getTimestamp();
            },
            'seconds' => function ($time, LambdaHelper $helper) use ($parse) {
                $time = $parse($time, $helper);

                return ( $time->getTimestamp() - time() );
            },
            'relative' => function ($time, LambdaHelper $helper) use ($parse, $catalog, $plural) {
                $from = $parse($time, $helper);
                $diff = time_diff($from);

                if ($diff < MINUTE_IN_SECONDS) {
                    $secs = $diff;
                    if ($secs <= 1) {
                        $secs = 1;
                    }
                    $since = sprintf($catalog->translate($plural('%s second', '%s seconds', $secs)), $secs);
                } elseif ($diff < HOUR_IN_SECONDS) {
                    $mins = round($diff / MINUTE_IN_SECONDS);
                    if ($mins <= 1) {
                        $mins = 1;
                    }
                    $since = sprintf($catalog->translate($plural('%s minute', '%s minutes', $mins)), $mins);
                } elseif ($diff < DAY_IN_SECONDS && $diff >= HOUR_IN_SECONDS) {
                    $hours = round($diff / HOUR_IN_SECONDS);
                    if ($hours <= 1) {
                        $hours = 1;
                    }
                    $since = sprintf($catalog->translate($plural('%s hour', '%s hours', $hours)), $hours);
                } elseif ($diff < WEEK_IN_SECONDS && $diff >= DAY_IN_SECONDS) {
                    $days = round($diff / DAY_IN_SECONDS);
                    if ($days <= 1) {
                        $days = 1;
                    }
                    $since = sprintf($catalog->translate($plural('%s day', '%s days', $days)), $days);
                } elseif ($diff < MONTH_IN_SECONDS && $diff >= WEEK_IN_SECONDS) {
                    $weeks = round($diff / WEEK_IN_SECONDS);
                    if ($weeks <= 1) {
                        $weeks = 1;
                    }
                    $since = sprintf($catalog->translate($plural('%s week', '%s weeks', $weeks)), $weeks);
                } elseif ($diff < YEAR_IN_SECONDS && $diff >= MONTH_IN_SECONDS) {
                    $months = round($diff / MONTH_IN_SECONDS);
                    if ($months <= 1) {
                        $months = 1;
                    }
                    $since = sprintf($catalog->translate($plural('%s month', '%s months', $months)), $months);
                } elseif ($diff >= YEAR_IN_SECONDS) {
                    $years = round($diff / YEAR_IN_SECONDS);
                    if ($years <= 1) {
                        $years = 1;
                    }
                    $since = sprintf($catalog->translate($plural('%s year', '%s years', $years)), $years);
                }

                return $since;
            }
        ];

        return $formats;
    }
}
