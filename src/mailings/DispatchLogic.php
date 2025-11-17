<?php

namespace de\xqueue\maileon\api\client\mailings;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use Exception;
use SimpleXMLElement;

use function filter_var;

/**
 * The wrapper class for a dispatch logic. This class wraps the XML structure.
 *
 * @author Andreas Lange
 */
class DispatchLogic extends AbstractXMLWrapper
{
    /**
     * The type of the trigger mail dispatch plan, this can be one of 'SINGLE' or 'MULTI'
     *
     * @var DispatchLogicType
     */
    public $type;

    /**
     * The ID of the transaction event that is used to either start the instant mailing or to control the mass mailing
     *
     * @var int
     */
    public $event;

    /**
     * Defines the target group of an intervall mailing. This can either be 'EVENT', 'CONTACTFILTER', or 'RSS'
     *
     * @var DispatchLogicTarget
     */
    public $target;

    /**
     * Valid values are 'LOW', 'MEDIUM', and 'HIGH'
     *
     * @var DispatchLogicSpeedLevel
     */
    public $speedLevel;

    /**
     * This defines the interval in which the mailing is sent. This can be one of 'HOUR', 'DAY', 'WEEK', or 'MONTH'
     *
     * @var DispatchLogicInterval
     */
    public $interval;

    /**
     * Sets the day of the month the mailing will be sent. Range: [1..31] If you set a larger number than the month has days, the last day
     * in the month will be used.
     *
     * @var int
     */
    public $dayOfMonth;

    /**
     * Sets the day of the week the mailing will be sent. Range: [1..7] 1 = Sunday, 2 = Monday, 3 = Tuesday, 4 = Wednesday, 5 = Thursday,
     * 6 = Friday, 7 = Saturday
     *
     * @var int
     */
    public $dayOfWeek;

    /**
     * Sets the tour of the day the mailing will be sent. Range: [0..23]
     *
     * @var int
     */
    public $hours;

    /**
     * Sets the minute of the hour the mailing will be sent. Range: [0..59]
     *
     * @var int
     */
    public $minutes;

    /**
     * Sets contact filter ID
     *
     * @var int
     */
    public $contactFilterId;

    /**
     * If set to true, the trigger will be instantly activated after setting the dispatching options.
     *
     * @var bool
     */
    public $startTrigger;

    /**
     * Defines the features that define an item as unique. Valid values are 'DEFAULT', 'PUBDATE', 'TITLE', and 'LINK'.
     *
     * @var DispatchLogicRSSUniqueFeature
     */
    public $rssUniqueFeature;

    /**
     * The URL of the RSS feed.
     *
     * @var string
     */
    public $rssFeedUrl;

    /**
     * Defines the attribute to order elements by. Valid are 'PUBDATE', 'TITLE', and 'LINK'
     *
     * @var DispatchLogicRSSOrderBy
     */
    public $rssOrderBy;

    /**
     * Defines if the order direction is ASC or DESC. If 'true' elements are handled in ascending order.
     *
     * @var bool
     */
    public $rssOrderAsc;

    /**
     * The minimal number of new entries to trigger the RSS2Email mailing.
     *
     * @var int
     */
    public $rssMinNewEntries;

    /**
     * The maximum of mailings a recipient should receive in a given period. Default is 0, which means unlimited.
     *
     * @var int
     */
    public $deliveryLimit;

    /**
     * The time period for the delivery limit. Can be one of 'DAY', 'WEEK', 'MONTH', or 'YEAR'.
     *
     * @var DispatchLogicDeliveryLimitUnit
     */
    public $deliveryLimitUnit;

    public function fromXML($xmlElement)
    {
        if (isset($xmlElement->type)) {
            $this->type = DispatchLogicType::getObject($xmlElement->type);
        }

        if (isset($xmlElement->event)) {
            $this->event = (int) $xmlElement->event;
        }

        if (isset($xmlElement->target)) {
            $this->target = DispatchLogicTarget::getObject($xmlElement->target);
        }

        if (isset($xmlElement->speed_level)) {
            $this->speedLevel = DispatchLogicSpeedLevel::getObject($xmlElement->speed_level);
        }

        if (isset($xmlElement->interval)) {
            $this->interval = DispatchLogicInterval::getObject($xmlElement->interval);
        }

        if (isset($xmlElement->day_of_month)) {
            $this->dayOfMonth = (int) $xmlElement->day_of_month;
        }

        if (isset($xmlElement->day_of_week)) {
            $this->dayOfWeek = (int) $xmlElement->day_of_week;
        }

        if (isset($xmlElement->hours)) {
            $this->hours = (int) $xmlElement->hours;
        }

        if (isset($xmlElement->minutes)) {
            $this->minutes = (int) $xmlElement->minutes;
        }

        if (isset($xmlElement->contact_filter_id)) {
            $this->contactFilterId = (int) $xmlElement->contact_filter_id;
        }

        if (isset($xmlElement->start_trigger)) {
            $this->startTrigger = filter_var($xmlElement->start_trigger, FILTER_VALIDATE_BOOLEAN);
        }

        if (isset($xmlElement->rss_unique_feature)) {
            $this->rssUniqueFeature = DispatchLogicRSSUniqueFeature::getObject($xmlElement->rss_unique_feature);
        }

        if (isset($xmlElement->rss_feed_url)) {
            $this->rssFeedUrl = (string) $xmlElement->rss_feed_url;
        }

        if (isset($xmlElement->rss_order_by)) {
            $this->rssOrderBy = DispatchLogicRSSOrderBy::getObject($xmlElement->rss_order_by);
        }

        if (isset($xmlElement->rss_order_asc)) {
            $this->rssOrderAsc = filter_var($xmlElement->rss_order_asc, FILTER_VALIDATE_BOOLEAN);
        }

        if (isset($xmlElement->rss_min_new_entries)) {
            $this->rssUniqueFeature = DispatchLogicRSSUniqueFeature::getObject($xmlElement->rss_unique_feature);
        }

        if (isset($xmlElement->delivery_limit)) {
            $this->rssUniqueFeature = DispatchLogicRSSUniqueFeature::getObject($xmlElement->rss_unique_feature);
        }

        if (isset($xmlElement->delivery_limit_unit)) {
            $this->rssUniqueFeature = DispatchLogicRSSUniqueFeature::getObject($xmlElement->rss_unique_feature);
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

        if (isset($this->type)) {
            $xml->addChild('type', $this->type->getValue());
        }

        if (isset($this->event)) {
            $xml->addChild('event', $this->event);
        }

        if (isset($this->target)) {
            $xml->addChild('target', $this->target->getValue());
        }

        if (isset($this->speedLevel)) {
            $xml->addChild('speed_level', $this->speedLevel->getValue());
        }

        if (isset($this->interval)) {
            $xml->addChild('interval', $this->interval->getValue());
        }

        if (isset($this->dayOfMonth)) {
            $xml->addChild('day_of_month', $this->dayOfMonth);
        }

        if (isset($this->dayOfWeek)) {
            $xml->addChild('day_of_week', $this->dayOfWeek);
        }

        if (isset($this->hours)) {
            $xml->addChild('hours', $this->hours);
        }

        if (! empty($this->minutes)) {
            $xml->addChild('minutes', $this->minutes);
        }

        if (isset($this->contactFilterId)) {
            $xml->addChild('contact_filter_id', $this->contactFilterId);
        }

        if (isset($this->startTrigger)) {
            $xml->addChild('start_trigger', $this->startTrigger ? 'true' : 'false');
        }

        if (isset($this->rssUniqueFeature)) {
            $xml->addChild('rss_unique_feature', $this->rssUniqueFeature->getValue());
        }

        if (isset($this->rssFeedUrl)) {
            $xml->addChild('rss_feed_url', $this->rssFeedUrl);
        }

        if (isset($this->rssOrderBy)) {
            $xml->addChild('rss_order_by', $this->rssOrderBy->getValue());
        }

        if (isset($this->rssOrderAsc)) {
            $xml->addChild('rss_order_asc', $this->rssOrderAsc ? 'true' : 'false');
        }

        if (isset($this->rssMinNewEntries)) {
            $xml->addChild('rss_min_new_entries', $this->rssMinNewEntries);
        }

        if (isset($this->deliveryLimit)) {
            $xml->addChild('delivery_limit', $this->deliveryLimit);
        }

        if (isset($this->deliveryLimitUnit)) {
            $xml->addChild('delivery_limit_unit', $this->deliveryLimitUnit->getValue());
        }

        return $xml;
    }

    public function toString(): string
    {
        return 'Dispatch Logic ['
            . 'type=' . $this->type->getValue()
            . ', event=' . $this->event
            . ', target=' . $this->target->getValue()
            . ', speedLevel=' . $this->speedLevel->getValue()
            . ', interval=' . $this->interval->getValue()
            . ', dayOfMonth=' . $this->dayOfMonth
            . ', dayOfWeek=' . $this->dayOfWeek
            . ', hours=' . $this->hours
            . ', minutes=' . $this->minutes
            . ', contactFilterId=' . $this->contactFilterId
            . ', startTrigger=' . ($this->startTrigger ? 'true' : 'false')
            . ', rssUniqueFeature=' . $this->rssUniqueFeature->getValue()
            . ', rssFeedUrl=' . $this->rssFeedUrl
            . ', rssOrderBy=' . $this->rssOrderBy->getValue()
            . ', rssOrderAsc=' . ($this->rssOrderAsc ? 'true' : 'false')
            . ', rssMinNewEntries=' . $this->rssMinNewEntries
            . ', deliveryLimit=' . $this->deliveryLimit
            . ', deliveryLimitUnit=' . $this->deliveryLimitUnit->getValue()
            . ']';
    }
}
