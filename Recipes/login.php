<?php

require 'connect.php';
require "vendor/autoload.php";
use \Firebase\JWT\JWT;

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	$request = json_decode($postdata);
	if(trim($request->login) === '' || trim($request->pwd) === '')
	  {
		echo 'Login and password have be not empty!';
		return http_response_code(400);
	  }
	  
	$login = mysqli_real_escape_string($con, trim($request->login));
	$pwd = mysqli_real_escape_string($con, trim($request->pwd));
	
	//$login = 'test';
	//$pwd = 'test';
	
	$try_login = mysqli_query($con, "SELECT * FROM login WHERE login = '{$login}'");
	if($try_login->num_rows !== 0) {
		$row = mysqli_fetch_assoc($try_login);
		if (password_verify($pwd, $row['pwd'])) {
			$secret_key = "SECRET UNDER THE SURFACE";
			$issuer_claim = "THE_ISSUER"; // this can be the servername
			$audience_claim = "THE_AUDIENCE";
			$issuedat_claim = time(); // issued at
			$notbefore_claim = $issuedat_claim + 10; //not before in seconds
			$expire_claim = $issuedat_claim + 3660; // expire time in seconds
			$token = array(
				"iss" => $issuer_claim,
				"aud" => $audience_claim,
				"iat" => $issuedat_claim,
				"nbf" => $notbefore_claim,
				"exp" => $expire_claim,
				"data" => array(
					"id" => $row['id_login'],
 					"login" => $row['login'],
			));
			
			http_response_code(200);
			$jwt = new \Firebase\JWT\JWT;
			$jwt::$leeway = 60;
			$jwt = JWT::encode($token, $secret_key);
			echo json_encode(
				array(
					"message" => "Successful login.",
					"token" => $jwt,
					"login" => $login
				));
		} else {
			echo 'Wrong password!';
			return http_response_code(400);
		}
	} else {
		echo 'No user with such login found!';
		return http_response_code(400);
	}
}