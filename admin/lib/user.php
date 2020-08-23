<?php
class user {
  
  public $conn;
  public $salt = 'adminPanel';
  private $info;
  
  public function __construct($conn) {
		$this->conn = $conn;
	}
  
  public function checkLogin() {
    if (!isset($_SESSION['admin_id']) && isset($_COOKIE['admin_id'])) {
      if(!isset($_COOKIE['admin_authkey'])) {
        exit('logout1');
        $this->logout();
      } else {
        if (md5($this->salt . $_COOKIE['admin_id']) != $_COOKIE['admin_authkey']) {
          exit('logout2');
          $this->logout();
        } else {
            $u_id = $_COOKIE['admin_id'];
            $data = $this->conn->first('SELECT * FROM cms_users WHERE id = ?', array($u_id));
            if ($data->username) {
              $_SESSION['admin_id'] = $data->id;
              $_SESSION['admin_password'] = $data->password;
            }
        }
      }
    } elseif (isset($_COOKIE['admin_id'])) {
      if(!isset($_COOKIE['admin_authkey'])) {
        exit('logout3');
        $this->logout();
      } else {
        $authkey = md5($this->salt . $_COOKIE['admin_id']);
        if ($authkey != $_COOKIE['admin_authkey']) {
          exit('logout4');
          $this->logout();
        }
      }
    }
    
    if(get('p') == 'logout') {
      $this->logout(true);
    }
    
    if($this->loggedIn()) {
      $user_id = $_SESSION['site_user_id'];
      $findUser = $this->conn->first("SELECT * FROM users WHERE id = ? LIMIT 1", array($user_id));
      if(!empty($findUser->id)) {
        $this->info = $findUser;
      }
    }
    
    $this->validateUser();
  }
  
  public function validateUser() {
    if($this->loggedIn()) {
      $u_id = $_SESSION['admin_id'];
      $data = $this->conn->first('SELECT u.* FROM users AS u JOIN cms_users_type_relationship AS r ON r.user_id = u.id WHERE u.id = ?', array($u_id));
      if(empty($data->id)) {
        $this->logout(true);
      }
    }
  }
  
  public function loggedIn() {
		if(isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id'])) {
      return true;
		} else {
			return false;
		}
	}
  
  public function logout($redirect = false) {
		unset($_SESSION['admin_id'], $_SESSION['admin_password']);
    setcookie('admin_id', null, time() - 300);
    setcookie('admin_authkey', null, time() - 300);
    
    if ($redirect) {
        header('Location: index.php');
    }
	}
  
  public function data($param) {
    return $this->info->$param;
  }
  
  public function myLevels() {
    $my_levels_query = $this->conn->data("SELECT * FROM cms_users_type_relationship WHERE user_id = ?", array($this->data('id')));
    $my_levels = array();
    foreach($my_levels_query as $level) {
      $my_levels[] = $level->type_user_id;
    }
    
    return $my_levels;
  }
  
  public function myLevelsInText() {
    $levels = "SELECT * FROM cms_users_type_relationship AS r LEFT JOIN cms_users_type AS t ON t.type_user_id = r.type_user_id JOIN cms_users AS u ON u.id = r.user_id WHERE r.user_id = '".$this->data('id')."' ORDER BY r.type_user_id ASC";
    $res_levels = $this->conn->data($levels);
    $levels = array();
    foreach($res_levels as $level) {
      $levels[] = $level->type_title;
    }
    
    return implode(', ', $levels);
  }
  
  public function hasLevel($level_id) {
    $sql = $this->conn->data("SELECT * FROM cms_users_type_relationship WHERE user_id = ? AND type_user_id = ?", array($this->data('id'), $level_id));
    if(isset($sql[0]) && !empty($sql[0])) {
      return true;
    }
    
    return false;
  }
}


global $conn;
$user = new user($conn);
$user->checkLogin();