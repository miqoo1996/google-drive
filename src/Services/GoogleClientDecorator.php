<?php

namespace Miqoo1996\GDrive\Services;

/**
 * Class GoogleClientDecorator
 *
 * ````
 * If you don't want to store the tokens into session
 * you can use the bellow function to store into DB.
 * $obj->tokenService->setPath(__DIR__ . '/../storage/token.json');
 * ````
 *
 * ````
 * If you want to set credentials from file you can use the bellow function
 * $obj->tokenService->setPath(__DIR__ . '/../storage/credentials.json');
 * ````
 */
class GoogleClientDecorator extends \Google_Client
{
    /**
     * @var StorageService
     */
    public $tokenService;

    /**
     * @var StorageService
     */
    private $credentialsService;

    public function __construct(array $config = array())
    {
        parent::__construct($config);

        $this->setDependencies($config);
    }

    public function setDependencies($config): void
    {
        $storageService = new StorageService();
        $this->credentialsService = $storageService;
        $this->tokenService = $storageService->newInstance();

        $this->setScopes(\Google_Service_Drive::DRIVE);
        $this->setAccessType('offline');
        $this->setPrompt('select_account consent');

        // set credentials from file is specified
        if (isset($config['json_file_credentials'])) {
            $this->setFiles($config);
        }
    }

    public function setFiles($config): void
    {
        $this->credentialsService->setPath($config['json_file_credentials']);

        if ($this->credentialsService->isStored() === false) {
            throw new \Exception('GoogleDriveError: $json_file_credentials path is not correct.');
        }

        if (isset($config['tokenPath'])) {
            $this->tokenService->setPath($config['tokenPath']);
        }

        $this->setAuthConfig($this->credentialsService->getPath());
    }

    /**
     * @return array|null
     */
    public function getStoredTokenAsArray(): ?array
    {
        $token = null;

        if ($this->tokenService->isStored()) {
            $token = json_decode(file_get_contents($this->tokenService->getPath()), true);
        } elseif (isset($_SESSION['google_drive_token'])) {
            $token = $_SESSION['google_drive_token'];
        }

        return $token;
    }

    public function getAccessToken(): ?array
    {
        $token = parent::getAccessToken() ?: $this->getStoredTokenAsArray();

        if ($this->isAccessTokenExpired() && $this->getRefreshToken()) {
            $token = $this->fetchAccessTokenWithRefreshToken($this->getRefreshToken());
        }

        if (!empty($_GET['code'])) {
            $authCode = trim($_GET['code']);
            $tokenWithCode = $this->fetchAccessTokenWithAuthCode($authCode);

            if (!isset($tokenWithCode['error'])) {
                $token = $tokenWithCode;
            }
        }

        if (!empty($token) && !isset($token['error'])) {
            if ($this->tokenService->isStored()) {
                file_put_contents($this->tokenService->getPath(), json_encode($token));
            } else {
                $_SESSION['google_drive_token'] = $token;
            }

            $this->setAccessToken($token);

            return $token;
        }

        return null;
    }
}