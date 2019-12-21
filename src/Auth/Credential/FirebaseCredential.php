<?php


namespace Firebase\Auth\Credential;


use Firebase\Auth\Credential;

interface FirebaseCredential extends Credential {
    public function getCertificate(): ?Certificate;
}
