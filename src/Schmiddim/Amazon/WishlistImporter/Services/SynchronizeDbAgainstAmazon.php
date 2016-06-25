<?php

namespace Schmiddim\Amazon\WishlistImporter\Services;

use AmazonWishlistExporter\Crawler\AmazonCrawler;
use Schmiddim\Amazon\Doctrine\Entities\Wishlist;
use Schmiddim\Amazon\Doctrine\Services\Product\ProductServiceInterface;
use Schmiddim\Amazon\Doctrine\Services\Wishlist\WishlistServiceInterface;
use Schmiddim\Amazon\ProductApi\ApaiIOWrapper;

class SynchronizeDbAgainstAmazon implements SynchronizeDbAgainstAmazonInterface
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


    public function synchronize($wishlistId, $countryCode)
    {
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
            $product = $this->productService->createProductByXml($itemDetails);
            $products[] = $product;
            $this->productService->getEntityManager()->persist($product);
        }

        $wishList = $this->wishlistService->findByWishlistID($wishlistId);
        if (null === $wishList) {
            $wishList = new  Wishlist();
            $wishList->setName('IDK');
            $wishList->setWishlistOwnerName('IDK');
            $wishList->setAmazonId($wishlistId);
            $wishList->setTld($countryCode);
            //@todo owner & so on
        }

        $wishList->setProducts($products);
        $this->wishlistService->persistWishList($wishList);
        $this->wishlistService->getEntityManager()->flush();
    }

    /**
     * @return ApaiIOWrapper
     */
    public function getApaiIOWrapper()
    {
        return $this->apaiIOWrapper;
    }



}
