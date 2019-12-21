<?php

namespace Firebase\Auth;

use Firebase\Auth\Credential\GoogleOAuthAccessToken;

interface Credential {
    public function getAccessToken(): GoogleOAuthAccessToken;
}
