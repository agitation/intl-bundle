<?php
declare(strict_types=1);
/*
 * @package    agitation/intl-bundle
 * @link       http://github.com/agitation/intl-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Tool;

use DateTimeInterface;

class Date
{
    public static function getMonthNames()
    {
        return [Translate::t('January'), Translate::t('February'), Translate::t('March'),
                Translate::t('April'), Translate::t('May'), Translate::t('June'),
                Translate::t('July'), Translate::t('August'), Translate::t('September'),
                Translate::t('October'), Translate::t('November'), Translate::t('December')];
    }

    public static function getShortMonthNames()
    {
        return [Translate::t('Jan'), Translate::t('Feb'), Translate::t('Mar'), Translate::t('Apr'),
                Translate::t('May'), Translate::t('Jun'), Translate::t('Jul'), Translate::t('Aug'),
                Translate::t('Sep'), Translate::t('Oct'), Translate::t('Nov'), Translate::t('Dec')];
    }

    public static function getWeekdayNames()
    {
        return [Translate::t('Sunday'), Translate::t('Monday'), Translate::t('Tuesday'),
                Translate::t('Wednesday'), Translate::t('Thursday'),
                Translate::t('Friday'), Translate::t('Saturday')];
    }

    public static function getShortWeekdayNames()
    {
        return [Translate::t('Sun'), Translate::t('Mon'), Translate::t('Tue'), Translate::t('Wed'),
                Translate::t('Thu'), Translate::t('Fri'), Translate::t('Sat')];
    }

    public static function getMonthName($id)
    {
        return self::getMonthNames()[$id];
    }

    public static function getShortMonthName($id)
    {
        return self::getShortMonthNames()[$id];
    }

    public static function getWeekdayName($id)
    {
        return self::getShortWeekdayNames()[$id];
    }

    public static function getShortWeekdayName($id)
    {
        return self::getShortWeekdayNames()[$id];
    }

    public static function getFirstDayOfWeek()
    {
        return (int) Translate::x('first day of week; 0: Sunday, 1: Monday', '0');
    }

    public static function format(DateTimeInterface $dateTime, $format)
    {
        $timestamp = $dateTime->getTimestamp();
        $result = '';

        for ($i = 0; $i < strlen($format); $i++)
        {
            $result .= ($format[$i] === '\\')
                ? $format[++$i]
                : self::idate($format[$i], $timestamp);
        }

        return $result;
    }

    private static function idate($format, $timestamp)
    {
        switch ($format) {
            case 'D':
                $value = self::getShortWeekdayName(idate('j', $timestamp) - 1);

                break;

            case 'l':
                $value = self::getWeekdayName(idate('j', $timestamp) - 1);

                break;

            case 'M':
                $value = self::getShortMonthName(idate('n', $timestamp) - 1);

                break;

            case 'F':
                $value = self::getMonthName(idate('n', $timestamp) - 1);

                break;

            default:
                $value = date($format, $timestamp); // using date() because idate() chokes on unknown chars and trims leading zeroes
                break;
        }

        return $value;
    }
}
