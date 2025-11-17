<?php

namespace de\xqueue\maileon\api\client\mailings;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use Exception;
use SimpleXMLElement;

use function str_pad;

/**
 * The wrapper class for a Maileon schedule. This class wraps the XML structure.
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class Schedule extends AbstractXMLWrapper
{
    /**
     * The schedule minutes in the format MM
     *
     * @var int
     */
    public $minutes;

    /**
     * The schedule hour in the format of HH, 24 hours format
     *
     * @var int
     */
    public $hours;

    /**
     * The state of the schedule
     *
     * @var string
     */
    public $state;

    /**
     * The SQL conform date of the schedule day in the format YYYY-MM-DD
     *
     * @var string
     */
    public $date;

    /**
     * The time distribution strategy to choose from {'hour', 'weekdayhour', 'uniform'}.
     *
     * @var string
     */
    public $dispatchOption;

    /**
     * Number of hours beginning from the dispatch start util which the dispatch distribution over the time has to be finished. Used in case
     * of 'hour' dispatch option and 'uniform' option. Allowed values for the 'uniform' distribution are in [2..96], whereas for 'hour'
     * strategy they are ranging from [2..24].
     *
     * @var int
     */
    public $dispatchEndInHours;

    /**
     * Number of days beginning from the dispatch start util which the dispatch distribution over the time has to be finished. Used only
     * with dispatch option 'weekdayhour' and its acceptable range is [1..7].
     *
     * @var int
     */
    public $dispatchEndInDays;

    /**
     * The exact end date util which the dispatch time distribution has to be finished. It is used when none of the arguments above
     * <code>dispatchEndInHours</code>, <code>dispatchEndInDays</code> aren't set i.e. equals 0. Note that one of
     * <code>dispatchEndInHours</code>, <code>dispatchEndInDays</code>, <code>dispatchEndExactDatetime</code> argument should be used in the
     * request according to the selected dispatch option. Format: yyyy-MM-dd HH:mm
     *
     * @var string
     */
    public $dispatchEndExactDatetime;

    /**
     * The parameter determines the inclusion/exclusion of clicks as a response criteria when selecting {'hour', 'weekdayhour'} options.
     *
     * @var boolean
     */
    public $clicksAsResponseReference;

    /**
     * The number determines how many consecutive sending waves will be grouped when using {'hour', 'weekdayhour'} distribution. Supported
     * values are {1, 2, 3 (default)}.
     *
     * @var int
     */
    public $dispatchWavesGroup;

    /**
     * The argument controls the interval {'hour', '30m', '20m', '15m', '10m'} for the 'uniform' strategy indicating the frequency of
     * mailing distribution over time. It should equal null for {'hour', 'weekdayhour'} dispatch options.
     *
     * @var string
     */
    public $dispatchUniformInterval;

    /**
     * The value represents the allowed hours. Comma separated values for the allowed hours and can be combined with a range of hours. The
     * required format looks like 0,3,5,17-21 as an example. The acceptable values range is 0..23. Note that if this argument is not
     * provided, all 24H of the day will be considered as acceptable dispatch hours.
     *
     * @var string
     */
    public $allowedHours;

    /**
     * Constructor initializing values.
     *
     * @param int     $minutes                   The schedule minutes in the format MM
     * @param int     $hours                     The schedule hour in the format of HH, 24 hours format
     * @param string  $state                     The state of the schedule
     * @param string  $date                      The SQL conform date of the schedule day in the format YYYY-MM-DD
     * @param string  $dispatchOption            The time distribution strategy to choose from {'hour', 'weekdayhour', 'uniform'}.
     * @param         $dispatchEndInHours        Number of hours beginning from the dispatch start util which the dispatch distribution over
     *                                           the time has to be finished. Used in case of 'hour' dispatch option and 'uniform' option.
     *                                           Allowed values for the 'uniform' distribution are in [2..96], whereas for 'hour' strategy
     *                                           they are ranging from [2..24].
     * @param         $dispatchEndInDays         Number of days beginning from the dispatch start util which the dispatch distribution over
     *                                           the time has to be finished. Used only with dispatch option 'weekdayhour' and its
     *                                           acceptable range is [1..7].
     * @param         $dispatchEndExactDatetime  The exact end date util which the dispatch time distribution has to be finished. It is used
     *                                           when none of the arguments above <code>dispatchEndInHours</code>,
     *                                           <code>dispatchEndInDays</code> aren't set i.e. equals 0. Note that one of
     *                                           <code>dispatchEndInHours</code>, <code>dispatchEndInDays</code>,
     *                                           <code>dispatchEndExactDatetime</code> argument should be used in the request according to
     *                                           the selected dispatch option. Format: yyyy-MM-dd HH:mm
     * @param boolean $clicksAsResponseReference The parameter determines the inclusion/exclusion of clicks as a response criteria when
     *                                           selecting {'hour', 'weekdayhour'} options.
     * @param int     $dispatchWavesGroup        The number determines how many consecutive sending waves will be grouped when using
     *                                           {'hour', 'weekdayhour'} distribution. Supported values are {1, 2, 3 (default)}.
     * @param string  $dispatchUniformInterval   The argument controls the interval {'hour', '30m', '20m', '15m', '10m'} for the 'uniform'
     *                                           strategy indicating the frequency of mailing distribution over time. It should equal null
     *                                           for {'hour', 'weekdayhour'} dispatch options.
     * @param string  $allowedHours              The value represents the allowed hours. Comma separated values for the allowed hours and
     *                                           can be combined with a range of hours. The required format looks like 0,3,5,17-21 as an
     *                                           example. The acceptable values range is 0..23. Note that if this argument is not provided,
     *                                           all 24H of the day will be considered as acceptable dispatch hours.
     */
    public function __construct(
        $minutes = null,
        $hours = null,
        $state = null,
        $date = null,
        $dispatchOption = null,
        $dispatchEndInHours = null,
        $dispatchEndInDays = null,
        $dispatchEndExactDatetime = null,
        $clicksAsResponseReference = null,
        $dispatchWavesGroup = null,
        $dispatchUniformInterval = null,
        $allowedHours = null
    ) {
        $this->minutes                   = $minutes;
        $this->hours                     = $hours;
        $this->state                     = $state;
        $this->date                      = $date;
        $this->dispatchOption            = $dispatchOption;
        $this->dispatchEndInHours        = $dispatchEndInHours;
        $this->dispatchEndInDays         = $dispatchEndInDays;
        $this->dispatchEndExactDatetime  = $dispatchEndExactDatetime;
        $this->clicksAsResponseReference = $clicksAsResponseReference;
        $this->dispatchWavesGroup        = $dispatchWavesGroup;
        $this->dispatchUniformInterval   = $dispatchUniformInterval;
        $this->allowedHours              = $allowedHours;
    }

    public function fromXML($xmlElement)
    {
        if (isset($xmlElement->minutes)) {
            $this->minutes = $xmlElement->minutes;
        }

        if (isset($xmlElement->hours)) {
            $this->hours = $xmlElement->hours;
        }

        if (isset($xmlElement->state)) {
            $this->state = $xmlElement->state;
        }

        if (isset($xmlElement->date)) {
            $this->date = $xmlElement->date;
        }

        if (isset($xmlElement->dispatchOption)) {
            $this->dispatchOption = (string) $xmlElement->dispatchOption;
        }

        if (isset($xmlElement->dispatchEndInHours)) {
            $this->dispatchEndInHours = (int) $xmlElement->dispatchEndInHours;
        }

        if (isset($xmlElement->dispatchEndInDays)) {
            $this->dispatchEndInDays = (int) $xmlElement->dispatchEndInDays;
        }

        if (isset($xmlElement->dispatchEndExactDatetime)) {
            $this->dispatchEndExactDatetime = (string) $xmlElement->dispatchEndExactDatetime;
        }

        if (isset($xmlElement->clicksAsResponseReference)) {
            $this->clicksAsResponseReference = (bool) $xmlElement->clicksAsResponseReference;
        }

        if (isset($xmlElement->dispatchWavesGroup)) {
            $this->dispatchWavesGroup = (int) $xmlElement->dispatchWavesGroup;
        }

        if (isset($xmlElement->dispatchUniformInterval)) {
            $this->dispatchUniformInterval = (string) $xmlElement->dispatchUniformInterval;
        }

        if (isset($xmlElement->allowedHours)) {
            $this->allowedHours = (string) $xmlElement->allowedHours;
        }
    }

    /**
     * Serialization to a simple XML element.
     *
     * @param bool $addXMLDeclaration
     *
     * @return SimpleXMLElement contains the serialized representation of the object
     *
     * @throws Exception
     */
    public function toXML($addXMLDeclaration = true)
    {
        $xmlString = $addXMLDeclaration ? '<?xml version="1.0"?><mailing></mailing>' : '<mailing></mailing>';
        $xml       = new SimpleXMLElement($xmlString);

        if (isset($this->minutes)) {
            $xml->addChild('minutes', $this->minutes);
        }

        if (isset($this->hours)) {
            $xml->addChild('hours', $this->hours);
        }

        if (isset($this->state)) {
            $xml->addChild('state', $this->state);
        }

        if (isset($this->date)) {
            $xml->addChild('date', $this->date);
        }

        if (isset($this->dispatchOption)) {
            $xml->addChild('dispatchOption', $this->dispatchOption);
        }

        if (isset($this->dispatchEndInHours)) {
            $xml->addChild('dispatchEndInHours', $this->dispatchEndInHours);
        }

        if (isset($this->dispatchEndInDays)) {
            $xml->addChild('dispatchEndInDays', $this->dispatchEndInDays);
        }

        if (isset($this->dispatchEndExactDatetime)) {
            $xml->addChild('dispatchEndExactDatetime', $this->dispatchEndExactDatetime);
        }

        if (! empty($this->clicksAsResponseReference)) {
            $xml->addChild('clicksAsResponseReference', $this->clicksAsResponseReference ? 'true' : 'false');
        }

        if (isset($this->dispatchWavesGroup)) {
            $xml->addChild('dispatchWavesGroup', $this->dispatchWavesGroup);
        }

        if (isset($this->dispatchUniformInterval)) {
            $xml->addChild('dispatchUniformInterval', $this->dispatchUniformInterval);
        }

        if (isset($this->allowedHours)) {
            $xml->addChild('allowedHours', $this->allowedHours);
        }

        return $xml;
    }

    public function toString(): string
    {
        return 'Schedule ['
            . 'minutes=' . $this->minutes
            . ', hours=' . $this->hours
            . ', state=' . $this->state
            . ', date=' . $this->date
            . ', dispatchOption=' . $this->dispatchOption
            . ', dispatchEndInHours=' . $this->dispatchEndInHours
            . ', dispatchEndInDays=' . $this->dispatchEndInDays
            . ', dispatchEndExactDatetime=' . $this->dispatchEndExactDatetime
            . ', clicksAsResponseReference=' . ($this->clicksAsResponseReference ? 'true' : 'false')
            . ', dispatchWavesGroup=' . $this->dispatchWavesGroup
            . ', dispatchUniformInterval=' . $this->dispatchUniformInterval
            . ', allowedHours=' . $this->allowedHours
            . ']';
    }

    /**
     * Date and time representation of this wrapper.
     *
     * @return string A date time version of the schedule.
     */
    public function toDateTime(): string
    {
        return $this->date
            . ' ' . str_pad($this->hours, 2, '0', STR_PAD_LEFT)
            . ':' . str_pad($this->minutes, 2, '0', STR_PAD_LEFT);
    }
}
