<?php


namespace Firebase\Auth;


use Firebase\Auth\Internal\CryptoSigners;
use Firebase\Auth\Internal\FirebaseTokenFactory;
use Firebase\FirebaseApp;

final class FirebaseTokenUtils
{
    private const ID_TOKEN_CERT_URL = 'https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com';

    private const ID_TOKEN_ISSUER_PREFIX = 'https://securetoken.google.com/';

    private const SESSION_COOKIE_CERT_URL = 'https://www.googleapis.com/identitytoolkit/v3/relyingparty/publicKeys';

    private const SESSION_COOKIE_ISSUER_PREFIX = 'https://session.firebase.google.com/';

    public static function createTokenFactory(FirebaseApp $firebaseApp) {
        return new FirebaseTokenFactory(CryptoSigners::getCryptoSigner($firebaseApp));
    }

    public static function createIdTokenVerifier(FirebaseApp $firebaseApp) {

    }
}