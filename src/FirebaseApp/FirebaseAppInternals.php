<?php

namespace Firebase\FirebaseApp;

use Firebase\Auth\Credential;

class FirebaseAppInternals {
    private bool $idDeleted = false;

    private FirebaseAccessToken $cacheToken;

    public function __construct(Credential $credential)
    {

    }

    public function getToken(bool $forceRefresh): FirebaseAccessToken {

    }
}
