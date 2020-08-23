<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


class Core {
  public static function init() {
    self::safeUser();
    self::safeSQLI();
  }
  
  public static function safeUser() {
    // Checa Useragent
    if (isset($_SESSION['HTTP_USER_AGENT']) && $_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT'])) {
      session_unset();
      session_destroy();
      setcookie('site_user_id', null, time() - 300);
      //exit('core-unlogged-useragent');
    } elseif(!isset($_SESSION['HTTP_USER_AGENT'])) {
      $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
    }
    // Checa ip - função desativada no momento
    /*if (isset($_SESSION['REMOTE_ADDR']) && $_SESSION['REMOTE_ADDR'] != md5($_SERVER['REMOTE_ADDR'])) {
      session_unset();
      session_destroy();
      setcookie('site_user_id', null, time() - 300);
      echo $_SESSION['REMOTE_ADDR'] . ' - ' . $_SERVER['REMOTE_ADDR'] . ' - ' . md5($_SERVER['REMOTE_ADDR']) . ' ---- ';
      exit('core-unlogged-ip');
    } elseif(!isset($_SESSION['REMOTE_ADDR']) || empty($_SESSION['REMOTE_ADDR'])) {
      $_SESSION['REMOTE_ADDR'] = md5($_SERVER['REMOTE_ADDR']);
    }*/
  }
  
  public static function safeSQLI() {
    // Remove magic_quotes_gpc.
    if (get_magic_quotes_gpc())
    {
      function stripslashes_deep($value)
      {
        $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
        return $value;
      }

      $_POST = array_map('stripslashes_deep', $_POST);
      $_GET = array_map('stripslashes_deep', $_GET);
      $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
      $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
    }
  }
  
  public static function includeController($controllerName) {
    $path = 'app/controllers/' . $controllerName . '.php';
    if(file_exists($path)) {
    include_once($path);
    } else {
      die('Não foi possível incluir o controller "'.$controllerName.'".');
    }
  }
}

Core::init();