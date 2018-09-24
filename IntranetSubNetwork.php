<?php

namespace Piwik\Plugins\IntranetSubNetwork;

use Exception;

use Piwik\Common;
use Piwik\Db;
use Piwik\Network\IP;
use Piwik\Plugin;

class IntranetSubNetwork extends Plugin
{

    public function registerEvents()
    {
        return array(
            'Tracker.newVisitorInformation'     => 'logIntranetSubNetworkInfo',
//            'WidgetsList.addWidgets'            => 'addWidget',
//            'API.getReportMetadata'             => 'getReportMetadata',
//            'API.getSegmentDimensionMetadata'   => 'getSegmentsMetadata',
        );
    }

    /*
    public function getReportMetadata(&$reports)
    {
        $reports[] = [
            'category'      => Piwik::translate('General_Visitors'),
            'name'          => Piwik::translate('IntranetSubNetwork_WidgetIntranetSubNetwork'),
            'module'        => 'IntranetSubNetwork',
            'action'        => 'getIntranetSubNetwork',
            'dimension'     => Piwik::translate('IntranetSubNetwork_ColumnIntranetSubNetwork'),
            'documentation' => Piwik::translate('IntranetSubNetwork_WidgetIntranetSubNetworkDocumentation', '<br />'),
            'metrics'       => [
                'nb_visits',
                'nb_uniq_visitors',
                'nb_visits_percentage' => Piwik::translate('General_ColumnPercentageVisits'),
            ],
            // There is no processedMetrics for this report
            'processedMetrics'  => [ ],
            'order'             => 50
        ];
    }
*/

    /*
    public function getSegmentsMetadata(&$segments)
    {
        $segments[] = [
            'type' => 'dimension',
            'category' => 'Visit',
            'name' => Piwik::translate('IntranetSubNetwork_ColumnIntranetSubNetwork'),
            'segment' => 'subnetwork',
            'acceptedValues' => 'Global IPv4, Global IPv6 etc.',
            'sqlSegment' => 'log_visit.location_subnetwork'
        ];
    }
    */

    public function install()
    {
    	// add column location_IntranetSubNetwork in the visit table
    	$query = "ALTER IGNORE TABLE `" . Common::prefixTable('log_visit') . "` ADD `location_subnetwork` VARCHAR( 100 ) NULL";
		
	    // if the column already exist do not throw error. Could be installed twice...
        try {
            Db::exec($query);
        }
        catch(Exception $e){
            if(!Db::get()->isErrNo($e, '1060'))
                throw $e;
        }
    }

    public function uninstall()
    {
	    // remove column location_IntranetSubNetwork from the visit table
	    $query = "ALTER TABLE `" . Common::prefixTable('log_visit') . "` DROP `location_subnetwork`";
	    Db::exec($query);
    }

    /**
     * @param array $visitorInfo
     *
     * Logs the IntranetSubNetwork in the log_visit table
     */
    public function logIntranetSubNetworkInfo(&$visitorInfo)
    {
                
        $ip = IP::fromBinaryIP($visitorInfo['location_ip']);
        // by default, we want the network name to be the IP address:
        // $networkName = $ip;

        $settings = new Systemsettings;
        $subnetworks_provider = $settings->subnetworks->getValue();
        $subnetworks = function() use ($subnetworks_provider) {
            $s = [];
            foreach ($subnetworks_provider as $row)
            {
                $s[] = [$row[0], $row[1] ];
            }
            return $s;
        };

        foreach ($subnetworks as $network_range => $network_name)
        {
            if ($ip->isInRange($network_range)) {
                $visitorInfo['location_subnetwork'] = substr($network_name, 0, 100);
            }
        }
    }
	
}
