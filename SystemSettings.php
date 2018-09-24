<?php

namespace Piwik\Plugins\IntranetSubNetwork;

use Piwik\Piwik;
use Piwik\Settings\Setting;
use Piwik\Settings\FieldConfig;

/**
 * Defines Settings for IntranetSubNetwork.
 *
 * Usage like this:
 * $settings->subnetworks->getValue();
 */
class SystemSettings extends \Piwik\Settings\Plugin\SystemSettings
{

    /** @var Setting */
    public $subnetworks;

    protected function init()
    {
        // System setting --> textarea
        $this->subnetworks = $this->createSubnetworksSetting();
    }

    private function createSubnetworksSetting()
    {
        $default = implode("\n", [
            '0.0.0.0/0|Global IPv4',
            '::/0|Global IPv6',
            '::ffff:0:0/96|Global IPv4',
        ]);

        return $this->makeSetting('subnetworks', $default, FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('IntranetSubNetwork_SettingsSubnetworksTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_TEXTAREA;
            $field->description = Piwik::translate('IntranetSubNetwork_SettingsSubnetworksDescription');
        });
    }

}
