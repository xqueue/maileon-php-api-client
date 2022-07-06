<?php

namespace de\xqueue\maileon\api\client\mailings;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

/**
 * The wrapper class for a dispatch logic. This class wraps the XML structure.
 *
 * @author Andreas Lange
 */
class DispatchLogic extends AbstractXMLWrapper
{
    /**
     * @var DispatchLogicType type
     * The type of the trigger mail dispatch plan, this can be one of 'SINGLE' or 'MULTI'
     */
    public $type;

    /**
     * @var int event
     * The ID of the transaction event that is used to either start the instant mailing or to controll the mass mailing
     */
    public $event;

    /**
     * @var DispatchLogicTarget target
     * Defines the target group of a intervall mailing. This can either be 'EVENT', 'CONTACTFILTER', or 'RSS'
     */
    public $target;

    /**
     * @var DispatchLogicSpeedLevel speedLevel
     * Valid values are 'LOW', 'MEDIUM', and 'HIGH'
     */
    public $speedLevel;

    /**
     * @var DispatchLogicInterval interval
     * This defines the interval in which the mailing is sent. This can be one of 'HOUR', 'DAY', 'WEEK', or 'MONTH'
     */
    public $interval;

    /**
     * @var int dayOfMonth
     * Sets the day of the month the mailing will be sent. Range: [1..31] If you set a larger number than the month has days, the last day in the month will be used.
     */
    public $dayOfMonth;

    /**
     * @var int dayOfWeek
     * Sets the day of the week the mailing will be sent. Range: [1..7] 1 = Sunday 2 = Monday 3 = Tuesday 4 = Wednesday 5 = Thursday 6 = Friday 7 = Saturday
     */
    public $dayOfWeek;

    /**
     * @var int hours
     * Sets the tour of the day the mailing will be sent. Range: [0..23]
     */
    public $hours;

    /**
     * @var int minutes
     * Sets the minute of the hour the mailing will be sent. Range: [0..59]
     */
    public $minutes;

    /**
     * @var int contactFilterId
     * Sets contact filter ID
     */
    public $contactFilterId;

    /**
     * @var bool startTrigger
     * If set to true, the trigger will be instantly activated after setting the dispatching options.
     */
    public $startTrigger;

    /**
     * @var DispatchLogicRSSUniqueFeature rssUniqueFeature 
     * Defines the features that define an item as unique. Valid values are 'DEFAULT', 'PUBDATE', 'TITLE', and 'LINK'.
     */
    public $rssUniqueFeature;

    /**
     * @var string rssFeedUrl 
     * The URL of the RSS feed.
     */
    public $rssFeedUrl;

    /**
     * @var DispatchLogicRSSOrderBy rssOrderBy 
     * Defines the attribute to order elements by. Valid are 'PUBDATE', 'TITLE', and 'LINK'
     */
    public $rssOrderBy;

    /**
     * @var bool rssOrderAsc
     * Defines if the order direction is ASC or DESC. If 'true' elements are handled in ascending order.
     */
    public $rssOrderAsc;

    /**
     * @var int rssMinNewEntries 
     * The minimal number of new entries to trigger the RSS2Email mailing.
     */
    public $rssMinNewEntries;

    /**
     * @var int deliveryLimit 
     * The maximum of mailings a repeipient should receive in a given period. Default is 0, which means unlimited.
     */
    public $deliveryLimit;

    /**
     * @var DispatchLogicDeliveryLimitUnit deliveryLimitUnit 
     * The time period for the delivery limit. Can be one of 'DAY', 'WEEK', 'MONTH', or 'YEAR'.
     */
    public $deliveryLimitUnit;

    /**
     * Initialization of the schedule from a simple xml element.
     *
     * @param \SimpleXMLElement $xmlElement
     * The xml element that is used to parse the schedule from.
     */
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
     * @return \SimpleXMLElement
     * Generate a XML element from the contact object.
     */
    public function toXML($addXMLDeclaration = true)
    {
        $xmlString = $addXMLDeclaration ? "<?xml version=\"1.0\"?><mailing></mailing>" : "<mailing></mailing>";
        $xml = new \SimpleXMLElement($xmlString);

        if (isset($this->type)) {
            $xml->addChild("type", $this->type->getValue());
        }
        if (isset($this->event)) {
            $xml->addChild("event", $this->event);
        }
        if (isset($this->target)) {
            $xml->addChild("target", $this->target->getValue());
        }
        if (isset($this->speedLevel)) {
            $xml->addChild("speed_level", $this->speedLevel->getValue());
        }
        if (isset($this->interval)) {
            $xml->addChild("interval", $this->interval->getValue());
        }
        if (isset($this->dayOfMonth)) {
            $xml->addChild("day_of_month", $this->dayOfMonth);
        }
        if (isset($this->dayOfWeek)) {
            $xml->addChild("day_of_week", $this->dayOfWeek);
        }
        if (isset($this->hours)) {
            $xml->addChild("hours", $this->hours);
        }
        if (!empty($this->minutes)) {
            $xml->addChild("minutes", $this->minutes);
        }
        if (isset($this->contactFilterId)) {
            $xml->addChild("contact_filter_id", $this->contactFilterId);
        }
        if (isset($this->startTrigger)) {
            $xml->addChild("start_trigger", $this->startTrigger ? 'true' : 'false');
        }
        if (isset($this->rssUniqueFeature)) {
            $xml->addChild("rss_unique_feature", $this->rssUniqueFeature->getValue());
        }
        if (isset($this->rssFeedUrl)) {
            $xml->addChild("rss_feed_url", $this->rssFeedUrl);
        }
        if (isset($this->rssOrderBy)) {
            $xml->addChild("rss_order_by", $this->rssOrderBy->getValue());
        }
        if (isset($this->rssOrderAsc)) {
            $xml->addChild("rss_order_asc", $this->rssOrderAsc ? 'true' : 'false');
        }
        if (isset($this->rssMinNewEntries)) {
            $xml->addChild("rss_min_new_entries", $this->rssMinNewEntries);
        }
        if (isset($this->deliveryLimit)) {
            $xml->addChild("delivery_limit", $this->deliveryLimit);
        }
        if (isset($this->deliveryLimitUnit)) {
            $xml->addChild("delivery_limit_unit", $this->deliveryLimitUnit->getValue());
        }

        return $xml;
    }

    /**
     * Serialization to a simple XML element as string
     *
     * @return string
     * The string representation of the XML document for this mailing.
     */
    public function toXMLString()
    {
        return $this->toXML()->asXML();
    }

    /**
     * Human readable representation of this wrapper.
     *
     * @return string
     * A human readable version of the dispatch logic.
     */
    public function toString()
    {
        return "Dispatch Logic [type={$this->type}, event={$this->event}, target={$this->target}, speedLevel={$this->speedLevel}, interval={$this->interval}, ".
               "dayOfMonth={$this->dayOfMonth}, dayOfWeek={$this->dayOfWeek}, hours={$this->hours}, minutes={$this->minutes}, contactFilterId={$this->contactFilterId}, ".
               "startTrigger={($this->startTrigger)?'true':'false'}, rssUniqueFeature={$this->rssUniqueFeature}, rssFeedUrl={$this->rssFeedUrl}, rssOrderBy={$this->rssOrderBy}, ".
               "rssOrderAsc={($this->rssOrderAsc)?'true':'false'}, rssMinNewEntries={$this->rssMinNewEntries}, deliveryLimit={$this->deliveryLimit}, deliveryLimitUnit={$this->deliveryLimitUnit}]"; 
    }
}
