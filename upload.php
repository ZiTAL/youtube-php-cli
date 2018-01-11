<?php
/**
 * Library Requirements
 *
 * 1. Install composer (https://getcomposer.org)
 * 2. On the command line, change to this directory (api-samples/php)
 * 3. Require the google/apiclient library
 *    $ composer require google/apiclient:~2.0
 */
if (!file_exists(__DIR__ . '/vendor/autoload.php'))
	throw new \Exception('please run "composer require google/apiclient:~2.0" in "' . __DIR__ .'"');

require_once __DIR__ . '/vendor/autoload.php';

$token_file = 'token.json';
$token_json = file_get_contents($token_file);

$OAUTH2_CLIENT_ID = 'CHANGE_OAUTH2_CLIENT_ID';
$OAUTH2_CLIENT_SECRET = 'CHANGE_OAUTH2_CLIENT_SECRET';

$video_title = 'Test title';
$video_desc = 'Test description';
$video_tags = array('tag1', 'tag2');
$video_path = "example.webm";

$client = new Google_Client();
$client->setClientId($OAUTH2_CLIENT_ID);
$client->setClientSecret($OAUTH2_CLIENT_SECRET);
$client->addScope('https://www.googleapis.com/auth/youtube');
$client->setAccessType('offline');
$client->setAccessToken($token_json);

$youtube = new Google_Service_YouTube($client);

if ($client->getAccessToken())
{
	// refresh token
    if($client->isAccessTokenExpired())
    {
        $newToken = json_decode($client->getAccessToken());
        $client->refreshToken($newToken->refresh_token);
        $newToken = json_decode($client->getAccessToken());
        $access_token = $newToken->access_token;
        $token_json = $client->getAccessToken();
        file_put_contents($token_file, $token_json);
    }

	// video metadata instance
    $snippet = new Google_Service_YouTube_VideoSnippet();
    $snippet->setTitle($video_title);
    $snippet->setDescription($video_desc);
    $snippet->setTags($video_tags);

    //$snippet->setCategoryId("22");

	// status instance
    $status = new Google_Service_YouTube_VideoStatus();

//    $status->privacyStatus = "public";
//    $status->privacyStatus = "unlisted";
    $status->privacyStatus = "private";

	// video resource instance
    $video = new Google_Service_YouTube_Video();
    $video->setSnippet($snippet);
    $video->setStatus($status);

    $chunkSizeBytes = 1 * 1024 * 1024;

    $client->setDefer(true);

    $insertRequest = $youtube->videos->insert("status,snippet", $video);

	// video media instance
    $media = new Google_Http_MediaFileUpload(
        $client,
        $insertRequest,
        'video/*',
        null,
        true,
        $chunkSizeBytes
    );
    $media->setFileSize(filesize($video_path));

	// read video file and upload
    $status = false;
    $handle = fopen($video_path, "rb");
    while (!$status && !feof($handle))
	{
      $chunk = fread($handle, $chunkSizeBytes);
      $status = $media->nextChunk($chunk);
    }

    fclose($handle);

    $client->setDefer(false);
}
