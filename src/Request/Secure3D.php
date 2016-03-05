<?php

namespace Academe\SagePay\Psr7\Request;

/**
 * The 3DSecure request sent to Sage Pay, after the user is returned
 * from entering their 3D Secure authentication details.
 * See https://test.sagepay.com/documentation/#3-d-secure
 */

use Exception;
use UnexpectedValueException;

use Academe\SagePay\Psr7\Helper;
use Academe\SagePay\Psr7\Model\Auth;
use Academe\SagePay\Psr7\Model\Endpoint;

class Secure3D extends AbstractRequest
{
    protected $paRes;
    protected $transactionId;

    protected $resource_path = ['transactions', '{transactionId}', '3d-secure'];

    /**
     * @param string|Secure3DAcsResponse $paRes The PA Result returned by the user's bank (or their agent)
     * @param string $transactionId The ID that Sage Pay gave to the transaction in its intial reponse
     */
    public function __construct(Endpoint $endpoint, Auth $auth, $paRes, $transactionId)
    {
        $this->endpoint = $endpoint;
        $this->auth = $auth;

        if ($paRes instanceof Secure3DAcsResponse) {
            $this->paRes = $paRes->getPaRes();
        } else {
            $this->paRes = $paRes;
        }

        $this->transactionId = $transactionId;
    }

    /**
     * Get the message body data for serializing.
     */
    public function jsonSerialize()
    {
        return [
            'paRes' => $this->getPaRes(),
        ];
    }

    /**
     * The HTTP Basic Auth header, as an array.
     * Use this if your transport tool does not do "Basic Auth" out of the box.
     */
    public function getHeaders()
    {
        return $this->getBasicAuthHeaders();
    }

    public function getPaRes()
    {
        return $this->paRes;
    }

    public function getTransactionId()
    {
        return $this->transactionId;
    }
}