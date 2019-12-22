<?php


namespace Firebase\Auth\TokenGenerator;


interface CryptoSigner
{
    /**
     * @param string $payload
     * @return mixed
     */
    public function sign(string $payload);

    /**
     * @return string
     */
    public function getAccountId(): string;
}
