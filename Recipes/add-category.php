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
		$postdata = file_get_contents("php://input");
        // Access is granted. Add code of the operation here 
        if(isset($postdata) && !empty($postdata))
		{
		  $request = json_decode($postdata);
		  
		  if(trim($request->name) === '' || trim($request->type) === '')
		  {
			echo 'Incorrect data!';
			return http_response_code(400);
		  }
			
		  $name = mysqli_real_escape_string($con, trim($request->name));
		  $type = mysqli_real_escape_string($con, trim($request->type));
			
		  $sql = "INSERT INTO {$type} (`name`, `type`) VALUES ('{$name}','{$type}')";
		  
		  if(mysqli_query($con,$sql))
		  {
			http_response_code(201);
			$category = [
			  'name' => $name,
			  'type' => $type,
			  'id'   => mysqli_insert_id($con)
			];
			echo json_encode($category);
		  }
		  else
		  {
			echo 'Failed to create category!';
			http_response_code(422);
		  }
		} else {
			echo 'Data cannot be empty!';
			return http_response_code(422);
		}
    }catch (Exception $e){
		echo 'Access denied!';
		http_response_code(401);
	}
}