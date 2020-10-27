<?php

require 'connect.php';

class Score { }
    
$sql = "SELECT * FROM score;";
$scores = [];

if($result = mysqli_query($con,$sql))
{
  while($row = mysqli_fetch_assoc($result))
  {
    $tmp = new Score;
	$tmp->id = $row['id'];
	$tmp->name = $row['name'];
	$tmp->score = (int)$row['score'];
	array_push($scores, $tmp);
  }
  echo json_encode($scores);
}
else
{
  http_response_code(404);
}
