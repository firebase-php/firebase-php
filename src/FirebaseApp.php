<?php

namespace Firebase;

use Firebase\Auth\ServiceAccount;
use Firebase\Internal\FirebaseService;
use Firebase\Util\Validator\Validator;
use Firebase\Auth\GoogleAuthLibrary\Credentials\ServiceAccountCredentials;

class FirebaseApp
{
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

    /**
     * @var bool
     */
    private $deleted;

    /**
     * @var FirebaseService[]
     */
    private $services = [];

    public function __construct(string $name, FirebaseOptions $options)
    {
        $this->name = Validator::isNonEmptyString($name);
        $this->options = Validator::isNonNullObject($options);
    }

    public static function getApps()
    {
        return self::$instances;
    }

    public static function getInstance(?string $name = self::DEFAULT_APP_NAME)
    {
        if (isset(self::$instances[$name])) {
            return self::$instances[$name];
        }

        $availableAppNames = self::getAllAppNames();
        $availableAppNamesMessage = null;
        if (empty($availableAppNames)) {
            $availableAppNamesMessage = '';
        } else {
            $availableAppNamesMessage = sprintf('Available app names: %s', implode(', ', $availableAppNames));
        }
        $errorMessage = sprintf('FirebaseApp with name %s does not exist. %s', $name, $availableAppNamesMessage);
        throw new FirebaseException($errorMessage);
    }

    public static function initializeApp(?FirebaseOptions $options = null, ?string $name = self::DEFAULT_APP_NAME)
    {
        $normalizedName = self::normalize($name);
        Validator::checkArgument(
            !in_array($normalizedName, self::$instances),
            sprintf('FirebaseApp name %s already exists!', $normalizedName)
        );
        if (isset(self::$instances[$normalizedName])) {
            if ($normalizedName === self::DEFAULT_APP_NAME) {
                throw new FirebaseException(
                    'The default Firebase app already exists. This means you called initializeApp() ' .
                    'more than once without providing an app name as the second argument. In most cases ' .
                    'you only need to call initializeApp() once. But if you do want to initialize ' .
                    'multiple apps, pass a second argument to initializeApp() to give each app a unique ' .
                    'name.',
                    self::ERROR_DUPLICATE_APP
                );
            } else {
                throw new FirebaseException(
                    sprintf('Firebase app named "%s" already exists. This means you called initializeApp() ', $normalizedName) .
                    'more than once with the same app name as the second argument. Make sure you provide a ' .
                    'unique name every time you call initializeApp().',
                    self::ERROR_DUPLICATE_APP
                );
            }
        }
        if (is_null($options)) {
            $options = self::getOptionsFromEnvironment();
        }
        $firebaseApp = new FirebaseApp($normalizedName, $options);
        self::$instances[$normalizedName] = $firebaseApp;

        return $firebaseApp;
    }

    /**
     * @return string[]
     */
    private static function getAllAppNames()
    {
        $allAppNames = [];
        foreach (self::$instances as $app) {
            $allAppNames[] = $app->getName();
        }
        sort($allAppNames);
        return $allAppNames;
    }

    private static function normalize(string $name = null)
    {
        return trim(Validator::isNonNullObject($name));
    }

    private static function getOptionsFromEnvironment(): FirebaseOptions
    {
        $options = (new FirebaseOptions())
            ->setServiceAccount(new ServiceAccount());
        $defaultConfig = getenv(self::FIREBASE_CONFIG_ENV_VAR);
        if (empty($defaultConfig)) {
            return $options;
        }
        $contents = null;
        if ($defaultConfig[0] === '{') {
            $contents = $defaultConfig;
        } elseif (file_exists($defaultConfig)) {
            $contents = file_get_contents($defaultConfig);
        }
        if (!$contents) {
            throw new \InvalidArgumentException('Failed to parse app options file.');
        }
        $contentArray = json_decode($contents, true);

        if (!is_array($contentArray)) {
            throw new \InvalidArgumentException();
        }
        $options
            ->setDatabaseAuthVariableOverride($contentArray['databaseAuthVariableOverride'] ?? null)
            ->setDatabaseUrl($contentArray['databaseUrl'] ?? null);
        if (isset($contentArray['projectId'])) {
            $options->setProjectId($contentArray['projectId']);
        }
        if (isset($contentArray['storageBucket'])) {
            $options->setStorageBucket($contentArray['storageBucket']);
        }
        return $options;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        $this->checkNotDeleted();
        return $this->name;
    }

    /**
     * @return FirebaseOptions
     */
    public function getOptions(): FirebaseOptions
    {
        $this->checkNotDeleted();
        return $this->options;
    }

    public function getProjectId(): string
    {
        $projectId = $this->getOptions()->getProjectId();

        if (empty($projectId)) {
            $sa = $this->getOptions()->getServiceAccount();
            if ($sa instanceof ServiceAccount) {
                $projectId = $sa->getProjectId();
            }
        }

        if (empty($projectId)) {
            $projectId = getenv('GOOGLE_CLOUD_PROJECT');
        }
        if (empty($projectId)) {
            $projectId = getenv('GCLOUD_PROJECT');
        }

        return $projectId;
    }

    public function isDefaultApp()
    {
        return $this->name === self::DEFAULT_APP_NAME;
    }

    public function delete()
    {
        foreach ($this->services as $key => $service) {
            $this->services[$key]->destroy();
        }
        $this->services = [];
        $this->deleted = true;
        unset(FirebaseApp::$instances[$this->name]);
    }

    private function checkNotDeleted()
    {
        Validator::checkArgument(!$this->deleted, sprintf(
            'FirebaseApp "%s" was deleted',
            $this->name
        ));
    }

    public function getService(string $id)
    {
        Validator::isNonEmptyString($id);
        return $this->services[$id] ?? null;
    }

    public function addService(FirebaseService $service = null)
    {
        $this->checkNotDeleted();
        Validator::isNonNullObject($service);
        Validator::checkArgument(!isset($this->services[$service->getId()]));
        $this->services[$service->getId()] = $service;
    }

    public static function clearInstancesForTest()
    {
        foreach (self::$instances as $app) {
            $app->delete();
        }
        self::$instances = [];
    }

    public function __toString()
    {
        $className = (new \ReflectionClass($this))->getShortName();
        return sprintf('%s{name=%s}', $className, $this->name);
    }

    /**
     * @return bool
     */
    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }
}
