<?
define('HGTN', true);
session_start();
header("Content-Type: application/json; charset=utf-8", true);

include 'Database.php';
include 'Functions.php';
include 'User.php';

if(!ajax_request()) {
  exit("Método não autorizado.");
}

class Facebook {
	public static function login() {
    $user_id = post('user_id');
		$email = post('email');
		$select = db::Query(sprintf("SELECT id, email FROM users WHERE fb_id = '%s' AND email = '%s'", $user_id, $email));
		$rows = db::NumRows($select);
		if(!$rows) {
			$data_cookie = array('user_id' => $user_id, 'email' => $email);
			$_SESSION['fb_data_retrieve'] = $data_cookie;
			return json_encode(array('save' => true));
		}
		else
		{
      $data_user = db::Assoc($select);
      $getBan = User::banned($data_user['id']);
      if(count($getBan)) {
        return json_encode(array('banned' => true, 'reason' => $getBan['reason'], 'ends' => date('d/m/Y - H:i', $getBan['started'] + $getBan['duration'])));
      } else {
        User::CreateSession($data_user['id'], false);
        db::Update("users", array("lastLoginAt" => time(), 'ip' => $_SERVER['REMOTE_ADDR']), array('id' => $data_user['id']));
        return json_encode(array('save' => false, 'msg' => 'Logged in.'));
      }
		}
	}
}

class Forum {
  public static function quote() {
    if(!User::loggedIn()) {
      exit;
    }
    header("Content-Type: text/html; charset=utf-8", true);
    $id = post('id');
    $data = db::Find('first', sprintf("SELECT topics_comments.comment, users.username AS author FROM topics_comments JOIN users ON users.id = topics_comments.user_id WHERE topics_comments.id = '%u'", intval($id)));
		$retorno = htmlspecialchars_decode("[quote][b]".$data['author']."[/b]:\n".$data['comment']."[/quote]", ENT_QUOTES);
		return $retorno;
  }
  
  public static function save() {
    $id = post('id', '/');
    if(User::loggedIn()) {
      $exists = db::Find('first', sprintf('SELECT * FROM users_topics_following WHERE topic_id = \'%u\' AND user_id = \'%u\'', $id, User::data('id')));
      if(!count($exists)) {
        db::Insert("users_topics_following", array('user_id' => User::data('id'), 'topic_id' => $id, 'createdAt' => time()));
        $data = array('title' => 'Sucesso!', 'text' => 'Tópico salvo com sucesso! Para gerenciar seus tópicos salvos, <a href="/topic/saved">clique aqui</a>.', 'color' => '#19c3aa');
      } else {
        $data = array('title' => 'Oops', 'text' => 'Você já salvou este tópico. Para gerenciar seus tópicos salvos, <a href="/topic/saved">clique aqui</a>.', 'color' => '#F2711C');
      }
    } else {
      $data = array('title' => 'Oops', 'text' => 'Você não está logado em nosso site. Para fazer login ou registrar-se, <a href="/topic/saved">clique aqui.</a>', 'color' => '#F2711C');
    }
    
		return json_encode($data);
  }
  
  public static function preview() {
    header("Content-Type: text/html; charset=utf-8", true);
    $comment = post('comment');
    return htmlspecialchars_decode(nl2br(BBCode($comment, true, false, true)), ENT_QUOTES);
  }
}

if(isset($_POST['preview'])) {
	echo Forum::preview();
}

if(isset($_POST['save'])) {
	echo Forum::save();
}

if(isset($_POST['quote'])) {
	echo Forum::quote();
}

if(isset($_POST['login'])) {
	echo Facebook::login();
}
