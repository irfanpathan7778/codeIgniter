<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '../vendor/autoload.php';

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
class JwtLib {
    private $key = "s3cR3t@K3y!987654321"; 

    public function encode($data) {
        return JWT::encode($data, $this->key, 'HS256');
    }

    public function decode($token) {
        try {
            return JWT::decode($token, $this->key, array('HS256'));
        } catch (Exception $e) {
            return false;
        }
    }
      public function verify_jwt($token) {
            try {
                $secret_key = $this->key;
                return JWT::decode($token, new Key($secret_key, 'HS256'));
            } catch (Exception $e) {
                return false;
            }
        }
}
