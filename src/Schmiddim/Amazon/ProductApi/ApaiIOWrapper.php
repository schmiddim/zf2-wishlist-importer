<?php
namespace Schmiddim\Amazon\ProductApi;

use ApaiIO\ApaiIO;

use ApaiIO\Configuration\GenericConfiguration;
use GuzzleHttp\Client;
use Zend\ServiceManager\ServiceManager;
use ApaiIO\Operations\Lookup;

class ApaiIOWrapper
{
    /**
     * @var GenericConfiguration
     */
    private $configuration = null;


    protected $itemsNotFound = array();

    /**
     * ApaiIOWrapper constructor.
     * @param array $apiConfig
     */
    public function __construct($apiConfig = array())
    {
        $client = new Client();
        $this->configuration = new GenericConfiguration();
        $this->configuration
            ->setAccessKey($apiConfig['AWS_API_KEY'])
            ->setSecretKey($apiConfig['AWS_API_SECRET_KEY'])
            ->setAssociateTag($apiConfig['AWS_ASSOCIATE_TAG'])
            ->setRequest(new \ApaiIO\Request\GuzzleRequest($client));

    }

    public function getByASIN($asin, $country)
    {
        return $this->getObjectBy($asin, $country);
    }
    public function getByISBN($isbn, $country)
    {
        return $this->getObjectBy($isbn, $country, Lookup::TYPE_ISBN);
    }
    protected function getObjectBy($identifier, $country, $idType = Lookup::TYPE_ASIN)
    {
        $this->getConfiguration()->setCountry($country);

        $apaiIO = new ApaiIO($this->getConfiguration());
        $lookup = new Lookup();
        $lookup->setIdType($idType);
        $lookup->setItemId($identifier);
        $lookup->setResponseGroup(array('Large')); // More detailed information
        $response = $apaiIO->runOperation($lookup);
        $xmlResponse = simplexml_load_string($response);

        return $xmlResponse;
    }

    public function getByISBNS($isbns = array(), $countryCode)
    {
        return $this->getObjects($isbns, $countryCode, Lookup::TYPE_ISBN);

    }

    public function getByASINS($asins = array(), $countryCode)
    {
        return $this->getObjects($asins, $countryCode, Lookup::TYPE_ASIN);
    }

    /**
     * @param array $asins
     * @param $countryCode
     * @return array
     */
    public function getObjects($asins = array(), $countryCode, $idType)
    {
        //We can request only 10 items at once:(
        $parts = array_chunk($asins, 10);
        $resultSets = array();

        //do the api requests
        foreach ($parts as $part) {
            $resultSets[] = $this->fetchByASINs($part, $countryCode, $idType);

        }
        //merge the results
        $responses = array();
        foreach ($resultSets as $result) {
            foreach ($result->Items->Item as $item) {
                $responses[] = $item;
            }
        }

        //did we made faulty requests?
        if (count($asins) !== count($responses)) {
            $foundAsins = array();

            foreach ($responses as $response) {
                $asinFromResponse = strval($response->ASIN);
                $foundAsins [] = $asinFromResponse;
                $key = array_search($asinFromResponse, $asins);
                unset($asins[$key]);
            }
        }
//@todo this does not work well with isbns
        if (false == array_key_exists($countryCode, $this->itemsNotFound)) {
            $this->itemsNotFound[$countryCode] = array();
        }
        $this->itemsNotFound[$countryCode] = array_merge($this->itemsNotFound[$countryCode], $asins);

        return $responses;
    }

    /**
     * @param array $asins
     * @param $countryCode
     * @param string $idType
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    protected function fetchByASINs($asins = array(), $countryCode, $idType = Lookup::TYPE_ASIN)
    {
        $this->getConfiguration()->setCountry($countryCode);

        $apaiIO = new ApaiIO($this->getConfiguration());
        $lookup = new Lookup();
        $lookup->setIdType($idType);
        $lookup->setItemIds($asins);
        $lookup->setResponseGroup(array('Large')); // More detailed information
        $response = $apaiIO->runOperation($lookup);

        $xmlResponse = simplexml_load_string($response);
        return $xmlResponse;

    }

    /**
     * @return GenericConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return array
     */
    public function getItemsNotFound()
    {
        return $this->itemsNotFound;
    }

}