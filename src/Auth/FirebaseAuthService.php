<?php


namespace Firebase\Auth;

use Firebase\FirebaseApp;
use Firebase\Internal\FirebaseService;

/**
 * Class FirebaseAuthService
 * @package Firebase\Auth
 * @property FirebaseAuth $instance
 */
class FirebaseAuthService extends FirebaseService
{
    public function __construct(FirebaseApp $app)
    {
        parent::__construct(FirebaseAuth::SERVICE_ID, FirebaseAuth::fromApp($app));
    }

    public function destroy()
    {
        $this->instance->destroy();
    }
}
