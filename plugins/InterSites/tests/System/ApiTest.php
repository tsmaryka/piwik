<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\InterSites\tests\System;

use Piwik\Date;
use Piwik\Plugins\InterSites\Tests\Fixtures\ThreeSitesWithSharedVisitors;
use Piwik\Tests\Framework\TestCase\SystemTestCase;

/**
 * @group InterSites
 * @group InterSites_Integration
 * @group InterSites_ApiTest
 */
class ApiTest extends SystemTestCase
{
    /**
     * @var ThreeSitesWithSharedVisitors
     */
    public static $fixture;

    public function testGetCommonVisitorsSucceedsWithAllSites()
    {
        $this->assertApiResponseEqualsExpected("InterSites.getCommonVisitors", array(
            'idSite' => 'all',
            'date' => Date::factory(self::$fixture->dateTime)->toString(),
            'period' => 'month',
        ));
    }

    public function testGetCommonVisitorsFailsWithSingleSite()
    {
        $this->assertApiResponseEqualsExpected("InterSites.getCommonVisitors", array(
            'idSite' => 1,
            'date' => Date::factory(self::$fixture->dateTime)->toString(),
            'period' => 'month'
        ));
    }

    public function testGetCommonVisitorsSucceedsWithSegment()
    {
        $this->assertApiResponseEqualsExpected("InterSites.getCommonVisitors", array(
            'idSite' => '1,2',
            'date' => Date::factory(self::$fixture->dateTime)->toString(),
            'period' => 'month',
            'segment' => 'city==Järvenpää'
        ));
    }

    public static function getPathToTestDirectory()
    {
        return PIWIK_INCLUDE_PATH . '/plugins/InterSites/tests/System';
    }
}

ApiTest::$fixture = new ThreeSitesWithSharedVisitors();