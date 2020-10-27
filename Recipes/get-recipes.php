<?php

require 'connect.php';

class recipe {}
class Ingredient {}
class Step {}

$sql = "SELECT * FROM recipe;";
$recipes = [];

if($result = mysqli_query($con,$sql))
{
  while($row = mysqli_fetch_assoc($result))
  {
    $recipe = new recipe;
	$recipe->id = (int)$row['id_recipe'];
	$recipe->name = $row['name'];
	$recipe->image = $row['image'];
	$recipe->difficulty = $row['difficulty'];
	$recipe->category_id = (int)$row['category_id'];
	$recipe->country_id = (int)$row['country_id'];
	$recipe->ingredients = [];
	$recipe->steps = [];
	$sql_ingredients = "SELECT ingredient.id_ingredient, name, amount, units FROM ingredient INNER JOIN recipe_has_ingredient ON ingredient.id_ingredient = recipe_has_ingredient.id_ingredient WHERE recipe_has_ingredient.id_recipe = {$recipe->id}";
	if($result_ingredients = mysqli_query($con,$sql_ingredients))
	{
		while($row = mysqli_fetch_assoc($result_ingredients))
		{
			$ingt = new Ingredient;
			$ingt->id = (int)$row['id_ingredient'];
			$ingt->name = $row['name'];
			$ingt->amount = (int)$row['amount'];
			$ingt->units = $row['units'];
			array_push($recipe->ingredients, $ingt);
		}
	}
	$sql_steps = "SELECT id_step, action FROM step WHERE id_recipe = {$recipe->id}";
	if($result_steps = mysqli_query($con,$sql_steps))
	{
		while($row = mysqli_fetch_assoc($result_steps))
		{
			$step = new Step;
			$step->id = (int)$row['id_step'];
			$step->description = $row['action'];
			array_push($recipe->steps, $step);
		}
	}
	array_push($recipes, $recipe);
  }
  echo json_encode($recipes);
}
else
{
  http_response_code(404);
}

?>