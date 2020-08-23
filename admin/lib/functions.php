<?php
function get($var, $default = false, $htmlspecialchars = true) {
	if (isset($_GET[$var])) {
		$value = $_GET[$var];

		if ($htmlspecialchars)
		{
			$value = htmlspecialchars($value, ENT_QUOTES, 'ISO-8859-1');
		}
		else
		{
			$value = addslashes($value);
		}

		$value = trim($value);

		return $value;
	} else {
		return $default;
	}
}

function post($var, $default = false, $htmlspecialchars = true, $addslashes = true) {
	$achou_shy = false;

	if (isset($_POST[$var])) {
		$value = $_POST[$var];

		if (is_array($value))
		{
			return $default;
		}

		$ent_valcheck = (mb_check_encoding($value, 'UTF-8')) ? $value : utf8_encode($value);

		if (strpos(htmlentities($ent_valcheck), '&Acirc;&shy;') !== false && !$achou_shy)
		{
			exit('Caractere invisível detectado.');

			$achou_shy = true;
		}

		if ($htmlspecialchars)
		{
			$value = htmlspecialchars($value, ENT_QUOTES, 'ISO-8859-1');
		}
		else
		{
			$value = ($addslashes ? addslashes($value) : $value);
		}

		$value = trim($value);

		return $value;
	} else {
		return $default;
	}
}

function obj2array ( $Instance ) {
    $clone = (array) $Instance;
    $rtn = array ();
    $rtn['___SOURCE_KEYS_'] = $clone;

    while ( list ($key, $value) = each ($clone) ) {
        $aux = explode ("\0", $key);
        $newkey = $aux[count($aux)-1];
        $rtn[$newkey] = &$rtn['___SOURCE_KEYS_'][$key];
    }

    return $rtn;
}

function userHasLevel($user_id, $level_id) {
  global $conn;
  $found = false;
  $getUserLevels = $conn->data("SELECT r.type_user_id FROM cms_users_type_relationship AS r JOIN users AS u ON u.id = r.user_id WHERE r.user_id = ?", array($user_id));
  foreach($getUserLevels as $r) {
    if($r->type_user_id == $level_id) {
      $found = true;
      break;
    }
  }
  
  return $found;
}

function userLevelsInText($user_id) {
  $levels = "SELECT * FROM cms_users_type_relationship AS r LEFT JOIN cms_users_type AS t ON t.type_user_id = r.type_user_id JOIN cms_users AS u ON u.id = r.user_id WHERE r.user_id = '".$user_id."' ORDER BY r.type_user_id ASC";
  $res_levels = $this->conn->data($levels);
  $levels = array();
  foreach($res_levels as $level) {
    $levels[] = $level->type_title;
  }
  
  return implode(', ', $levels);
}

function cuts_text($str, $length, $title = true) {
	if(strlen($str) > $length + 3) {
		if($title) {
			return '<span title="'.htmlentities($str).'">'.substr($str, 0, $length).'...</span>';
		} else {
			return trim(substr($str, 0, $length).'...');
		}
	} else {
		return $str;
	}
}

function url_slug($str) {
	$str = str_replace('-', ' ', $str);
	$str = trim($str);

	$str = str_replace(' ', '-', $str);
	$str = strtolower(trim($str));
	$str = preg_replace('/[aáàãâä]/', 'a', $str);
	$str = preg_replace('/[eééèêë]/', 'e', $str);
	$str = preg_replace('/[iíìîï]/', 'i', $str);
	$str = preg_replace('/[oóòõôö]/', 'o', $str);
	$str = preg_replace('/[uúùûü]/', 'u', $str);
	$str = str_replace('ç', 'c', $str);
	$str = str_replace('ñ', 'n', $str);
	$str = preg_replace('/[^a-z0-9\_\-]/', '', $str);
	$str = preg_replace('/-+/', '-', $str);

	return trim($str, '-');
}

function paginacao($url, $total, $start, $per_page, $view_pages = 3, $back_next = true, $on_bool = true) {
	return pagination($url, $total, $start, $per_page, $view_pages, $back_next, $on_bool);
}

function pagination($url, $total, $start, $per_page, $view_pages = 7, $back_next = true, $on_bool = true)
{

	$prev	= '';
	$next	= '<li class="footable-page-arrow">›</li>';
	$pag	= '';
	$pags	= ceil($total / $per_page);

	$on = floor($start / $per_page) + 1;
	
	if ($back_next)
	{
    if($on - 1 <= 0) {
      $pag .= '<li class="footable-page-arrow"><a href="'. sprintf($url, '1') .'" title="Página anterior">‹</a></li>';
    } else {
      $pag .= '<li class="footable-page-arrow"><a href="'. sprintf($url, $on-1) .'" title="Página anterior">‹</a></li>';
    }
	}

	if($on > $view_pages+1)
	{
		$pag .= '<li class="footable-page"><a href="' . sprintf($url, '1') . '" class="item" title="Página 1">1</a></li> <li class="footable-page"><a href="javascript:;" class="item">...</a></li>';
	}
  
  if(!$total || $total == 1)
	{
		$pag.= '<li class="footable-page"><a href="' . sprintf($url, '1') . '" class="item" title="Página 1">1</a></li>';
	}
	
	for ($i = $on-$view_pages ;$i < $on+$view_pages ; $i++)
	{
		if($i > 0 && $i <= $pags && $total && $total > 1)
		{
			$pag .= ($i == $on && $on_bool) ? '<li class="footable-page active"><a title="Página '.$i.'">'.$i.'</a></li>' : '<li class="footable-page"><a href="' . sprintf($url, $i) . '" title="Página '. $i .'">'. $i .'</a></li>';
		}
	}
	
	if($on < $pags-($view_pages-1))
	{
		$pag .= ' ... <li class="footable-page"><a href="' . sprintf($url, $pags) . '" class="item" title="Página '. $pags .'">'. $pags .'</a></li>';
	}
	
	if ($back_next)
	{
    if(($on + 1) > $pags) {
      $pag .= '<li class="footable-page-arrow"><a href="javascript:;" data-page="next" title="Próxima página">›</a></li>';
    } else {
      $pag .= '<li class="footable-page-arrow"><a href="'. sprintf($url, $on+1) .'" title="Próxima página - '.$total.'">›</a></li>';
    }
	}
	
	return $pag;
}

function dTime($fromTime, $toTime = 0, $showLessThanAMinute = false) {
  $distanceInSeconds = round(abs($toTime - $fromTime));
  $distanceInMinutes = round($distanceInSeconds / 60);

  if ($distanceInMinutes <= 1) {
    if (!$showLessThanAMinute) {
      return ($distanceInMinutes == 0) ? 'menos de 1m' : '1 min';
    } else {
      if($distanceInSeconds < 5) {
          return ($distanceInSeconds + 1).'s';
      }
      if($distanceInSeconds < 10) {
          return 'Menos de 10s';
      }
      if($distanceInSeconds < 20) {
          return 'Menos de 20s';
      }
      if($distanceInSeconds < 40) {
          return 'Meio min';
      }
      if($distanceInSeconds < 60) {
          return 'Menos de um min';
      }

      return '1 minute';
    }
  }
  if($distanceInMinutes < 45) {
      return $distanceInMinutes . ' minutes';
  }
  if($distanceInMinutes < 90) {
      return '1 hour';
  }
  if($distanceInMinutes < 1440) {
      return '' . round(floatval($distanceInMinutes) / 60.0) . ' hours';
  }
  if($distanceInMinutes < 2880) {
      return '1 day';
  }
  if($distanceInMinutes < 43200) {
      return '' . round(floatval($distanceInMinutes) / 1440) . ' days';
  }
  if($distanceInMinutes < 86400) {
      return utf8_decode('1 mounth');
  }
  if($distanceInMinutes < 525600) {
      return round(floatval($distanceInMinutes) / 43200) . ' mounths';
  }
  if($distanceInMinutes < 1051199) {
      return '1 year';
  }

  return strtolower(round(floatval($distanceInMinutes) / 525600) . ' years');
}