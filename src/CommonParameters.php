<?php

namespace Ampeco\OmnipayRedsys;

trait CommonParameters
{
    public function setMerchantEmv3ds($value): void
    {
        $this->setParameter('merchantEmv3ds', (array) $value);
    }

    public function getMerchantEmv3ds()
    {
        return $this->getParameter('merchantEmv3ds');
    }

    public function setMerchantCode($value): void
    {
        $this->setParameter('merchantCode', (string) $value);
    }

    public function getMerchantCode(): string
    {
        return $this->getParameter('merchantCode');
    }

    public function setMerchantTerminal($value): void
    {
        $this->setParameter('merchantTerminal', (string) $value);
    }

    public function getMerchantTerminal(): string
    {
        return $this->getParameter('merchantTerminal');
    }
    public function setApiToken($value): void
    {
        $this->setParameter('apiToken', (string) $value);
    }

    public function getApiToken(): string
    {
        return $this->getParameter('apiToken');
    }

    public function setMerchantDirectPayment($value): void
    {
        $this->setParameter('merchantDirectPayment', (string) $value);
    }

    public function getMerchantDirectPayment(): string
    {
        return $this->getParameter('merchantDirectPayment');
    }
    public function setMerchantExceptSca($value): void
    {
        $this->setParameter('merchantExceptSca', (string) $value);
    }

    public function getMerchantExceptSca(): string
    {
        return $this->getParameter('merchantExceptSca');
    }
}
