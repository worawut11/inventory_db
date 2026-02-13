<?php
$password_plain = "1234";
$hash = password_hash($password_plain, PASSWORD_DEFAULT);
echo $hash;
