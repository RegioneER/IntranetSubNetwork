<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 * @category Piwik_Plugins
 * @package IntranetSubNetwork
 */
namespace Piwik\Plugins\IntranetSubNetwork;

use Exception;

use Piwik\Common;
use Piwik\Db;
use Piwik\IP;
use Piwik\Piwik;
use Piwik\WidgetsList;

class IntranetSubNetwork extends \Piwik\Plugin
{
    /**
     * @see Piwik\Plugin::getListHooksRegistered
     */
    public function getListHooksRegistered()
    {
        return array(
            'Tracker.newVisitorInformation'     => 'logIntranetSubNetworkInfo',
            'WidgetsList.addWidgets'            => 'addWidget',
            'API.getReportMetadata'             => 'getReportMetadata',
            'API.getSegmentDimensionMetadata'   => 'getSegmentsMetadata',
        );
    }

    public function getReportMetadata(&$reports)
    {
        $reports[] = array(
            'category'      => Piwik::translate('General_Visitors'),
            'name'          => Piwik::translate('IntranetSubNetwork_WidgetIntranetSubNetwork'),
            'module'        => 'IntranetSubNetwork',
            'action'        => 'getIntranetSubNetwork',
            'dimension'     => Piwik::translate('IntranetSubNetwork_ColumnIntranetSubNetwork'),
            'documentation' => Piwik::translate('IntranetSubNetwork_WidgetIntranetSubNetworkDocumentation', '<br />'),
            'metrics'       => array(
                                'nb_visits',
                                'nb_uniq_visitors',
                                'nb_visits_percentage' => Piwik::translate('General_ColumnPercentageVisits'),
            ),
            // There is no processedMetrics for this report
            'processedMetrics' => array(),
            'order'         => 50
        );
    }

    public function getSegmentsMetadata(&$segments)
    {
        $segments[] = array(
            'type' => 'dimension',
            'category' => 'Visit',
            'name' => Piwik::translate('IntranetSubNetwork_ColumnIntranetSubNetwork'),
            'segment' => 'subnetwork',
            'acceptedValues' => 'Global IPv4, Global IPv6 etc.',
            'sqlSegment' => 'log_visit.location_IntranetSubNetwork'
        );
    }  

    public function install()
    {
	// add column location_IntranetSubNetwork in the visit table
	$query = "ALTER IGNORE TABLE `" . Common::prefixTable('log_visit') . "` ADD `location_IntranetSubNetwork` VARCHAR( 100 ) NULL";
		
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
	    $query = "ALTER TABLE `" . Common::prefixTable('log_visit') . "` DROP `location_IntranetSubNetwork`";
	    Db::exec($query);
    }

    function addWidget()
    {
        WidgetsList::add('General_Visitors', 'IntranetSubNetwork_WidgetIntranetSubNetwork', 'IntranetSubNetwork', 'getIntranetSubNetwork');
    }

    /**
     * Logs the IntranetSubNetwork in the log_visit table
     */
    public function logIntranetSubNetworkInfo(&$visitorInfo)
    {
                
        $ip = IP::N2P($visitorInfo['location_ip']);
        // by default, we want the network name to be the IP address:
        $networkName = $ip;
        /**
         *********************************************************************************************
         ****************** adopt the following lines according to your subnets **********************
         **/
        // Some default subnets:
        if (IP::isIpInRange($visitorInfo['location_ip'], array('0.0.0.0/0')))     { $networkName = 'Global IPv4'; } // all IPv4 addresses
        if (IP::isIpInRange($visitorInfo['location_ip'], array('::/0')))          { $networkName = 'Global IPv6'; } // IPv6 addresses
        if (IP::isIpInRange($visitorInfo['location_ip'], array('::ffff:0:0/96'))) { $networkName = 'Global IPv4'; } // IPv4 mapped IPv6 addresses
        // You may include your custom subnets:
        //if (Piwik_IP::isIpInRange($visitorInfo['location_ip'], array('141.2.0.0/16')))         { $networkName = 'University Frankfurt'; }
        //if (Piwik_IP::isIpInRange($visitorInfo['location_ip'], array('192.0.2.0/24')))         { $networkName = 'TEST-NET'; }
        //if (Piwik_IP::isIpInRange($visitorInfo['location_ip'], array('198.51.100.0/24')))   { $networkName = 'TEST-NET-2'; } 
        //if (Piwik_IP::isIpInRange($visitorInfo['location_ip'], array('2001:db8::/33', 
        //                                                             '2001:db8:8000::/33'))) { $networkName = 'Doc-IPv6'; }
        /**
         ******************* end adopt here to your subnets *****************************************
         *********************************************************************************************
         **/

        // add the IntranetSubNetwork value in the table log_visit
        $visitorInfo['location_IntranetSubNetwork'] = substr($networkName, 0, 100);
    }
	
}
