<?php
require_once('database.php');
// Get fuel ID
if (!isset($fuel_id)) {
    $fuel_id = filter_input(INPUT_GET, 'fuel_id', 
    FILTER_VALIDATE_INT);
    if ($fuel_id == NULL || $fuel_id == FALSE) {
    $fuel_id = 1;
    }
    }

// Get all fuels
$queryAllFuels = 'SELECT * FROM fuel
ORDER BY fuelID';
$statement1 = $db->prepare($queryAllFuels);
$statement1->execute();
$fuels = $statement1->fetchAll();
$statement1->closeCursor();

// Get model ID
if (!isset($model_id)) {
    $model_id = filter_input(INPUT_GET, 'model_id', 
    FILTER_VALIDATE_INT);
    if ($model_id == NULL || $model_id == FALSE) {
    $model_id = 1;
    }
    }
    
    
// Get all models
$queryAllmodels = 'SELECT * FROM models
ORDER BY modelID';
$statement1 = $db->prepare($queryAllmodels);
$statement1->execute();
$models = $statement1->fetchAll();
$statement1->closeCursor();


// Get the record data
$record_id = filter_input(INPUT_POST, 'record_id', FILTER_VALIDATE_INT);
$model_id = filter_input(INPUT_POST, 'model_id', FILTER_VALIDATE_INT);
$fuel_id = filter_input(INPUT_POST, 'fuel_id', FILTER_VALIDATE_INT);
$name = filter_input(INPUT_POST, 'name');
$description = filter_input(INPUT_POST, 'description');
$price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);


// Validate inputs
if ($record_id == NULL || $record_id == FALSE || $model_id == NULL ||
$model_id == FALSE || $fuel_id == NULL || $fuel_id == FALSE || empty($name) ||empty($description) ||
$price == NULL || $price == FALSE) {
$error = "Invalid record data. Check all fields and try again.";
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

// If valid, update the record in the database
require_once('database.php');


$query = 'UPDATE records
SET modelID = :model_id,
fuelID = :fuel_id,
name = :name,
description = :description,
price = :price,
image = :image
WHERE recordID = :record_id';
$statement = $db->prepare($query);
$statement->bindValue(':model_id', $model_id);
$statement->bindValue(':fuel_id', $fuel_id);
$statement->bindValue(':name', $name);
$statement->bindValue(':description', $description);
$statement->bindValue(':price', $price);
$statement->bindValue(':image', $image);
$statement->bindValue(':record_id', $record_id);
$statement->execute();
$statement->closeCursor();

// Display the Product List page
include('index.php');
}
?>