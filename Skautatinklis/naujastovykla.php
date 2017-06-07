<?php
include("include/session.php");
if (0) {
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
    <link rel="mask-icon" href="img/favicons/safari-pinned-tab.svg">
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
                            <li><a href="stovykluadministravimas.php">Grįžtį į stovyklų administravimą</a></li>
                            <li class="active"><a href="#newcamp">Kurti naują stovyklą</a></li>
                            <!--<li><a href="manopaskyra.php">Mano paskyra</a></li>-->
                            <li><a href="manopaskyra.php">Sveiki, <b><?php echo $session->username; ?></b></a></li>
                            <li><a href="process.php" class="btn btn-sm btn-blue">Atsijungti</a></li>
                        </ul>
                </div>
                <!-- /.navbar-collapse -->
            </div>
            <!-- /.container-fluid -->
	</nav>
	<section id="campsmanagement" class="section section-padded">
		<div class="container">
                    <div class="row text-center title">
                            <h2>Kurti naują stovyklą</h2>
                    </div>
                    <div class="row">
                        <div class="center-block col-md-6" style="float: none;">
                            <?php
                            if (isset($_SESSION['newevent'])) {
                                unset($_SESSION['newevent']);
                                echo "<p style=\"text-align: center;\"><b>$session->username</b>, stovykla buvo sėkmingai pridėta.<br><br>";
                            } else {
                                echo "<div style=\"text-center\">";
                                if ($form->num_errors > 0) {
                                    echo "<font size=\"3\" color=\"#ff0000\">Klaidų: " . $form->num_errors . "</font>";
                                } else {
                                    echo "";
                                }
                                ?>
                            <div class="account">
                                <table class="table table-hover">
                                    <form action="process.php" method="POST" class="popup-form">
                                        <tbody>
                                            <tr>
                                                <td>Renginio pavadinimas</td>
                                                
                                                <td><input name="title" type="text" class="form-control input-narrow" placeholder="Pavadinimas" value="<?php echo $form->value("title"); ?>" required="required">
                                                <?php echo $form->error("title"); ?></td>
                                            </tr>
                                            <tr>
                                                <?php $countStarredEvents = $database->countStarredlEvents(); ?>
                                                <td colspan="2"><input type="checkbox" id="starred" name="starred" value="1" 
                                                    <?php if($form->value("starred") == 1) { 
                                                        echo "checked"; 
                                                    }
                                                    if($countStarredEvents['count'] >= MAX_STARRED_EVENTS) {
                                                        echo "disabled";
                                                    }?>>  Pateikti pirmame puslapyje<br>
                                                <small>Pažymėjus stovykla bus rodoma pagrindiniame skautatinklio puslapyje.</small><br>
                                                <small>Šiuo metu pažymėta stvoyklų yra <?php echo $countStarredEvents['count'] . "/" .  MAX_STARRED_EVENTS?>.</small></td>
                                            </tr>
                                            <tr>
                                                <td>Renginio pradžia</td>
                                                <td><input name="date" type="text" class="form-control input-narrow" placeholder="Data" value="<?php echo $form->value("date"); ?>" required="required">
                                                <?php echo $form->error("date"); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Trukmė dienomis</td>
                                                <td><input name="duration" type="text" class="form-control input-narrow" placeholder="Dienų kiekis" value="<?php echo $form->value("duration"); ?>" required="required">
                                                <?php echo $form->error("duration"); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Vieta</td>
                                                <td><input name="whereabout" type="text" class="form-control input-narrow" placeholder="Vieta" value="<?php echo $form->value("whereabout"); ?>" required="required">
                                                <?php echo $form->error("whereabout"); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Kaina eurais</td>
                                                <td><input name="price" type="text" class="form-control input-narrow" placeholder="Kaina eurais" value="<?php echo $form->value("price"); ?>" required="required">
                                                <?php echo $form->error("price"); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Vietų kiekis stovykloje</td>
                                                <td><input name="capacity" type="text" class="form-control input-narrow" placeholder="Vietų kiekis" value="<?php echo $form->value("capacity"); ?>" required="required">
                                                <?php echo $form->error("capacity"); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Atsakingas asmuo</td>
                                                <td><input name="leader" type="text" class="form-control input-narrow" placeholder="Vardas Pavardė" value="<?php echo $form->value("leader"); ?>" required="required">
                                                <?php echo $form->error("leader"); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Kontaktinis numeris</td>
                                                <td><input name="phone_number" type="text" class="form-control input-narrow" placeholder="Telefono numeris" value="<?php echo $form->value("phone_number"); ?>" required="required">
                                                <?php echo $form->error("phone_number"); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Trumpas aprašymas</td>
                                                <td><textarea rows="5" name="short_description" class="form-control input-narrow" required="required"><?php echo $form->value("short_description"); ?></textarea>
                                                <?php echo $form->error("short_description"); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Detalus aprašymas</td>
                                                <td><textarea rows="10" name="description" class="form-control input-narrow" required="required"><?php echo $form->value("description"); ?></textarea>
                                                <?php echo $form->error("description"); ?></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><input type="hidden" name="newevent" value="1"/>
                                                <input type="submit" class="btn btn-submit" value="Sukurti"/></td>
                                            </tr>
                                        </form>
                                    </tbody>
                                </table>
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
<?php
}
?>