<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\InterSites;
use Piwik\Config;
use Piwik\Notification;
use Piwik\Piwik;

/**
 */
class InterSites extends \Piwik\Plugin
{
    /**
     * @see Piwik\Plugin::getListHooksRegistered
     */
    public function getListHooksRegistered()
    {
        return array(
            'AssetManager.getStylesheetFiles' => 'getStylesheetFiles',
            'AssetManager.getJavaScriptFiles' => 'getJsFiles',
            'Translate.getClientSideTranslationKeys' => 'getClientSideTranslationKeys',
            'Request.dispatch' => 'notifyWhenPiwikNotConfiguredCorrectly',
        );
    }

    public function notifyWhenPiwikNotConfiguredCorrectly()
    {
        $fingerprintingEnabled = (bool)Config::getInstance()->Tracker['enable_fingerprinting_across_websites'];
        if($fingerprintingEnabled) {
            return;
        }

        $message = "<b>Warning: InterSites Plugin requires a change in your Piwik configuration:</b><br/>"
            . " To collect data for InterSites plugin, you must configure Piwik to allow the tracking of your visitors across websites. </br>"
            . " (By default Piwik does not know if a given user has visited several of your websites.) </br>"
            . " To continue using InterSites you must edit your file config/config.ini.php and add the following setting: </br>"
            . "<code>[Tracker]</br>enable_fingerprinting_across_websites=1</code><br/>"
            . " After you make this change, give Piwik time to collect new data. You can then view the '%s' in the <b>All Websites</b> dashboard</a>.";
        $message = sprintf($message, Piwik::translate('InterSites_CommonVisitorsTool_LaunchLinkText'));
        $notification = new Notification($message);
        $notification->raw = true;
        $notification->context = Notification::CONTEXT_WARNING;
        Notification\Manager::notify('InterSites_MustBeConfigured', $notification);
    }

    public function getStylesheetFiles(&$files)
    {
        $files[] = 'plugins/InterSites/javascripts/angularjs/compareVisitorsTool/compareVisitorsTool.less';
    }

    public function getJsFiles(&$jsFiles)
    {
        $jsFiles[] = 'plugins/InterSites/javascripts/all-websites-common-visitors-addition.js';
        $jsFiles[] = 'plugins/InterSites/javascripts/angularjs/compareVisitorsTool/controller.js';
        $jsFiles[] = 'plugins/InterSites/javascripts/angularjs/compareVisitorsTool/directive.js';
    }

    public function getClientSideTranslationKeys(&$keys)
    {
        $keys[] = "InterSites_SharedVisitors";
        $keys[] = "InterSites_CommonVisitorsToolIntro";
        $keys[] = "InterSites_CommonVisitorsTool_SiteStatsDesc";
        $keys[] = "InterSites_CommonVisitorsTool_SiteStatsNoVisits";
        $keys[] = "InterSites_CommonVisitorsTool_AllSitesStatsDesc";
        $keys[] = "InterSites_CommonVisitorsTool_AllSitesNoVisits";
        $keys[] = "InterSites_CommonVisitorsTool_LaunchLinkText";
        $keys[] = "InterSites_Go";
        $keys[] = "InterSites_AddSite";
        $keys[] = "InterSites_None";
    }
}
