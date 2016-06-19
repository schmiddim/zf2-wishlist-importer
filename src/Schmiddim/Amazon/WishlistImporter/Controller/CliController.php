<?php


namespace Schmiddim\Amazon\WishlistImporter\Controller;


use AmazonWishlistExporter\Crawler\AmazonCrawler;
use Schmiddim\Amazon\Doctrine\Entities\Wishlist;
use Schmiddim\Amazon\Doctrine\Services\Product\ProductServiceInterface;
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

    /**
     * @var ProductServiceInterface
     */
    protected $productService;

    public function __construct(AmazonCrawler $amazonCrawler,
                                WishlistServiceInterface $wishlistService,
                                ApaiIOWrapper $apaiIOWrapper,
                                ProductServiceInterface $productService
    )
    {
        $this->amazonCrawler = $amazonCrawler;
        $this->wishlistService = $wishlistService;
        $this->apaiIOWrapper = $apaiIOWrapper;
        $this->productService = $productService;
    }

    public function importAction()
    {


        $wishlistId = $this->getRequest()->getParam('id');
        $countryCode =strtoupper( $this->getRequest()->getParam('tld'));

        $this->amazonCrawler->setWishlistId($wishlistId);
        $this->amazonCrawler->setCountryCode($countryCode);
        $items = $this->amazonCrawler->crawlItems();

        $itemsToFetch = array();
        $products = array();


        foreach ($items as $item) {


            $product = $this->productService->getProductByAsin($item['asin']);
            if (null === $product) {
                $itemsToFetch[] = $item['asin'];
            } else {
                $products[] = $product;
            }

        }

        $apaiIoResultSet = $this->apaiIOWrapper->getByASINS($itemsToFetch, $item['tld']);
        foreach ($apaiIoResultSet as $itemDetails) {
            $products[] = $this->productService->createProductByXml($itemDetails);
        }

        $wishList = $this->wishlistService->findByWishlistID($wishlistId);
        if (null === $wishList) {
            $wishList = new  Wishlist();
            $wishList->setId($wishlistId);
        }
        $wishList->setProducts($products);
        $this->wishlistService->persistWishList($wishList);
        $this->wishlistService->getEntityManager()->flush();

    }
}