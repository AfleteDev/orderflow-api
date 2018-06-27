<?php

namespace SixBySix\RealtimeDespatch\Gateway\Guzzle\Factory;

use SixBySix\RealtimeDespatch\Api\Credentials;
use SixBySix\RealtimeDespatch\Gateway\Guzzle\DefaultGateway;

use GuzzleHttp\Client as HttpClient;


/**
 * Default Gateway Factory.
 */
class DefaultGatewayFactory
{
    /**
     * Creates a new default gateway instance
     *
     * @param \SixBySix\RealtimeDespatch\Api\Credentials $credentials
     *
     * @return \SixBySix\RealtimeDespatch\Gateway\Guzzle\DefaultGateway
     */
    public function create( Credentials $credentials )
    {
        $params = [
            'auth' => [ $credentials->getUsername(), $credentials->getPassword() ],
            'base_uri' => $credentials->getEndpoint(),
        ];
        //$adapter->setOption(CURLOPT_SSL_VERIFYPEER, false);
        //$adapter->setOption(CURLOPT_TIMEOUT, 300);

        $client = new HttpClient( $params );

        $options = array(
            'query' => array(
                'channel'      => $credentials->getChannel(),
                'organisation' => $credentials->getOrganisation()
            )
        );

        return new DefaultGateway( $client, $options );
    }
}