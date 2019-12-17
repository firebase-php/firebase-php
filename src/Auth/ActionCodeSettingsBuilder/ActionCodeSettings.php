<?php

namespace Firebase\Auth\ActionCodeSettingsBuilder;


class ActionCodeSettings
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var bool | null
     */
    private $handleCodeInApp;

    /**
     * @var ActionCodeSettingsiOS | null
     */
    private $iOS;

    /**
     * @var ActionCodeSettingsAndroid | null
     */
    private $android;

    /**
     * @var string
     */
    private $dynamicLinkDomain;

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return self
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHandleCodeInApp(): ?bool
    {
        return $this->handleCodeInApp;
    }

    /**
     * @param bool|null $handleCodeInApp
     *
     * @return self
     */
    public function setHandleCodeInApp(?bool $handleCodeInApp): self
    {
        $this->handleCodeInApp = $handleCodeInApp;
        return $this;
    }

    /**
     * @return ActionCodeSettingsiOS|null
     */
    public function getIOS(): ?ActionCodeSettingsiOS
    {
        return $this->iOS;
    }

    /**
     * @param ActionCodeSettingsiOS|null $iOS
     *
     * @return self
     */
    public function setIOS(?ActionCodeSettingsiOS $iOS): self
    {
        $this->iOS = $iOS;
        return $this;
    }

    /**
     * @return ActionCodeSettingsAndroid|null
     */
    public function getAndroid(): ?ActionCodeSettingsAndroid
    {
        return $this->android;
    }

    /**
     * @param ActionCodeSettingsAndroid|null $android
     *
     * @return self
     */
    public function setAndroid(?ActionCodeSettingsAndroid $android): self
    {
        $this->android = $android;
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
     *
     * @return self
     */
    public function setDynamicLinkDomain(string $dynamicLinkDomain): self
    {
        $this->dynamicLinkDomain = $dynamicLinkDomain;
        return $this;
    }


}