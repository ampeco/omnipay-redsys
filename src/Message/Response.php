<?php

namespace Ampeco\OmnipayRedsys\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

class Response extends AbstractResponse
{
    public int $statusCode;
    public function __construct(RequestInterface $request, int $statusCode, ?array $data = null)
    {
        $this->request = $request;
        $this->statusCode = $statusCode;
        $this->data = isset($data['Ds_MerchantParameters']) ? json_decode(urldecode(base64_decode($data['Ds_MerchantParameters'])), true) : $data;
    }

    // Code for successful transactions
    const TRANSACTION_AUTHORIZED_MIN = 0;
    const TRANSACTION_AUTHORIZED_MAX = 99;
    const TRANSACTION_AUTHORIZED_RETURN = 900;
    const TRANSACTION_AUTHORIZED_CANCELLATION = '0400';

    // Specific response codes for failed transactions
    const EXPIRED_CARD = '0101';
    const CARD_TEMP_EXCEPTION_SUSPICION_FRAUD = '0102';
    const PIN_ATTEMPTS_EXCEEDED = '0106';
    const CARD_NOT_EFFECTIVE = '0125';
    const INCORRECT_SECURITY_CODE = '0129';
    const DENIED_DO_NOT_REPEAT = '0172';
    const DENIED_DO_NOT_REPEAT_UPDATE_CARD_DETAILS = '0173';
    const DENIED_DO_NOT_REPEAT_WITHIN_72_HOURS = '0174';
    const NON_SERVICE_CARD = '0180';
    const OWNER_AUTHENTICATION_ERROR = '0184';
    const DENIAL_ISSUER_NO_REASON = '0190';
    const WRONG_EXPIRATION_DATE = '0191';
    const REQUIRES_SCA_AUTHENTICATION = '0195';
    const CARD_TEMP_EXCEPTION_SUSPICION_FRAUD_WITHDRAWAL = '0202';
    const TRADE_NOT_REGISTERED_IN_FUC = '0904';
    const SYSTEM_ERROR = '0909';
    const REPEAT_ORDER = '0913';
    const BAD_SESSION = '0944';
    const RETURN_OPERATION_NOT_ALLOWED = '0950';
    const ISSUER_NOT_AVAILABLE = '9912';
    const INCORRECT_NUMBER_CARD_POSITIONS = '9064';
    const OPERATION_NOT_ALLOWED_FOR_CARD = '9078';
    const NON_EXISTENT_CARD = '9093';
    const REJECT_INTERNATIONAL_SERVERS = '9094';
    const TRADE_SECURE_HOLDER_WITHOUT_KEY = '9104';
    const TRADE_NOT_ALLOW_SECURE_ENTRY = '9218';
    const CARD_DOES_NOT_COMPLY_CHECK_DIGIT = '9253';
    const BUSINESS_CANNOT_PRE_AUTHORIZE = '9256';
    const CARD_DOES_NOT_ALLOW_PRE_AUTHORIZATION = '9257';
    const OPERATION_STOPPED_EXCEEDING_CONTROL = '9261';
    const USER_REQUEST_PAYMENT_CANCELED = '9915';
    const ANOTHER_TRANSACTION_IN_PROCESS = '9997';
    const OPERATION_REQUESTING_CARD_DATA = '9998';
    const OPERATION_REDIRECTED_TO_ISSUER = '9999';

    // Define a mapping of response codes to their meanings or actions
    const RESPONSE_CODE_MAPPING = [
        self::EXPIRED_CARD => 'Expired card',
        self::CARD_TEMP_EXCEPTION_SUSPICION_FRAUD => 'Temporary card exception or under suspicion of fraud',
        self::PIN_ATTEMPTS_EXCEEDED => 'PIN attempts exceeded',
        self::CARD_NOT_EFFECTIVE => 'Card not effective',
        self::INCORRECT_SECURITY_CODE => 'Incorrect security code (CVV2/CVC2)',
        self::DENIED_DO_NOT_REPEAT => 'Denied, do not repeat',
        self::DENIED_DO_NOT_REPEAT_UPDATE_CARD_DETAILS => 'Denied, do not repeat without updating card details',
        self::DENIED_DO_NOT_REPEAT_WITHIN_72_HOURS => 'Denied, do not repeat within 72 hours',
        self::NON_SERVICE_CARD => 'Non-service card',
        self::OWNER_AUTHENTICATION_ERROR => 'Owner authentication error',
        self::DENIAL_ISSUER_NO_REASON => 'Denied by the issuer without specifying reason',
        self::WRONG_EXPIRATION_DATE => 'Wrong expiration date',
        self::REQUIRES_SCA_AUTHENTICATION => 'Requires SCA authentication',
        self::CARD_TEMP_EXCEPTION_SUSPICION_FRAUD_WITHDRAWAL => 'Temporary card exception or under suspicion of fraud for withdrawal',
        self::TRADE_NOT_REGISTERED_IN_FUC => 'Trade not registered in FUC',
        self::SYSTEM_ERROR => 'System error',
        self::REPEAT_ORDER => 'Repeat order',
        self::BAD_SESSION => 'Bad Session',
        self::RETURN_OPERATION_NOT_ALLOWED => 'Return operation not allowed',
        self::ISSUER_NOT_AVAILABLE => 'Issuer not available',
        self::INCORRECT_NUMBER_CARD_POSITIONS => 'Incorrect number of card positions',
        self::OPERATION_NOT_ALLOWED_FOR_CARD => 'Type of operation not allowed for that card',
        self::NON_EXISTENT_CARD => 'Non-existent card',
        self::REJECT_INTERNATIONAL_SERVERS => 'Rejected by international servers',
        self::TRADE_SECURE_HOLDER_WITHOUT_KEY => 'Trade with "secure holder" and holder without secure purchase key',
        self::TRADE_NOT_ALLOW_SECURE_ENTRY => 'The trade does not allow op. secure entry/operations',
        self::CARD_DOES_NOT_COMPLY_CHECK_DIGIT => 'Card does not comply with check-digit',
        self::BUSINESS_CANNOT_PRE_AUTHORIZE => 'The business cannot carry out pre-authorizations',
        self::CARD_DOES_NOT_ALLOW_PRE_AUTHORIZATION => 'This card does not allow pre-authorization operations',
        self::OPERATION_STOPPED_EXCEEDING_CONTROL => 'Operation stopped for exceeding the control of restrictions at the entrance to the SIS',
        self::USER_REQUEST_PAYMENT_CANCELED => 'At the user\'s request the payment has been canceled',
        self::ANOTHER_TRANSACTION_IN_PROCESS => 'Another transaction is being processed in SIS with the same card',
        self::OPERATION_REQUESTING_CARD_DATA => 'Operation in the process of requesting card data',
        self::OPERATION_REDIRECTED_TO_ISSUER => 'Operation that has been redirected to the issuer to authenticate',
    ];

    protected function notErrorResponse(): bool
    {
        return !isset($this->data['errorCode']);
    }

    protected function hasResponseCode(): bool
    {
        return isset($this->data['Ds_Response']);
    }

    public function isSuccessful(): bool
    {
        $responseCode = $this->getResponseCodeAsInt();

        return ($this->statusCode === 200 && $this->notErrorResponse() && $this->hasResponseCode())
            && (
                ($responseCode >= self::TRANSACTION_AUTHORIZED_MIN && $responseCode <= self::TRANSACTION_AUTHORIZED_MAX)
                || ($responseCode === self::TRANSACTION_AUTHORIZED_RETURN)
            );
    }

    public function isRedirect()
    {
        return $this->notErrorResponse() && $this->hasResponseCode() && $this->data['Ds_Response'] === self::OPERATION_REDIRECTED_TO_ISSUER;
    }

    public function isCancelled()
    {
        return $this->notErrorResponse() && $this->hasResponseCode() && $this->data['Ds_Response'] === self::TRANSACTION_AUTHORIZED_CANCELLATION;
    }

    public function getMessage()
    {
        return $this->notErrorResponse() && $this->hasResponseCode() ? self::RESPONSE_CODE_MAPPING[$this->data['Ds_Response']] : $this->getError();
    }

    public function getCode()
    {
        return $this->hasResponseCode() ? $this->data['Ds_Response'] : $this->statusCode;
    }

    protected function getResponseCodeAsInt(): ?int
    {
        return $this->hasResponseCode() ? (int) $this->data['Ds_Response'] : null;
    }

    public function getError()
    {
        return $this->data['errorCode'] ?? null;
    }

    public function getTransactionReference()
    {
        return $this->data['Ds_AuthorisationCode'] ?? null;
    }

    public function isPending(): bool
    {
        return $this->statusCode === 200  && isset($this->data['Ds_EMV3DS']['threeDSInfo']) && $this->data['Ds_EMV3DS']['threeDSInfo'] === 'ChallengeRequest';
    }
}
