<?php
define('HGTN', true);
session_start();
ob_start();
include 'lib/database.php';
include 'lib/functions.php';
include 'lib/user.php';

if(!isset($_SESSION['site_user_id'])) {
  header('Location: /', true);
} else {
  $dataUser = $conn->first('SELECT u.* FROM users AS u JOIN cms_users_type_relationship AS r ON r.user_id = u.id WHERE u.id = ?', array($_SESSION['site_user_id']));
  if(empty($dataUser->id)) {
    header('Location: /', true);
  }
}

date_default_timezone_set('America/Sao_Paulo');
//session_destroy();
if(isset($_SESSION['admin_id'])) {  include 'loggedIn.php';} else {  include 'login.php';}