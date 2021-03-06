<?php

// Get the product data
$model_id = filter_input(INPUT_POST, 'model_id', FILTER_VALIDATE_INT);
$fuel_id = filter_input(INPUT_POST, 'fuel_id', FILTER_VALIDATE_INT);
$name = filter_input(INPUT_POST, 'name');
$description = filter_input(INPUT_POST, 'description');
$price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);


// Validate inputs
if ($model_id == null || $model_id == false || $fuel_id == null || $fuel_id == false ||
    $name == null ||$description == null  || $price == null  ) 
    {
          $error = "Invalid product data. Check all fields and try again.";
          include('error.php');
          exit();
    } 
    else {

    /**************************** Image upload ****************************/

    error_reporting(~E_NOTICE); 



// avoid notice

    $imgFile = $_FILES['image']['name'];
    $tmp_dir = $_FILES['image']['tmp_name'];
    echo $_FILES['image']['tmp_name'];
    $imgSize = $_FILES['image']['size'];

    if (empty($imgFile)) {
        $image = "";
    } else {
        $upload_dir = 'image_uploads/'; // upload directory

        $imgExt = strtolower(pathinfo($imgFile, PATHINFO_EXTENSION)); // get image extension
        // valid image extensions
        $valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); // valid extensions
        // rename uploading image
        $image = rand(1000, 1000000) . "." . $imgExt;
        // allow valid image file formats
        if (in_array($imgExt, $valid_extensions)) {
        // Check file size '5MB'
            if ($imgSize < 5000000) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $upload_dir . $image)) {
                    echo "The file ". basename( $_FILES["image"]["name"]). " has been uploaded.";
                } else {
                    $error =  "Sorry, there was an error uploading your file.";
                    include('error.php');
                    exit();
                }
            } else {
                $error = "Sorry, your file is too large.";
                include('error.php');
                exit();
            }
        } else {
            $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            include('error.php');
            exit();
        }
    }

    /************************** End Image upload **************************/
    
    require_once('database.php');

    // Add the product to the database 
    $query = "INSERT INTO cars
                 (modelID,fuelID, name, description, price, image)
              VALUES
                 (:model_id, :fuel_id, :name, :description, :price, :image)";
    $statement = $db->prepare($query);
    $statement->bindValue(':model_id', $model_id);
    $statement->bindValue(':fuel_id', $fuel_id);
    $statement->bindValue(':name', $name);
    $statement->bindValue(':description', $description);
    $statement->bindValue(':price', $price);
    $statement->bindValue(':image', $image);
    $statement->execute();
    $statement->closeCursor();
    include('index.php');

}
header("Location: index.php");
?>