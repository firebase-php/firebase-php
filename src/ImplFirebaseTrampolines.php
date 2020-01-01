<?php


namespace Firebase;

use Firebase\Internal\FirebaseService;

class ImplFirebaseTrampolines
{
    private function __construct()
    {
    }

    public static function getServiceAccount(FirebaseApp $app) {
        return $app->getOptions()->getServiceAccount();
    }

    public static function getCredentials(FirebaseApp $app)
    {
        return $app->getOptions()->getCredentials();
    }

    public static function getProjectId(FirebaseApp $app)
    {
        return $app->getProjectId();
    }

    public static function isDefaultApp(FirebaseApp $app)
    {
        return $app->isDefaultApp();
    }

    /**
     * @param FirebaseApp|null $app
     * @param string $id
     * @param string $class
     * @return FirebaseService|mixed
     */
    public static function getService(?FirebaseApp $app, string $id, string $class)
    {
        $service = $app->getService($id);
        if (is_null($service)) {
            return null;
        }
        return $service;
    }

    public static function addService(?FirebaseApp $app, FirebaseService $service)
    {
        $app->addService($service);
        return $service;
    }
}
