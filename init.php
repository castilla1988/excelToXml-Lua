<?php
require 'Classes/Factory.class.php';
$doc = new Factory();
$doc->run(0); //参数 = 0 输出List格式的lua，参数 = 1 输出Map格式的lua