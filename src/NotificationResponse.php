<?php

namespace Ampeco\OmnipayRedsys;

use Ampeco\OmnipayRedsys\Message\Response;
use Omnipay\Common\Message\ResponseInterface;

class NotificationResponse implements ResponseInterface
{
    private array $responseData;
    public function __construct(string $response)
    {
        $this->responseData = json_decode(urldecode(base64_decode($response)), true);
    }

    public function getData(): array
    {
        return $this->responseData;
    }

    public function getRequest()
    {
        return null;
    }

    public function isSuccessful()
    {
        return ((int) $this->responseData['Ds_Response'] >= Response::TRANSACTION_AUTHORIZED_MIN && (int) $this->responseData['Ds_Response'] <= Response::TRANSACTION_AUTHORIZED_MAX);
    }

    public function isRedirect()
    {
        return $this->responseData['Ds_Response'] === Response::OPERATION_REDIRECTED_TO_ISSUER;
    }

    public function isCancelled()
    {
        return $this->responseData['Ds_Response'] === Response::TRANSACTION_AUTHORIZED_CANCELLATION;
    }

    public function getMessage()
    {
        return Response::RESPONSE_CODE_MAPPING[$this->responseData['Ds_Response']] ?? null;
    }

    public function getCode()
    {
        return $this->responseData['Ds_Response'];
    }

    public function getTransactionReference()
    {
        return $this->responseData['Ds_AuthorisationCode'] ?? null;
    }

    public function getCardReference()
    {
        return $this->responseData['Ds_Merchant_Identifier'] ?? null;
    }

    public function getPaymentMethod()
    {
        $cardType = match ($this->responseData['Ds_Card_Brand']) {
            '1' => 'visa',
            '2' => 'mastercard',
            '6' => 'diners',
            '7' => 'private',
            '8' => 'amex',
            '9' => 'jcb',
            '22' => 'upi',
            default => 'unknown',
        };
        $expireMonth = substr($this->responseData['Ds_ExpiryDate'], 2);
        $expireYear = '20' . substr($this->responseData['Ds_ExpiryDate'], 0, 2);
        $paymentMethod = new \stdClass();
        $paymentMethod->imageUrl = '';
        $paymentMethod->cardType = $cardType;
        $paymentMethod->expirationMonth = (int) $expireMonth;
        $paymentMethod->expirationYear = (int) $expireYear;
        $paymentMethod->last4 = substr($this->responseData['Ds_Card_Number'], -4);

        return $paymentMethod;
    }
}
