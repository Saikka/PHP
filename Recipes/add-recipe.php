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

		if(isset($postdata) && !empty($postdata))
		{
		  $request = json_decode($postdata);
		  $ingredients = [];
		  class Recipe {};
		  
		  // Validate.
		  if(trim($request->name) === '' || trim($request->image) === '' || (int)$request->difficulty < 1 || (int)$request->difficulty > 5)
		  {
			echo 'Incorrect data!';
			return http_response_code(400);
		  }  
			
		  // Sanitize.
		  $name = mysqli_real_escape_string($con, trim($request->name));
		  $image = mysqli_real_escape_string($con, trim($request->image));
		  $difficulty = mysqli_real_escape_string($con, (int)$request->difficulty);
		  (int)$request->category_id == 0 ? $category_id = "NULL" : $category_id = mysqli_real_escape_string($con, (int)$request->category_id);
		  (int)$request->country_id == 0 ? $country_id = "NULL" : $country_id = mysqli_real_escape_string($con, (int)$request->country_id);
		  $ingredients = $request->ingredients; 
		  $steps = $request->steps;
		  
		  if ($category_id != "NULL") {
			$try_category = "SELECT * FROM category WHERE id = {$category_id}";
			if (!mysqli_query($con,$try_category)) {
			  echo 'Incorrect data!';
			  return http_response_code(400);
			}
		  }	  
		  if ($country_id != "NULL") {
					  $try_country = "SELECT * FROM country WHERE id = {$country_id}";
			if (!mysqli_query($con,$try_country)) {
			  echo 'Incorrect data!';
			  return http_response_code(400);
			}
		  }
			
		  // Store.
		  $sql = "INSERT INTO recipe (name, image, difficulty, category_id, country_id) VALUES ('{$name}','{$image}', '{$difficulty}', {$category_id}, {$country_id})";
		  
		  if(mysqli_query($con,$sql))
		  {
			$recipe = new Recipe;
			$recipe->id = (int)mysqli_insert_id($con);
			$recipe->name = $name;
			$recipe->image = $image;
			$recipe->difficulty = $difficulty;
			$recipe->category_id = (int)$category_id;
			$recipe->country_id = (int)$country_id;
			$recipe->ingredients = [];
			$recipe->steps = [];
			//adding ingredients
			if (count($ingredients) > 0) {
			  foreach ($ingredients as $ingredient) {
				  $ingredient->name = ucfirst(strtolower($ingredient->name));
			  }
			  $sql = "SELECT * FROM ingredient";
			  $result = mysqli_query($con,$sql);
			  $ingredients_names = [];
			  while($row = mysqli_fetch_assoc($result))
			  {
				array_push($ingredients_names, $row['name']);
			  }
			  foreach ($ingredients as $ingredient) {
				  //check if it's new ingredient
				if (!in_array($ingredient->name, $ingredients_names)) {
					$sql_ingr = "INSERT INTO ingredient (name) VALUES ('{$ingredient->name}')";
					$result = mysqli_query($con,$sql_ingr);
				}
				$sql_ingr = "SELECT id_ingredient, name FROM ingredient WHERE name = '{$ingredient->name}'";
				if ($res = mysqli_query($con,$sql_ingr)) {
					while($row = mysqli_fetch_assoc($res)){
						$id = (int)$row['id_ingredient'];
					}
					$sql_ingr = "INSERT INTO recipe_has_ingredient (`id_recipe`, `id_ingredient`, `amount`, `units`) VALUES ({$recipe->id}, {$id}, '{$ingredient->amount}', '{$ingredient->units}')";
					if(mysqli_query($con,$sql_ingr)) {
						array_push($recipe->ingredients, $ingredient);
					} else {
						echo 'Failed to add ingredients!';
						http_response_code(422);
					}
				} else {
					http_response_code(422);
				}
			  }
			}
			//adding instructions
			if (count($steps) > 0) {
			  foreach ($steps as $step) {
				$sql_step = "INSERT INTO step (`action`, `id_recipe`) VALUES ('{$step->description}', {$recipe->id})";
				$result_steps = mysqli_query($con,$sql_step);
				array_push($recipe->steps, $step);
				if (!$result_steps) {
					echo 'Failed to add steps!';
					http_response_code(422);
				}
			  }
			}
			http_response_code(201);
			echo json_encode($recipe);
		  }
		  else
		  {
			echo 'Failed to create recipe!';
			http_response_code(422);
		  }
		}
	}catch (Exception $e){
		echo 'Access denied!';
		http_response_code(401);
	}
}