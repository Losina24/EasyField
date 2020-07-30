<?php

http_response_code($http_code);
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE');
header('Content-Type: application/json; charset=utf-8');
