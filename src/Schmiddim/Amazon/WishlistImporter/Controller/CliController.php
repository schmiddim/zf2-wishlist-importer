<?php


namespace Schmiddim\Amazon\WishlistImporter\Controller;


use AmazonWishlistExporter\Crawler\AmazonCrawler;
use Schmiddim\Amazon\Doctrine\Services\Wishlist\WishlistServiceInterface;
use Schmiddim\Amazon\ProductApi\ApaiIOWrapper;
use Zend\Mvc\Controller\AbstractActionController;

class CliController extends AbstractActionController
{

    /**
     * @var AmazonCrawler
     */
    protected $amazonCrawler;

    /**
     * @var ApaiIOWrapper
     */
    protected $apaiIOWrapper;
    /**
     * @var WishlistServiceInterface
     */
    protected $wishlistService;

    public function __construct(AmazonCrawler $amazonCrawler, WishlistServiceInterface $wishlistService, ApaiIOWrapper $apaiIOWrapper)
    {
        $this->amazonCrawler = $amazonCrawler;
        $this->wishlistService = $wishlistService;
        $this->apaiIOWrapper = $apaiIOWrapper;
    }

    public function importAction()
    {

        $wishlistId = '3PNTY4VFL6H2Q';
   #     $wishList = $this->wishlistService->findByWishlistID($wishlistId);
    #    $wishListProducts = $wishList->getProducts();


        $this->amazonCrawler->setWishlistId($wishlistId);
        $this->amazonCrawler->setCountryCode('DE');
        $items = $this->amazonCrawler->crawlItems();



        foreach($items as $item) {

        }


     #   echo count($wishListProducts);
        echo PHP_EOL;
        echo count($items);
        echo PHP_EOL;
    }
}