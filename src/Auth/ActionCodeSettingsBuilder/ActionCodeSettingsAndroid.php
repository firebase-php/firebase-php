<?php

namespace Firebase\Auth\ActionCodeSettingsBuilder;


class ActionCodeSettingsAndroid
{
    /**
     * @var string
     */
    private $packageName;

    /**
     * @var bool | null
     */
    private $installApp;

    /**
     * @var string | null
     */
    private $minimumVersion;

    /**
     * @return string
     */
    public function getPackageName(): string
    {
        return $this->packageName;
    }

    /**
     * @param string $packageName
     *
     * @return self
     */
    public function setPackageName(string $packageName): self
    {
        $this->packageName = $packageName;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getInstallApp(): ?bool
    {
        return $this->installApp;
    }

    /**
     * @param bool|null $installApp
     *
     * @return self
     */
    public function setInstallApp(?bool $installApp): self
    {
        $this->installApp = $installApp;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMinimumVersion(): ?string
    {
        return $this->minimumVersion;
    }

    /**
     * @param string|null $minimumVersion
     *
     * @return self
     */
    public function setMinimumVersion(?string $minimumVersion): self
    {
        $this->minimumVersion = $minimumVersion;
        return $this;
    }
}
