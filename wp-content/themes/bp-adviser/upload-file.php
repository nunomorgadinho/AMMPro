<?php
$uploaddir = './uploads/';
$file = $uploaddir . basename($_FILES['userfile']['name']); 

if (move_uploaded_file($_FILES['userfile']['tmp_name'], $file)) {
  echo "success";
} else {
	echo "error";
}
?>