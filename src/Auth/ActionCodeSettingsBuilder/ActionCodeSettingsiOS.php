<?php

namespace Firebase\Auth\ActionCodeSettingsBuilder;


class ActionCodeSettingsiOS
{
    /**
     * @var string
     */
    private $bundleId;

    /**
     * @return string
     */
    public function getBundleId(): string
    {
        return $this->bundleId;
    }

    /**
     * @param string $bundleId
     *
     * @return self
     */
    public function setBundleId(string $bundleId): self
    {
        $this->bundleId = $bundleId;
        return $this;
    }

}