<?php

namespace Ampeco\OmnipayRedsys\Message;

class CreateCardRequest extends AbstractRequest
{
    /** Defines the full path as we don't send a request here, we just need the url to pass it to the form.
     * @return string
     */
    public function getEndpoint(): string
    {
        $base = self::API_URL_PROD;
        if ($this->getTestMode()) {
            $base = self::API_URL_TEST;
        }

        return $base . 'realizarPago';
    }

    public function getData()
    {
        $this->validate('transactionId', 'amount', 'currency', 'merchantTerminal', 'merchantCode', 'notifyUrl', 'returnUrl');

        return $this->generateEncodedRequestAttributes([
            'DS_MERCHANT_AMOUNT' => $this->getAmountInteger(),
            'DS_MERCHANT_CURRENCY' => $this->getCurrencyNumeric(),
            'DS_MERCHANT_MERCHANTCODE' => $this->getMerchantCode(),
            'DS_MERCHANT_ORDER' => $this->getTransactionId(),
            'DS_MERCHANT_TERMINAL' => $this->getMerchantTerminal(),
            'DS_MERCHANT_TRANSACTIONTYPE' => '0',
            //'DS_MERCHANT_COF_TYPE' => 'C', - optional. maybe we shouldn't use it at all.
            'DS_MERCHANT_IDENTIFIER' => 'REQUIRED',
            'DS_MERCHANT_MERCHANTURL' => $this->getNotifyUrl(),
            'DS_MERCHANT_URLOK' => $this->getReturnUrl(),
            'DS_MERCHANT_URLKO' => $this->getReturnUrl(),
        ]);
    }
}
