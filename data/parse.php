<?php
$file=array_map("json_decode",array_map("base64_decode",array_map("trim", file(__DIR__."/votes"))));
var_dump($file);
