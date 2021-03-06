<?php

// Get the car data
$car_id = filter_input(INPUT_POST, 'car_id', FILTER_VALIDATE_INT);
$model_id = filter_input(INPUT_POST, 'model_id', FILTER_VALIDATE_INT);
$fuel_id = filter_input(INPUT_POST, 'fuel_id', FILTER_VALIDATE_INT);
$name = filter_input(INPUT_POST, 'name');
$description = filter_input(INPUT_POST, 'description');
$price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);


// Validate inputs
if ($car_id == NULL || $car_id == FALSE || $model_id == NULL ||
$model_id == FALSE || $fuel_id == NULL || $fuel_id == FALSE || empty($name) ||empty($description) ||
$price == NULL || $price == FALSE) {
$error = "Invalid car data . Check all fields and try again.";
include('error.php');
} else {

/**************************** Image upload ****************************/

$imgFile = $_FILES['image']['name'];
$tmp_dir = $_FILES['image']['tmp_name'];
$imgSize = $_FILES['image']['size'];
$original_image = filter_input(INPUT_POST, 'original_image');

if ($imgFile) {
$upload_dir = 'image_uploads/'; // upload directory	
$imgExt = strtolower(pathinfo($imgFile, PATHINFO_EXTENSION)); // get image extension
$valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); // valid extensions
$image = rand(1000, 1000000) . "." . $imgExt;
if (in_array($imgExt, $valid_extensions)) {
if ($imgSize < 5000000) {
if (filter_input(INPUT_POST, 'original_image') !== "") {
unlink($upload_dir . $original_image);                    
}
move_uploaded_file($tmp_dir, $upload_dir . $image);
} else {
$errMSG = "Sorry, your file is too large it should be less then 5MB";
}
} else {
$errMSG = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
}
} else {
// if no image selected the old image remain as it is.
$image = $original_image; // old image from database
}

/************************** End Image upload **************************/

// If valid, update the car in the database
require_once('database.php');


$query = 'UPDATE cars
SET modelID = :model_id,
fuelID = :fuel_id,
name = :name,
description = :description,
price = :price,
image = :image
WHERE carID = :car_id';
$statement = $db->prepare($query);
$statement->bindValue(':model_id', $model_id);
$statement->bindValue(':fuel_id', $fuel_id);
$statement->bindValue(':name', $name);
$statement->bindValue(':description', $description);
$statement->bindValue(':price', $price);
$statement->bindValue(':image', $image);
$statement->bindValue(':car_id', $car_id);
$statement->execute();
$statement->closeCursor();

include('index.php');

}

header("Location: index.php");
?>

