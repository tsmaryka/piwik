<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\Contents\tests;

use Piwik\Tests\IntegrationTestCase;
use Piwik\Plugins\Contents\tests\fixtures\TwoVisitsWithContents;

/**
 * Testing Contents
 *
 * @group ContentsTest
 * @group Integration
 * @group Plugins
 */
class ContentsTest extends IntegrationTestCase
{
    public static $fixture = null; // initialized below class definition

    /**
     * @dataProvider getApiForTesting
     */
    public function testApi($api, $params)
    {
        $this->runApiTests($api, $params);
    }

    protected function getApiToCall()
    {
        return array(
            'Contents.getContentNames',
            'Contents.getContentPieces',
            'Actions.get',
      //      'Live.getLastVisitsDetails',
      //      'Actions.getPageUrls',
        );
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function getApiForTesting()
    {
        $dateTime = self::$fixture->dateTime;
        $idSite1 = self::$fixture->idSite;

        $apiToCallProcessedReportMetadata = $this->getApiToCall();

        $dayPeriod = 'day';
        $periods = array($dayPeriod, 'month');

        $apisToTest = array('Contents');
        $result = array(
            array($apiToCallProcessedReportMetadata, array(
                'idSite'       => $idSite1,
                'date'         => $dateTime,
                'periods'      => $periods,
                'setDateLastN' => false,
                'testSuffix'   => '')),

            array($apisToTest, array(
                'idSite'       => $idSite1,
                'date'         => $dateTime,
                'periods'      => $dayPeriod,
                'segment'      => "contentName==ImageAd,contentPiece==".urlencode('Click to download Piwik now'),
                'setDateLastN' => false,
                'testSuffix'   => 'contentNameOrPieceMatch')
            ),

            array($apisToTest, array(
                'idSite'       => $idSite1,
                'date'         => $dateTime,
                'periods'      => $dayPeriod,
                'segment'      => "contentTarget==".urlencode('http://www.example.com'),
                'setDateLastN' => false,
                'testSuffix'   => '_contentTargetMatch')
            ),

            array($apisToTest, array(
                'idSite'       => $idSite1,
                'date'         => $dateTime,
                'periods'      => $dayPeriod,
                'segment'      => "contentInteraction==click",
                'setDateLastN' => false,
                'testSuffix'   => '_contentInteractionMatch')
            )
        );

        $apiToCallProcessedReportMetadata = array(
            'Contents.getContentNames',
            'Contents.getContentPieces'
        );
        // testing metadata API for Contents reports
        foreach ($apiToCallProcessedReportMetadata as $api) {
            list($apiModule, $apiAction) = explode(".", $api);

            $result[] = array(
                'API.getProcessedReport', array('idSite'       => $idSite1,
                                                'date'         => $dateTime,
                                                'periods'      => $dayPeriod,
                                                'setDateLastN' => true,
                                                'apiModule'    => $apiModule,
                                                'apiAction'    => $apiAction,
                                                'testSuffix'   => '_' . $api . '_lastN')
            );
        }

        return $result;
    }

    public static function getOutputPrefix()
    {
        return 'Contents';
    }

    public static function getPathToTestDirectory()
    {
        return dirname(__FILE__);
    }
}

ContentsTest::$fixture = new TwoVisitsWithContents();