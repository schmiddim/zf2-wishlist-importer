<?php

namespace Schmiddim\Amazon\WishlistImporter\Factories;

use Schmiddim\Amazon\WishlistImporter\Services\SynchronizeDbAgainstAmazon;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Schmiddim\Amazon\Doctrine\Services\Wishlist\WishlistServiceInterface;
use Schmiddim\Amazon\Doctrine\Services\Product\ProductServiceInterface;
use AmazonWishlistExporter\Crawler\AmazonCrawler;
use Psr\Log\NullLogger;
use Schmiddim\Amazon\ProductApi\ApaiIOWrapper;

class SynchronizeDbAgainstAmazonFactory implements FactoryInterface
{
    public function __invoke(ServiceManager $container, $name, array
    $options = null)
    {
        /**
         * @var $productService ProductServiceInterface
         */
        $productService = $container->get(ProductServiceInterface::class);

        /**
         * @var $wishlistService WishlistServiceInterface
         */
        $wishlistService = $container->get(WishlistServiceInterface::class);

        $client = new \GuzzleHttp\Client();
        $amazonCrawler = new AmazonCrawler($client, new NullLogger());



        //@todo zendify this shit!
        /*
        $logger = new \Monolog\Logger('StandardOutput');
        $handler = new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Logger::DEBUG);
        $handler->setFormatter(new \Monolog\Formatter\LineFormatter("%message%\n"));
        $logger->pushHandler($handler);
      */
        $client = new \GuzzleHttp\Client();
        $amazonCrawler = new AmazonCrawler($client, new NullLogger());

        $apaiIoWrapper = new ApaiIOWrapper($container->get('config')['amazon-apai']);



        return new SynchronizeDbAgainstAmazon(
            $amazonCrawler,
            $wishlistService,
            $apaiIoWrapper,
            $productService


        );

    }

    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, SynchronizeDbAgainstAmazon::class);
    }
}