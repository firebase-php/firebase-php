<?php


namespace Firebase\Auth\Internal;


use Google\Auth\OAuth2;
use GuzzleHttp\ClientInterface;
use Psr\Cache\CacheItemPoolInterface;

class IdTokenVerifierBuilder
{
    /**
     * @var OAuth2
     */
    private $oAuth2;

    /**
     * @return OAuth2
     */
    public function getOAuth2(): OAuth2
    {
        return $this->oAuth2;
    }

    /**
     * @param OAuth2 $oAuth2
     * @return IdTokenVerifierBuilder
     */
    public function setOAuth2(OAuth2 $oAuth2): IdTokenVerifierBuilder
    {
        $this->oAuth2 = $oAuth2;
        return $this;
    }
}
