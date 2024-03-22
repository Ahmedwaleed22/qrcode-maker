<?php

spl_autoload_register('myAutoLoader');

function myAutoLoader($className) {
    $path = '../classes/';
    $extention = '.class.php';
    $fullpath = $path . $className . $extention;

    include_once $fullpath;
}