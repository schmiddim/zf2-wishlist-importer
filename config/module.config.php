<?php
use Schmiddim\Amazon\WishlistImporter\Controller\Factories\CliControllerFactory;
use Schmiddim\Amazon\WishlistImporter\Controller\CliController;

return array(


    'controllers' => array(
        'factories' => array(
            CliController::class =>
                CliControllerFactory::class
        )
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
            )
        )
    )
);