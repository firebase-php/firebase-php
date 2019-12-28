<?php


namespace Firebase\Tests\Testing;

use Firebase\FirebaseApp;
use Firebase\FirebaseOptions;
use Firebase\Util\Validator\Validator;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;

class AppHttpClient
{
    /**
     * @var FirebaseApp
     */
    private $app;

    /**
     * @var FirebaseOptions
     */
    private $options;

    /**
     * @var ClientInterface
     */
    private $httpClient;

    public function __construct(?FirebaseApp $app = null)
    {
        if (is_null($app)) {
            self::__construct(FirebaseApp::getInstance());
            return;
        }

        $this->app = Validator::isNonNullObject($app);
        $this->options = $this->app->getOptions();
        $this->httpClient = $this->options->getHttpClient();
    }

    public function put(?string $path, ?string $json)
    {
        $url = $this->options->getDatabaseUrl() . $path . '?access_token=' . $this->getToken();
        $request = new Request(
            'PUT',
            $url,
            [
                'Content-Type' => 'application/json'
            ],
            $json
        );
        $response = $this->httpClient->send($request);
        return new ResponseInfo($response);
    }

    private function getToken()
    {
        return TestOnlyImplFirebaseTrampolines::getToken($this->app, false);
    }
}
