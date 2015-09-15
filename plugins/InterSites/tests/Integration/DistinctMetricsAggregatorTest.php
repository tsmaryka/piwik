<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\InterSites\tests\Integration;

use Piwik\Date;
use Piwik\Plugins\InterSites\Model\DistinctMetricsAggregator;
use Piwik\Segment;

/**
 * @group InterSites
 * @group InterSites_Unit
 * @group InterSites_DistinctMetricsAggregatorTest
 */
class DistinctMetricsAggregatorTest extends \Piwik\Tests\Framework\TestCase\IntegrationTestCase
{
    /**
     * @var DistinctMetricsAggregator
     */
    private $instance;

    public function setUp()
    {
        parent::setUp();

        // TODO: this test case is waiting for https://github.com/piwik/piwik/issues/6207

        $this->instance = new DistinctMetricsAggregator();
    }

    /**
     * @expectedException \Exception
     */
    public function test_getCommonVisitorCount_Throws_WhenOneSiteSupplied()
    {
        $this->instance->getCommonVisitorCount(
            array(1),
            Date::factory("2012-01-01"),
            Date::factory("2012-02-01"),
            new Segment("", array(1))
        );
    }

    public function testGetCommonVisitorCountSucceedsWithMultipleSitesAndDateRange()
    {
        // TODO
    }

    public function testGetCommonVisitorCountSucceedsWithMultipleSitesAndDateRangeAndSegment()
    {
        // TODO
    }

    public function testGetCommonVisitorCountSucceedsWhenThereAreNoVisits()
    {
        // TODO
    }
}