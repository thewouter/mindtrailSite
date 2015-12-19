<?php
$message = $_POST["group"]." ".$_POST["message"];
file_put_contents(getcwd()."/message.txt", $message, FILE_APPENDs);
include "index.php";
