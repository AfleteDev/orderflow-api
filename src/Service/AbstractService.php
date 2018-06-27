<?php

namespace SixBySix\RealtimeDespatch\Service;

use \SixBySix\RealtimeDespatch\Gateway\Guzzle\DefaultGateway;

use \SixBySix\RealtimeDespatch\Document\DocFactory;
use \SixBySix\RealtimeDespatch\Report\ImportReportFactory;

/**
 * Abstract Service.
 */
abstract class AbstractService
{
    /**
     * Gateway.
     *
     * @param SixBySix\RealtimeDespatch\Gateway\Gateway
     */
     protected $_gateway;

     /**
      * Constructor.
      *
      * @param \SixBySix\RealtimeDespatch\Gateway\DefaultGateway $gateway
      */
     public function __construct(DefaultGateway $gateway)
     {
         $this->_gateway = $gateway;
     }

     /**
      * Returns the current import report builder.
      *
      * @param \SimpleXMLElement $response
      *
      * @return \SixBySix\RealtimeDespatch\Report\ImportReportFactor
      */
     protected function _createImportReport(\SimpleXMLElement $response)
     {
        $importReportFactory = new ImportReportFactory;

        return $importReportFactory->createFromResponse($response);
     }

     /**
      * Returns the current document builder.
      *
      * @todo dependency inject
      *
      * @return \SixBySix\RealtimeDespatch\Service\DocFactory
      */
     protected function _getDocumentBuilder()
     {
        return new DocFactory;
     }

     /**
      * Returns the latest request body.
      *
      * @return string
      */
     public function getLastRequestBody()
     {
         try {
             return $this->_gateway->getLastRequest()->saveXML();
         } catch (\Exception $ex) {
             return $this->_gateway->getLastRequest()->getUrl();
         }
     }

     /**
      * Returns the latest response body.
      *
      * @return string
      */
     public function getLastResponseBody()
     {
         return $this->_gateway->getLastResponse() ? $this->_gateway->getLastResponse()->saveXML() : '';
     }
}