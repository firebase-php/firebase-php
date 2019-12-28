<?php

namespace Firebase\Tests\Auth;

use Firebase\Auth\ActionCodeSettings;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class ActionCodeSettingsTest extends TestCase
{
    public function testNoUrl()
    {
        $this->expectException(InvalidArgumentException::class);
        ActionCodeSettings::builder()->build();
    }

    public function testMalformedUrl()
    {
        $this->expectException(InvalidArgumentException::class);
        ActionCodeSettings::builder()
            ->setUrl('not a url')
            ->build();
    }

    public function testEmptyUrl()
    {
        $this->expectException(InvalidArgumentException::class);
        ActionCodeSettings::builder()
            ->setUrl('')
            ->build();
    }

    public function testUrlOnly()
    {
        $settings = ActionCodeSettings::builder()
            ->setUrl('https://example.com')
            ->build();
        $expected = [
            'continueUrl' => 'https://example.com',
            'canHandleCodeInApp' => false,
        ];
        self::assertEquals($expected, $settings->getProperties());
    }

    public function testNoAndroidPackageName()
    {
        $this->expectException(InvalidArgumentException::class);
        ActionCodeSettings::builder()
            ->setUrl('https://example.com')
            ->setAndroidMinimumVersion('6.0')
            ->setAndroidInstallApp(true)
            ->build();
    }

    public function testAllSettings()
    {
        $settings = ActionCodeSettings::builder()
            ->setUrl('https://example.com')
            ->setHandleCodeInApp(true)
            ->setDynamicLinkDomain('myapp.page.link')
            ->setIosBundleId('com.example.ios')
            ->setAndroidPackageName('com.example.android')
            ->setAndroidMinimumVersion('6.0')
            ->setAndroidInstallApp(true)
            ->build();
        $expected = [
            'continueUrl' => 'https://example.com',
            'canHandleCodeInApp' => true,
            'dynamicLinkDomain' => 'myapp.page.link',
            'iOSBundleId' => 'com.example.ios',
            'androidPackageName' => 'com.example.android',
            'androidMinimumVersion' => '6.0',
            'androidInstallApp' => true,
        ];

        self::assertEquals($expected, $settings->getProperties());
    }
}
