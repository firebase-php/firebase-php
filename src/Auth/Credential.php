<?php

namespace Firebase\Auth;

interface Credential {
    public function getAccessToken(): GoogleOAuthAccessToken;
}
