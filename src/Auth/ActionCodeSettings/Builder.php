<?php


namespace Firebase\Auth\ActionCodeSettings;


use Firebase\Auth\ActionCodeSettings;

final class Builder
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
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return Builder
     */
    public function setUrl(string $url): Builder
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHandleCodeInApp(): bool
    {
        return $this->handleCodeInApp;
    }

    /**
     * @param bool $handleCodeInApp
     * @return Builder
     */
    public function setHandleCodeInApp(bool $handleCodeInApp): Builder
    {
        $this->handleCodeInApp = $handleCodeInApp;
        return $this;
    }

    /**
     * @return string
     */
    public function getDynamicLinkDomain(): string
    {
        return $this->dynamicLinkDomain;
    }

    /**
     * @param string $dynamicLinkDomain
     * @return Builder
     */
    public function setDynamicLinkDomain(string $dynamicLinkDomain): Builder
    {
        $this->dynamicLinkDomain = $dynamicLinkDomain;
        return $this;
    }

    /**
     * @return string
     */
    public function getIOSBundleId(): string
    {
        return $this->iOSBundleId;
    }

    /**
     * @param string $iOSBundleId
     * @return Builder
     */
    public function setIosBundleId(string $iOSBundleId): Builder
    {
        $this->iOSBundleId = $iOSBundleId;
        return $this;
    }

    /**
     * @return string
     */
    public function getAndroidPackageName(): string
    {
        return $this->androidPackageName;
    }

    /**
     * @param string $androidPackageName
     * @return Builder
     */
    public function setAndroidPackageName(string $androidPackageName): Builder
    {
        $this->androidPackageName = $androidPackageName;
        return $this;
    }

    /**
     * @return string
     */
    public function getAndroidMinimumVersion(): string
    {
        return $this->androidMinimumVersion;
    }

    /**
     * @param string $androidMinimumVersion
     * @return Builder
     */
    public function setAndroidMinimumVersion(string $androidMinimumVersion): Builder
    {
        $this->androidMinimumVersion = $androidMinimumVersion;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAndroidInstallApp(): bool
    {
        return $this->androidInstallApp;
    }

    /**
     * @param bool $androidInstallApp
     * @return Builder
     */
    public function setAndroidInstallApp(bool $androidInstallApp): Builder
    {
        $this->androidInstallApp = $androidInstallApp;
        return $this;
    }

    public function builder() {
        return new ActionCodeSettings($this);
    }
}
