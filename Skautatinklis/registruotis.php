<?php
include("include/session.php");
if ($session->logged_in) {
    header("Location: index.php");
}
else {
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

<body class="register-page">
	<div class="preloader">
		<img src="img/loader.gif" alt="Preloader image">
	</div>
        <nav class="navbar">
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
                                    <li><a href="index.php">Grįžti į Skautatinklį</a></li>
				</ul>
			</div>
			<!-- /.navbar-collapse -->
		</div>
		<!-- /.container-fluid -->
	</nav>
        <div class="container" style="padding-top:100px">
            <div class="row text-center title">
                <h3 class="white"><b>Registracija</b></h3>
            </div>
            <div class="row">
                <div class="center-block col-md-6" style="float: none;">
                    <?php
                    if (isset($_SESSION['regsuccess'])) {
                        $regname = $_SESSION['reguname'];
                        $regemail = $_SESSION['regemail'];
                        unset($_SESSION['regsuccess']);
                        echo "<br><p class=\"white\" style=\"text-align:center\"><b>$regname</b>, sėkmingai prisiregistravote. <br>Visa informacija buvo nusiųsta el. paštu: <b>$regemail</b><br><br>";
                    } else {
                        echo "<div style=\"text-align: center\">";
                        if ($form->num_errors > 0) {
                            echo "<font size=\"3\" color=\"#ff0000\">Klaidų: " . $form->num_errors . "</font>";
                        } else {
                            echo "";
                        }
                        ?>
                        <form action="process.php" method="POST" class="popup-form">
                            <input name="user" type="text" class="form-control form-white" placeholder="Prisijungimo vardas" value="<?php echo $form->value("user"); ?>" required="required">
                            <?php echo $form->error("user"); ?>
                            <input name="pass" type="password" class="form-control form-white" placeholder="Slaptažodis" value="<?php echo $form->value("pass"); ?>" required="required">
                            <?php echo $form->error("pass"); ?>
                            <input name="email" type="email" class="form-control form-white" placeholder="El. paštas" value="<?php echo $form->value("email"); ?>" required="required">
                            <?php echo $form->error("email"); ?>
                            <input name="name" type="text" class="form-control form-white" placeholder="Jūsų vardas" value="<?php echo $form->value("name"); ?>" required="required">
                            <?php echo $form->error("name"); ?>
                            <input name="surname" type="text" class="form-control form-white" placeholder="Pavardė" value="<?php echo $form->value("surname"); ?>" required="required">
                            <?php echo $form->error("surname"); ?>
                            <input name="borndate" type="text" class="form-control form-white" placeholder="Gimimo data (YYYY-MM-DD)" value="<?php echo $form->value("borndate"); ?>" required="required">
                            <?php echo $form->error("borndate"); ?>
                            <input name="leader" type="text" class="form-control form-white" placeholder="Vadovo Vardas ir Pavardė*" value="<?php echo $form->value("leader"); ?>">
                            <?php echo $form->error("leader"); ?>
                            <p class="white small" style="text-align:left;">*Vadovo įvesti nebūtina</p>
                            <div style="display: inline-block;"class="g-recaptcha" data-sitekey="6LdP3A8UAAAAADCqkQyrz9CRreBgfR4hpHJb62qg" data-theme="dark"></div>
                            <?php echo $form->error("recaptcha"); ?>
                            <input type="hidden" name="subjoin" value="1"/>
                            <input type="submit" class="btn btn-submit" value="Registruotis"/>
                        </form>
                    <?php 
                    echo "</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
	<!-- Holder for mobile navigation -->
	<div class="mobile-nav">
            <ul>
            </ul>
            <a href="#" class="close-link"><i class="arrow_up"></i></a>
	</div>
	<!-- Scripts -->
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
        <script src="https://www.google.com/recaptcha/api.js?hl=lt" async defer></script>
	<script src="js/main.js"></script>
</body>

</html>
<?php
}
?>