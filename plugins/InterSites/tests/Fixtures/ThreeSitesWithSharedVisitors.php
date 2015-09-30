<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
<<<<<<< HEAD
namespace Piwik\Plugins\InterSites\tests\Fixtures;
=======
namespace Piwik\Plugins\InterSites\Tests\Fixtures;
>>>>>>> 87f4508d47e83bf183574adda233e26a5847d868

use Piwik\Date;
use Piwik\Db;
use Piwik\Tests\Framework\Fixture;

/**
 * Adds three sites and tracks some visits w/ visitors that visit each site.
 */
class ThreeSitesWithSharedVisitors extends Fixture
{
    public $idSite = 1;
    public $idSite1 = 2;
    public $idSite2 = 3;
    public $dateTime = '2010-03-06 11:22:33';

    public function setUp()
    {
        $this->setUpWebsitesAndGoals();
        $this->trackVisits();
    }

    public function tearDown()
    {
        // empty
    }

    private function setUpWebsitesAndGoals()
    {
        if (!self::siteCreated($this->idSite)) {
            self::createWebsite($this->dateTime, $ecommerce = 0, "Site 1");
        }

        if (!self::siteCreated($this->idSite1)) {
            self::createWebsite($this->dateTime, $ecommerce = 1, "Site 2");
        }

        if (!self::siteCreated($this->idSite2)) {
            self::createWebsite($this->dateTime, $ecommerce = 2, "Site 3");
        }
    }

    private function trackVisits()
    {
        $dateTime = $this->dateTime;
        $idSite = $this->idSite;

        // visitors
        $visitor1 = $this->makeTracker($idSite, $dateTime);
        $visitor1->setIp("123.45.67.8");
        $visitor1->setCity("Järvenpää");
        $visitor1->setUserAgent("Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36");

        $visitor2 = $this->makeTracker($idSite, $dateTime);
        $visitor2->setIp("99.44.55.66");
        $visitor2->setCity("Shirahama");
        $visitor2->setUserAgent("Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20120101 Firefox/29.0");

        $visitor3 = $this->makeTracker($idSite, $dateTime);
        $visitor3->setIp("121.244.65.34");
        $visitor3->setCity("Porto Covo");
        $visitor3->setUserAgent("Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36");

        $visitor4 = $this->makeTracker($idSite, $dateTime);
        $visitor4->setIp("87.45.34.98");
        $visitor4->setCity("Järvenpää");
        $visitor4->setUserAgent("Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20120101 Firefox/29.0");

        $visitor5 = $this->makeTracker($idSite, $dateTime);
        $visitor5->setIp("76.45.63.27");
        $visitor5->setCity("Järvenpää");
        $visitor5->setUserAgent("Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20120101 Firefox/29.0");

        // visits to site 1 (2 from visitor1, 1 from visitor2, 2 from visitor4, 2 from visitor5)
        $visitor1->setIdSite($this->idSite);
        $visitor1->setForceVisitDateTime(Date::factory($this->dateTime)->getDatetime());
        $visitor1->setUrl('http://kilamanjaro.org/kibo');
<<<<<<< HEAD
        Fixture::checkResponse($visitor1->doTrackPageView("page title"));

        $visitor1->setForceVisitDateTime(Date::factory($this->dateTime)->addHour(1)->getDatetime());
        $visitor1->setUrl('http://ellsworth.org/vinsonmassif');
        Fixture::checkResponse($visitor1->doTrackPageView("page title"));
=======
        $visitor1->doTrackPageView("page title");

        $visitor1->setForceVisitDateTime(Date::factory($this->dateTime)->addHour(1)->getDatetime());
        $visitor1->setUrl('http://ellsworth.org/vinsonmassif');
        $visitor1->doTrackPageView("page title");
>>>>>>> 87f4508d47e83bf183574adda233e26a5847d868

        $visitor2->setIdSite($this->idSite);
        $visitor2->setForceVisitDateTime(Date::factory($this->dateTime)->getDatetime());
        $visitor2->setUrl('http://himalayas.org/everest');
<<<<<<< HEAD
        Fixture::checkResponse($visitor2->doTrackPageView("page title"));
=======
        $visitor2->doTrackPageView("page title");
>>>>>>> 87f4508d47e83bf183574adda233e26a5847d868

        $visitor4->setIdSite($this->idSite);
        $visitor4->setForceVisitDateTime(Date::factory($this->dateTime)->getDatetime());
        $visitor4->setUrl('http://caucasus.org/elbrus');
<<<<<<< HEAD
        Fixture::checkResponse($visitor4->doTrackPageView("page title"));

        $visitor4->setForceVisitDateTime(Date::factory($this->dateTime)->addHour(1)->getDatetime());
        $visitor4->setUrl('http://alaskarange.org/mckinley');
        Fixture::checkResponse($visitor4->doTrackPageView("page title"));
=======
        $visitor4->doTrackPageView("page title");

        $visitor4->setForceVisitDateTime(Date::factory($this->dateTime)->addHour(1)->getDatetime());
        $visitor4->setUrl('http://alaskarange.org/mckinley');
        $visitor4->doTrackPageView("page title");
>>>>>>> 87f4508d47e83bf183574adda233e26a5847d868

        $visitor5->setIdSite($this->idSite);
        $visitor5->setForceVisitDateTime(Date::factory($this->dateTime)->getDatetime());
        $visitor5->setUrl('http://andes.org/aconcagua');
<<<<<<< HEAD
        Fixture::checkResponse($visitor5->doTrackPageView("page title"));

        $visitor5->setForceVisitDateTime(Date::factory($this->dateTime)->addHour(1)->getDatetime());
        $visitor5->setUrl('http://bigben.org/mawson');
        Fixture::checkResponse($visitor5->doTrackPageView("page title"));
=======
        $visitor5->doTrackPageView("page title");

        $visitor5->setForceVisitDateTime(Date::factory($this->dateTime)->addHour(1)->getDatetime());
        $visitor5->setUrl('http://bigben.org/mawson');
        $visitor5->doTrackPageView("page title");
>>>>>>> 87f4508d47e83bf183574adda233e26a5847d868

        // visits to site 2 (1 from visitor1, 2 from visitor2, 1 from visitor4)
        $visitor1->setIdSite($this->idSite1);
        $visitor1->setForceVisitDateTime(Date::factory($this->dateTime)->getDatetime());
        $visitor1->setUrl('http://janmayen.org/beerenberg');
<<<<<<< HEAD
        Fixture::checkResponse($visitor1->doTrackPageView("page title"));
=======
        $visitor1->doTrackPageView("page title");
>>>>>>> 87f4508d47e83bf183574adda233e26a5847d868

        $visitor2->setIdSite($this->idSite1);
        $visitor2->setForceVisitDateTime(Date::factory($this->dateTime)->getDatetime());
        $visitor2->setUrl('http://saotome.org/picodesaotome');
<<<<<<< HEAD
        Fixture::checkResponse($visitor2->doTrackPageView("page title"));

        $visitor2->setForceVisitDateTime(Date::factory($this->dateTime)->addHour(1)->getDatetime());
        $visitor2->setUrl('http://serradosorgaos.org/dedodedeus');
        Fixture::checkResponse($visitor2->doTrackPageView("page title"));
=======
        $visitor2->doTrackPageView("page title");

        $visitor2->setForceVisitDateTime(Date::factory($this->dateTime)->addHour(1)->getDatetime());
        $visitor2->setUrl('http://serradosorgaos.org/dedodedeus');
        $visitor2->doTrackPageView("page title");
>>>>>>> 87f4508d47e83bf183574adda233e26a5847d868

        $visitor4->setIdSite($this->idSite1);
        $visitor4->setForceVisitDateTime(Date::factory($this->dateTime)->getDatetime());
        $visitor4->setUrl('http://sumatra.org/gunungleuser');
<<<<<<< HEAD
        Fixture::checkResponse($visitor4->doTrackPageView("page title"));
=======
        $visitor4->doTrackPageView("page title");
>>>>>>> 87f4508d47e83bf183574adda233e26a5847d868

        // visits to site 3 (1 from visitor1, 2 from visitor3, 2 from visitor4, 1 from visitor5)
        $visitor1->setIdSite($this->idSite2);
        $visitor1->setForceVisitDateTime(Date::factory($this->dateTime)->getDatetime());
        $visitor1->setUrl('http://victoria.org/feathertop');
<<<<<<< HEAD
        Fixture::checkResponse($visitor1->doTrackPageView("page title"));
=======
        $visitor1->doTrackPageView("page title");
>>>>>>> 87f4508d47e83bf183574adda233e26a5847d868

        $visitor3->setIdSite($this->idSite2);
        $visitor3->setForceVisitDateTime(Date::factory($this->dateTime)->getDatetime());
        $visitor3->setUrl('http://cordilleracentral.org/volcanirazu');
<<<<<<< HEAD
        Fixture::checkResponse($visitor3->doTrackPageView("page title"));

        $visitor3->setForceVisitDateTime(Date::factory($this->dateTime)->addHour(1)->getDatetime());
        $visitor3->setUrl('http://cascaderange.org/brokentop');
        Fixture::checkResponse($visitor3->doTrackPageView("page title"));
=======
        $visitor3->doTrackPageView("page title");

        $visitor3->setForceVisitDateTime(Date::factory($this->dateTime)->addHour(1)->getDatetime());
        $visitor3->setUrl('http://cascaderange.org/brokentop');
        $visitor3->doTrackPageView("page title");
>>>>>>> 87f4508d47e83bf183574adda233e26a5847d868

        $visitor4->setIdSite($this->idSite2);
        $visitor4->setForceVisitDateTime(Date::factory($this->dateTime)->getDatetime());
        $visitor4->setUrl('http://watkinsrange.org/gunnbjorn');
<<<<<<< HEAD
        Fixture::checkResponse($visitor4->doTrackPageView("page title"));

        $visitor4->setForceVisitDateTime(Date::factory($this->dateTime)->addHour(1)->getDatetime());
        $visitor4->setUrl('http://niut.org/razorback');
        Fixture::checkResponse($visitor4->doTrackPageView("page title"));
=======
        $visitor4->doTrackPageView("page title");

        $visitor4->setForceVisitDateTime(Date::factory($this->dateTime)->addHour(1)->getDatetime());
        $visitor4->setUrl('http://niut.org/razorback');
        $visitor4->doTrackPageView("page title");
>>>>>>> 87f4508d47e83bf183574adda233e26a5847d868

        $visitor5->setIdSite($this->idSite2);
        $visitor5->setForceVisitDateTime(Date::factory($this->dateTime)->getDatetime());
        $visitor5->setUrl('http://stikineicecap.org/katesneedle');
<<<<<<< HEAD
        Fixture::checkResponse($visitor5->doTrackPageView("page title"));
=======
        $visitor5->doTrackPageView("page title");
>>>>>>> 87f4508d47e83bf183574adda233e26a5847d868
    }

    /**
     * @param $idSite
     * @param $dateTime
     * @return \PiwikTracker
     */
    private function makeTracker($idSite, $dateTime)
    {
        $tracker = self::getTracker($idSite, $dateTime, $defaultInit = true);
        $tracker->setDebugStringAppend('forceEnableFingerprintingAcrossWebsites=1');
        return $tracker;
    }
}
