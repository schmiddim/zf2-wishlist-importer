<?php


namespace Schmiddim\Amazon\WishlistImporter\Controller;


use AmazonWishlistExporter\Crawler\AmazonCrawler;
use Schmiddim\Amazon\Doctrine\Entities\Wishlist;
use Schmiddim\Amazon\Doctrine\Services\Product\ProductServiceInterface;
use Schmiddim\Amazon\Doctrine\Services\Wishlist\WishlistServiceInterface;
use Schmiddim\Amazon\ProductApi\ApaiIOWrapper;
use Schmiddim\Amazon\WishlistImporter\Services\SynchronizeDbAgainstAmazonInterface;
use Zend\Mvc\Controller\AbstractActionController;

class CliController extends AbstractActionController
{


    /**
     * @var SynchronizeDbAgainstAmazonInterface
     */
    protected $synchronize;

    public function __construct(SynchronizeDbAgainstAmazonInterface $synchronize)
    {
        $this->synchronize = $synchronize;
    }


    public function importAction()
    {


        $wishlistId = $this->getRequest()->getParam('id');
        $countryCode = strtoupper($this->getRequest()->getParam('tld'));

        $this->synchronize->synchronize($wishlistId, $countryCode);

    }

    public function askApiAction()
    {
        $asin = $this->getRequest()->getParam('id');
        $countryCode = strtoupper($this->getRequest()->getParam('tld'));
        /** @var  $apaiIO  ApaiIOWrapper */
        $apaiIO = $this->synchronize->getApaiIOWrapper();

     $response =   $apaiIO->getByASIN($asin, $countryCode);
        print_r($response);
    }
}