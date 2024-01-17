<?php

namespace Ampeco\OmnipayRedsys;

use Ampeco\OmnipayRedsys\Message\AbstractRequest;
use Ampeco\OmnipayRedsys\Message\AuthenticationConfirmationRequest;
use Ampeco\OmnipayRedsys\Message\CreateCardRequest;
use Ampeco\OmnipayRedsys\Message\PreparePurchaseRequest;
use Ampeco\OmnipayRedsys\Message\CreatePreAuthRequest;
use Ampeco\OmnipayRedsys\Message\PurchaseRequest;
use Ampeco\OmnipayRedsys\Message\CapturePreAuthRequest;
use Ampeco\OmnipayRedsys\Message\RedsysNotification;
use Ampeco\OmnipayRedsys\Message\VoidRequest;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\RequestInterface;

class Gateway extends AbstractGateway
{
    public function getName(): string
    {
        return 'Redsys';
    }

    public function getBaseUrl(): string
    {
        return $this->getTestMode() ? AbstractRequest::API_URL_TEST : AbstractRequest::API_URL_PROD;
    }

    public function acceptNotification(array $options = []): RedsysNotification
    {
        return new RedsysNotification($options);
    }

    public function authorize(array $options = []): RequestInterface
    {
        return $this->createRequest(CreatePreAuthRequest::class, $options);
    }

    public function capture(array $options = []): RequestInterface
    {
        return $this->createRequest(CapturePreAuthRequest::class, $options);
    }

    public function preparePurchase(array $options = []): RequestInterface
    {
        return $this->createRequest(PreparePurchaseRequest::class, $options);
    }

    public function purchase(array $options = []): RequestInterface
    {
        return $this->createRequest(PurchaseRequest::class, $options);
    }

    public function authenticationConfirmation(array $options = []): RequestInterface
    {
        return $this->createRequest(AuthenticationConfirmationRequest::class, $options);
    }

    public function supportsDeleteCard(): bool
    {
        return false;
    }

    public function supportsAcceptNotification(): bool
    {
        return true;
    }

    public function supportsAuthorize(): bool
    {
        return true;
    }
    public function deleteCard(): array
    {
        return [];
    }

    public function void(array $options = []): RequestInterface
    {
        return $this->createRequest(VoidRequest::class, $options);
    }

    public function createCard(array $options = array()): RequestInterface
    {
        return $this->createRequest(CreateCardRequest::class, $options);
    }
}
