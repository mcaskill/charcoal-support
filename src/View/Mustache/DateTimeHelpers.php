<?php

namespace Charcoal\Support\View\Mustache;

use ArrayAccess;
use InvalidArgumentException;

use Closure;
use DateTime;
use DateTimeZone;
use DateTimeInterface;
use DateTimeImmutable;

// From Mustache
use Mustache_LambdaHelper as LambdaHelper;

// From 'charcoal-view'
use Charcoal\View\Mustache\HelpersInterface;

/**
 * Mustache Date/Time Helpers
 */
class DateTimeHelpers implements
    ArrayAccess,
    HelpersInterface
{
    const MINUTE_IN_SECONDS =  60;
    const HOUR_IN_SECONDS   =  60 * self::MINUTE_IN_SECONDS;
    const DAY_IN_SECONDS    =  24 * self::HOUR_IN_SECONDS;
    const WEEK_IN_SECONDS   =   7 * self::DAY_IN_SECONDS;
    const MONTH_IN_SECONDS  =  30 * self::DAY_IN_SECONDS;
    const YEAR_IN_SECONDS   = 365 * self::DAY_IN_SECONDS;

    /**
     * Store the current date/time.
     *
     * @var DateTimeImmutable
     */
    private static $now;

    /**
     * Store the given macro or format.
     *
     * @var string
     */
    private $macro;

    /**
     * The registered date/time macros.
     *
     * @var array
     */
    protected $macros = [];

    /**
     * Returns a new DateTimeHelpers object.
     *
     * @param  array|null                $macros    Macros to add to the presenter.
     *     Valid formats are explained in {@link http://php.net/manual/en/datetime.formats.php Date and Time Formats}.
     * @param  DateTimeZone|string|null  $timezone  One of the supported
     *     {@link http://php.net/manual/en/timezones.php timezone names} or a DateTimeZone object.
     *     If $timezone is omitted, the current time zone will be used.
     */
    public function __construct( array $macros = null, $timezone = null)
    {
        if (is_string($timezone)) {
            $timezone = new DateTimeZone($timezone);
        }

        if (!static::$now) {
            static::$now = new DateTimeImmutable('now', $timezone);
        }

        $this->macros = $this->macrosForDateTime();

        if ($macros) {
            $this->macros = array_merge($this->macros, $macros);
        }
    }

    /**
     * Retrieve the collection of helpers.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'now'  => $this->macrosForNow(),
            'date' => $this,
            'time' => $this,
        ];
    }

    /**
     * Parse the given date and time into a DateTime object.
     *
     * @param  DateTimeInterface|string $time A date/time string or a DateTime object.
     * @return DateTimeImmutable
     */
    private function parseDateTime($time)
    {
        if (is_string($time)) {
            $time = new DateTimeImmutable($time);
        }

        if ($time instanceof DateTime) {
            $time = DateTimeImmutable::createFromMutable($time);
        }

        if ($time instanceof DateTimeImmutable) {
            $time = $time->setTimezone(static::$now->getTimezone());
        } else {
            throw new InvalidArgumentException('Invalid date/time for the presenter.');
        }

        return $time;
    }

    /**
     * Date/Time formats for the current local time
     *
     * @return array Returns an associative array of values and Closures for the current time.
     */
    private function macrosForNow()
    {
        $now = static::$now;

        return [
            'year'      => $now->format('Y'),
            'atom'      => $now->format(DateTime::ATOM),
            'sqlFull'   => $now->format('Y-m-d H:i:s'),
            'sqlDate'   => $now->format('Y-m-d'),
            'sqlTime'   => $now->format('H:i:s'),
            'timestamp' => $now->getTimestamp(),
            'format'    => function ($format, LambdaHelper $helper) use ($now) {
                $format = $helper->render($format);

                return $now->format($format);
            },
        ];
    }

    /**
     * Retrieve the default date/time macros.
     *
     * @return callable[]
     */
    private function macrosForDateTime()
    {
        /**
         * Retrieves the singular or plural form based on the supplied number.
         *
         * @var callable
         */
        $plural = function ($singular, $plural, $number) {
            return ($number === 1 ? $singular : $plural);
        };

        return [
            'year' => function ($time, LambdaHelper $helper = null) {
                if ($time === null || $time === '') {
                    return;
                }

                if ($helper && is_string($time)) {
                    $time = $this->parseDateTime($helper->render($time));
                }

                return $time->format('Y');
            },
            'atom' => function ($time, LambdaHelper $helper = null) {
                if ($time === null || $time === '') {
                    return;
                }

                if ($helper && is_string($time)) {
                    $time = $this->parseDateTime($helper->render($time));
                }

                return $time->format(DateTime::ATOM);
            },
            'sqlFull' => function ($time, LambdaHelper $helper = null) {
                if ($time === null || $time === '') {
                    return;
                }

                if ($helper && is_string($time)) {
                    $time = $this->parseDateTime($helper->render($time));
                }

                error_log(var_export($time, true));

                return $time->format('Y-m-d H:i:s');
            },
            'sqlDate' => function ($time, LambdaHelper $helper = null) {
                if ($time === null || $time === '') {
                    return;
                }

                if ($helper && is_string($time)) {
                    $time = $this->parseDateTime($helper->render($time));
                }

                return $time->format('Y-m-d');
            },
            'sqlTime' => function ($time, LambdaHelper $helper = null) {
                if ($time === null || $time === '') {
                    return;
                }

                if ($helper && is_string($time)) {
                    $time = $this->parseDateTime($helper->render($time));
                }

                return $time->format('H:i:s');
            },
            'timestamp' => function ($time, LambdaHelper $helper = null) {
                if ($time === null || $time === '') {
                    return;
                }

                if ($helper && is_string($time)) {
                    $time = $this->parseDateTime($helper->render($time));
                }

                return $time->getTimestamp();
            },
            'seconds' => function ($time, LambdaHelper $helper = null) {
                if ($time === null || $time === '') {
                    return;
                }

                if ($helper && is_string($time)) {
                    $time = $this->parseDateTime($helper->render($time));
                }

                return ($time->getTimestamp() - time());
            },
            'format' => function ($text, LambdaHelper $helper) {
                if ($text === null || $text === '') {
                    return;
                }

                $parts = preg_split('#(?<!\\\\)\|#', $text);
                list($time, $format) = array_pad($parts, 2, null);

                $time = $this->parseDateTime($helper->render($time));

                return $time->format($helper->render($format));
            },
            'relative' => function ($time, LambdaHelper $helper = null) use ($plural) {
                if ($time === null || $time === '') {
                    return;
                }

                if ($helper && is_string($time)) {
                    $time = $this->parseDateTime($helper->render($time));
                }

                $diff = time_diff($from);

                if ($diff < static::MINUTE_IN_SECONDS) {
                    $secs = $diff;
                    if ($secs <= 1) {
                        $secs = 1;
                    }
                    $since = sprintf($plural('%s second', '%s seconds', $secs), $secs);
                } elseif ($diff < static::HOUR_IN_SECONDS) {
                    $mins = round($diff / static::MINUTE_IN_SECONDS);
                    if ($mins <= 1) {
                        $mins = 1;
                    }
                    $since = sprintf($plural('%s minute', '%s minutes', $mins), $mins);
                } elseif ($diff < static::DAY_IN_SECONDS && $diff >= static::HOUR_IN_SECONDS) {
                    $hours = round($diff / static::HOUR_IN_SECONDS);
                    if ($hours <= 1) {
                        $hours = 1;
                    }
                    $since = sprintf($plural('%s hour', '%s hours', $hours), $hours);
                } elseif ($diff < static::WEEK_IN_SECONDS && $diff >= static::DAY_IN_SECONDS) {
                    $days = round($diff / static::DAY_IN_SECONDS);
                    if ($days <= 1) {
                        $days = 1;
                    }
                    $since = sprintf($plural('%s day', '%s days', $days), $days);
                } elseif ($diff < static::MONTH_IN_SECONDS && $diff >= static::WEEK_IN_SECONDS) {
                    $weeks = round($diff / static::WEEK_IN_SECONDS);
                    if ($weeks <= 1) {
                        $weeks = 1;
                    }
                    $since = sprintf($plural('%s week', '%s weeks', $weeks), $weeks);
                } elseif ($diff < static::YEAR_IN_SECONDS && $diff >= static::MONTH_IN_SECONDS) {
                    $months = round($diff / static::MONTH_IN_SECONDS);
                    if ($months <= 1) {
                        $months = 1;
                    }
                    $since = sprintf($plural('%s month', '%s months', $months), $months);
                } elseif ($diff >= static::YEAR_IN_SECONDS) {
                    $years = round($diff / static::YEAR_IN_SECONDS);
                    if ($years <= 1) {
                        $years = 1;
                    }
                    $since = sprintf($plural('%s year', '%s years', $years), $years);
                }

                return $since;
            }
        ];
    }



    // Magic Methods
    // =========================================================================

    /**
     * Magic: Determine if a property is set and is not NULL.
     *
     * Required by Mustache.
     *
     * @param  string  $macro A macro.
     * @return boolean
     */
    public function __isset($macro)
    {
        return boolval($macro);
    }

    /**
     * Magic: Format a given date/time by a given format.
     *
     * @param  string $macro A macro or a format accepted by {@see date()}.
     * @return mixed
     */
    public function __get($macro)
    {
        if ($macro === 'now') {
            $this->macro = $macro;
            return $this;
        }

        if ($this->macro === 'now') {
            $macros = $this->macrosForNow();

            if (isset($macros[$macro])) {
                return $macros[$macro];
            }

            return $macros['format'];
        }

        $this->macro = $macro;

        if (!isset($this->macros[$macro])) {
            $macro = 'format';
        }

        if ($this->macros[$macro] instanceof Closure) {
            return $this->macros[$macro]->bindTo($this);
        }

        return null;
    }



    // Satisfies ArrayAccess
    // =========================================================================

    /**
     * Determine if an macro exists at an offset.
     *
     * @param  mixed $key The name of the macro to lookup.
     * @return bool
     */
    public function offsetExists($key)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException('The name of the macro must be a string.');
        }

        return array_key_exists($key, $this->macros);
    }

    /**
     * Get an macro at a given offset.
     *
     * @param  mixed $key The name of the macro to retrieve.
     * @return mixed
     */
    public function offsetGet($key)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException('The name of the macro must be a string.');
        }

        return $this->macros[$key];
    }

    /**
     * Set the macro at a given offset.
     *
     * @param  mixed $key   The name of the macro.
     * @param  mixed $value The macro's effect.
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException('The name of the macro must be a string.');
        }

        if (!is_callable($value)) {
            throw new InvalidArgumentException('The macro must be a callable value.');
        }

        $this->macros[$key] = $value;
    }

    /**
     * Unset the macro at a given offset.
     *
     * @param  string $key The name of the macro to remove.
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->macros[$key]);
    }
}
