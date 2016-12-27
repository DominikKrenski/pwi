<?php

session_start();

header('Content-Description: File Transfer');
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="' . basename('tmp/results.txt'). '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: '. filesize('tmp/results.txt'));
readfile('tmp/results.txt');
