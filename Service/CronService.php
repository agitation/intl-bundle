<?php

/*
 * @package    agitation/base-bundle
 * @link       http://github.com/agitation/base-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Service;

use Agit\IntlBundle\Event\CronjobRegistrationEvent;
use Agit\IntlBundle\Exception\InternalErrorException;
use DateTime;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CronService
{
    private $eventDispatcher;

    private $eventRegistrationTag = "agit.cron.register";

    private $serviceList = [];

    // min/max values for minute, hour, day, month, weekday
    private $ranges = [[0, 59], [0, 23], [1, 31], [1, 12], [0, 6]];

    private $now;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->setDate(new DateTime());
    }

    public function setDate(DateTime $dateTime)
    {
        $this->now = [
            (int) $dateTime->format("i"),
            (int) $dateTime->format("H"),
            (int) $dateTime->format("d"),
            (int) $dateTime->format("m"),
            (int) $dateTime->format("w")
        ];
    }

    public function run()
    {
        $this->eventDispatcher->dispatch(
            $this->eventRegistrationTag,
            new CronjobRegistrationEvent($this)
        );
    }

    public function cronApplies($cronTime)
    {
        $cronParts = $this->parseCronTime($cronTime);
        $applies = true;

        foreach ($cronParts as $pos => $value) {
            if ($value !== null && ! in_array($this->now[$pos], $value)) {
                $applies = false;
                break;
            }
        }

        return $applies;
    }

    public function parseCronTime($cronTime)
    {
        $cronParts = preg_split("|\s+|", $cronTime, null, PREG_SPLIT_NO_EMPTY);

        if (count($cronParts) !== 5) {
            throw new InternalErrorException("Invalid cron time.");
        }

        $parsedParts = [];

        foreach ($cronParts as $pos => $value) {
            if ($value === "*") {
                $parsedParts[$pos] = null;
            } else {
                $elements = [];

                if (preg_match("|^\*/\d+$|", $value)) {
                    $step = (int) substr($value, 2);

                    for ($i = $this->ranges[$pos][0]; $i < $this->ranges[$pos][1]; $i += $step) {
                        $elements[] = $i;
                    }
                } elseif (preg_match("|^\d+(,\d+)*$|", $value)) {
                    $elements = array_map("intval", explode(",", $value));
                } else {
                    throw new InternalErrorException("Invalid cron time parameter at position $pos.");
                }

                foreach ($elements as $element) {
                    if ($element < $this->ranges[$pos][0] || $element > $this->ranges[$pos][1]) {
                        throw new InternalErrorException("Invalid cron time parameter at position $pos.");
                    }
                }

                $parsedParts[$pos] = $elements;
            }
        }

        return $parsedParts;
    }
}
