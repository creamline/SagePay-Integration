<?php namespace Academe\SagePay\Psr7\Request;

/**
 * Request the result of a transaction, stored on Sage Pay.
 * See "Retrieve and Transaction" https://test.sagepay.com/documentation/#transactions
 */

use Exception;
use UnexpectedValueException;
use Academe\SagePay\Psr7\Model\Auth;
use Academe\SagePay\Psr7\Model\Endpoint;

class TransactionResult extends AbstractRequest
{
    protected $resource_path = ['transactions', '{transactionId}'];
    protected $method = 'GET';

    /**
     * @param string $transactionId The ID that Sage Pay gave to the transaction
     */
    public function __construct(Endpoint $endpoint, Auth $auth, $transactionId)
    {
        $this->setEndpoint($endpoint);
        $this->setAuth($auth);
        $this->transactionId = $transactionId;
    }

    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * Get the message body data for serializing.
     * There is no body data for this message.
     */
    public function jsonSerialize()
    {
    }

    public function getHeaders()
    {
        return $this->getBasicAuthHeaders();
    } 
}