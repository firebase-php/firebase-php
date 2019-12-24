<?php


namespace Firebase\Auth;

use Firebase\Util\Validator\Validator;

class ActionCodeSettings
{
    private $properties;

    public function __construct(ActionCodeSettingsBuilder $builder)
    {
        Validator::isURL($builder->getUrl(), 'URL');
        if($builder->isAndroidInstallApp() || Validator::isNonEmptyString($builder->getAndroidMinimumVersion(), null, false)) {
            Validator::isNonEmptyString($builder->getAndroidPackageName(), 'Android package name is required when specifying other Android settings');
        }
        $properties = [
            'continueUrl' => $builder->getUrl(),
            'canHandleCodeInApp' => $builder->isHandleCodeInApp()
        ];
        if(Validator::isNonEmptyString($builder->getDynamicLinkDomain(), null, false)) {
            $properties['dynamicLinkDomain'] = $builder->getDynamicLinkDomain();
        }
        if(Validator::isNonEmptyString($builder->getIOSBundleId(), null, false)) {
            $properties['iOSBundleId'] = $builder->getIOSBundleId();
        }
        if(Validator::isNonEmptyString($builder->getAndroidPackageName(), null, false)) {
            $properties['androidPackageName'] = $builder->getAndroidPackageName();
            if(Validator::isNonEmptyString($builder->getAndroidMinimumVersion(), null, false)) {
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

    public static function builder(): ActionCodeSettingsBuilder {
        return new ActionCodeSettingsBuilder();
    }
}
