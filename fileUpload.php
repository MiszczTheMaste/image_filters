
<?php
header('Content-type: application/json');

function getName($n)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';

    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }

    return $randomString;
}

$fileName = getName(10);

$target_dir = "input/";
$ext = pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION);
$target_file = $target_dir . $fileName . "." . $ext;
$uploadOk = 1;
$message;
$returnCode;

if (isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }
}

if ($_FILES["fileToUpload"]["size"] > 500000) {
    $message = "Plik jest za duży";
    $uploadOk = 0;
}

if ($ext != "jpg") {
    $message = "Dozwolone format JPG";
    $uploadOk = 0;
}

if ($uploadOk == 0) {
    $returnCode = 400;
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $returnCode = 200;
        $message = "Plik wysłany";
    } else {
        $returnCode = 400;
        $message = "Wystąpił problem";
    }
}

http_response_code($returnCode);
echo json_encode(["message" => $message, "fileName" => $fileName]);
?>

