<?php

require 'connect.php';

class Category {}

$sql = "SELECT * FROM category UNION SELECT * FROM country;";
$cats = [];

if($result = mysqli_query($con,$sql))
{
  while($row = mysqli_fetch_assoc($result))
  {
    $tmp = new Category;
	$tmp->id = (int)$row['id'];
	$tmp->name = $row['name'];
	$tmp->type = $row['type'];
	array_push($cats, $tmp);
  }
  echo json_encode($cats);
}
else
{
  http_response_code(404);
}

?>