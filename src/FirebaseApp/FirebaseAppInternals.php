<?php

namespace Firebase\FirebaseApp;

use Firebase\Auth\Credential;

class FirebaseAppInternals {
    /**
     * @var bool
     */
    private $idDeleted = false;

    /**
     * @var FirebaseAccessToken
     */
    private $cacheToken;

    public function __construct(Credential $credential)
    {

    }

    public function getToken(bool $forceRefresh): FirebaseAccessToken {

    }
}
