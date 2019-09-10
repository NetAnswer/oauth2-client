<?php

namespace League\OAuth2\Client\Provider;

class Microsoft extends IdentityProvider
{
    public $scopes = array('wl.basic', 'wl.emails');
    public $responseType = 'json';

    public function urlAuthorize()
    {
        return 'https://login.live.com/oauth20_authorize.srf';
    }

    public function urlAccessToken()
    {
        return 'https://login.live.com/oauth20_token.srf';
    }

    public function urlUserDetails(\League\OAuth2\Client\Token\AccessToken $token)
    {
        return 'https://apis.live.net/v5.0/me?access_token='.$token;
    }

    public function userDetails($response, \League\OAuth2\Client\Token\AccessToken $token)
    {
        $client = $this->getHttpClient();
        $client->setBaseUrl($this->urlUserDetails($token));
        $request = $client->get()->send();
        $info = $request->getInfo();
        $imageUrl = null;

        $user = new User;

        $user->uid = $response->id;
        $user->name = $response->name;
        $user->firstName = $response->first_name;
        $user->lastName = $response->last_name;
        $user->email = isset($response->emails->preferred) ? $response->emails->preferred : null;
        $user->imageUrl = $imageUrl;
        $user->urls = $response->link.'/cid-'.$response->id;

        return $user;
    }

    public function userUid($response, \League\OAuth2\Client\Token\AccessToken $token)
    {
        return $response->id;
    }

    public function userEmail($response, \League\OAuth2\Client\Token\AccessToken $token)
    {
        return isset($response->emails->preferred) && $response->emails->preferred ? $response->emails->preferred : null;
    }

    public function userScreenName($response, \League\OAuth2\Client\Token\AccessToken $token)
    {
        return array($response->first_name, $response->last_name);
    }
}
