<?php
defined ('HGTN') or exit('Not allowed');
class User {
  
  private static $salt = 'haroldnoobdescobriu';

  public static function init() {
    if(!isset($_SESSION['site_user_id']) && isset($_COOKIE['site_user_id'])) {
      if(!isset($_COOKIE['site_authkey'])) {
        self::Deslogar();
        //exit('user-unlogged-cookie-authkey');
      } else {
        if(md5(self::$salt . $_COOKIE['site_user_id']) != $_COOKIE['site_authkey']) {
          self::Deslogar();
          //exit('user-unlogged-cookie-authkey-different');
        } else {
          $u_id = $_COOKIE['site_user_id'];
          $busca_usuario = db::Query(sprintf("SELECT * FROM users WHERE id = '%u'", $u_id));
          $first = db::NumRows($busca_usuario);
          if($first) {
            $row_user = db::Assoc($busca_usuario);
            $_SESSION['site_user_id'] = $row_user['id'];
          }
        }
      }
    } elseif(isset($_COOKIE['site_user_id'])) {
      if(!isset($_COOKIE['site_authkey'])) {
        self::Deslogar();
        //exit('user-unlogged-missing-cookie-authkey');
      } else {
        if(md5(self::$salt . $_COOKIE['site_user_id']) != $_COOKIE['site_authkey']) {
          self::Deslogar();
          //exit('user-unlogged-cookie-user-authkey-different');
          //header('Location: ' . $_SERVER['REQUEST_URI']);
        }
      }
    }
    
    global $url_params;
    
    if(($url_params[1] == 'login' || $url_params[1] == 'register') && self::loggedIn()) {
      header("Location: /");
    }
    
    if(self::loggedIn() && self::banned($_SESSION['site_user_id'])) {
      self::Deslogar();
    }
  }
  
  public static function banned($user_id)
  {
    $select = db::Find('first', sprintf('SELECT * FROM users_bans WHERE started + duration > %u AND user_id = %u LIMIT 1', time(), $user_id));
    return $select;
  }
  
  public static function Login() {
    if(!self::loggedIn() && isset($_REQUEST['submit-login'])) {
      $username = post('user');
      $password = md5(post('pass'));
      $remember = post('remember');
      if(self::AccountExists($username)) {
        $data_user = self::Exists($username, $password);
        if(count($data_user)) {
          $getBan = User::banned($data_user['id']);
          if(count($getBan)) {
            return message('Você foi banido!', 'Motivo: ' . $getBan['reason'] . '<br />Seu banimento acaba em: ' . date('d/m/Y H:i', $getBan['started'] + $getBan['duration']), 'remove circle', 'error');
          } else {
            self::CreateSession($data_user['id'], $remember);
            db::Update("users", array("lastLoginAt" => time(), 'ip' => $_SERVER['REMOTE_ADDR']), array('id' => $data_user['id']));
            header("Location: /");
          }
        }
        
        return message('Oops!', 'Os dados informados não coincidem.', 'remove circle', 'error');
      }
      
      return message('Oops!', 'O usuário ou email digitado não correspondem a nenhuma conta no nosso banco de dados.', 'remove circle', 'error');
    }
    
    //db::Update("teste", array('cu' => 'buceta'), array('id' => 'cuzao', 'cuzao2' => 'meucu'));
  }
  
  public static function CreateSession($id, $remember = false) {
    $_SESSION['site_user_id'] = $id;
    
    if($remember) {
      $ate = time() + 3600 * 24 * 120;
      setcookie('site_user_id', $id, $ate);
      /* Authkey para evitar login em contas indesejadas */
      setcookie('site_authkey', md5(self::$salt . $id), $ate);
    }
    
  }
  
  public static function CreateSessionFB($id, $remember = false) {
    $_SESSION['site_user_id'] = $id;
    
    if($remember) {
      $ate = time() + 3600 * 24 * 120;
      setcookie('site_user_id', $id, $ate);
      /* Authkey para evitar login em contas indesejadas */
      setcookie('site_authkey', md5(self::$salt . $id), $ate);
    }
    
  }
  
  public static function ExistsFB($fb_id, $email) {
    return db::Find('first', sprintf("SELECT * FROM users WHERE (fb_id = '%s' OR email = '%s') LIMIT 1", $fb_id, $email));
  }
  
  public static function Exists($user, $pass) {
    return db::Find('first', sprintf("SELECT * FROM users WHERE (username = '%s' OR email = '%s') AND password = '%s' LIMIT 1", $user, $user, $pass));
  }
  
  public static function AccountExists($user) {
    return count(db::Find('first', sprintf("SELECT * FROM users WHERE username = '%s' OR email = '%s'", $user, $user)));
  }
  
    public static function active() {
    if(User::loggedIn()) {
      return self::data('activated');
    }
  }
  
  public static function ID() {
    if(self::Logado()) {
      return $_SESSION['site_user_id'];
    }
  }
  
	public static function Logado() {
		if(isset($_SESSION['site_user_id']) && trim($_SESSION['site_user_id']) != '') {
				return true;
		} else {
			return false;
		}
	}
	
	public static function loggedIn() {
		return self::Logado();
	}
	
	public static function Info($param) {
		if(self::Logado()) {
			$sql = db::Assoc(db::Query(sprintf("SELECT * FROM users WHERE id = '%s'", $_SESSION['site_user_id'])));
			return $sql[$param];
		} else {
			return false;
		}
	}
  
  public static function data($param) {
    return self::Info($param);
  }
	
	public static function userInfoById($id) {
		$sql = db::Query(sprintf("SELECT * FROM users WHERE id = '%s'", $id));
		if(db::NumRows($sql)) {
			$assoc = db::Assoc($sql);
			return $assoc[$param];
		}
		else
		{
			return array();
		}
	}
	
	public static function userInfoByName($nick) {
		$sql = db::Query(sprintf("SELECT * FROM users WHERE username = '%s'", $nick));
		if(db::NumRows($sql)) {
			$assoc = db::Assoc($sql);
			return $assoc[$param];
		}
		else
		{
			return array();
		}
	}
	
	public static function Deslogar() {
		session_unset();
		session_destroy();
    setcookie('site_user_id', null, time() - 300);
    setcookie('site_authkey', null, time() - 300);
	}
}

User::init();