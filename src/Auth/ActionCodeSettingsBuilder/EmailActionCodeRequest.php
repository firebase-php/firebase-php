<?php

namespace Firebase\Auth\ActionCodeSettingsBuilder;


class EmailActionCodeRequest
{
    /**
     * @var string | null
     */
    private $continueUrl;

    /**
     * @var bool | null
     */
    private $canHandleCodeInApp;

    /**
     * @var string | null
     */
    private $dynamicLinkDomain;

    /**
     * @var string | null
     */
    private $androidPackageName;

    /**
     * @var string
     */
    private $androidMinimumVersion;

    /**
     * @var bool | null
     */
    private $androidInstallApp;

    /**
     * @var string | null
     */
    private $iOSBundleId;

    /**
     * @return string|null
     */
    public function getContinueUrl(): ?string
    {
        return $this->continueUrl;
    }

    /**
     * @param string|null $continueUrl
     *
     * @return self
     */
    public function setContinueUrl(?string $continueUrl): self
    {
        $this->continueUrl = $continueUrl;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getCanHandleCodeInApp(): ?bool
    {
        return $this->canHandleCodeInApp;
    }

    /**
     * @param bool|null $canHandleCodeInApp
     *
     * @return self
     */
    public function setCanHandleCodeInApp(?bool $canHandleCodeInApp): self
    {
        $this->canHandleCodeInApp = $canHandleCodeInApp;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDynamicLinkDomain(): ?string
    {
        return $this->dynamicLinkDomain;
    }

    /**
     * @param string|null $dynamicLinkDomain
     *
     * @return self
     */
    public function setDynamicLinkDomain(?string $dynamicLinkDomain): self
    {
        $this->dynamicLinkDomain = $dynamicLinkDomain;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAndroidPackageName(): ?string
    {
        return $this->androidPackageName;
    }

    /**
     * @param string|null $androidPackageName
     *
     * @return self
     */
    public function setAndroidPackageName(?string $androidPackageName): self
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
     *
     * @return self
     */
    public function setAndroidMinimumVersion(string $androidMinimumVersion): self
    {
        $this->androidMinimumVersion = $androidMinimumVersion;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getAndroidInstallApp(): ?bool
    {
        return $this->androidInstallApp;
    }

    /**
     * @param bool|null $androidInstallApp
     *
     * @return self
     */
    public function setAndroidInstallApp(?bool $androidInstallApp): self
    {
        $this->androidInstallApp = $androidInstallApp;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIOSBundleId(): ?string
    {
        return $this->iOSBundleId;
    }

    /**
     * @param string|null $iOSBundleId
     *
     * @return self
     */
    public function setIOSBundleId(?string $iOSBundleId): self
    {
        $this->iOSBundleId = $iOSBundleId;
        return $this;
    }
}