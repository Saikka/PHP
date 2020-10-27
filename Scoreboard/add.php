<?php
require 'connect.php';

// Get the posted data.
$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
  // Extract the data.
  $request = json_decode($postdata);
	

  // Validate.
  if(trim($request->name) === '' || (int)$request->score < 1)
  {
    return http_response_code(400);
  }
	
  // Sanitize.
  $name = mysqli_real_escape_string($con, trim($request->name));
  $score = mysqli_real_escape_string($con, (int)$request->score);
  class Score { }

  // Store.
  $sql = "INSERT INTO `score`(`name`,`score`) VALUES ('{$name}','{$score}')";

  if(mysqli_query($con,$sql))
  {
    http_response_code(201);
    $scoreItem = new Score;
	$scoreItem->id = mysqli_insert_id($con);
	$scoreItem->name = $name;
	$scoreItem->score = $score;
    echo json_encode($scoreItem);
  }
  else
  {
    http_response_code(422);
  }
}