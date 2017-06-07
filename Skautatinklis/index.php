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
                    <li><a href="#intro">Skautatinklis</a></li>
                    <li><a href="#stovyklos">Stovyklos</a></li>
                    <li><a href="#kontaktai">Kontaktai</a></li>
                    <?php
                    if (!$session->logged_in) {
                    ?>
                    <li><a href="#" data-toggle="modal" data-target="#modal1" class="btn btn-blue">Prisijungti</a></li>
                    <li><a href="registruotis.php" class="btn btn-blue">Registruotis</a></li>
                    <?php
                    if ($form->num_errors > 0 && (bccomp(strip_tags($form->error("loginAttempt"), '1', 5)) == 0)) {
                        echo "<li><font size=\"3\" color=\"#ff0000\">Prisijungti nepavyko.</font></li>";
                    } ?>
                    <?php
                    }
                    else{
                        if($session->isAdmin()){
                            ?>
                    <li><a href="/admin/admin.php">Admin sąsaja</a></li>
                            <?php
                        }
                        else if($session->isModerator()){
                            ?>
                    <li><a href="stovykluadministravimas.php">Stovyklų administravimas</a></li>
                            <?php
                        }
                        else {
                        ?>
                        <li><a href="manostovyklos.php">Mano stovyklos</a></li>
                        <?php
                        }
                        ?>
                    <li><a href="manopaskyra.php">Sveiki, <b><?php echo $session->username; ?></b></a></li>
                    <li><a href="process.php" class="btn btn-sm btn-blue">Atsijungti</a></li>
                    <?php
                    }
                    ?>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>
    <header id="intro">
        <div class="container">
            <div class="table">
                <div class="header-text">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h3 class="light white">Sveiki atvykę.</h3>
                            <h1 class="white typed">Toliau nuo kranto, arčiau prie jūros!</h1>
                            <span class="typed-cursor">|</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <section id="stovyklos" class="section section-padded">
        <div class="cut cut-top"></div>
            <div class="container">
                <div class="row text-center title">
                    <h2>Stovyklos</h2>
                    <h4 class="light muted">Kuriose privalai sudalyvauti</h4>
                </div>
                <div class="row events">
                    <?php 
                    $starredEvents = $database->getStarredEventsData();
                    while($result = mysqli_fetch_array($starredEvents)){
                    ?>
                    <div class="col-md-4">
                        <div class="event">
                            <div class="icon-holder">
                                    <img src="img/icons/camp.png" alt="" class="icon">
                            </div>
                            <h4 class="heading"><?php echo $result['title']; ?></h4>
                            <h6 class="heading"><?php echo $result['date']; ?></h6>
                            <p class="description"><?php echo $result['short_description']; ?></p>
                            <p><a class="description" href="stovyklos.php#stovykla<?php echo $result['id'];?>">Daugiau informacijos čia.</a></p>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                </div>
                <div class="row text-center title">
                    <h4><a href="stovyklos.php">Visos stovyklos</a></h4>
                </div>
            </div>
        <div class="cut cut-bottom"></div>
    </section>
	<div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="modal1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content modal-popup">
                    <a href="#" class="close-link"><i class="icon_close_alt2"></i></a>
                    <h3 class="white">Prisijungti</h3>
                    <!--<form action="" class="popup-form">-->
                    <form action="process.php" method="POST" class="popup-form">
                        <input name="user" type="text" class="form-control form-white" placeholder="Prisijungimo vardas" value="<?php echo $form->value("user"); ?>">
                        <?php echo $form->error("user"); ?>
                        <input name="pass" type="password" class="form-control form-white" placeholder="Slaptažodis" value ="<?php echo $form->value("pass"); ?>">
                        <?php echo $form->error("pass"); ?>
                        <div class="checkbox-holder text-left">
                            <div class="checkbox">
                                <input type="checkbox" value="true" id="squaredOne" name="remember" />
                                <label for="squaredOne"><span><strong>Prisiminti mane</strong></span></label>
                            </div>
                        </div>
                        <a style="color: white;" href="priminimas.php">Pamiršote slaptažodį?</a>
                        <input type="hidden" name="sublogin" value="1"/>
                        <input type="submit" class="btn btn-submit" value="Prisijungti"/>
                    </form>
                </div>
            </div>
	</div>
    <footer id="kontaktai">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 text-center-mobile">
                        <h3 class="white">Kontaktai</h3>
                        <h5 class="light regular light-white"><b>Skautatinklis</b></h5>
                        <h5 class="light regular light-white">El. paštas: <b>infoskautatinklis@gmail.com</b></h5>
                </div>
                <div class="col-sm-6 text-center-mobile">
                    <h3 class="white">Susisiekite su mumis <span class="open-blink"></span></h3>
                    <div class="col-sm-10 text-center-mobile">
                        <?php
                        if (isset($_SESSION['contactform'])) {
                            unset($_SESSION['contactform']);
                            echo "<p class=\"text-center\"><h5 class=\"regular white\">Jūsų užklausos forma buvo sėkmingai išsiųsta</h5></p><br><br>";
                        } else {
                            echo "<div class=\"text-center\">";
                            ?>
                            <form action="process.php" method="POST" class="contact-form">
                                <div class="col-sm-6 text-center-mobile">
                                    <p class="regular white">El. paštas</p>
                                    <p><input name="email" type="text" class="form-control input-narrow" placeholder="Jūsų el. paštas" value="<?php echo $form->value("email"); ?>" required="required">
                                    <?php echo $form->error("email"); ?></p>
                                </div>
                                <div class="col-sm-6 text-center-mobile">
                                    <p class="regular white">Tema</p>
                                    <p><input name="heading" type="text" class="form-control input-narrow" placeholder="Tema" value="<?php echo $form->value("heading"); ?>" required="required">
                                    <?php echo $form->error("heading"); ?></p>
                                </div>
                                <div class="col-sm-12 text-center-mobile">
                                    <p class="regular white">Žinutė</p>
                                    <p><textarea rows="10" name="message" class="contact-form" required="required"><?php echo $form->value("message"); ?></textarea></p>
                                    <?php echo $form->error("message"); ?>
                                </div>
                                <div class="text-center-mobile">
                                    <input type="hidden" name="contactform" value="1"/>
                                <input type="submit" class="btn btn-form" value="Siųsti"/>
                                </div>
                            </form>
                            <?php 
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="row bottom-footer text-center-mobile">
                <div class="col-sm-8">
                    <p>&copy; 2016 Visos teisės saugomos. <a>Aidas Balčaitis</a> produktas.</p>
                </div>
                <div class="col-sm-4 text-right text-center-mobile">
                    <ul class="social-footer">
                        <li><a href="https://www.facebook.com/juruskautija/"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="https://www.instagram.com/juruskautija/"><i class="fa fa-instagram"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <!-- Holder for mobile navigation -->
    <div class="mobile-nav">
        <ul>
        </ul>
        <a href="#" class="close-link"><i class="arrow_up"></i></a>
    </div>
    <!-- Scripts -->
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/typewriter.js"></script>
    <script src="js/jquery.onepagenav.js"></script>
    <script src="js/main.js"></script>
</body>

</html>
