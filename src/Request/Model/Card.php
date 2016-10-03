<?php

namespace Academe\SagePay\Psr7\Request\Model;

/**
 * Card object to be passed to SagePay for payment of a transaction.
 * Reasonable validation is done at creation.
 * A card in the copntext of this API is a temporary ID generated by SagePay
 * in response to card details being POSTed direct to the API.
 * The transaction does not take the full card details, as would be the case
 * for SagePay Direct.
 * The card identifier lasts for 400 seconds from creation.
 *
 * This value object is used to pass card details to a transaction request.
 */

use Academe\SagePay\Psr7\Response\CardIdentifier;
use Academe\SagePay\Psr7\Response\SessionKey;
use Academe\SagePay\Psr7\Helper;

class Card implements PaymentMethodInterface
{
    /**
     * @var Supplied when sending card identifier.
     */
    protected $sessionKey;

    /**
     * @var Tokenised card.
     */
    protected $cardIdentifier;

    /**
     * @var Flag indicates this is a reusable card identifier; it has been used before.
     */
    protected $reusable;

    /**
     * @var Flag indicates this card identifier must be saved on next use, so it can be used again.
     */
    protected $save;

    /**
     * @var Captured (safe) details for the card.
     * TODO: move these to response card class (this is requestcard class).
     */
    protected $cardType;
    protected $lastFourDigits;
    protected $expiryDate; // MMYY

    /**
     * Card constructor.
     * @param SessionKey $sessionKey
     * @param CardIdentifier $cardIdentifier
     */
    public function __construct(SessionKey $sessionKey, CardIdentifier $cardIdentifier, $reusable = null, $save = null)
    {
        if (isset($reusable)) {
            $this->reusable = (bool)$reusable;
        }

        if (isset($save)) {
            $this->save = (bool)$save;
        }

        $this->cardIdentifier = $cardIdentifier;
        $this->sessionKey = $sessionKey;
    }

    /**
     * Construct an instance from stored data (e.g. JSON serialised object).
     */
    public static function fromData($data)
    {
        // For convenience.
        if (is_string($data)) {
            $data = json_decode($data);
        }

        // The data will normally be in a "card" wrapper element.
        // Remove it to make processing easier.
        if ($card = Helper::dataGet($data, 'card')) {
            $data = $card;
        }

        return new static(
            SessionKey::fromData(['merchantSessionKey' => Helper::dataGet($data, 'merchantSessionKey')]),
            CardIdentifier::fromData(['cardIdentifier' => Helper::dataGet($data, 'cardIdentifier')])
        );
    }

    /**
     * Return the body partial for message construction.
     * @return array
     */
    public function jsonSerialize()
    {
        $message = [
            'card' => [
                'merchantSessionKey' => $this->sessionKey->getMerchantSessionKey(),
                'cardIdentifier' => $this->cardIdentifier->getCardIdentifier(),
            ],
        ];

        if ($this->reusable !== null) {
            $message['card']['reusable'] = $this->reusable;
        }

        if ($this->save !== null) {
            $message['card']['save'] = $this->save;
        }

        return $message;
    }
}