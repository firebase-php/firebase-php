<?php


namespace Firebase\Auth;


use Firebase\Auth\ActionCodeSettings\Builder;
use Firebase\Util\Error\AuthClientErrorCode;
use Firebase\Util\Error\ErrorInfo;
use Firebase\Util\Error\FirebaseAuthError;
use Firebase\Util\Validator\Validator;

class ActionCodeSettings
{
    private $properties;

    public function __construct(Builder $builder)
    {
        if(!Validator::isURL($builder->getUrl())) {
            $error = AuthClientErrorCode::INVALID_CONTINUE_URI;
            throw new FirebaseAuthError(
                new ErrorInfo($error['code'], $error['message'])
            );
        }
        if($builder->isAndroidInstallApp() || Validator::isNonEmptyString($builder->getAndroidMinimumVersion())) {
            $error = AuthClientErrorCode::INVALID_ARGUMENT;
            throw new FirebaseAuthError(
                new ErrorInfo($error['code']),
                'Android package name is required when specifying other Android settings'
            );
        }
        $properties = [
            'continueUrl' => $builder->getUrl(),
            'canHandleCodeInApp' => $builder->isHandleCodeInApp()
        ];
        if(Validator::isNonEmptyString($builder->getDynamicLinkDomain())) {
            $properties['dynamicLinkDomain'] = $builder->getDynamicLinkDomain();
        }
        if(Validator::isNonEmptyString($builder->getIOSBundleId())) {
            $properties['iOSBundleId'] = $builder->getIOSBundleId();
        }
        if(Validator::isNonEmptyString($builder->getAndroidPackageName())) {
            $properties['androidPackageName'] = $builder->getAndroidPackageName();
            if(Validator::isNonEmptyString($builder->getAndroidMinimumVersion())) {
                $properties['androidMinimumVersion'] = $builder->getAndroidMinimumVersion();
            }
            if($builder->isAndroidInstallApp()) {
                $properties['androidInstallApp'] = $builder->isAndroidInstallApp();
            }
        }
        $this->properties = $properties;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public static function builder(): Builder {
        return new Builder();
    }
}
