<?php
if (!file_exists(__DIR__ . '/vendor/autoload.php'))
    throw new \Exception('please run "composer require google/apiclient:~2.0" in "' . __DIR__ .'"');

require_once(__DIR__."/vendor/autoload.php");

class YoutubeUploader
{
	public $params;

	// 1 * 1024 * 1024;
	private $chunk_size_bytes = 1048576;


	private $oauth_file = 'oauth.json';
	private $oauth;

	private $token_file = 'token.json';
	private $token;

	private $client;
	private $youtube;
	private $snippet;
	private $status;
	private $video;

        public function __construct($params)
        {
		$this->setPublicParams($params);
		$this->setPrivateParams();
		$this->setClient();
		$this->youtube = new Google_Service_YouTube($this->client);
		$this->tokenRefresh();
		$this->setSnippet();
		$this->setStatus();
		$this->setVideo();
        }

	private function setPublicParams($params)
	{
		$this->params = $params;
	}

	private function setPrivateParams()
	{
		// dirname(__FILE__)
		$this->oauth_file = __DIR__."/".$this->oauth_file;
		$this->token_file = __DIR__."/".$this->token_file;

		$oauth = file_get_contents($this->oauth_file);
		$this->oauth = json_decode($oauth, true);

		$this->token = file_get_contents($this->token_file);
	}

	private function setClient()
	{
		$client = new Google_Client();
		$client->setClientId($this->oauth['OAUTH2_CLIENT_ID']);
		$client->setClientSecret($this->oauth['OAUTH2_CLIENT_SECRET']);
		$client->addScope('https://www.googleapis.com/auth/youtube');
		$client->setAccessType('offline');
		$client->setApprovalPrompt('force');
		$client->setAccessToken($this->token);

		$this->client = $client;
	}

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

	private function setSnippet()
	{
		$snippet = new Google_Service_YouTube_VideoSnippet();
		$snippet->setTitle($this->params['title']);
		$snippet->setDescription($this->params['description']);
		$snippet->setTags($this->params['tags']);
		$snippet->setCategoryId($this->params['category']);

		$this->snippet = $snippet;
	}

	// erabiltzaile motaren arabera aukeratu beharrekoa
	private function setStatus()
	{
		$status = new Google_Service_YouTube_VideoStatus();

//		$status->privacyStatus = "public";
//		$status->privacyStatus = "unlisted";
//		$status->privacyStatus = "private";

		$status->privacyStatus = $this->params['status'];

		$this->status = $status;
	}

	private function setVideo()
	{
		$video = new Google_Service_YouTube_Video();
		$video->setSnippet($this->snippet);
		$video->setStatus($this->status);

		$this->video = $video;
	}

	public function upload()
	{
		$this->client->setDefer(true);

		$insertRequest = $this->youtube->videos->insert("status,snippet", $this->video);

		// video media instance
		$media = new Google_Http_MediaFileUpload
		(
			$this->client,
			$insertRequest,
			'video/*',
			null,
			true,
			$this->chunk_size_bytes
		);

		$media->setFileSize(filesize($this->params['video_path']));

		// read video file and upload
		$status = false;
		$handle = fopen($this->params['video_path'], "rb");
		while (!$status && !feof($handle))
		{
			$chunk = fread($handle, $this->chunk_size_bytes);
			$status = $media->nextChunk($chunk);
		}
		fclose($handle);

		$this->client->setDefer(false);
	}
}
/*
$params = array
(
	'title' => 'title example',
	'description' => 'description example',
	'tags' => array
	(
		'tag1',
		'tag2'
	),
	'status' => 'private',
	'video_path' => '/net/localhost/biltegia/web-server/uploads/Earth_Zoom_In.mov'
);
$y = new YoutubeUploader($params);
$y->upload();
*/
