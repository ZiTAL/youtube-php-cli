# YOUTUBE API PHP CONSOLE #

Based on Google's official php api: https://github.com/google/google-api-php-client

## CREATE PROJECT ##

https://console.cloud.google.com/projectselector/iam-admin/settings

- Create: *youtube* project.

## ENABLE APIS AND SERVICES ##

https://console.developers.google.com

- click on *ENABLE APIS AND SERVICES*.
- find *Youtube Data API v3* and *ENABLE* it.

## CREATE CREDENTIALS ##

https://console.developers.google.com/apis/credentials

- click on *down arrow* from *Create credentials* and choose *OAuth client ID*.
- create *Product name shown to users* in *OAuth consent screen*, for example: *youtube-api*.
- repeat *Create credentials* step and choose *Web application* option.
- in *Authorised redirect URIs* field add *http://zital.youtube.eus:8080/index.php*
- click on *create*

## GET OAuth client CREDENTIALS ##
- copy the *client ID* and the *client secret* that are shown in the screen.

example:  
```
$OAUTH2_CLIENT_ID = '618761730062-2sf8fo1qphk3c69ir8enrbp2ou821f5e.apps.googleusercontent.com';
$OAUTH2_CLIENT_SECRET = '6ND6-eb4hjMyHqTxoKL95gmg';
```

## CODE ##

- Replace the *$OAUTH2_CLIENT_ID* and *$OAUTH2_CLIENT_SECRET* vars in *index.php* file.
- edit */etc/hosts* and add line, change IP for yours:
```
10.211.252.10       zital.youtube.eus
```

## RUN SERVER AND GET TOKEN ##
```
php -S zital.youtube.eus:8080
```
- open browser: http://zital.youtube.eus:8080
- click on *authorise access*.
- choose google account.
- allow app
- the token is saved in *token.json* file

## RENEW TOKEN THROUGH PHP ##

When the *token.json* is created we never use the google page to get the token, the token can be refreshed through code:

```
// ...
$token_file = 'token.json';

if ($client->getAccessToken())
{
    if($client->isAccessTokenExpired())
    {
        $newToken = json_decode($client->getAccessToken());
        $client->refreshToken($newToken->refresh_token);
        $newToken = json_decode($client->getAccessToken());
        $access_token = $newToken->access_token;
        $token_json = $client->getAccessToken();
        file_put_contents($token_file, $token_json);
    }
}
// ...
```

## UPLOAD EXAMPLE ##

- Replace the *$OAUTH2_CLIENT_ID* and *$OAUTH2_CLIENT_SECRET* vars in *upload.php* file.

This example uploads a video to your youtube account in private mode, check it in: https://www.youtube.com/my_videos?o=U

It can run through console to automate processes:

```
php upload.php
```

## LICENSE ##

All the code that I have writed has GPL 3.0 license

## ESKERRAK ##

Urko Zalduegi Biar
