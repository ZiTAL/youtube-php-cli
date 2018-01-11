<?php
if (!file_exists(__DIR__ . '/vendor/autoload.php'))
    throw new \Exception('please run "composer require google/apiclient:~2.0" in "' . __DIR__ .'"');

require_once __DIR__ . '/vendor/autoload.php';

$OAUTH2_CLIENT_ID = 'CHANGE_OAUTH2_CLIENT_ID';
$OAUTH2_CLIENT_SECRET = 'CHANGE_CLIENT_SECRET ';
$REDIRECT = 'http://zital.youtube.eus:8080/index.php';
$TOKEN_FILE = 'token.json';

$client = new Google_Client();
$client->setClientId($OAUTH2_CLIENT_ID);
$client->setClientSecret($OAUTH2_CLIENT_SECRET);
$client->setScopes('https://www.googleapis.com/auth/youtube');
$client->setRedirectUri($REDIRECT);
$client->setAccessType('offline');
$client->setApprovalPrompt('force');

$youtube = new Google_Service_YouTube($client);

if (isset($_GET['code']))
{
    $client->authenticate($_GET['code']);
	$token = $client->getAccessToken();
}

if (isset($token))
    $client->setAccessToken($token);

// Check to ensure that the access token was successfully acquired.
if ($client->getAccessToken())
{
	$token = $client->getAccessToken();
	$token_json = json_encode($token, JSON_PRETTY_PRINT);
	file_put_contents($TOKEN_FILE, $token_json);
}
else
{
    $state = mt_rand();
    $client->setState($state);
    $authUrl = $client->createAuthUrl();

    $htmlBody = <<<END
  <h2>GET YOUTUBE API TOKEN</h2>
  <h3>Authorization Required</h3>
  <p>You need to <a href="$authUrl">authorise access</a> before proceeding.<p>
END;
}
?>
<!doctype html>
<html>
<head>
    <title>GET YOUTUBE API TOKEN</title>
</head>
<body>
<?php if(isset($token)): ?>
	TOKEN SAVED IN <strong><?php echo $TOKEN_FILE; ?></strong>:
	<pre>
	<?php print_r($token_json); ?>
	</pre>
<?php elseif(isset($htmlBody)): ?>
	<?php echo $htmlBody; ?>
<?php endif; ?>
</pre>
</body>
</html>
