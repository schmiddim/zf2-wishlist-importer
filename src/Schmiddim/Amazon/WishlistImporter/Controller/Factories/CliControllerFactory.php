<?php


namespace Schmiddim\Amazon\WishlistImporter\Controller\Factories;

use AmazonWishlistExporter\Crawler\AmazonCrawler;
use Psr\Log\NullLogger;
use Schmiddim\Amazon\ProductApi\ApaiIOWrapper;
use Schmiddim\Amazon\WishlistImporter\Controller\CliController;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Schmiddim\Amazon\Doctrine\Services\Wishlist\WishlistServiceInterface;
use Schmiddim\Amazon\Doctrine\Services\Product\ProductServiceInterface;
class CliControllerFactory implements FactoryInterface
{
    public function __invoke(ControllerManager $container, $name, array
    $options = null)
    {
        /**
         * @var $productService ProductServiceInterface
         */
       $productService = $container->getServiceLocator()->get(ProductServiceInterface::class);

        /**
         * @var $wishlistService WishlistServiceInterface
         */
        $wishlistService = $container->getServiceLocator()->get(WishlistServiceInterface::class);

        //@todo zendify this shit!
        /*
        $logger = new \Monolog\Logger('StandardOutput');
        $handler = new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Logger::DEBUG);
        $handler->setFormatter(new \Monolog\Formatter\LineFormatter("%message%\n"));
        $logger->pushHandler($handler);
      */
        $client = new \GuzzleHttp\Client();
        $amazonCrawler = new AmazonCrawler($client, new NullLogger());



        $apaiIoWrapper = new ApaiIOWrapper($container->getServiceLocator());
        return new  CliController($amazonCrawler, $wishlistService, $apaiIoWrapper, $productService);
    }

    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, CliController::class);
    }
}