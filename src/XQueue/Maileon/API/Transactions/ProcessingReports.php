<?php

namespace XQueue\Maileon\API\Transactions;

use XQueue\Maileon\API\JSON\AbstractJSONWrapper;
use XQueue\Maileon\API\Transactions\ReportContact;

/**
 * A wrapper class for transaction processing reports
 *
 * @author Balogh Viktor <balogh.viktor@maileon.hu> | Maileon - Wanadis Kft.
 */
class ProcessingReports extends AbstractJSONWrapper {
    /**
     * An array of reports for the
     * 
     * @var com_maileon_api_transactions_ProcessingReport|array
     */
    public $reports = array();
    
    function fromArray($object_vars) {
        if(!property_exists($object_vars, 'reports')) { 
            trigger_error( __CLASS__ . "->" . __FUNCTION__ . ": failed to initialize object; passed object doesn't have a 'reports' property");
            return;
        }
        
        foreach($object_vars->reports as $report) {
            $reportObject = new ProcessingReport();
            $reportObject->fromArray($report);
            
            $this->reports []= $reportObject;
        }
    }
}
