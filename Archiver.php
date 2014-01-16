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

use Piwik\Metrics;

class Archiver extends \Piwik\Plugin\Archiver
{
    const INTRANETSUBNETWORK_RECORD_NAME = 'IntranetSubNetwork_networkNameExt';
    const INTRANETSUBNETWORK_FIELD = "location_IntranetSubNetwork";

    public function aggregateMultipleReports()
    {
        $this->getProcessor()->aggregateDataTableRecords(array(self::INTRANETSUBNETWORK_RECORD_NAME));
    }

    /**
     * Archive the IntranetSubNetwork count
     */

    public function aggregateDayReport()
    {
        $metrics = $this->getLogAggregator()->getMetricsFromVisitByDimension(self::INTRANETSUBNETWORK_FIELD)->asDataTable();
        $this->getProcessor()->insertBlobRecord(self::INTRANETSUBNETWORK_RECORD_NAME, $metrics->getSerialized());
    }

}