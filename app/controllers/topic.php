<?php
defined('HGTN') or exit;
class Topic 
{
	private static $per_page = 10;
  
  public static function init() {
    
  }
	
	public static function exists () {
		$id = get('id', '/');
		$url = get('slug', '/');
		$topics = db::Query(sprintf("SELECT topics.* FROM topics JOIN users ON users.id = topics.user_id JOIN topics_categories ON topics_categories.id = topics.category_id WHERE topics.id = '%u' AND topics.slug = '%s' AND topics.status = '%s'", $id, $url, true));
		$num_topics = db::NumRows($topics);
		if($num_topics) {
			return true;
		}
    
    return false;
	}
  
	public static function data()
	{
	
		if(self::Exists())
		{
      $id = get('id', '/');
      $url = get('slug', '/');
      if(($data_topic = Cache::ler('topic-' . $id)) === false) {
        $data_topic = db::Find('first', sprintf("SELECT topics.*, users.username AS author, users.avatar AS avatar, users.signature, users.createdAt AS user_createdAt, topics_categories.name AS category_name, (SELECT COUNT(id) FROM topics_comments WHERE user_id = topics.user_id) AS num_comments, (SELECT COUNT(id) FROM topics WHERE user_id = topics.user_id) AS num_topics FROM topics JOIN users ON users.id = topics.user_id JOIN topics_categories ON topics_categories.id = topics.category_id WHERE topics.id = '%u' AND topics.slug = '%s' AND topics.status = '%s'", $id, $url, true));
        
        $page = !empty($_GET['page']) && isset($_GET['page']) ? get('page') : '1';
        $inicio = ($page * self::$per_page) - self::$per_page;
        
        $ult_resposta_this_topic = db::Find('first', sprintf("SELECT COUNT(id) AS num FROM topics_comments WHERE topic_id = '%u'", $id));
        $num_comments = $ult_resposta_this_topic['num'];
        
        // Atualiza número de views
        db::Query("UPDATE topics SET num_views = num_views + 1 WHERE id = " . $id);
        
        //$nota_bom = db::NumRows(db::Query(sprintf("SELECT 0 FROM topics_poll WHERE topic_id = '%u' AND type = '1'", $id)));
        //$nota_ruim = db::NumRows(db::Query(sprintf("SELECT 0 FROM topics_poll WHERE topic_id = '%u' AND type = '0'", $id)));
        
        $data_topic['total_comments'] = intval($data_topic['num_comments']) + intval($data_topic['num_topics']);
        $data_topic['post'] = nl2br(BBCode($data_topic['post'], true, false, true));
        $data_topic['created_at'] = dTime($data_topic['createdAt']);
        //$data_topic['voteGood'] = $nota_bom;
        //$data_topic['voteBad'] = $nota_ruim;
        $data_topic['signature'] = (!empty($data_topic['signature']) ? nl2br(BBCode($data_topic['signature'], true, false, true)) : 'Sem assinatura.');
        $data_topic['pagination'] = pagination('/topic/'.$data_topic['slug'].'/'.$data_topic['id'].'/page/%u', $num_comments, $inicio, self::$per_page, 7, true);
        Cache::salva($data_topic, 'topic-' . $id, 60 * 60);
			}
      
			return $data_topic;
		} else {
			return false;
		}
	}
	
	public static function comments()
	{
		$id = get('id', '/');
    $url = get('slug', '/');
    $page = !empty($_GET['page']) && isset($_GET['page']) ? get('page') : '1';
    $inicio = ($page * self::$per_page) - self::$per_page;
    $data_comment = db::Find('all', sprintf("SELECT topics_comments.*, users.username AS author, users.avatar AS avatar, users.signature, users.createdAt AS user_createdAt, (SELECT COUNT(id) FROM topics_comments WHERE user_id = topics_comments.user_id) AS num_comments, (SELECT COUNT(id) FROM topics WHERE user_id = topics_comments.user_id) AS num_topics FROM topics_comments JOIN users ON users.id = topics_comments.user_id WHERE topics_comments.topic_id = '%u' ORDER BY topics_comments.id ASC LIMIT %u, %u", $id, $inicio, self::$per_page));
    
    foreach($data_comment as $i => $row) {
      $data_comment[$i]['comment'] = nl2br(BBCode($data_comment[$i]['comment'], true, false, true));
      $data_comment[$i]['total_comments'] = intval($data_comment[$i]['num_comments']) + intval($data_comment[$i]['num_topics']);
      $data_comment[$i]['created_at'] = dTime($data_comment[$i]['createdAt']);
      $data_comment[$i]['signature'] = (!empty($data_comment[$i]['signature']) ? nl2br(BBCode($data_comment[$i]['signature'], true, false, true)) : 'Sem assinatura.');
    }
    return $data_comment;
	}
  
  public static function comment() {
    $id = get('id');
    $slug = get('slug');
    //db::Insert('topics_comments', array("topic_id" => 1, 'user_id' => 1, 'comment' => 'iae dg, escama so de peixe!', 'createdAt' => time(), 'updatedAt' => time()));
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && User::loggedIn()) {
      $errors = array();
      $post = post('comment-field');
      if(empty(trim($post)))
			{
				$errors[] = 'Você deve escrever algo no campo de postagem.';
			}
			
			$parte_retorno = '';
			if(count($errors)) {
				foreach($errors as $erro)
				{
					$parte_retorno .= '<li>'.$erro.'</li>';
				}
				$parte_retorno = message('Oops', $parte_retorno, 'remove', 'error');
				
				return $parte_retorno;
			} else {
        db::Insert('topics_comments', array("topic_id" => $id, 'user_id' => User::data('id'), 'comment' => $post, 'createdAt' => time(), 'updatedAt' => time()));
        $lastId = db::InsertId();
        db::Query("UPDATE topics SET num_comments = num_comments + 1 WHERE id = " . $id);
        db::Query(sprintf("UPDATE users SET topics = topics + 1 WHERE id = '%u'", User::data('id')));
        $total = db::Find('first', sprintf("SELECT COUNT(id) AS num FROM topics_comments WHERE topic_id = '%u'", $id));
        $total = $total['num'];
        $totalPages = ceil($total / self::$per_page);
        self::apagaCache();
        header("Location: /topic/" . $slug . "/" . $id . "/page/". $totalPages ."/#comment-" . $lastId);
      }
    }
  }
  
  public static function saved() {
		$itens = array();
		$sql = db::Query(sprintf("SELECT topics.*, users.username AS author, users_topics_following.createdAt AS savedAt, topics_categories.name AS category FROM topics JOIN users ON users.id = topics.user_id JOIN topics_categories ON topics_categories.id = topics.category_id JOIN users_topics_following ON users_topics_following.topic_id = topics.id WHERE status = 1 AND users_topics_following.user_id = '%u' ORDER BY users_topics_following.id DESC", User::data('id')));
		$i = 0;
		while($row = db::Assoc($sql)) {
      $lastComment = db::Find('first', "SELECT topics_comments.createdAt AS createdAt, users.username AS author, users.avatar AS avatar FROM topics_comments JOIN users ON users.id = topics_comments.user_id WHERE topics_comments.topic_id = " . $row['id'] . " ORDER BY topics_comments.id DESC");
			$itens[] = array(
				'id' => $row['id'],
				'slug' => $row['slug'],
				'title' => $row['title'],
				'author' => $row['author'],
				'category' => $row['category'],
				'category_id' => $row['category_id'],
				'comments' => number_format($row['num_comments'], 0, '.', '.'),
				'views' => number_format($row['num_views'], 0, '.', '.'),
				'createdAt' => $row['createdAt'],
        'savedAt' => $row['savedAt'],
        'lastComment' => $lastComment
			);

      $i++;
		}
    
    
		return $itens;
	}
  
  public static function delete_saved() {
    if(User::loggedIn()) {
      $id = get('delete');
      db::Query(sprintf("DELETE FROM users_topics_following WHERE topic_id = '%u' AND user_id = '%u'", $id, User::data('id')));
      header('Location: /topic/saved/', true);
    }
  }
  
  public static function categories($category = 0) {
    $cats = db::Find('all', "SELECT * FROM topics_categories WHERE parent = " . $category);
    return $cats;
  }
	
	public static function create() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && User::loggedIn()) {
			$errors = array();
			$titulo = post('title');
			$titulo_trim = trim($titulo);
			$categoria = (int) post('category');
			$post = post('post');
			$post_trim = trim($post);
			
			/*if(!isset($_SESSION['attemps_send_topics']) || empty($_SESSION['attemps_send_topics']))
			{
				$_SESSION['attemps_send_topics'] = time() - 301;
			}
			
			if($_SESSION['attemps_send_topics'] > time() - 300)
			{
				$errors[] = 'Você criou um tópico recentemente. Espere 5 minutos para criar outro.';
			}*/
			
			if(empty($titulo_trim))
			{
				$errors[] = 'Você deve escrever algo no campo de título.';
			}
			
			if(empty($categoria) || !is_numeric($categoria))
			{
				$errors[] = 'Você não selecionou categoria alguma.';
			}
			
			if(empty($post_trim))
			{
				$errors[] = 'Você deve escrever algo no campo de postagem.';
			}
			
			
			$parte_retorno = '';
			if(count($errors))
			{
				$parte_retorno .= '<div class="mensagem_vermelha">Erros encontrados.';
				foreach($errors as $erro)
				{
					$parte_retorno .= '<li>'.$erro.'</li>';
				}
				$parte_retorno .= '</div>';
				
				return $parte_retorno;
			}
			else
			{
				$inserir_topico = db::Query(sprintf("INSERT INTO topics(slug, user_id, createdAt, updatedAt, post, title, ip, category_id) VALUES('%s', '%u', '%u', '%u', '%s', '%s', '%s', '%u')", url_slug($titulo), User::data('id'), time(), time(), $post, $titulo, $_SERVER['REMOTE_ADDR'], $categoria));
        if($inserir_topico) {
					$_SESSION['attemps_send_topics'] = time();
					$ult_topic = db::Assoc(db::Query("SELECT * FROM topics ORDER BY id DESC"));
					db::Query(sprintf("UPDATE users SET topics = topics + 1 WHERE id = '%u'", User::data('id')));
					header(sprintf('Location: /topic/%s/%u', $ult_topic['slug'], $ult_topic['id']));
				//	return msg_cor('verde', sprintf('Tópico criado com sucesso! <b><a href="/topics/%u-%s">Clique aqui</a></b> para visualiza-lo.', $ult_topic['id'], $ult_topic['url']));
				}
				else
				{
					return message('Oops', 'Erro ao inserir o tópico no banco de dados. Se isso voltar a acontecer, contate um webmaster.');
				}
			}
		}
	}
  
  private function apagaCache()
	{
    $id = get('id');
		Cache::exclui('topic-' . $id);
	}
}

Topic::init();

if(get('delete')) {
  Topic::delete_saved();
}