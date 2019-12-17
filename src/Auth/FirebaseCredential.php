<?php


namespace Firebase\Auth;


interface FirebaseCredential {
    public function getCertificate(): ?Certificate;
}
