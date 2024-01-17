<?php

namespace Ampeco\OmnipayRedsys\Message;

use Omnipay\Common\Message\NotificationInterface;

class RedsysNotification implements NotificationInterface
{
    private array $merchantParams;

    public function __construct(protected array $data)
    {
        $this->merchantParams = json_decode(urldecode(base64_decode($this->data['Ds_MerchantParameters'])), true);
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        return $this->data;
    }

    public function getMerchantParameters(): array
    {
        return $this->merchantParams;
    }

    public function isScaNotification()
    {
        return isset($this->data['cres']) && isset($this->data['option']);
    }

    /**
     * @inheritDoc
     */
    public function getTransactionReference()
    {
        // TODO: Implement getTransactionReference() method.
    }

    /**
     * @inheritDoc
     */
    public function getTransactionStatus()
    {
        // TODO: Implement getTransactionStatus() method.
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        // TODO: Implement getMessage() method.
    }
}
