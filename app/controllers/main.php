<?php
defined('HGTN') or exit;
class Main {
  
  private static $perPageRecent = 10;
  
	public static function setPage() {
		if(isset($_GET['url']) && strlen($_GET['url']) > 0 && strpos($_GET['url'], 'painel') === false) {
			$pathPages = 'app/views/'.get('url').'.php';
			if(file_exists($pathPages)) {
				require $pathPages;
			}  else  {
				require 'app/views/error.php';
			}
		} else {
			require 'app/views/home.php';
		}
	}
  
	public static function recentTopics($featured = false) {
		$itens = array();
		$sql = db::Query(sprintf("SELECT topics.*, users.username AS author, topics_categories.name AS category FROM topics JOIN users ON users.id = topics.user_id JOIN topics_categories ON topics_categories.id = topics.category_id WHERE status = %u AND featured = %u ORDER BY id DESC LIMIT %u", true, $featured, self::$perPageRecent));
		$i = 0;
		while($row = db::Assoc($sql)) {
      $lastComment = db::Find('first', "SELECT topics_comments.createdAt AS createdAt, users.username AS author, users.avatar AS avatar FROM topics_comments JOIN users ON users.id = topics_comments.user_id WHERE topics_comments.topic_id = " . $row['id'] . " ORDER BY topics_comments.id DESC");
			$itens[] = array(
				'id' => $row['id'],
				'slug' => $row['slug'],
				'title' => cuts_text($row['title'], 70),
				'author' => $row['author'],
				'category' => $row['category'],
				'category_id' => $row['category_id'],
				'comments' => number_format($row['num_comments'], 0, '.', '.'),
				'views' => number_format($row['num_views'], 0, '.', '.'),
				'createdAt' => $row['createdAt'],
        'lastComment' => $lastComment
			);

      $i++;
		}
    
    
		return $itens;
	}
  
  public static function topicsList($category = 1) {
    $page = !empty($_GET['page']) && isset($_GET['page']) ? get('page') : '1';
    $initial = ($page * self::$perPageRecent) - self::$perPageRecent;
		$itens = array();
		$sql = db::Query(sprintf("SELECT topics.*, users.username AS author, topics_categories.name AS category FROM topics JOIN users ON users.id = topics.user_id JOIN topics_categories ON topics_categories.id = topics.category_id WHERE status = %u AND category_id = %u ORDER BY updatedAt DESC LIMIT %u, %u", true, $category, $initial, self::$perPageRecent));
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
        'lastComment' => $lastComment
			);

      $i++;
		}
    
    
		return $itens;
	}
  
  public static function catExists($id) {
    $cats = db::Find('first', "SELECT * FROM topics_categories WHERE parent != 0 AND id = " . $id);
    if(count($cats)) {
      return ['name' => $cats['name']];
    }
    
    return [];
  }
  
  public static function categories($category = 0) {
    $cats = db::Find('all', "SELECT * FROM topics_categories WHERE parent = " . $category);
    
    if($category) {
      foreach($cats as $i => $row) {
        $countTopics = db::Assoc(db::Query("SELECT COUNT(id) AS rows FROM topics WHERE category_id = " . $row['id']));
        $dataLastTopic = db::Find('first', "SELECT topics.id, topics.slug, topics.createdAt, topics.title, users.username AS author, users.avatar FROM topics JOIN users ON users.id = topics.user_id WHERE topics.status = 1 AND topics.category_id = ".$row['id']." ORDER BY topics.id DESC");
        $cats[$i]['num_topics'] = $countTopics['rows'];
        $cats[$i]['lastTopic'] = $dataLastTopic;
      }
    }
    
    return $cats;
  }
  
}