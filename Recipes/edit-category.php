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
		// Get the posted data.
		$postdata = file_get_contents("php://input");

		if(isset($postdata) && !empty($postdata))
		{
		  // Extract the data.
		  $request = json_decode($postdata);
			
		  // Validate.
		  if ((int)$request->id < 1 || trim($request->name) == '') {
			echo 'Incorrect data!';
			return http_response_code(400);
		  }
			
		  // Sanitize.
		  $id    = mysqli_real_escape_string($con, (int)$request->id);
		  $name = mysqli_real_escape_string($con, trim($request->name));
		  $type = mysqli_real_escape_string($con, trim($request->type));
		  // Update.
		  $sql = "UPDATE {$type} SET `name`= '$name' WHERE `id` = '{$id}' LIMIT 1";

		  if(mysqli_query($con, $sql))
		  {
			http_response_code(200);
			$category = [
			  'name' => $name,
			  'type' => $type,
			  'id'   => mysqli_insert_id($con)
			];
			echo json_encode($category);
		  }
		  else
		  {
			echo 'Failed to edit category!';
			return http_response_code(422);
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
