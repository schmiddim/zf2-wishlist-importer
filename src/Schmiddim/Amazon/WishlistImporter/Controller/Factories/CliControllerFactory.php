<?php


namespace Schmiddim\Amazon\WishlistImporter\Controller\Factories;

use Schmiddim\Amazon\WishlistImporter\Controller\CliController;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Schmiddim\Amazon\WishlistImporter\Services\SynchronizeDbAgainstAmazonInterface;

class CliControllerFactory implements FactoryInterface
{
    public function __invoke(ControllerManager $container, $name, array
    $options = null)
    {

        /**
         * @var $synchronizeDbAgainstAmazon SynchronizeDbAgainstAmazonInterface
         */
        $synchronizeDbAgainstAmazon = $container->getServiceLocator()->get(SynchronizeDbAgainstAmazonInterface::class);
        return new  CliController($synchronizeDbAgainstAmazon);
    }

    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, CliController::class);
    }
}