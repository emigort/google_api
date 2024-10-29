<?php

namespace App\Http\Controllers;

use App\Clients\GoogleApi;
use App\Models\User;
use Google\Service\OAuth2;
use Google_Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    private Google_Client $client;

    public function __construct()
    {
        $this->client = (new GoogleApi())->client;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAuthUrl(Request $request): JsonResponse
    {
        return response()->json(['url' => $this->client->createAuthUrl()]);
    }

    public function postLogin(Request $request): JsonResponse
    {
        $auth_code = urldecode($request->auth_code);
        $access_token = $this->client->fetchAccessTokenWithAuthCode($auth_code);
        $access_token_encode = json_encode($access_token);
        $this->client->setAccessToken($access_token_encode);

        // Get user's data from google
        $service = new OAuth2($this->client);
        $user_from_google = $service->userinfo->get();

        $user = User::where('provider_name', 'google')
            ->where('provider_id', $user_from_google->id)
            ->first();

        if (!$user) {
            $user = new User();
            $user->name = $user_from_google->name;
            $user->email = $user_from_google->email;
            $user->provider_id = $user_from_google->id;
            $user->provider_name = 'google';
            $user->google_access_token_json = $access_token_encode;
            $user->save();
        } else {
            $user->google_access_token_json = $access_token_encode;
            $user->save();
        }
        return response()->json($access_token, 201);
    }
}
