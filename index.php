<?php
session_start();
ob_start();
define('HGTN', true);

function displayErrors() {
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
}

//displayErrors();

session_cache_expire(86400);

header('Content-Type: text/html; charset=utf-8');

date_default_timezone_set('America/Sao_Paulo');

$url_params = explode('/', $_SERVER['REQUEST_URI']);

include('lib/Database.php');
include('lib/Cache.php');
include('lib/Core.php');
include('lib/Functions.php');
include('lib/User.php');
//include('lib/autoload.php');

// deslogar caso conta seja deletada.
if(User::loggedIn() && !User::data('username')) {
  User::Deslogar();
  //exit('unlogged-missing-username');
}

if(isset($_GET['logout']) || isset($_GET['sair'])) {
	User::Deslogar();
	header('Location: index.php');
}

$url = get('url', false);
$sair = get('sair', false);

// Inicialização de classes, funções e derivados.
if(isset($url_params[1]) && !empty($url_params[1]) && !in_array($url_params[1], array('index.php')) && !in_array(get('url'), array('main'))) 
{
	$param = trim($url_params[1]);
	$param_c = $param;
	if(file_exists('app/controllers/' . $param_c. '.php'))
	{
		include_once 'app/controllers/' . $param_c . '.php';
		if(class_exists($param_c)) 
		{
			$Module = new $param_c;
		}
	}
}

if(!class_exists('Main')) {
	include 'app/controllers/main.php';
	$Main = new Main();
}

global $title, $description, $meta_keywords, $moderate, $logado_painel;
?>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
    <meta name="language" content="pt-br">
    <meta name="author" content="gabrielrvita">
    
    <title>Geração Z - O Fórum mais badalado da Internet!</title>
    

    <link rel="icon" href="/favicon.png">

    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Titillium+Web:400,600,300,700">

    <link rel="stylesheet" href="/assets/semantic.min.css">
    <link rel="stylesheet" href="/assets/flaticon.css">
    <link rel="stylesheet" href="/assets/style.css" class="cssfx">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.11.3/jquery-ui.min.js" integrity="sha256-xI/qyl9vpwWFOXz7+x/9WkG5j/SVnSw21viy8fWwbeE=" crossorigin="anonymous"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/2.1.2/jquery.scrollTo.min.js"></script>
    <script type="text/javascript" src="/assets/jquery.js"></script>
    <script type="text/javascript" src="/assets/semantic.min.js"></script>
    <script type="text/javascript" src="/assets/plugins.js"></script>
    <script type="text/javascript" src="/assets/front.js"></script>
    <? if(User::loggedIn()): ?>
    <script>
    var User = {
      nick: '<?= User::data('username'); ?>',
      id: '<?= User::data('id'); ?>',
      created: '<?= User::data('createdAt'); ?>',
      active: '<?= User::active(); ?>'
    }
    </script>
    <? endif; ?>
</head>

<body>
<? if(isset($_SESSION['account_create'])): ?>
<div class="ui tiny modal" id="modal-register">
  <i class="close icon"></i>
  <div class="header">
    Bem-vindo ao Fórum Geração Z!
  </div>
  <div class="image content">
    <div class="ui medium image">
      <a href="/settings/avatar"><img src="/assets/uploads/avatar/default.png"></a>
    </div>
    <div class="description">
      <div class="ui header">Esperamos que você tenha uma ótima estadia por aqui.</div>
      <p>
        <a href="/settings/password">Alterar senha</a><br />
        <a href="/settings/avatar">Alterar avatar</a><br />
        <a href="/settings/signture">Alterar assinatura</a><br />
        <a href="/settings/my">Seus dados</a><br />
      </p>
      <p>
        <a href="/topic/new">Criar um tópico</a><br />
        <a href="/profile/<?= User::data('username'); ?>">Visitar meu perfil</a>
      </p>
    </div>
  </div>
  <div class="actions">
    <div class="ui black deny button">
      Depois faço isso
    </div>
  </div>
</div>
<script>$("#modal-register").modal('show');</script>
<? unset($_SESSION['account_create']); endif; ?>
   
<!-- Following Menu -->
<!-- Sidebar Menu -->
<div class="ui vertical inverted sidebar menu">
  <a href="/" class="active item">Home</a>
  <a class="item">Work</a>
  <a class="item">Company</a>
  <a class="item">Careers</a>
  <? if(!User::loggedIn()): ?>
  <a href="/login" class="item">Login</a>
  <a href="/register" class="item">Registrar-se</a>
  <? endif; ?>
</div>


<!-- Page Contents -->
<div class="pusher" id="pusher">
  
  <div class="ui inverted vertical aligned segment" id="header">
  
    <div class="ui container">
      <div class="ui large secondary inverted pointing menu" id="menu">
        <a class="toc item">
          <i class="sidebar icon"></i>
        </a>
        <a href="/" class="item active"><i class="icon heart"></i> Home</a>
        <a class="item">Work</a>
        <a class="item">Company</a>
        <a class="item">Careers</a>
        <div class="right item">
          <? if(!User::loggedIn()): ?>
          <a href="/login" class="ui inverted button" id="login-button">Login</a>
          <? else: ?>
          <!-- <a id="loggedIn" href="#" class="ui violet button"><img class="ui tiny image" src="/assets/uploads/avatar/<?= User::data('avatar'); ?>" /> <?= User::data('username'); ?></a> -->
          <div class="content" id="loggedIn">
            <a href="/profile/<?= User::data('username'); ?>"><img class="ui avatar image" src="/assets/uploads/avatar/<?= User::data('avatar'); ?>"> <b><?= User::data('username'); ?></a></b></i>
          </div>
          <div class="ui flowing popup top left transition hidden">
            <div class="ui grid">
              <div class="ui center aligned column dropdown-login">
                <h4>Usuário</h4>
                <a href="/profile/<?= User::data('username'); ?>">Perfil</a><br />
                <a href="/topic/new">Novo tópico</a><br />
                <a href="/topic/saved">Postagens salvas</a><br />
                <a href="/logout">Sair</a>
                <div class="ui divider"></div>
                <h4>Configurações</h4>
                <a href="/settings/avatar">Alterar avatar</a><br />
                <a href="/settings/signature">Alterar assinatura</a><br />
                <a href="/settings/password">Alterar senha</a><br />
                <a href="/settings/my">Meus dados</a><br />
              </div>
            </div>
          </div>
          <? endif; ?>
        </div>
      </div>
    </div>
  
    <div class="ui vertical segment">
      <div class="ui container">
        <div class="row">
          <div class="ui stackable grid">
            <div class="four wide column"><img src="/assets/images/logo.jpg" class="ui fluid image" /></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <?= $Main->setPage(); ?>

  <div class="ui inverted vertical footer segment">
    <div class="ui container center aligned">
      <h4 class="ui inverted header">Geração Z - Todos os direitos reservados.</h4>
      <p>Site desenvolvido por <i class="icon github"></i><a href="http://haroldogondim.github.io" target="_blank">Haroldo Gondim</a>.</p>
    </div>
  </div>
</div>
<!-- <?= db::$queries; ?> -->

</body>
    
</html>