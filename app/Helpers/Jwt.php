<?php
/**
 * Created by PhpStorm
 * User: a_obidov
 * Date: 2025-07-31
 * Time: 12:34
 */

namespace App\Helpers;

class Jwt
{

  protected static function base64UrlEncode($str): string
  {
    return str_replace("/", "_", str_replace("+", "-", trim(base64_encode($str), "=")));
  }

  public static function encode($payload, $secret): string
  {
    $header = [
      "alg" => "HS256",  // the hashing algorithm
      "typ" => "JWT"  // the token type
    ];
    // three parts of token
    $encHeader = self::base64UrlEncode(json_encode($header));  // header
    $encPayload = self::base64UrlEncode(json_encode($payload));  // payload
    $hash = self::base64UrlEncode(hash_hmac("sha256", "$encHeader.$encPayload", $secret, true));  // signature

    return "$encHeader.$encPayload.$hash";
  }

}
