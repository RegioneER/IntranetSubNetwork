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

use Piwik\Piwik;

function getSubnetName($in)
{
	if(empty($in))
		return Piwik::Translate('General_Unknown');
	return $in;
}
