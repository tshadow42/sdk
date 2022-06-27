<?php

define("CLIENT_ID", '67dc2be521bec2ff862d3ab057de216b');
define("CLIENT_SECRET", '04054cf433eeb3976252c81b6d657fda');

class FacebookProvider
{
// Facebook oauth: exchange code with token then get user info
    public static function callback()
    {
        $token = FacebookProvider::getToken("https://graph.facebook.com/v13.0/oauth/access_token", FB_CLIENT_ID, FB_CLIENT_SECRET);
        $user = FacebookProvider::getFbUser($token);
        $unifiedUser = (fn () => [
            "id" => $user["id"],
            "name" => $user["name"],
            "email" => $user["email"],
            "firstName" => $user['first_name'],
            "lastName" => $user['last_name'],
        ])();
        var_dump($unifiedUser);
    }

    private static function getFbUser($token)
    {
        $context = stream_context_create([
            "http"=>[
                "header"=>"Authorization: Bearer {$token}"
            ]
        ]);
        $response = file_get_contents("https://graph.facebook.com/v13.0/me?fields=last_name,first_name,email", false, $context);
        if (!$response) {
            echo $http_response_header;
            return;
        }
        return json_decode($response, true);
    }

    private static function getToken($baseUrl, $clientId, $clientSecret)
    {
        ["code"=> $code, "state" => $state] = $_GET;
        $queryParams = http_build_query([
            "client_id"=> $clientId,
            "client_secret"=> $clientSecret,
            "redirect_uri"=>"https://localhost/fb_oauth_success",
            "code"=> $code,
            "grant_type"=>"authorization_code",
        ]);

        $url = $baseUrl . "?{$queryParams}";
        $response = file_get_contents($url);
        if (!$response) {
            echo $http_response_header;
            return;
        }
        ["access_token" => $token] = json_decode($response, true);

        return $token;
    }
}