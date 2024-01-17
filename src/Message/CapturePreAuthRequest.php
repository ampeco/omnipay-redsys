<?php

namespace Ampeco\OmnipayRedsys\Message;

class CapturePreAuthRequest extends AbstractRequest
{
    public function getEndpoint(): string
    {
        return 'rest/trataPeticionREST';
    }

    public function getData()
    {
        $this->validate('transactionId', 'amount', 'token');

        return $this->generateEncodedRequestAttributes([
            'DS_MERCHANT_IDENTIFIER' => $this->getToken(),
            'DS_MERCHANT_AMOUNT' => $this->getAmountInteger(),
            'DS_MERCHANT_CURRENCY' => $this->getCurrencyNumeric(),
            'DS_MERCHANT_MERCHANTCODE' => $this->getMerchantCode(),
            'DS_MERCHANT_ORDER' => $this->getTransactionId(),
            'DS_MERCHANT_TERMINAL' => $this->getMerchantTerminal(),
            'DS_MERCHANT_TRANSACTIONTYPE' => '2',
        ]);
    }
}
