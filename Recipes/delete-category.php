<?php

require 'connect.php';
require "vendor/autoload.php";
use \Firebase\JWT\JWT;

$secret_key = "SECRET UNDER THE SURFACE";

$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$arr = explode(" ", $authHeader);
$jwt = $arr[1];

if($jwt){
    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
		$id = ($_GET['id'] !== null && (int)$_GET['id'] > 0)? mysqli_real_escape_string($con, (int)$_GET['id']) : false;
		$type = $_GET['type'];

		if ($type === 'category') {
			$sql = "UPDATE recipe SET `category_id`= Null WHERE `category_id` = '{$id}'";

		} else {
			$sql = "UPDATE recipe SET `country_id`= Null WHERE `country_id` = '{$id}'";
		}

		mysqli_query($con, $sql);

		$sql = "DELETE FROM {$type} WHERE `id` ='{$id}' LIMIT 1";
		
		if(mysqli_query($con, $sql))
		  {
			http_response_code(200);
			$category = [
			  'type' => $type,
			  'id'   => (int)$id
			];
			echo json_encode($category);
		  }
		  else
		  {
			echo 'Failed to delete category!';
			return http_response_code(422);
		  }  
		
    }catch (Exception $e){
		echo 'Access denied!';
		http_response_code(401);
	}
}