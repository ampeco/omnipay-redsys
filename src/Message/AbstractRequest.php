<?php

namespace Ampeco\OmnipayRedsys\Message;

use Ampeco\OmnipayRedsys\CommonParameters;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Omnipay\Common\Message\AbstractRequest as OmnipayAbstractRequest;

abstract class AbstractRequest extends OmnipayAbstractRequest
{
    use CommonParameters;

    public const API_URL_PROD = 'https://sis-t.redsys.es/sis/';
    public const API_URL_TEST = 'https://sis-t.redsys.es:25443/sis/';

    public const TIMEOUT_IN_SECONDS = 30;

    abstract public function getEndpoint(): string;

    protected function getClient(): PendingRequest
    {
        $base = self::API_URL_PROD;

        if ($this->getTestMode()) {
            $base = self::API_URL_TEST;
        }

        return Http::baseUrl($base)
            ->timeout(self::TIMEOUT_IN_SECONDS)
            ->asJson()
            ->acceptJson();
    }

    protected function getResponseClass(): string
    {
        return Response::class;
    }

    protected function createResponse(int $statusCode, ?array $data = null)
    {
        $responseClass = $this->getResponseClass();

        return $this->response = new $responseClass($this, $statusCode, $data);
    }

    public function sendData($data)
    {
        $client = $this->getClient();
        $response = $client->post($this->getEndpoint(), $data);

        return $this->createResponse($response->status(), $response->json());
    }

    protected function generateEncodedRequestAttributes(array $requestData): array
    {
        $requestAttributes['Ds_SignatureVersion'] = 'HMAC_SHA256_V1';
        $requestAttributes['Ds_MerchantParameters'] = base64_encode(json_encode($requestData));
        $requestAttributes['Ds_Signature'] = $this->generateSignatureForTransaction($requestAttributes['Ds_MerchantParameters']);

        return $requestAttributes;
    }

    private function generateSignatureForTransaction(string $encodedParameters): string
    {
        $transactionId = $this->getTransactionId();
        $apiToken = $this->getApiToken();
        $l = (int) ceil(strlen($transactionId) / 8) * 8;
        $key = substr(
            openssl_encrypt(
                $transactionId . str_repeat("\0", $l - strlen($transactionId)),
                'des-ede3-cbc',
                base64_decode($apiToken),
                OPENSSL_RAW_DATA,
                "\0\0\0\0\0\0\0\0",
            ),
            0,
            $l,
        );

        return base64_encode(hash_hmac('sha256', $encodedParameters, $key, true));
    }
}
