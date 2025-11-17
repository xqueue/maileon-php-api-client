<?php

namespace de\xqueue\maileon\api\client\transactions;

use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;

use function property_exists;
use function trigger_error;

/**
 * A wrapper class for transaction processing reports
 *
 * @author Viktor Balogh | XQueue GmbH | <a href="mailto:viktor.balog@xqueue.com">viktor.balog@xqueue.com</a>
 */
class ProcessingReports extends AbstractJSONWrapper
{
    /**
     * An array of reports
     *
     * @var ProcessingReport[]
     */
    public $reports = [];

    public function fromArray($object_vars)
    {
        if (! property_exists($object_vars, 'reports')) {
            trigger_error(
                __CLASS__ . '->' . __FUNCTION__
                . ': failed to initialize object'
                . '; passed object doesn\'t have a "reports" property'
            );

            return;
        }

        foreach ($object_vars->reports as $report) {
            $reportObject = new ProcessingReport();
            $reportObject->fromArray($report);

            $this->reports[] = $reportObject;
        }
    }
}
