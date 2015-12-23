<?php
$message = $_POST["group"]." ".$_POST["message"];
file_put_contents(getcwd()."/message.txt", $message."\n", FILE_APPEND);
header('Location: index.php');
