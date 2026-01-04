<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class JWTAuthentication extends BaseController
{
  public function encode($data = array(), $longexpiry = false)
  {
    $issuedAt   = new DateTimeImmutable();
    $expire     = $issuedAt->modify($longexpiry ? '+24 hour' : '+1 hour')->getTimestamp();      // Add 60 seconds

    $payloadData = [
      'iat'  => $issuedAt->getTimestamp(),         // Issued at: time when the token was generated
      'nbf'  => $issuedAt->getTimestamp(),         // Not Before
      'iss'  => SERVER_NAME,                       // Issuer
      'exp'  => $expire,                           // Expiry: time after which token is not valid
      ...$data
    ];

    return JWT::encode(
      $payloadData,
      JWT_SECRET_KEY,
      'HS512'
    );
  }
  public function decode($jwt)
  {
    return JWT::decode($jwt, new Key(JWT_SECRET_KEY, 'HS512'));
  }
  public function validate()
  {
    $headers = apache_request_headers();
    if (!isset($headers['Authorization'])) {
      header("HTTP/1.1 401 Unauthorized");
      $this->sendOutput(array(
        'code' => 401,
        'status' => 'error',
        'message' => 'Unauthorized Access!!, No Access Token present in the request.'
      ));
    } else {
      try {
        $data = $this->decode($headers['Authorization']);
        return $data;
       } catch (ExpiredException $e) {
        // provided JWT is trying to be used after "exp" claim.
        header('HTTP/1.1 401 Unauthorized');
        $this->sendOutput(array(
          'code' => 401,
          'status' => 'error',
          'message' => 'Unauthorized Access!!, Access token expired.'
        ));
       } catch (SignatureInvalidException $e) {
        // provided JWT is trying to be used after "exp" claim.
        header('HTTP/1.1 401 Unauthorized');
        $this->sendOutput(array(
          'code' => 401,
          'status' => 'error',
          'message' => 'Unauthorized Access!!, Access token expired.'
        ));
      } catch (Exception $e) {
        header('HTTP/1.1 401 Unauthorized');
        $this->sendOutput(array(
          'code' => 401,
          'status' => 'error',
          'message' => 'Unauthorized Access!!, Invalid Access token.'
        ));
      }
    }
  }
}
