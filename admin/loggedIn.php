<?php
defined('HGTN') or exit;
$file = $_SERVER['REQUEST_URI']; $file = explode('/', $file); $file = end($file);
if($file == 'loggedIn.php' || !isset($_SESSION['admin_id'])) {
  exit('Esta página não está disponível através desse endereço URL.');
}

$page = get('p');
global $conn, $user;

// Permissions, channels and level types is only available to some user levels.
if($page) {
  $sql = "SELECT * FROM cms_channels c, cms_permissions p, cms_users_type_relationship r WHERE c.link = '$page' AND r.user_id = '".$user->data('id')."' AND r.type_user_id = p.type_user_id AND c.id = p.channel_id GROUP BY p.channel_id LIMIT 1";
  $res = $conn->data($sql);
  if(isset($res[0])) {
    $pageTitle = $res[0]->title;
  } else {
    $pageTitle = 'Página não encontrada!';
  }
} else {
  $sql = $conn->first("SELECT * FROM cms_channels WHERE id = 4");
  $pageTitle = $sql->title;
  $page = 'home';
}

if($page == 'settings') {
  $pageTitle = 'Configurações';
}


?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="iAdmin Content Management">
        <meta name="author" content="haroldogondim.github.io">
        <link href="assets/plugins/sweetalert/dist/sweetalert.css" rel="stylesheet" type="text/css">


        <link rel="shortcut icon" href="assets/images/favicon_1.ico">

        <title><?= $pageTitle; ?> -  iAdmin</title>

        <!--Morris Chart CSS -->
        <link rel="stylesheet" href="assets/plugins/morris/morris.css">

        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/core.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/components.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/pages.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />

        <script src="assets/js/jquery.min.js"></script>

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="assets/js/modernizr.min.js"></script>


    </head>


    <body class="fixed-left">

        <!-- Begin page -->
        <div id="wrapper">

            <!-- Top Bar Start -->
            <div class="topbar">

                <!-- LOGO -->
                <div class="topbar-left">
                  <div class="text-center">
                    <a href="index.php" class="logo">
                    <div class="icon-c-logo">Z</div>
                    <span><img src="assets/images/logo.jpg" width="50" height="50" /></span>
                    </a>
                  </div>
                </div>

                <!-- Button mobile view to collapse sidebar menu -->
                <div class="navbar navbar-default" role="navigation">
                    <div class="container">
                        <div class="">
                            <div class="pull-left">
                                <button class="button-menu-mobile open-left">
                                    <i class="ion-navicon"></i>
                                </button>
                                <span class="clearfix"></span>
                            </div>


                            <ul class="nav navbar-nav navbar-right pull-right">

                                <li class="hidden-xs">
                                    <a href="#" id="btn-fullscreen" class="waves-effect waves-light"><i class="icon-size-fullscreen"></i></a>
                                </li>
                                <li class="dropdown">
                                    <a href="" class="dropdown-toggle profile" data-toggle="dropdown" aria-expanded="true"><img src="../assets/uploads/avatar/<?= $user->data('avatar'); ?>" alt="user-img" class="img-circle"> </a>
                                    <ul class="dropdown-menu">
                                        <li><a href="?p=settings"><i class="ti-settings m-r-5"></i> Configurações</a></li>
                                        <li><a href="?p=logout"><i class="ti-power-off m-r-5"></i> Sair</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <!--/.nav-collapse -->
                    </div>
                </div>
            </div>
            <!-- Top Bar End -->


            <!-- ========== Left Sidebar Start ========== -->

            <div class="left side-menu">
                <div class="sidebar-inner slimscrollleft">
                  <div class="user-details">
                        <div class="pull-left">
                            <img src="../assets/uploads/avatar/<?= $user->data('avatar'); ?>" alt="" class="thumb-md img-circle">
                        </div>
                        <div class="user-info">
                            <div class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><?= $user->data('name'); ?> <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="?p=settings"><i class="md md-settings"></i> Configurações</a></li>
                                    <li><a href="?p=logout"><i class="md md-settings-power"></i> Sair</a></li>
                                </ul>
                            </div>
                            <p class="text-muted m-0"><?= $user->myLevelsInText(); ?></p>
                        </div>
                    </div>

                    <!--- Divider -->
                    <div id="sidebar-menu">
                        <ul>

                        	<li class="text-muted menu-title">Navegação</li>

                            <li class="has_sub">
                                <a href="index.php" class="waves-effect<? if($page == 'home' || !$page): ?> active<? endif; ?>"><i class="ti-home"></i> <span> Home </span> </a>
                            </li>
                            <?php
                            $parent_menu = $conn->data("SELECT *
                            FROM cms_channels AS c
                            INNER JOIN cms_permissions AS p
                              ON c.id = p.channel_id
                            INNER JOIN cms_users_type_relationship AS r
                              ON r.type_user_id = p.type_user_id
                            WHERE
                              c.status = '1' AND r.user_id='".$user->data('id')."' AND (c.parent IS NULL OR c.parent = 0) AND c.id != 4
                            GROUP BY p.channel_id
                            ORDER BY c.orderBy
                            ");
                            foreach($parent_menu as $row):
                            ?>
                            <li class="has_sub">
                                <a href="#" class="waves-effect"><i class="<? if($row->id == '1'): ?>ti-settings<? else: ?>ti-menu-alt<? endif; ?>"></i> <span> <?= $row->title; ?> </span> </a>
                                <?php
                                $submenu = $conn->data("SELECT * FROM cms_channels c, cms_permissions p, cms_users_type_relationship r WHERE c.status = '1' AND r.user_id='".$user->data('id')."' AND r.type_user_id = p.type_user_id AND c.id = p.channel_id AND c.parent = ? GROUP BY p.channel_id ORDER BY c.orderBy", array($row->id));
                                if(count($submenu) || isset($submenu[0]->title) || isset($submenu->title)):
                                ?>
                                <ul class="list-unstyled">
                                <? foreach($submenu as $sub): ?>
                                	<li <? if($page == $sub->link): ?> class="active"<? endif; ?>><a href="index.php?p=<?= $sub->link; ?>"><?= $sub->title; ?></a></li>
                                <? endforeach; ?>
                                </ul>
                                <? endif; ?>
                            </li>
                            <? endforeach; ?>

                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <!-- Left Sidebar End -->



            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container">

                        <!-- Page-Title -->
                        <div class="row">
                            <div class="col-sm-12">
                                <h4 class="page-title">Início</h4>
                                <p class="text-muted page-title-alt">Bem-vindo ao iAdmin!</p>
                            </div>
                        </div>

                        <!-- end row -->
                        
                        <div class="row">
                          <div class="portlet"><!-- /primary heading -->
                            <div class="portlet-heading">
                                <h3 class="portlet-title text-dark text-uppercase">
                                    <?= $pageTitle; ?>
                                </h3>
                                <div class="portlet-widgets">
                                    <a data-toggle="collapse" data-parent="#accordion1" href="#portlet2"><i class="ion-minus-round"></i></a>
                                    <span class="divider"></span>
                                    <a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div id="portlet2" class="panel-collapse collapse in">
                              <div class="portlet-body">
                              <?
                                if($page == 'settings') {
                                  include 'pages/' . $page . '.php';
                                } else {
                                  $allowedOpen = false;
                                  $channel_data = $conn->first("SELECT * FROM cms_channels WHERE link = ?", array($page));
                                  $permissionsToThisLevel_query = $conn->data("SELECT * FROM cms_permissions WHERE channel_id = ?", array($channel_data->id));
                                  $permissionsToThisLevel = array();
                                  foreach($permissionsToThisLevel_query as $row) {
                                    if($user->hasLevel($row->type_user_id)) {
                                      $allowedOpen = true;
                                    }
                                  }
                                  if($allowedOpen) {
                                    $file = 'pages/' . $page . '.php';
                                    if(file_exists($file)) {
                                      include $file;
                                    } else {
                                      include 'pages/error.php';
                                    }
                                  } else {
                                    include 'pages/error.php';
                                  }
                                }
                              ?>
                              </div>
                            </div>
                        </div>
                        </div>

                        <!-- end row -->


                    </div> <!-- container -->

                </div> <!-- content -->

                <footer class="footer text-right">
                    <?= date('Y'); ?> © iAdmin | Developed by <a target="_blank" href="http://haroldogondim.github.io">Haroldo Gondim</a>
                </footer>

            </div>


            <!-- ============================================================== -->
            <!-- End Right content here -->
            <!-- ============================================================== -->

            <!-- /Right-bar -->

        </div>
        <!-- END wrapper -->



        <script>
            var resizefunc = [];
        </script>

        <!-- jQuery  -->

        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/detect.js"></script>
        <script src="assets/js/fastclick.js"></script>

        <script src="assets/js/jquery.slimscroll.js"></script>
        <script src="assets/js/jquery.blockUI.js"></script>
        <script src="assets/js/waves.js"></script>
        <script src="assets/js/wow.min.js"></script>
        <script src="assets/js/jquery.nicescroll.js"></script>
        <script src="assets/js/jquery.scrollTo.min.js"></script>

        <script src="assets/plugins/peity/jquery.peity.min.js"></script>

        <!-- jQuery  -->
        <script src="assets/plugins/waypoints/lib/jquery.waypoints.js"></script>
        <script src="assets/plugins/counterup/jquery.counterup.min.js"></script>



        <script src="assets/plugins/morris/morris.min.js"></script>
        <script src="assets/plugins/raphael/raphael-min.js"></script>

        <script src="assets/plugins/jquery-knob/jquery.knob.js"></script>

        <script src="assets/pages/jquery.dashboard.js"></script>

        <script src="assets/js/jquery.core.js"></script>
        <script src="assets/js/jquery.app.js"></script>

        <script src="assets/plugins/sweetalert/dist/sweetalert.min.js"></script>
        <script src="assets/pages/jquery.sweet-alert.init.js"></script>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('.counter').counterUp({
                    delay: 100,
                    time: 1200
                });

                $(".knob").knob();

            });
        </script>
        <script src="assets/plugins/notifyjs/dist/notify.min.js"></script>
        <script src="assets/plugins/notifications/notify-metro.js"></script>
</body>
</html>
