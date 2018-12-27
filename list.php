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

$OAUTH_FILE = 'oauth.json';
$OAUTH_FILE = file_get_contents($OAUTH_FILE);
$OAUTH_FILE = json_decode($OAUTH_FILE, true);

$TOKEN_FILE = 'token.json';

$REDIRECT = 'http://zital.youtube.eus:8080/index.php';

$client = new Google_Client();
$client->setClientId($OAUTH2_CLIENT_ID);
$client->setClientSecret($OAUTH2_CLIENT_SECRET);
$client->addScope('https://www.googleapis.com/auth/youtube');
$client->setAccessType('offline');
$client->setAccessToken($token_json);


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
  $youtube = new Google_Service_YouTube($client);
  $channelsResponse = $youtube->channels->listChannels('contentDetails', array(
    'mine' => 'true',
  ));

  $result = array();
  foreach ($channelsResponse['items'] as $channel)
  {
    // Extract the unique playlist ID that identifies the list of videos
    // uploaded to the channel, and then call the playlistItems.list method
    // to retrieve that list.
    $uploadsListId = $channel['contentDetails']['relatedPlaylists']['uploads'];

    $playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('snippet', array(
      'playlistId' => $uploadsListId,
      'maxResults' => 50
    ));

    foreach($playlistItemsResponse as $pi)
    {
/*      
      print_r($pi);
      exit();
*/      
      $result[] = array
      (
        'title' => $pi['snippet']['title'],
        'description' => $pi['snippet']['description'],
        'videoId' => $pi['snippet']['resourceId']['videoId'],
        'thumbnail' => $pi['snippet']['thumbnails']['default']['url'],
        'date' => $pi['snippet']['publishedAt'],
        'link' => "https://www.youtube.com/watch?v={$pi['snippet']['resourceId']['videoId']}"
      );
    }
  }
  echo "<pre>";
  print_r($result);
  echo "</pre>";
}

