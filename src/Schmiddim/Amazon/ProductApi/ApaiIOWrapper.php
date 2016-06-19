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

    public function __construct(ServiceManager $sl)
    {

        $apiConfig = $sl->get('config')['amazon-apai'];
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
        $this->getConfiguration()->setCountry($country);

        $apaiIO = new ApaiIO($this->getConfiguration());
        $lookup = new Lookup();
        $lookup->setItemId($asin);
        $lookup->setResponseGroup(array('Large')); // More detailed information
        $response = $apaiIO->runOperation($lookup);

        return simplexml_load_string($response);
    }

    /**
     * @param array $asins
     * @param $countryCode
     * @return array
     */
    public function getByASINS($asins = array(), $countryCode)
    {
        //We can request only 10 items at once:(
        $parts = array_chunk($asins, 10);
        $resultSets = array();

        //do the api requests
        foreach ($parts as $part) {
            $resultSets[] = $this->fetchByASINs($part, $countryCode);

        }
        //merge the results
        $responses = array();
        foreach ($resultSets as $result) {
            foreach ($result->Items->Item as $item) {
                $responses[] = $item;
            }
        }
        return $responses;
    }

    /**
     * Make the api call
     * @param array $asins
     * @param $countryCode
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    protected function fetchByASINs($asins = array(), $countryCode)
    {
        $this->getConfiguration()->setCountry($countryCode);

        $apaiIO = new ApaiIO($this->getConfiguration());
        $lookup = new Lookup();
        $lookup->setItemIds($asins);
        $lookup->setResponseGroup(array('Large')); // More detailed information
        $response = $apaiIO->runOperation($lookup);

        return simplexml_load_string($response);
    }

    /**
     * @return GenericConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

}