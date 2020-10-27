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

		$sql = "SELECT * FROM `recipe_has_ingredient` WHERE `id_recipe` ='{$id}'";

		if(mysqli_query($con, $sql))
		{
		  $sql = "DELETE FROM `recipe_has_ingredient` WHERE `id_recipe` ='{$id}'";
		  mysqli_query($con, $sql);
		}
		
		$sql = "DELETE FROM `step` WHERE `id_recipe` ='{$id}'";
		mysqli_query($con, $sql);

		$sql = "DELETE FROM recipe WHERE `id_recipe` ='{$id}' LIMIT 1";
		
		if(mysqli_query($con, $sql))
		{
		  http_response_code(201);
		  echo json_encode((int)$id);
		}
		else
		{
		  echo 'Failed to delete recipe';
		  return http_response_code(422);
		}
	}catch (Exception $e){
		echo 'Access denied!';
		http_response_code(401);
	}
}