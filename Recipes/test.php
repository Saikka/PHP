<?php

class Ingredeint {};
$ingredient = new Ingredeint();

$ingredient->name ="boo";

$ingredient->name = ucfirst(strtolower($ingredient->name));

echo $ingredient->name;