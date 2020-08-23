<?php
function ajax_request()
{
	$req_with = (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) ? strtolower(
			$_SERVER['HTTP_X_REQUESTED_WITH']) : false;
	return ($req_with == 'xmlhttprequest');
}

function message($title = '', $text = '', $icon = 'configure', $type = '', $segment = false) {
  $return = '';
  if($segment)
    $return = '<div class="ui basic segment">';
  
  $return .= '<div class="ui icon '.$type.' message">
      <i class="'.$icon.' icon"></i>
      <div class="content">
        <div class="header">
          '.$title.'
        </div>
        <p>'.$text.'</p>
      </div>
    </div>';
    
  if($segment)
    $return .= '</div>';
  
  return $return;
}

function generateCode($length = 13)
{
	$letra = true;
	$letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$code = $letters[rand(0, strlen($letters) - 1)];
	
	for ($i = 1; $i < $length; $i++)
	{
		if ($letra)
		{
			$code{$i} = rand(0, 9);
		}
		else
		{
			$code{$i} = $letters[rand(0, strlen($letters) - 1)];
		}
		
		$letra = !$letra;
	}

	return $code;
}

function get($var, $default = false, $htmlspecialchars = true)
{
	if (isset($_GET[$var]))
	{
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
	}
	else
	{
		return $default;
	}
}

function post($var, $default = false, $htmlspecialchars = true, $addslashes = true)
{
	$achou_shy = false;

	if (isset($_POST[$var]))
	{
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
	}
	else
	{
		return $default;
	}
}

function meta_keywords($arr)
{
	if (!is_array($arr))
	{
		return false;
	}

	$keywords = array();

	foreach ($arr as $keyword)
	{
		$keyword = trim(preg_replace('/(\.|\,|\?|\!|\=|\+|\:|\(|\)|[0-9])+/', '', strtolower($keyword)));

		if (strlen($keyword) > 2 && !in_array($keyword, array('and', 'com', 'dos', 'das', 'até', 'ate', 'sob', 'seu', 'sua', 'meu')))
		{
			$keywords[] = $keyword;
		}
	}

	$keywords = implode(', ', $keywords);

	return $keywords;
}

function cuts_text($str, $length, $title = true)
{
	if(strlen($str) > $length + 3)
	{
		if($title)
		{
			return '<span title="'.htmlentities($str).'">'.substr($str, 0, $length).'...</span>';
		}
		else
		{
			return substr($str, 0, $length).'...';
		}
	}
	else
	{
		return $str;
	}
}

function BBCode($str, $completo = false, $extra = false, $shadowbox = false, $artigo = false)
{
	if($artigo)
	{
		$str = preg_replace('/\[lista\](.*?)\[\/lista\]/is', '<span style="margin-left:15px;display:inline-block;">&bull; $1</span>', $str); 
		$str = preg_replace('/\[li\](.*?)\[\/li\]/is', '<li>$1</li>', $str); 
		$str = preg_replace('/\[subtitulo\](.*?)\[\/subtitulo\]/is', '<h3>$1</h3>', $str);
		$str = preg_replace('/\[alinhar\=(left|center|right)\](.*?)\[\/alinhar\]/is', '<div style="text-align:$1;">$2</div>', $str);
		$str = preg_replace('/\[img altura\=([0-9]*) largura\=([0-9]*) lightbox=sim\](.*?)\[\/img\]/is' , '<img style="height:$1px;width:$2px;max-width:100%;" src="$3" />', $str);
		$str = preg_replace('/\[img altura\=([0-9]*) largura\=([0-9]*) lightbox=sim\](.*?)\[\/img\]/is' , '<a href="$3" rel="shadowbox"><img style="height:$1px;width:$2px;max-width:100%;" src="$3" /></a>', $str);
		$str = preg_replace('/\[notificacao\=(.*?)\](.*?)\[\/notificacao\]/is' , '<div class="mensagem_$1" style="margin-bottom: -10px;">$2</div>', $str);
	}

	if($extra == true)
	{
		$str = str_replace('[br]', '<br />', $str);
		$str = str_replace('[hr]', '<hr />', $str);
	}
	
	// $str = preg_replace('/\[quebra\]([ ]+)?\[quebra\]/', '', $str);
	$str = preg_replace('/\[b\](.*?)\[\/b\]/is', '<b>$1</b>', $str); 
	$str = preg_replace('/\[i\](.*?)\[\/i\]/is', '<i>$1</i>', $str);
	$str = preg_replace('/\[u\](.*?)\[\/u\]/is', '<u>$1</u>', $str);
	$str = preg_replace('/\[s\](.*?)\[\/s\]/is', '<s>$1</s>', $str);
	$str = preg_replace('/\[center\](.*?)\[\/center\]/is', '<center>$1</center>', $str);
	$str = preg_replace('/\[url\=?\](http|https)\:\/\/(.*?)\[\/url\]/is', '<a href="$1://$2" rel="nofollow" target="_blank">$1://$2</a>', $str);
	$str = preg_replace('/\[color\=(.*?)\](.*?)\[\/color\]/is', '<span style="color: $1;">$2</span>', $str);
	$str = preg_replace('/\[url=(http|https)\:\/\/(.*?)\](.*?)\[\/url\]/is', '<a target="_blank" href="$1://$2" rel="nofollow">$3</a>', $str);
	$str = preg_replace('/\[url\](.*?)\[\/url\]/is', '<a target="_blank" href="$1" rel="nofollow">$1</a>', $str);
	
	if($completo == true)
	{
		$youtube = '<div style="z-index:0;position:relative;"><object style="width:100%;" height="340"><param name="movie" value="http://www.youtube.com/v/$1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/$1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="550" height="340"></embed></object></div><br/>';
		$youtube_dois = '<div style="z-index:0;position:relative;"><object style="width:100%;" height="340"><param name="movie" value="http://www.youtube.com/v/$2"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/$2" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="550" height="340"></embed></object></div><br/>';
	
		if(preg_match('/\[youtube\](http|https)\:\/\/www\.youtube\.com\/watch\?v\=(.*?)\[\/youtube\]/', $str))
		{
			$str = preg_replace('/\[youtube\](http|https)\:\/\/www\.youtube\.com\/watch\?v\=(.*?)\[\/youtube\]/is', $youtube_dois, $str);
		}
		else
		{
			$str = preg_replace('/\[youtube\](.*?)\[\/youtube\]/is', $youtube, $str);
		}
		
		if ($shadowbox)
		{
			$str = preg_replace('/\[img\](.*?)\[\/img\]/is' , '<a href="$1" target="_blank" rel="shadowbox"><img src="$1" style="width: 500px; max-width:100%;" alt="" /></a>', $str);		
		}
		else
		{
			$str = preg_replace('/\[img\](.*?)\[\/img\]/is' , '<img src="$1" style="max-width:100%;" alt="" />', $str);			
		}

		/*
		// $str = str_replace('[quebra]', '<br /><br />', $str);
		$emoticons = array(
			'[:D]'			=> 'icon_e_biggrin.gif',
			'[:)]'			=> 'icon_e_smile.gif',
			'[;-)]'			=> 'icon_lol.gif',
			'[:-)]'			=> 'icon_razz.gif',
			'[8-)]'			=> 'icon_eek.gif',
			'[:O]'			=> 'icon_e_surprised.gif',
			'[;)]'			=> 'icon_e_wink.gif',
			'[&gt;:@]'		=> 'icon_redface.gif',
			'[*-)]'			=> 'icon_rolleyes.gif',
			'[&gt;:]]'		=> 'icon_twisted.gif',
			'[&gt;:[]'		=> 'icon_evil.gif',
			'[:l]'			=> 'icon_neutral.gif',
			'[:B]'			=> 'icon_mrgreen.gif',
			'[:@]'			=> 'icon_mad.gif',
			'[8)]'			=> 'icon_e_geek.gif'
		);

		foreach ($emoticons as $codigo => $imagem)
		{
			$str = str_replace($codigo, '<img src="/media/img/smileys/' . $imagem . '" alt="" />', $str);
		}
		*/
		
		$str = bbcode_quote($str);
	}
	
	return $str;
}

function bbcode_quote($str)
{
	$open = '<div class="ui black small message">';  
	$close = '</div>'; 
  
	preg_match_all ('/\[quote\]/i', $str, $matches);  
	$opentags = count($matches['0']);  
  
    preg_match_all ('/\[\/quote\]/i', $str, $matches);  
    $closetags = count($matches['0']);  
  
	if($opentags != $closetags)
	{
		$open = '';  
		$close = '';
	}
    $str = str_replace ('[' . 'quote]', $open, $str);  
    $str = str_replace ('[/' . 'quote]', $close, $str);  
  
    return $str;  
}

function url_slug($str)
{
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

// Atalho
function paginacao($url, $total, $start, $per_page, $view_pages = 7, $back_next = true, $on_bool = true)
{
	return pagination($url, $total, $start, $per_page, $view_pages, $back_next, $on_bool);
}

function pagination($url, $total, $start, $per_page, $view_pages = 7, $back_next = true, $on_bool = true)
{

	$prev	= '<i class="left chevron icon"></i>';
	$next	= '<i class="right chevron icon"></i>';
	$pag	= '';
	$pags	= ceil($total / $per_page);
	#$pags	= round($total / $per_page);

	$on = floor($start / $per_page) + 1;
  
  
	
	if ($back_next)
	{
    if($on - 1 <= 0) {
      $pag .= '<a href="'. sprintf($url, '1') .'" title="Página anterior" class="icon item">' . $prev . '</a> ';
    } else {
      $pag .= '<a href="'. sprintf($url, $on-1) .'" title="Página anterior" class="icon item">' . $prev . '</a> ';
    }
	}

	if($on > $view_pages+1)
	{
		$pag .= '<a href="' . sprintf($url, '1') . '" class="item" title="Página 1">1</a> <a href="javascript:;" class="item">...</a>';
	}
  
  if(!$total || $total == 1)
	{
		$pag.= '<a href="' . sprintf($url, '1') . '" class="item" title="Página 1">1</a>';
	}
	
	for ($i = $on-$view_pages ;$i < $on+$view_pages ; $i++)
	{
		if($i > 0 && $i <= $pags && $total && $total > 1)
		{
			$pag .= ($i == $on && $on_bool) ? ' <a class="item active" title="Página '.$i.'">'.$i.'</a>' : ' <a class="item" href="' . sprintf($url, $i) . '" title="Página '. $i .'">'. $i .'</a>';
		}
	}
	
	if($on < $pags-($view_pages-1))
	{
		$pag .= ' ... <a href="' . sprintf($url, $pags) . '" class="item" title="Página '. $pags .'">'. $pags .'</a>';
	}
	
	if ($back_next)
	{
    if(($on + 1) > $pags) {
      $pag .= '<a href="javascript:;" title="Próxima página" class="icon item">' . $next . '</a> ';
    } else {
      $pag .= '<a href="'. sprintf($url, $on+1) .'" title="Próxima página - '.$total.'" class="icon item">' . $next . '</a> ';
    }
	}
	
	return $pag;
}


function filtro_palavrao($text)
{
	$words = array(
		'buceta', 'caralho', 'karalho', 'boceta', 'vadia', 'piroca', 'rola',
		'vagabunda', 'pentelho', 'foda', 'prostituta',
		'merda', 'putaria', 'pinto', 'sexo', 'cuzão', 'cuzinho', 'cu', 'fdp', 'fdpt', 'fdpta',
		'transa', 'fude', 'fode', 'pariu', 'porra', 'puta', 'pênis', 'penis', 'pinto', 'bunda', 'xavasca'
	);
	
	$expression = array(
		'filho da puta',
		'filho de rapariga',
		'filho de uma puta',
		'filho d puta',
		'filho de kenga',
		'filho de uma kenga',
		'filho de uma quenga',
		'filho de quenga'
	);

	foreach ($expression as $word)
	{
		$regexp = '/' . implode(str_split($word), '+([^\w\d]*)') . '+([^\w\d:]*)r?s?/i';
		$text = preg_replace($regexp, ' **** ', $text);
	}
	
	foreach ($words as $word)
	{
		$regexp = '/\b' . implode(str_split($word), '+([^\w\d]*)') . '+([^\w\d:]*)r?s?\b/i';

		$text = preg_replace($regexp, ' **** ', $text);
	}

	return $text;
}

function RewriteRedirect($url, $expression) 
{
	if(preg_match($url, trim($_SERVER['REQUEST_URI']))) {
		$preg = preg_replace($url, $expression, trim($_SERVER['REQUEST_URI']));
		return header('Location: '.$preg);
	}
}

function dTime($fromTime, $toTime = 0, $showLessThanAMinute = false) {
  $toTime = $toTime == 0 ? time() : $toTime;
  $distanceInSeconds = round(abs($toTime - $fromTime));
  $distanceInMinutes = round($distanceInSeconds / 60);

  if ( $distanceInMinutes <= 1 ) {
    if ( !$showLessThanAMinute ) {
      return ($distanceInMinutes == 0) ? 'menos de 1m' : '1 min';
    } else {
      if ( $distanceInSeconds < 5 ) {
        return ($distanceInSeconds + 1).'s';
      }
      if ( $distanceInSeconds < 10 ) {
        return 'Menos de 10s';
      }
      if ( $distanceInSeconds < 20 ) {
        return 'Menos de 20s';
      }
      if ( $distanceInSeconds < 40 ) {
        return 'Meio min';
      }
      if ( $distanceInSeconds < 60 ) {
        return 'Menos de um min';
      }

      return '1 min';
    }
  }
  if ( $distanceInMinutes < 45 ) {
    return $distanceInMinutes . ' mins';
  }
  if ( $distanceInMinutes < 90 ) {
    return '1 hora';
  }
  if ( $distanceInMinutes < 1440 ) {
    return '' . round(floatval($distanceInMinutes) / 60.0) . ' horas';
  }
  if ( $distanceInMinutes < 2880 ) {
    return '1 dia';
  }
  if ( $distanceInMinutes < 43200 ) {
    return '' . round(floatval($distanceInMinutes) / 1440) . ' dias';
  }
  if ( $distanceInMinutes < 86400 ) {
    return '1 mês';
  }
  if ( $distanceInMinutes < 525600 ) {
    return round(floatval($distanceInMinutes) / 43200) . ' meses';
  }
  if ( $distanceInMinutes < 1051199 ) {
    return '1 ano';
  }

  return strtolower(round(floatval($distanceInMinutes) / 525600) . ' anos');
}