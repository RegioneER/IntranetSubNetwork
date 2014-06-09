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

use Piwik\Archive;
use Piwik\DataTable;
use Piwik\Metrics;
use Piwik\Piwik;

/**
 * @see plugins/IntranetSubNetwork/functions.php
 */

require_once PIWIK_INCLUDE_PATH . '/plugins/IntranetSubNetwork/functions.php';

/**
 * API for plugin IntranetSubNetwork
 * @method static \Piwik\Plugins\IntranetSubNetwork\API getInstance()
 */
class API extends \Piwik\Plugin\API
{
    
    public function getIntranetSubNetwork( $idSite, $period, $date, $segment = false )
    {
        Piwik::checkUserHasViewAccess( $idSite );
        $archive = Archive::build($idSite, $period, $date, $segment );
        $dataTable = $archive->getDataTable(Archiver::INTRANETSUBNETWORK_RECORD_NAME);
        $dataTable->filter('Sort', array(Metrics::INDEX_NB_VISITS));
        $dataTable->queueFilter('ColumnCallbackReplace', array('label',  __NAMESPACE__ . '\getSubnetName'));
        $dataTable->queueFilter('ReplaceColumnNames');

        $column = 'nb_visits';
        $percCol = 'nb_visits_percentage';
        $percColName = 'General_ColumnPercentageVisits';

        $visitsSums = $archive->getDataTableFromNumeric($column);

        // check whether given tables are arrays
        if($dataTable instanceof DataTable\Map) {
            $tableArray = $dataTable->getDataTables();
            $visitSumsArray = $visitsSums->getDataTables();
        } else {
            $tableArray = Array($dataTable);
            $visitSumsArray = Array($visitsSums);
        }
        // walk through the results and calculate the percentage
        foreach($tableArray as $key => $table) {
            foreach($visitSumsArray AS $k => $visits) {
                if($k == $key) {
                    if(is_object($visits))
                        $visitsSumTotal = (float)$visits->getFirstRow()->getColumn($column);
                    else
                        $visitsSumTotal = (float)$visits;
                }
            }

            // Skip aggregation of percentages when AddSummaryRow is called.
            $columnAggregationOps = $table->getMetadata(DataTable::COLUMN_AGGREGATION_OPS_METADATA_NAME);
            $columnAggregationOps[$percCol] = 'skip';
            $table->setMetadata(DataTable::COLUMN_AGGREGATION_OPS_METADATA_NAME, $columnAggregationOps);


            $table->filter('ColumnCallbackAddColumnPercentage', array($percCol, Metrics::INDEX_NB_VISITS, $visitsSumTotal, 1));
            // we don't want <0% or >100%:
            $table->filter('RangeCheck', array($percCol));
        }
        return $dataTable;
    }

}
