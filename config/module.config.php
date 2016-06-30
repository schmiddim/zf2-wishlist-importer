<?php
use Schmiddim\Amazon\WishlistImporter\Controller\Factories\CliControllerFactory;
use Schmiddim\Amazon\WishlistImporter\Controller\CliController;
use Schmiddim\Amazon\WishlistImporter\Services\SynchronizeDbAgainstAmazonInterface;
use Schmiddim\Amazon\WishlistImporter\Factories\SynchronizeDbAgainstAmazonFactory;

return array(


    'controllers' => array(
        'factories' => array(
            CliController::class =>
                CliControllerFactory::class
        )
    ),
    'service_manager' => array(
        'factories' => array(
            SynchronizeDbAgainstAmazonInterface::class => SynchronizeDbAgainstAmazonFactory::class
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'import-wishlist' => array(
                    'options' => array(
                        'route' => 'importWishlist  <id> <tld>',
                        'defaults' => array(
                            'controller' => CliController::class,
                            'action' => 'import'
                        ),
                    ),
                ),

                'ask-api' => array(
                    'options' => array(
                        'route' => 'ask-api  <id> <tld>',
                        'defaults' => array(
                            'controller' => CliController::class,
                            'action' => 'askApi'
                        ),
                    ),
                ),
                'ask-api-isbn' => array(
                    'options' => array(
                        'route' => 'ask-api-isbn  <id> <tld>',
                        'defaults' => array(
                            'controller' => CliController::class,
                            'action' => 'askApiByISBN'
                        ),
                    ),
                ),
            )
        )
    )
);