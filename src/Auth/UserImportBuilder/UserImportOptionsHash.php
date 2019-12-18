<?php


namespace Firebase\Auth\UserImportBuilder;


class UserImportOptionsHash
{
    /**
     * @var HashAlgorithmType
     */
    private $algorithm;

    private $key;

    private $saltSeparator;

    private $rounds;

    private $memoryCost;

    private $parallelization;

    private $blockSize;

    private $derivedKeyLength;

    public function __construct(HashAlgorithmType $algorithm)
    {
        $this->algorithm = $algorithm;
    }

    public function toArray() {
        return [];
    }
}