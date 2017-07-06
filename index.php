<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    ob_start(); 
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Neon Admin Panel" />
    <meta name="author" content="Laborator.co" />
    
    <title>Gallantique | Login</title>

    <link rel="stylesheet" href="neon/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css"  id="style-resource-1">
    <link rel="stylesheet" href="neon/css/font-icons/entypo/css/entypo.css"  id="style-resource-2">
    <link rel="stylesheet" href="neon/css/font-icons/entypo/css/animation.css"  id="style-resource-3">
    <link rel="stylesheet" href="neon/css/neon.css"  id="style-resource-5">
    <link rel="stylesheet" href="neon/css/custom.css"  id="style-resource-6">

    <script src="neon/js/jquery-1.10.2.min.js"></script>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    
    <!-- TS1387507135: Neon - Responsive Admin Template created by Laborator -->
</head>
    <body class="page-body login-page login-form-fall">
        <div id="container">
            <?php include('public/login_form.php');?>
        </div>


    <script src="neon/js/gsap/main-gsap.js" id="script-resource-1"></script>
    <script src="neon/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js" id="script-resource-2"></script>
    <script src="neon/js/bootstrap.min.js" id="script-resource-3"></script>
    <script src="neon/js/joinable.js" id="script-resource-4"></script>
    <script src="neon/js/resizeable.js" id="script-resource-5"></script>
    <script src="neon/js/neon-api.js" id="script-resource-6"></script>
    <script src="neon/js/jquery.validate.min.js" id="script-resource-7"></script>
    <script src="neon/js/neon-login.js" id="script-resource-8"></script>
    <script src="neon/js/neon-custom.js" id="script-resource-9"></script>
    <script src="neon/js/neon-demo.js" id="script-resource-10"></script>


    
    </body>
</html>