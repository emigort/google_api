<?php

namespace App\Clients;

use App\Models\User;
use Google\Exception;
use Google\Service\Drive;
use Google\Service\Oauth2;
use Google\Service\YouTube;
use Google_Client;

class GoogleApi
{
    public Google_Client $client;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $config_json = base_path().'/'.env('GOOGLE_CONFIG_JSON');

        $app_name = 'Emilio test google';

        $this->client = new Google_Client();
        $this->client->setApplicationName($app_name);
        $this->client->setAuthConfig($config_json);
        $this->client->setAccessType('offline');
        $this->client->setApprovalPrompt('force');

        $this->client->setScopes([
            Oauth2::USERINFO_PROFILE,
            Oauth2::USERINFO_EMAIL,
            Oauth2::OPENID,
            Drive::DRIVE_METADATA,
            YouTube::YOUTUBE
        ]);

        $this->client->setIncludeGrantedScopes(true);
    }

    public function refreshUserToken(User $user): Google_Client
    {
        $access_token = stripslashes($user->google_access_token_json);
        $this->client->setAccessToken($access_token);

        if($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            $access_token = $this->client->getAccessToken();
            $this->client->setAccessToken($this->client->getAccessToken());
            $user->google_access_token_json = json_encode($access_token);
            $user->save();
        }
        return $this->client;
    }
}
