<?php

namespace Firebase;

use Firebase\Util\Validator\Validator;

class FirebaseApp {
    /**
     * @var FirebaseApp[]
     */
    private static $instances = [];

    const DEFAULT_APP_NAME = '[DEFAULT]';

    const FIREBASE_CONFIG_ENV_VAR = 'FIREBASE_CONFIG';

    const ERROR_DUPLICATE_APP = 'duplicate-app';

    /**
     * @var string
     */
    private $name;

    /**
     * @var FirebaseOptions
     */
    private $options;

    public function __construct(string $name, FirebaseOptions $options)
    {
        $this->name = Validator::isNonEmptyString($name);
        $this->options = Validator::isNonNullObject($options);
    }

    public static function getApps() {
        return self::$instances;
    }

    public static function getInstance(string $name = null) {
        if(is_null($name)) {
            $name = self::DEFAULT_APP_NAME;
        }
        if(isset(self::$instances[$name])) {
            return self::$instances[$name];
        }

        $availableAppNames = self::getAllAppNames();
        $availableAppNamesMessage = null;
        if(empty($availableAppNames)) {
            $availableAppNamesMessage = '';
        } else {
            $availableAppNamesMessage = sprintf('Available app names: %s', implode(', ', $availableAppNames));
        }
        $errorMessage = sprintf('FirebaseApp with name %s does not exist. %s', $name, $availableAppNamesMessage);
        throw new \Exception($errorMessage);
    }

    public static function initializeApp(FirebaseOptions $options = null, string $name = null) {
        $normalizedName = self::normalize($name);
        Validator::checkArgument(
            !in_array($normalizedName, self::$instances),
            sprintf('FirebaseApp name %s already exists!', $normalizedName)
        );
        if(isset(self::$instances[$normalizedName])) {
            if($normalizedName === self::DEFAULT_APP_NAME) {
                throw new FirebaseException(
                    'The default Firebase app already exists. This means you called initializeApp() ' .
                    'more than once without providing an app name as the second argument. In most cases ' .
                    'you only need to call initializeApp() once. But if you do want to initialize ' .
                    'multiple apps, pass a second argument to initializeApp() to give each app a unique ' .
                    'name.', self::ERROR_DUPLICATE_APP);
            } else {
                throw new FirebaseException(
                    sprintf('Firebase app named "%s" already exists. This means you called initializeApp() ', $normalizedName) .
                    'more than once with the same app name as the second argument. Make sure you provide a ' .
                    'unique name every time you call initializeApp().',
                    self::ERROR_DUPLICATE_APP);
            }
        }
        if(is_null($options)) {
            $options = self::getOptionsFromEnvVar();
        }
        $firebaseApp = new FirebaseApp($normalizedName, $options);
        self::$instances[$normalizedName] = $firebaseApp;

        return $firebaseApp;
    }

    /**
     * @return string[]
     */
    private static function getAllAppNames() {
        $allAppNames = [];
        foreach(self::$instances as $app) {
            $allAppNames[] = $app->getName();
        }
        sort($allAppNames);
        return $allAppNames;
    }

    private static function normalize(string $name) {
        return trim(Validator::isNonNullObject($name));
    }

    private static function getOptionsFromEnvVar(): FirebaseOptions {
        $defaultConfig = getenv(self::FIREBASE_CONFIG_ENV_VAR);
        if(empty($defaultConfig)) {
            return (new FirebaseOptionsBuilder())
                ->setCredentials(FirebaseOptions::getApplicationDefaultCredentials())
                ->build();
        }
        $contents = $defaultConfig[0] === '{' ? $defaultConfig : file_get_contents($defaultConfig);
        if(!$contents) {
            throw new FirebaseException('Failed to parse app options file.');
        }
        $contentArray = json_decode($contents, true);
        $builder = new FirebaseOptionsBuilder();
        $builder
            ->setDatabaseAuthVariableOverride($contentArray['databaseAuthVariableOverride'])
            ->setDatabaseUrl($contentArray['databaseURL'])
            ->setProjectId($contentArray['projectId'])
            ->setStorageBucket($contentArray['storageBucket'])
            ->setConnectTimeout($contentArray['connectTimeout'])
            ->setReadTimeout($contentArray['readTimeout']);
        return $builder->setCredentials(FirebaseOptions::getApplicationDefaultCredentials())->build();
    }
}
