<?php


namespace Firebase\Auth\TokenVerifier;


use Firebase\FirebaseApp;
use Firebase\Util\Error\AuthClientErrorCode;
use Firebase\Util\Error\ErrorInfo;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class TokenVerifierHelpers
{
    const SESSION_COOKIE_CERT_URL = 'https://www.googleapis.com/identitytoolkit/v3/relyingparty/publicKeys';

    const CLIENT_CERT_URL = 'https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com';

    const FIREBASE_AUDIENCE = 'https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit';

    public static function createSessionCookieVerifier(FirebaseApp $app): FirebaseTokenVerifier {
        $tokenInfo = (new FirebaseTokenInfo())
            ->setUrl('https://firebase.google.com/docs/auth/admin/manage-cookies')
            ->setVerifyApiName('verifySessionCookie()')
            ->setJwtName('Firebase session cookie')
            ->setShortName('session cookie')
            ->setExpiredErrorCode(new ErrorInfo(AuthClientErrorCode::SESSION_COOKIE_EXPIRED['code']));
        return new FirebaseTokenVerifier(
            self::SESSION_COOKIE_CERT_URL,
            (new Sha256())->getAlgorithmId(),
            'https://session.firebase.google.com/',
            $tokenInfo,
            $app
        );
    }

    public static function createIdTokenVerifier(FirebaseApp $app): FirebaseTokenVerifier {
        $tokenInfo = (new FirebaseTokenInfo())
            ->setUrl('https://firebase.google.com/docs/auth/admin/verify-id-tokens')
            ->setVerifyApiName('verifyIdToken()')
            ->setJwtName('Firebase ID token')
            ->setShortName('ID cookie')
            ->setExpiredErrorCode(new ErrorInfo(AuthClientErrorCode::ID_TOKEN_EXPIRED['code']));
        return new FirebaseTokenVerifier(
            self::CLIENT_CERT_URL,
            (new Sha256())->getAlgorithmId(),
            'https://session.firebase.google.com/',
            $tokenInfo,
            $app
        );
    }
}