<?php


namespace Firebase\Auth\Internal;


interface CryptoSigner
{
    /**
     * @param array $payload
     * @return mixed
     */
    public function sign(array $payload);

    /**
     * @return string
     */
    public function getAccountId(): string;
}
