<?php

namespace Prelude;

final class Dates {

    /**
     * Returns number of seconds since January 1 1970 00:00:00 UTC
     *
     * Note: This function uses internally `strtotime`
     *
     * @uses strtotime
     *
     * @param string|int $time
     * @return int
     */
    static function epoch($time) {
        if (is_string($time)) {
            $time = strtotime($time);
        }
        Check::argument(is_numeric($time), 'Dates::epoch() supports string, or numeric times');
        return (int) $time;
    }

    /**
     * Returns a format time string
     *
     * @uses date
     *
     * @param string $format
     * @param int|string $time
     *
     * @return string
     */
    static function format($format, $time) {
        if (is_string($time)) {
            $time = Dates::epoch($time);
        }
        return date($format, $time);
    }

    /**
     * Creates a new {@link DateTime}
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @param int $second
     *
     * @return int
     */
    static function create($year, $month, $day = 1, $hour = 0, $minute = 0, $second = 0) {
        return mktime($hour, $minute, $second, $month, $day, $year);
    }

    /**
     * Returns the full year, 4 digits
     *
     * @param int|string $time
     * @return int
     */
    static function year($time) {
        return (int) Dates::format('Y', $time);
    }

    /**
     * Returns the month, from 1 to 12
     *
     * @param int|string $time
     * @return int
     */
    static function month($time) {
        return (int) Dates::format('n', $time);
    }

    /**
     * Returns the day of the month, from 1 to 31
     *
     * @param int|string $time
     * @return int
     */
    static function day($time) {
        return (int) Dates::format('j', $time);
    }

    /**
     * Returns the hours, from 0 to 23
     *
     * @param int|string $time
     * @return int
     */
    static function hour($time) {
        return (int) Dates::format('G', $time);
    }

    /**
     * Returns the minutes, from 0 to 59
     *
     * @param int|string $time
     * @return int
     */
    static function minute($time) {
        return (int) Dates::format('i', $time);
    }

    /**
     * Returns the seconds, from 0 to 59
     *
     * @param int|string $time
     * @return int
     */
    static function second($time) {
        return (int) Dates::format('s', $time);
    }

    /**
     * Returns `true` if the given $time has not yet occurred
     *
     * @param int|string $time
     * @param int|string $clock [optional] reference time, default is "now"
     * @return boolean
     */
    static function isFuture($time, $clock = null) {
        if (null === $clock) {
            $clock = 'now';
        }
        return Dates::epoch($time) > Dates::epoch($clock);
    }

    /**
     * Returns `true` if the given $time has not yet occurred
     *
     * @param int|string $time
     * @param int|string $clock [optional] default is 'now'
     * @return boolean
     */
    static function isPast($time, $clock = null) {
        if (null === $clock) {
            $clock = 'now';
        }
        return Dates::epoch($time) < Dates::epoch($clock);
    }
}
