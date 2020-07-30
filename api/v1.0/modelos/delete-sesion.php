<?php

session_start();

unset($_SESSION['registrado']);
session_destroy();

$http_code = 200;
?>