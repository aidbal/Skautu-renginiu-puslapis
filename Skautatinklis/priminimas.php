<?php
include("include/session.php");
?>
<!DOCTYPE html>
<html lang="lt">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Skautatinklis</title>
    <meta name="description" content="Tikrieji skautų namai - čia!" />
    <meta name="keywords" content="skautai, jūra, vanduo, stovyklos, miškas, jachta, laisvalaikis, pramogos" />
    <meta name="author" content="Aidas Balcaitis" />
    <link rel="apple-touch-icon" sizes="180x180" href="img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="img/favicons/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="img/favicons/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="img/favicons/manifest.json">
    <link rel="mask-icon" href="img/favicons/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="img/favicons/favicon.ico">
    <meta name="msapplication-config" content="img/favicons/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
    <!-- Normalize -->
    <link rel="stylesheet" type="text/css" href="css/normalize.css">
    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <!-- Animate.css -->
    <link rel="stylesheet" type="text/css" href="css/animate.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.1.0/css/font-awesome.min.css">
    <!-- Elegant Icons -->
    <link rel="stylesheet" type="text/css" href="fonts/eleganticons/et-icons.css">
    <!-- Main style -->
    <link rel="stylesheet" type="text/css" href="css/main.css">
</head>

<body>
	<div class="preloader">
		<img src="img/loader.gif" alt="Preloader image">
	</div>
	<nav class="navbar navbar-fixed-top" style="position: fixed; top: 0px; margin-top: 0px; opacity: 1;">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="index.php"><img src="img/logo.png" data-active-url="img/logo-active.png" alt=""></a>
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav navbar-right main-nav">
                            <li><a href="stovykluadministravimas.php">Grįžtį į pradžią</a></li>
                            <li class="active"><a>Naujo slaptažodžio generavimas</a></li>
                        </ul>
                </div>
                <!-- /.navbar-collapse -->
            </div>
            <!-- /.container-fluid -->
	</nav>
	<section id="campsmanagement" class="section section-padded">
		<div class="container">
                    <div class="row text-center title">
                            <h2>Naujo slaptažodžio generavimas</h2>
                            <?php
                            if (isset($_SESSION['forgotpass'])) {
                                unset($_SESSION['forgotpass']);
                                echo "<p>Naujas slaptažodis buvo išsiųstas į Jūsų paštą.</p><br><br>";
                            } else {
                                if ($form->num_errors > 0) {
                                    echo "<font size=\"3\" color=\"#ff0000\">Klaidų: " . $form->num_errors . "</font>";
                                } else {
                                    echo "";
                                }
                                ?>
                    </div>
                    <div class="row">
                        <div class="center-block col-md-6" style="float: none;">
                            
                            <div class="account">
                                    <form action="process.php" method="POST" class="popup-form">
                                        Įveskite prisijungimo vardą:
                                        <input name="user" type="text" class="form-control input-narrow" placeholder="Prisijungimo vardas" value="<?php echo $form->value("user"); ?>" required="true">
                                        <input type="hidden" name="subforgot" value="1"/>
                                        <input type="submit" class="btn btn-submit" value="Generuoti"/>
                                    </form>
                            </div>
                            <?php 
                            }
                            ?>
                        </div>
                    </div>
		</div>
		<div class="cut cut-bottom"></div>
	</section>
	<!-- Holder for mobile navigation -->
	<div class="mobile-nav">
		<ul>
		</ul>
		<a href="#" class="close-link"><i class="arrow_up"></i></a>
	</div>
	<!-- Scripts -->
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/owl.carousel.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/wow.min.js"></script>
	<script src="js/typewriter.js"></script>
	<script src="js/main.js"></script>
        <script src="js/jquery.onepagenav.js"></script>
</body>

</html>