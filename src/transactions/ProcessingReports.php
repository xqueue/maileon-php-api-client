<?php

namespace de\xqueue\maileon\api\client\transactions;

use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;

/**
 * A wrapper class for transaction processing reports
 *
 * @author Balogh Viktor <balogh.viktor@maileon.hu> | Maileon - Wanadis Kft.
 */
class ProcessingReports extends AbstractJSONWrapper
{
    /**
     * An array of reports for the
     *
     * @var ProcessingReport|array
     */
    public $reports = array();
    
    public function fromArray($object_vars)
    {
        if (!property_exists($object_vars, 'reports')) {
            trigger_error(
                __CLASS__ .
                "->" .
                __FUNCTION__ .
                ": failed to initialize object; passed object doesn't have a 'reports' property"
            );
            return;
        }
        
        foreach ($object_vars->reports as $report) {
            $reportObject = new ProcessingReport();
            $reportObject->fromArray($report);
            
            $this->reports []= $reportObject;
        }
    }
}
