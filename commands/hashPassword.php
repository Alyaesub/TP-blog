<?php

$password = 'password123';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
echo $hash;
