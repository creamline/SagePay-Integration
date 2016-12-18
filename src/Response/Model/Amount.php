<?php

namespace Academe\SagePay\Psr7\Response\Model;

/**
 * Amount in a transaction response.
 * This is split into multiple elements: totalAmount, saleAmount and surchargeAmount.
 */

use Academe\SagePay\Psr7\Helper;
use JsonSerializable;

class Amount implements JsonSerializable
{
}