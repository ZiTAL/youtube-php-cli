# YOUTUBE PHP CLI #

Based on Google's official php api: https://github.com/google/google-api-php-client

## CREATE PROJECT ##

https://developers.google.com/youtube/registering_an_application

- Create: *youtube* project.

## ENABLE APIS AND SERVICES ##

https://console.developers.google.com/apis/api/youtube.googleapis.com/overview

## CREATE CREDENTIALS ##

**IMPORTANT**: to add URL-s like redirect domain etc... when you insert the URL push **ENTER** to add it in the list.

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
OAUTH2_CLIENT_ID = '618761730062-2sf8fo1qphk3c69ir8enrbp2ou821f5e.apps.googleusercontent.com';
OAUTH2_CLIENT_SECRET = '6ND6-eb4hjMyHqTxoKL95gmg';
```

## CODE ##

- Replace the *OAUTH2_CLIENT_ID* and *OAUTH2_CLIENT_SECRET* vars in *oauth.json* file.
- Add redirect url to **oauth.json** file.

for example:

```
{
    "OAUTH2_CLIENT_ID": "618761730062-2sf8fo1qphk3c69ir8enrbp2ou821f5e.apps.googleusercontent.com",
    "OAUTH2_CLIENT_SECRET": "6ND6-eb4hjMyHqTxoKL95gmg",
    "REDIRECT": "http://zital.eus:8080/index.php"
}
```

- edit */etc/hosts* and add line:
```
127.0.0.1       zital.eus
```

## RUN SERVER AND GET TOKEN ##
```
php -S zital.eus:8080
```
- open browser: http://zital.eus:8080
- click on *authorise access*.
- choose google account.
- allow app
- the token is saved in *token.json* file

## RENEW TOKEN THROUGH PHP ##

When the *token.json* is created we never use the google page to get the token, the token can be refreshed through code -youtube_uploader.php-:

```
// ...
    private function tokenRefresh()
    {
        if($this->client->isAccessTokenExpired())
        {
            $newToken = $this->client->getAccessToken();
            $this->client->refreshToken($newToken['refresh_token']);
            $newToken = $this->client->getAccessToken();

            file_put_contents($this->token_file, json_encode($newToken, JSON_PRETTY_PRINT));
        }
    }
// ...
```

## UPLOAD EXAMPLE ##

This example uploads a example video to your youtube account in private mode, check it in: https://www.youtube.com/my_videos?o=U

It can run through console to automate processes:

```
php upload.php
```

## LICENSE ##

All the code that I have written has GPL 3.0 license

## ESKERRAK ##

Urko Zalduegi Biar
