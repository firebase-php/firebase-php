<?php


namespace Firebase\Auth\Internal;


interface CryptoSigner
{
    /**
     * @param string $content
     * @return mixed
     */
    public function sign(string $content);

    /**
     * @return string
     */
    public function getAccount(): string;
}
