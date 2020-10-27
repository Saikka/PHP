<?php

require 'connect.php';

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	$request = json_decode($postdata);
	if(trim($request->login) === '' || trim($request->pwd) === '' || trim($request->pwd_confirm) === '')
	  {
		return http_response_code(400);
	  } 
	if(trim($request->pwd) !== trim($request->pwd_confirm))
	  {
		return http_response_code(400);
	  }
	  
	$login = mysqli_real_escape_string($con, trim($request->login));
	
	$try_login = mysqli_query($con, "SELECT * FROM login WHERE login = '{$login}'");
	if($try_login->num_rows !== 0) {
		return http_response_code(400);
	}
	
	$pwd = mysqli_real_escape_string($con, trim($request->pwd));
	$pwd_hash = password_hash($pwd, PASSWORD_BCRYPT);
	
	$sql = "INSERT INTO login (login, pwd) VALUES ('{$login}', '{$pwd_hash}');";
	if(mysqli_query($con,$sql))
	{
		http_response_code(201);
	}
}