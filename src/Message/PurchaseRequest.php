<?php

namespace Ampeco\OmnipayRedsys\Message;

class PurchaseRequest extends AbstractRequest
{
    public function getEndpoint(): string
    {
        return 'rest/trataPeticionREST';
    }

    public function getData()
    {
        $this->validate('transactionId', 'amount', 'token', 'currency', 'merchantTerminal', 'merchantCode');

        return $this->generateEncodedRequestAttributes([
            'DS_MERCHANT_IDENTIFIER' => $this->getToken(),
            'DS_MERCHANT_AMOUNT' => $this->getAmountInteger(),
            'DS_MERCHANT_CURRENCY' => $this->getCurrencyNumeric(),
            'DS_MERCHANT_MERCHANTCODE' => $this->getMerchantCode(),
            'DS_MERCHANT_ORDER' => $this->getTransactionId(),
            'DS_MERCHANT_TERMINAL' => $this->getMerchantTerminal(),
            'DS_MERCHANT_TRANSACTIONTYPE' => '0',
            'DS_MERCHANT_DIRECTPAYMENT' => 'true',
            'DS_MECHANT_COF_TXNID' => $this->getTransactionId(), // no idea why we need this. we could maybe remove it
            'DS_MERCHANT_EXCEP_SCA' => 'MIT',
        ]);
    }
}
