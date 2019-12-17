<?php

namespace Firebase\Auth\ActionCodeSettingsBuilder;


use Firebase\Util\Error\AuthClientErrorCode;
use Firebase\Util\Error\ErrorInfo;
use Firebase\Util\Error\FirebaseAuthError;
use Firebase\Util\Validator\Validator;

class ActionCodeSettingsBuilder
{
    private $continueUrl;

    private $apn;

    private $amv;

    private $installApp;

    private $ibi;

    private $canHandleCodeInApp;

    private $dynamicLinkDomain;

    public function __construct(ActionCodeSettings $actionCodeSettings)
    {
        if(Validator::isNonNullObject($actionCodeSettings)) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT),
                '"ActionCodeSettings" must be a non-null object'
            );
        }

        if(!$actionCodeSettings->getUrl()) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::MISSING_CONTINUE_URI)
            );
        } else if (!Validator::isURL($actionCodeSettings->getUrl())) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_CONTINUE_URI)
            );
        }

        $this->continueUrl = $actionCodeSettings->getUrl();

        if(!Validator::isBoolean($actionCodeSettings->getHandleCodeInApp())) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT),
                '"ActionCodeSettings::handleCodeInApp" must be a boolean.'
            );
        }

        $this->canHandleCodeInApp = $actionCodeSettings->getHandleCodeInApp() || false;

        if(!Validator::isNonEmptyString($actionCodeSettings->getDynamicLinkDomain())) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_DYNAMIC_LINK_DOMAIN)
            );
        }

        $this->dynamicLinkDomain = $actionCodeSettings->getDynamicLinkDomain();

        if($actionCodeSettings->getIOS()) {
            if(!Validator::isNonNullObject($actionCodeSettings->getIOS())) {
                throw new FirebaseAuthError(
                    new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT),
                    '"ActionCodeSettings::iOS" must be a valid non-null object.'
                );
            } else if(!$actionCodeSettings->getIOS()->getBundleId()) {
                throw new FirebaseAuthError(
                    new ErrorInfo(AuthClientErrorCode::MISSING_IOS_BUNDLE_ID)
                );
            } else if(!Validator::isNonEmptyString($actionCodeSettings->getIOS()->getBundleId())) {
                throw new FirebaseAuthError(
                    new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT),
                    '"ActionCodeSettings::iOS::bundleId" must be a valid non-empty string.'
                );
            }
        }

        $this->ibi = $actionCodeSettings->getIOS()->getBundleId();

        if ($actionCodeSettings->getAndroid()) {
            if(!Validator::isNonNullObject($actionCodeSettings->getAndroid())) {
                throw new FirebaseAuthError(
                    new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT),
                    '"ActionCodeSettings::android" must be a valid non-null object.'
                );
            } else if(!$actionCodeSettings->getAndroid()->getPackageName()) {
                throw new FirebaseAuthError(
                    new ErrorInfo(AuthClientErrorCode::MISSING_ANDROID_PACKAGE_NAME)
                );
            } else if(!Validator::isNonEmptyString($actionCodeSettings->getAndroid()->getPackageName())) {
                throw new FirebaseAuthError(
                    new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT),
                    '"ActionCodeSettings::android::packageName" must be a valid non-empty string.'
                );
            } else if (!Validator::isNonEmptyString($actionCodeSettings->getAndroid()->getMinimumVersion())) {
                throw new FirebaseAuthError(
                    new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT),
                    '"ActionCodeSettings::android::minimumVersion" must be a valid non-empty string.'
                );
            } else if (!Validator::isBoolean($actionCodeSettings->getAndroid()->getInstallApp())) {
                throw new FirebaseAuthError(
                    new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT),
                    '"ActionCodeSettings::android::installApp" must be a valid boolean.'
                );
            }
        }

        $this->apn = $actionCodeSettings->getAndroid()->getPackageName();
        $this->amv = $actionCodeSettings->getAndroid()->getMinimumVersion();
        $this->installApp = $actionCodeSettings->getAndroid()->getInstallApp() || false;
    }

    public function buildRequest(): EmailActionCodeRequest {
        $request = new EmailActionCodeRequest();
        $request
            ->setContinueUrl($this->continueUrl)
            ->setCanHandleCodeInApp($this->canHandleCodeInApp)
            ->setDynamicLinkDomain($this->dynamicLinkDomain)
            ->setAndroidPackageName($this->apn)
            ->setAndroidMinimumVersion($this->amv)
            ->setAndroidInstallApp($this->installApp)
            ->setIOSBundleId($this->ibi);

        return $request;
    }
}