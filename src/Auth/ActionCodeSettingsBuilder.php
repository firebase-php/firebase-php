<?php


namespace Firebase\Auth;


final class ActionCodeSettingsBuilder
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var bool
     */
    private $handleCodeInApp;

    /**
     * @var string
     */
    private $dynamicLinkDomain;

    /**
     * @var string
     */
    private $iOSBundleId;

    /**
     * @var string
     */
    private $androidPackageName;

    /**
     * @var string
     */
    private $androidMinimumVersion;

    /**
     * @var bool
     */
    private $androidInstallApp;

    /**
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return ActionCodeSettingsBuilder
     */
    public function setUrl(string $url): ActionCodeSettingsBuilder
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHandleCodeInApp(): ?bool
    {
        return $this->handleCodeInApp;
    }

    /**
     * @param bool $handleCodeInApp
     * @return ActionCodeSettingsBuilder
     */
    public function setHandleCodeInApp(bool $handleCodeInApp): ActionCodeSettingsBuilder
    {
        $this->handleCodeInApp = $handleCodeInApp;
        return $this;
    }

    /**
     * @return string
     */
    public function getDynamicLinkDomain(): ?string
    {
        return $this->dynamicLinkDomain;
    }

    /**
     * @param string $dynamicLinkDomain
     * @return ActionCodeSettingsBuilder
     */
    public function setDynamicLinkDomain(string $dynamicLinkDomain): ActionCodeSettingsBuilder
    {
        $this->dynamicLinkDomain = $dynamicLinkDomain;
        return $this;
    }

    /**
     * @return string
     */
    public function getIOSBundleId(): ?string
    {
        return $this->iOSBundleId;
    }

    /**
     * @param string $iOSBundleId
     * @return ActionCodeSettingsBuilder
     */
    public function setIosBundleId(string $iOSBundleId): ActionCodeSettingsBuilder
    {
        $this->iOSBundleId = $iOSBundleId;
        return $this;
    }

    /**
     * @return string
     */
    public function getAndroidPackageName(): ?string
    {
        return $this->androidPackageName;
    }

    /**
     * @param string $androidPackageName
     * @return ActionCodeSettingsBuilder
     */
    public function setAndroidPackageName(string $androidPackageName): ActionCodeSettingsBuilder
    {
        $this->androidPackageName = $androidPackageName;
        return $this;
    }

    /**
     * @return string
     */
    public function getAndroidMinimumVersion(): ?string
    {
        return $this->androidMinimumVersion;
    }

    /**
     * @param string $androidMinimumVersion
     * @return ActionCodeSettingsBuilder
     */
    public function setAndroidMinimumVersion(string $androidMinimumVersion): ActionCodeSettingsBuilder
    {
        $this->androidMinimumVersion = $androidMinimumVersion;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAndroidInstallApp(): ?bool
    {
        return $this->androidInstallApp;
    }

    /**
     * @param bool $androidInstallApp
     * @return ActionCodeSettingsBuilder
     */
    public function setAndroidInstallApp(bool $androidInstallApp): ActionCodeSettingsBuilder
    {
        $this->androidInstallApp = $androidInstallApp;
        return $this;
    }

    public function build() {
        return new ActionCodeSettings($this);
    }
}
