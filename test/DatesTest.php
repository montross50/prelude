<?php

namespace Prelude;


class DatesTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider providerFormat
     */
    function testFormat($date, $format, $result) {
        $this->assertEquals($result, Dates::format($format, $date));
    }

    /**
     * @return date, format, result
     */
    function providerFormat() {
        $time = time();
        return array(

            array($time, 'r', date('r', $time)),
            array($time, 'c', date('c', $time)),
            array($time, 'c', date('c', $time)),

            array(null, '2010-1-1', '2010-1-1'),
            array('2010-1-1', '2010-1-1', '2010-1-1'),

            array('2001-2-3', 'Y-m-d', '2001-02-03'),
            array('2001-2-3', 'H:i:s', '00:00:00'),
        );
    }

    /**
     * @dataProvider providerCreate
     */
    function testCreate($year, $month, $day=null, $hour=null, $minute=null, $second=null) {

        $time = Dates::create($year, $month, $day, $hour, $minute, $second);

        $this->assertEquals($year  , Dates::year  ($time));
        $this->assertEquals($month , Dates::month ($time));
        $this->assertEquals($day   , Dates::day   ($time));
        $this->assertEquals($hour  , Dates::hour  ($time));
        $this->assertEquals($minute, Dates::minute($time));
        $this->assertEquals($second, Dates::second($time));
    }

    function providerCreate() {
        return array(
            array(2000, 1, 2),
            array(2000, 1, 2, 3),
            array(2000, 1, 2, 3, 4),
            array(2000, 1, 2, 3, 4, 5),
        );
    }

    /**
     * @dataProvider providerIsFuture
     */
    function testIsFuture($time, $clock = null) {
        $this->assertTrue (Dates::isFuture($time, $clock));
    }

    /**
     * @dataProvider providerIsPast
     */
    function testIsPast($time, $clock = null) {
        $this->assertTrue (Dates::isPast  ($time, $clock));
    }

    function providerIsFuture() {
        $times = array(
            time() + 10,
            '+1 hour',
            'tomorrow',
            '2099-12-20'
        );

        $clocks = array(
            null,
            'now',
            'yesterday',
            '2000-10-1'
        );

        return $this->clockAndTimes($times, $clocks);
    }

    function providerIsPast() {
        $times = array(
            time() - 10,
            '-1 hour',
            'yesterday',
            '1970-12-20'
        );

        $clocks = array(
            null,
            'now',
            'tomorrow',
            '2099-10-1'
        );

        return $this->clockAndTimes($times, $clocks);
    }

    function clockAndTimes($times, $clocks) {
        $result = array();

        foreach ($clocks as $clock) {
            foreach ($times as $time) {
                $result[] = array($time, $clock);
            }
        }

        return $result;
    }
}
