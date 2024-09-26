<?php declare(strict_types=1);


namespace App\Commands\Services\Euronics;

use App\Models\Category;
use App\Models\Product;
use DOMNode;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Facebook\WebDriver\Exception\UnsupportedOperationException;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Sleep;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Panther\Client;
use Facebook\WebDriver\Remote\RemoteWebElement;
use voku\helper\HtmlDomParser;

final class EuronicsScrapingService
{
    public function getProductsByCategories(string $key) : void
    {
        $categories = $this->scrapeCategoriesFromHomePage();
        foreach ($categories as $category) {
            foreach ($category->getSubCategories() as $subCategory){
                foreach ($subCategory->getSubCategories() as $subSubCategory) {
                    foreach ($this->scrapeProductsFromLink($subSubCategory->getLink()) as $product) {
                        $subSubCategory->addProduct($product);
                    }
                    Storage::disk('local')->put("productsByCategory$key.json", json_encode($categories));
                    break;
                }
                break;
            }
            break;
        }
    }

    public function scrapeProductsFromLink(string $link) {
        $client = Client::createChromeClient();
        $client->request('GET', $link);

        $products = [];

        //Load all the data
        while (true) {
            try {
                $client->wait(10)->until(function () use ($client) {
                    $elements = $client->getCrawler()->filter(".loading-button");
                    return count($elements) === 1;
                });
            } catch (NoSuchElementException $e) {
                break;
            } catch (TimeoutException $e) {
                echo "Timed out searching for element.";
                break;
            } catch (\Exception $e) {
                break;
            }
//            break; //TODO: REMOVE THIS!!!
            $crawler = $client->getCrawler();
            $newLink = $crawler->findElement(WebDriverBy::cssSelector(".loading-button"))->getAttribute("href");
            $newLink .= "&p=1";
            echo $newLink;
            echo "\n";
            $client->request('GET', $newLink);
        }

        echo $client->getCurrentURL();
        echo "\n";

        $crawler = $client->getCrawler();
        echo $client->getCurrentURL();


        $productElements = $crawler->findElements(WebDriverBy::cssSelector(".product-card.vertical"));

        $i = 0;
        foreach ($productElements as $productHtml) {
            $i++;
            echo $i;
            $product = $this->MakeProduct($productHtml);
            $products[] = $product;
        }

        return $products;
    }

    private function MakeProduct(RemoteWebElement $remoteWebElement) : Product
    {
        $productName = $remoteWebElement->findElement(WebDriverBy::cssSelector(".product-card__title"))->getDomProperty("innerHTML");
        $productName = trim($productName);
        $productCode = $remoteWebElement->findElement(WebDriverBy::cssSelector(".overview-producer__code > .value"))->getDomProperty("innerHTML");
        $productCode = trim($productCode);
        $availabilityStatus = $remoteWebElement->findElement(WebDriverBy::cssSelector(".badge__wrapper > .badge"))->getDomProperty("innerHTML");
        $availabilityStatus = trim($availabilityStatus);

        $price = (string) $remoteWebElement->findElement(WebDriverBy::cssSelector(".price"))->getDomProperty("innerHTML");
        preg_match("#(\d+)\s*?(?:<span.*?>)\s*?(\.\d{2})#" ,$price, $matches);
        if (count($matches) > 1) {
            $price = $matches[1] . ($matches[2] ?? "") . "€";
        }
        $price = trim($price);

        $oldPriceElements = $remoteWebElement->findElements(WebDriverBy::cssSelector(".discount__old > .label"));
        if (count($oldPriceElements) > 0) {
            $oldPrice = $oldPriceElements[0]->getDomProperty("innerHTML");
            $oldPrice = trim($oldPrice);
        } else {
            $oldPrice = null;
        }

        return new Product($productName, $productCode, $price, $oldPrice, $availabilityStatus);
    }



    public function scrapeCategoriesFromHomePage(): ?array
    {
        $client = Client::createChromeClient();
        $client->request('GET', 'https://www.euronics.ee/');

        try {
            $client->wait()->until(function () use ($client) {
                $elements = $client->getCrawler()->filter(".main-nav__item");

                return count($elements) > 10;
            });
        } catch (NoSuchElementException $e) {
            return null;
        } catch (TimeoutException $e) {
            echo "Timed out searching for element.";
            return null;
        } catch (\Exception $e) {
        }
        $crawler = $client->getCrawler();
//        $crawler->filter(".main-nav__list > .main-nav__item")->text();
        $categories = [];


        $elements = $crawler->findElements(WebDriverBy::cssSelector("#categoryMenu > .header__wrapper.container > .main-nav > .main-nav__list > .main-nav__item"));
        foreach ($elements as $element) {
            $category = $this->MakeCategory($element);
            $categories[] = $category;
        }

        return $categories;
    }

    private function MakeCategory(RemoteWebElement $remoteWebElement) : Category
    {
        $categoryNameElement = $remoteWebElement->findElement(WebDriverBy::cssSelector(".nav-item__label-name"));
        $categoryName = trim($categoryNameElement->getDomProperty("innerHTML"));
        $category = new Category($categoryName);

        $subMenuRemoteElement = $remoteWebElement->findElements(WebDriverBy::cssSelector(".sub-menu__block.with-banner"));
        foreach ($subMenuRemoteElement as $element) {
            $subCategory = $this->MakeSubCategory($element);
            $category->addSubCategory($subCategory);
        }
        return $category;
    }

        private function MakeSubCategory(RemoteWebElement $remoteWebElement) : Category
    {
        $categoryName = $remoteWebElement->findElement(WebDriverBy::cssSelector(".sub-menu__block-heading"))->getDomProperty("innerHTML");
        $categoryName = trim($categoryName);
        $categoryLink = $remoteWebElement->findElement(WebDriverBy::cssSelector(".sub-menu__block-heading"))->getAttribute("href");

        $category = new Category($categoryName, $categoryLink);

        $subSubMenuRemoteElement = $remoteWebElement->findElements(WebDriverBy::cssSelector(".bottom-level__block"));
        foreach ($subSubMenuRemoteElement as $element) {
            $subCategory = $this->MakeSubSubCategory($element);
            $category->addSubCategory($subCategory);
        }

        return $category;
    }

    private function MakeSubSubCategory(RemoteWebElement $remoteWebElement) : Category
    {
        $categoryName = $remoteWebElement->findElement(WebDriverBy::cssSelector(".bottom-level__item__title"))->getDomProperty("innerHTML");
        $categoryName = trim($categoryName);
        $categoryLink = $remoteWebElement->findElement(WebDriverBy::cssSelector(".bottom-level__item__title"))->getAttribute("href");
        return new Category($categoryName, $categoryLink);
    }
}
