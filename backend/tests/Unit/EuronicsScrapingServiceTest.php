<?php

namespace Tests\Unit;

use App\Commands\Services\Euronics\EuronicsScrapingService;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class EuronicsScrapingServiceTest extends TestCase
{
    public function testGetProductsByCategories()
    {
        $service = new EuronicsScrapingService();
//        $uuid = Uuid::uuid4()->toString();
        $uuid = "AllData";
        $service->getProductsByCategories($uuid);
    }
    public function testScrapeProductsFromLink()
    {
        $service = new EuronicsScrapingService();
        echo "starting: \n";
        $products = $service->scrapeProductsFromLink("https://www.euronics.ee/kodumasinad/kulmikud/kulmikud");
    }

    public function testScrapeCategories() {
        $service = new EuronicsScrapingService();
        echo "starting: \n";
        $categories = $service->scrapeCategoriesFromHomePage();
        if ($categories != null) {
//            foreach ($categories as $category) {
//                echo $category->getName();
//            }
            $categories = (array) $categories;
            $mainCategory = $categories[0];
            $subCategory = $mainCategory->getSubCategorys()[0];
            $subSubCategory = $subCategory->getSubCategorys()[0];
            echo "Main: \n";
            echo $mainCategory->getName();
            echo $mainCategory->getLink();
            echo "Sub: \n";
            echo $subCategory->getName();
            echo $subCategory->getLink();
            echo "SubSub: \n";
            echo $subSubCategory->getName();
            echo $subSubCategory->getLink();
        }
    }
}
