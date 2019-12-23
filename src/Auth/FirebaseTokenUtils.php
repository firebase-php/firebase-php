<?php


namespace Firebase\Auth;


use Firebase\Auth\Internal\CryptoSigners;
use Firebase\Auth\Internal\FirebaseTokenFactory;
use Firebase\Auth\Internal\GooglePublicKeysManagerBuilder;
use Firebase\Auth\Internal\IdTokenVerifier;
use Firebase\Auth\Internal\IdTokenVerifierBuilder;
use Firebase\FirebaseApp;
use Firebase\FirebaseOptions;
use Firebase\ImplFirebaseTrampolines;
use Firebase\Util\Validator\Validator;

final class FirebaseTokenUtils
{
    private const ID_TOKEN_CERT_URL = 'https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com';

    private const ID_TOKEN_ISSUER_PREFIX = 'https://securetoken.google.com/';

    private const SESSION_COOKIE_CERT_URL = 'https://www.googleapis.com/identitytoolkit/v3/relyingparty/publicKeys';

    private const SESSION_COOKIE_ISSUER_PREFIX = 'https://session.firebase.google.com/';

    public static function createTokenFactory(FirebaseApp $app) {
        return new FirebaseTokenFactory(CryptoSigners::getCryptoSigner($app));
    }

    public static function createIdTokenVerifier(FirebaseApp $app) {
        $projectId = ImplFirebaseTrampolines::getProjectId($app);
        Validator::isNonEmptyString($projectId, 'Must initialize FirebaseApp with a project ID to call verifyIdToken()');
        $idTokenVerifier = self::newIdTokenVerifier(self::ID_TOKEN_ISSUER_PREFIX, $projectId);
        $publicKeysManager = self::newPublicKeysManager(self::ID_TOKEN_CERT_URL);

        return (new FirebaseTokenVerifierImplBuilder())
            ->setShortName('ID Token')
            ->setMethod('verifyIdToken()')
            ->setDocUrl('https://firebase.google.com/docs/auth/admin/verify-id-tokens')
            ->setPublicKeysManager($publicKeysManager)
            ->setIdTokenVerifier($idTokenVerifier)
            ->build();
    }

    public static function createSessionCookieVerifier(FirebaseApp $app) {
        $projectId = ImplFirebaseTrampolines::getProjectId($app);
        Validator::isNonEmptyString($projectId, 'Must initialize FirebaseApp with a project ID to call verifySessionCookie()');
        $idTokenVerifier = self::newIdTokenVerifier(self::SESSION_COOKIE_ISSUER_PREFIX, $projectId);
        $publicKeysManager = self::newPublicKeysManager(self::SESSION_COOKIE_CERT_URL);

        return (new FirebaseTokenVerifierImplBuilder())
            ->setShortName('session cookie')
            ->setMethod('verifySessionCookie()')
            ->setDocUrl('https://firebase.google.com/docs/auth/admin/manage-cookies')
            ->setPublicKeysManager($publicKeysManager)
            ->setIdTokenVerifier($idTokenVerifier)
            ->build();
    }

    /**
     * @param string $issuerPrefix
     * @param string $projectId
     * @return IdTokenVerifier
     */
    private static function newIdTokenVerifier(string $issuerPrefix, string $projectId): IdTokenVerifier {
        $idTokenVerifier = new IdTokenVerifier([]);
        $idTokenVerifier->setAudience($projectId);
        $idTokenVerifier->setIssuer($issuerPrefix . $projectId);
        return $idTokenVerifier;
    }

    private static function newPublicKeysManager(string $certUrl) {
        return (new GooglePublicKeysManagerBuilder())
            ->setPublicCertsEncodeUrl($certUrl)
            ->build();
    }
}