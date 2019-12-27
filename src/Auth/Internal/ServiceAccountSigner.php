<?php


namespace Firebase\Auth\Internal;

use Firebase\Auth\GoogleAuthLibrary\Credentials\ServiceAccountCredentials;

class ServiceAccountSigner implements CryptoSigner
{
    private $signer;

    public function __construct(ServiceAccountCredentials $signer)
    {
        $this->signer = $signer;
    }

    public function sign(string $payload)
    {
        return $this->signer->signBlob($payload);
    }

    public function getAccount(): string
    {
        return $this->signer->getClientName();
    }
}
