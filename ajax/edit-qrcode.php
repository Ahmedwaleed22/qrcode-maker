<?php
include '../include/autoloader.php';
date_default_timezone_set('UTC');

require dirname(dirname(__FILE__))."/lib/functions.php";

if (qrcdr()->getConfig('debug_mode')) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL ^ E_NOTICE);
}

$page = filter_var($_GET['page'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if ($page === 'data') {
  $linkID = filter_var($_GET['linkID'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $link = new Link();
  $link = $link->getLink($linkID);
  echo json_encode($link);
}

if ($page === 'update') {
  $name = filter_var($_GET['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $linkID = filter_var($_GET['linkID'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $destination = filter_var($_GET['destination'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $link = new Link();
  $link = $link->updateData($name, $linkID, $destination);
  echo json_encode($link);
}