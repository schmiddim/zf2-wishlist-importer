<?php
namespace Schmiddim\Amazon\WishlistImporter\Services;


interface SynchronizeDbAgainstAmazonInterface
{
    public function synchronize($wishlistId, $countryCode);
}