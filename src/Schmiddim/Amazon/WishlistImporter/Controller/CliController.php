<?php


namespace Schmiddim\Amazon\WishlistImporter\Controller;


use AmazonWishlistExporter\Crawler\AmazonCrawler;
use Zend\Mvc\Controller\AbstractActionController;

class CliController extends AbstractActionController
{

    /**
     * @var AmazonCrawler
     */
    protected  $amazonCrawler;
    public function __construct(AmazonCrawler $amazonCrawler)
    {
        $this->amazonCrawler = $amazonCrawler;
    }

    public function importAction()
    {


        echo "do an import";
    }
}