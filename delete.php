<?php

include './classes/Dbh.class.php';
include './classes/Link.class.php';

$IDs = explode(",", filter_var($_GET['id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));

$link = new Link();

foreach($IDs as $ID) {
  $link->delete($ID);
}

header('Location: index.php');
exit;