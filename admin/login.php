<?php
defined('HGTN') or exit;
$file = $_SERVER['REQUEST_URI'];
$file = explode('/', $file);
$file = end($file);
if($file == 'login.php') {
  exit('Esta página não está disponível através desse endereço URL.');
}

global $dataUser, $user;

$message = false;
if($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = post('username');
  $password = post('password');
  $remember = post('check-remember');
  if(password_verify($password, $dataUser->password)) {
    $authkey = md5($user->salt . $dataUser->id);
    $_SESSION['admin_id'] = $dataUser->id;
    if($remember) {
      setcookie('admin_id', $dataUser->id, time() + (60 * 60 * 24 * 31));
      setcookie('admin_authkey', $authkey, time() + 5184000 * 3);
    }
    header('Location: index.php');
    exit;
  } else {
    $message = '<div class="alert alert-danger"><strong>Oops!</strong> Sua senha está incorreta! Tente novamente.</div>';
  }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="iAdmin Content Management">
        <meta name="author" content="haroldogondim.github.io">

        <link rel="shortcut icon" href="assets/images/favicon_1.ico">

        <title>Fazer Login - iAdmin</title>

        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/core.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/components.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/pages.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="assets/js/modernizr.min.js"></script>

    </head>
    <body>

        <div class="account-pages"></div>
        <div class="clearfix"></div>
        <div class="wrapper-page"><div class="row"><center><img src="assets/images/logo.jpg" alt="image" class="img-responsive" style="max-width:150px;" /></center></div></div>
        <div class="wrapper-page">
        	<div class="card-box">
            <div class="panel-heading">
                <h3 class="text-center"> Fazer login no iAdmin </h3>
            </div>
            <div class="panel-body">
            <? if(isset($message)) echo $message; ?>
            <form class="form-horizontal m-t-20" name="form-login" action="" method="post">

                Bem-vindo, <?= $dataUser->username; ?>
                <div class="form-group">
                    <div class="col-xs-12">
                        <input class="form-control" name="password" type="password" required="" placeholder="Senha">
                    </div>
                </div>

                <div class="form-group ">
                    <div class="col-xs-12">
                        <div class="checkbox checkbox-primary">
                            <input id="checkbox-signup" type="checkbox" name="check-remember">
                            <label for="checkbox-signup">
                                Lembrar-me
                            </label>
                        </div>

                    </div>
                </div>

                <div class="form-group text-center m-t-40">
                    <div class="col-xs-12">
                        <button class="btn btn-success btn-block text-uppercase waves-effect waves-light" type="submit">Entrar</button>
                    </div>
                </div>
            </form>

            </div>
            </div>

        </div>




    	<script>
            var resizefunc = [];
        </script>

        <!-- jQuery  -->
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/detect.js"></script>
        <script src="assets/js/fastclick.js"></script>
        <script src="assets/js/jquery.slimscroll.js"></script>
        <script src="assets/js/jquery.blockUI.js"></script>
        <script src="assets/js/waves.js"></script>
        <script src="assets/js/wow.min.js"></script>
        <script src="assets/js/jquery.nicescroll.js"></script>
        <script src="assets/js/jquery.scrollTo.min.js"></script>


        <script src="assets/js/jquery.core.js"></script>
        <script src="assets/js/jquery.app.js"></script>

	</body>
</html>
