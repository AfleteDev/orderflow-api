<?php

namespace SixBySix\RealtimeDespatch\Gateway\Guzzle;

use Buzz\Browser as HttpClient;


/**
 * Default Gateway.
 */
class DefaultGateway
{
    const API_ENDPOINT_INVENTORY_RETRIEVAL = 'remotewarehouse/inventory.xml';
    const API_ENDPOINT_PRODUCT_IMPORT = 'remotewarehouse/imports/importitems.xml';
    const API_ENDPOINT_PRODUCT_UPDATE = 'remotewarehouse/product/update.xml';
    const API_ENDPOINT_ORDER_IMPORT = 'remoteorder/imports/importitems.xml';
    const API_ENDPOINT_ORDER_CANCEL = 'remoteorder/order/cancel.xml';
    const API_ENDPOINT_ORDER_DETAIL = 'remoteorder/order/detail.xml';
    const API_ENDPOINT_ORDER_UPDATE = 'remoteorder/order/update.xml';
    const API_ENDPOINT_RETURN_IMPORT = 'remotewarehouse/imports/importitems.xml';

    /**
     * Api Client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $_client;

    /**
     * Base URL.
     *
     * @var string
     */
    protected $_baseUrl;

    /**
     * Default Options.
     *
     * @var array
     */
    protected $_options;

    /**
     * Request Body.
     *
     * @var \DOMDocument
     */
    protected $_lastRequest;

    /**
     * Response Body.
     *
     * @var \DOMDocument
     */
    protected $_lastResponse;

    /**
     * Constructor
     *
     * @param \GuzzleHttp\Client $client
     */
    public function __construct( HttpClient $client, $options = array() )
    {
        $this->_client = $client;
        $this->_options = $options;
    }

    public function _setResponse( $response )
    {
        $this->_lastResponse = new \SimpleXMLElement( $response->getBody() );
    }

    /**
     * {@inheritdoc}
     */
    //public function preSend(RequestInterface $request)
    //{
    //    $this->_lastRequest = null;
//
    //    try
    //    {
    //        $this->_lastRequest = new \SimpleXMLElement($request->getContent());
    //    }
    //    catch (\Exception $ex)
    //    {
    //        $this->_lastRequest = $request;
    //        return;
    //    }
    //}

    /**
     * {@inheritdoc}
     */
    //public function postSend(RequestInterface $request, MessageInterface $response)
    //{
    //    $this->_lastResponse = new \SimpleXMLElement($response->getContent());
    //}

    /**
     * Returns the last request.
     *
     * @return \SimpleXMLElement
     */
    //public function getLastRequest()
    //{
    //    return $this->_lastRequest;
    //}

    /**
     * Returns the lastresponse.
     *
     * @return \SimpleXMLElement
     */
    public function getLastResponse()
    {
        return $this->_lastResponse;
    }

    /**
     * Retrieve Inventory.
     *
     * @return \SimpleXMLElement
     */
    public function retrieveInventory()
    {
        $response = $this->_client->get(
            $this->_createUrl(self::API_ENDPOINT_INVENTORY_RETRIEVAL)
        );

        $this->_setResponse( $response );

        return $this->getLastResponse();
    }

    /**
     * Import Products.
     *
     * @param string $body
     *
     * @return \SimpleXMLElement
     */
    public function importProducts( $body )
    {
        $response = $this->_client->post(
            $this->_createUrl(self::API_ENDPOINT_PRODUCT_IMPORT),
            [
                'headers' => [
                    'Content-Type' => 'application/xml',
                ],
                'body' => $body,
            ]
        );

        $this->_setResponse( $response );

        return $this->getLastResponse();
    }

    /**
     * Order Notification.
     *
     * @param string $incrementId Order Increment ID
     * @param string $type Notification Type
     *
     * @return \SimpleXMLElement
     */
    public function orderNotification( $incrementId, $type )
    {
        $response = $this->_client->post(
            $this->_createUrl(
                self::API_ENDPOINT_ORDER_UPDATE,
                [
                    'query' => [
                        'thirdPartyReference' => $incrementId,
                        'event' => 'order.' . $type,
                        'action' => $type,
                    ],
                ]
            )
        );

        $this->_setResponse( $response );

        return $this->getLastResponse();
    }

    /**
     * Product Notification.
     *
     * @param string $sku Product SKU
     * @param string $type Notification Type
     *
     * @return \SimpleXMLElement
     */
    public function productNotification( $sku, $type )
    {
        $response = $this->_client->post(
            $this->_createUrl(
                self::API_ENDPOINT_PRODUCT_UPDATE,
                [
                    'query' => [
                        'thirdPartyReference' => $sku,
                        'event' => 'product.' . $type,
                        'action' => $type,
                    ],
                ]
            )
        );

        $this->_setResponse( $response );

        return $this->getLastResponse();
    }

    /**
     * Cancel Order.
     *
     * @param string $externalReference
     *
     * @return \SimpleXMLElement
     */
    public function cancelOrder( $externalReference )
    {
        $response = $this->_client->post(
            $this->_createUrl(
                self::API_ENDPOINT_ORDER_CANCEL,
                [ 'query' => [ 'externalReference' => $externalReference ] ]
            )
        );

        $this->_setResponse( $response );

        return $this->getLastResponse();
    }

    /**
     * Retrieve Order Details.
     *
     * @return \SimpleXMLElement
     */
    public function retrieveOrderDetails( $externalReference )
    {
        $response = $this->_client->post(
            $this->_createUrl(
                self::API_ENDPOINT_ORDER_DETAIL,
                [ 'query' => [ 'externalReference' => $externalReference ] ]
            )
        );

        $this->_setResponse( $response );

        return $this->getLastResponse();
    }

    /**
     * Import Orders.
     *
     * @param string $body
     *
     * @return \SimpleXMLElement
     */
    public function importOrders( $body )
    {
        $response = $this->_client->post(
            $this->_createUrl( self::API_ENDPOINT_ORDER_IMPORT ),
            [ 'Content-Type' => 'application/xml' ],
            $body
        );

        $this->_setResponse( $response );

        return $this->getLastResponse();
    }

    /**
     * Import Returns.
     *
     * @param string $body
     *
     * @return \SimpleXMLElement
     */
    public function importReturns( $body )
    {
        $response = $this->_client->post(
            $this->_createUrl( self::API_ENDPOINT_RETURN_IMPORT ),
            [ 'Content-Type' => 'application/xml' ],
            $body
        );

        $this->_setResponse( $response );

        return $this->getLastResponse();
    }

    /**
     * Creates a new endpoint url.
     *
     * @param string $resource
     * @param array $options
     *
     * @return string
     */
    protected function _createUrl( $resource, $options = [] )
    {
        $options = array_merge_recursive( $this->_options, $options );

        return $resource . '?' . http_build_query( $options[ 'query' ] );
    }
}