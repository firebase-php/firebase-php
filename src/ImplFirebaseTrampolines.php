<?php


namespace Firebase;


class ImplFirebaseTrampolines
{
    private function __construct() {}

    public static function getCredentials(FirebaseApp $app) {
        return $app->getOptions()->getCredentials();
    }

    public static function getProjectId(FirebaseApp $app) {
        return $app->getProjectId();
    }

    public static function isDefaultApp(FirebaseApp $app) {
        return $app->isDefaultApp();
    }
}