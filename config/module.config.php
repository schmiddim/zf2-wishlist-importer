<?php
use Schmiddim\WishlistImporter\Controller\Factories\CliControllerFactory;
use Schmiddim\WishlistImporter\Controller\CliController;

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
                        'route' => 'importWishlist [-v] <id>',
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