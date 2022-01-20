<?php

use DocuSign\eSign\Client\ApiClient;
use DocuSign\eSign\Client\Auth\OAuthToken;
use DocuSign\eSign\Configuration;

include('vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$authServer = $_ENV['JWT_AUTHORIZATION_SERVER'];
$privKeyFile = $_ENV['JWT_PRIVATE_KEY_FILE'];
$scopes = $_ENV['JWT_DEFAULT_SCOPES'];
$client_id = $_ENV['INTEGRATOR_KEY'];
$user_id = $_ENV['USER_ID'];

$config = new Configuration();
$apiClient = new ApiClient($config);

$apiClient->getOAuth()->setOAuthBasePath($authServer);
$privateKey = file_get_contents(
  $_SERVER['DOCUMENT_ROOT']
    . '/'
    . $privKeyFile,
  true
);

try
{
  $response_api = $apiClient->requestJWTUserToken(
    $client_id,
    $user_id,
    $privateKey,
    $scopes,
  );

  /**
   * @var OAuthToken
   */
  $token = $response_api[0];
  $response = new stdClass;
  $response->access_token = $token->getAccessToken();
  $response->expires_in = $token->getExpiresIn();
  $response->refresh_token = $token->getRefreshToken();
  $response->scope = $token->getScope();
  $response->token_type = $token->getTokenType();
  $response->integration_key = $client_id;
  header('Content-Type: application/json');
  echo json_encode($response);
}
catch (\Throwable $th)
{
  // we found consent_required in the response body meaning first time consent is needed
  if (strpos($th->getMessage(), "consent_required") !== false)
  {
    $_SESSION['consent_set'] = true;
    $authorizationURL = 'https://account-d.docusign.com/oauth/auth?' . http_build_query([
      'scope'         => $scopes,
      'redirect_uri'  => $_ENV['APP_URL'],
      'client_id'     => $client_id,
      'state'         => $_SESSION['oauth2state'],
      'response_type' => 'code'
    ]);
    header('Location: ' . $authorizationURL);
  }
}
