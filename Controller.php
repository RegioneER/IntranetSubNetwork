<?php

namespace Piwik\Plugins\IntranetSubNetwork;

use Piwik\Piwik;
use Piwik\View;
use Piwik\ViewDataTable\Factory;

class Controller extends \Piwik\Plugin\Controller
{

	/*
	 * IntranetSubNetwork
	*/ 
	public function getIntranetSubNetwork($fetch = false)
	{
		$view = Factory::build( $this->pluginName, "IntranetSubNetwork.getIntranetSubNetwork", $this->pluginName . '.' . __FUNCTION__ );
		$this->setPeriodVariablesView($view);
		$column = 'nb_visits';
		$percCol = 'nb_visits_percentage';
		$percColName = 'General_ColumnPercentageVisits';
		if($view->period == 'day')
			$column = 'nb_uniq_visitors';
		$view->config->columns_to_display = array('label',$percCol,$column);
		$view->config->addTranslation('label', Piwik::translate('IntranetSubNetwork_ColumnIntranetSubNetwork'));
		$view->config->addTranslation($percCol, str_replace('% ', '%&nbsp;', Piwik::translate($percColName)));
		$view->config->show_bar_chart = false;
		$view->config->show_pie_chart = false;
		$view->config->show_tag_cloud = false;
		$view->requestConfig->filter_sort_column = $percCol;
		$view->requestConfig->filter_id = 5;

		return $view->render();
	}
	
}
