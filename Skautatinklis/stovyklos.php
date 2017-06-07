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
    <!-- Favicons (created with http://realfavicongenerator.net/)-->
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
                    <li><a href="index.php">Grįžtį į skautatinklį</a></li>
                    <li class="active"><a href="#stovyklos">Visos stovyklos</a></li>
                    <li><a href="#kontaktai">Kontaktai</a></li>
                    <?php
                    if (!$session->logged_in) {
                    ?>
                    <li><a href="#" data-toggle="modal" data-target="#modal1" class="btn btn-blue">Prisijungti</a></li>
                    <li><a href="registruotis.php" class="btn btn-blue">Registruotis</a></li>

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
                        <li class=\"active\"><a href="manopaskyra.php">Sveiki, <b><?php echo $session->username; ?></b></a></li>
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
    <section id="stovyklos" class="section section-padded">
        <div class="container">
            <div class="row text-center title">
                    <h2>Stovyklos</h2>
                    <h4 class="light muted">Kuriose privalai sudalyvauti</h4>
            </div>
            <?php
            if (isset($_SESSION['unregisterfromevent'])) {
                unset($_SESSION['unregisterfromevent']);
                echo "<br><br><p style=\"text-align: center;\"><b>$session->username</b>, sėkmingai išsiregistravote iš stovyklos.<p>";
            }
            if (isset($_SESSION['registertoevent'])) {
                unset($_SESSION['registertoevent']);
                echo "<br><br><p style=\"text-align: center;\"><b>$session->username</b>, sėkmingai prisiregistravote į stovyklą.<p>";
            }
            ?>
            <div class="row events">
                <?php 
                $starredEvents = $database->getAllEvents();
                while($result = mysqli_fetch_array($starredEvents)){
                ?>
                    <div id="stovykla<?php echo $result['id'];?>" class="center-block col-md-10" style="float: none;">
                            <div class="event">
                                    <div class="icon-holder">
                                            <img src="img/icons/camp.png" alt="" class="icon">
                                    </div>
                                    <h4 class="heading"><?php echo $result['title'];?></h4>
                                    <h6 class="heading"><?php echo $result['date'];?></h6>
                                    <p class="description">
                                        Kur? <b><?php echo $result['whereabout'];?></b><br>
                                        Kaina? <b><?php echo $result['price'];?>€</b><br>
                                        Trukmė dienomis? <b><?php echo $result['duration'];?></b><br>
                                        Užsiregistravusiųjų kiekis: <b><?php 
                                            $registeredUsersCount = $database->countRegisteredUsers($result['id']);
                                            echo $registeredUsersCount['count'] . "/" . $result['capacity'];
                                        ?></b><br>
                                        Atsakingas asmuo: <b><?php echo $result['leader'];?> (<?php echo $result['phone_number'];?>)</b><br>
                                        <br>
                                        <?php echo $result['description'];?>
                                    </p>
                                    <?php
                                    if ($session->logged_in) {
                                        //checks if user is registered to this event
                                        $checkUserEvent = $database->checkUserEvent($result['id'], $session->username);
                                        //If user is registered to event
                                        if($checkUserEvent['count'] != 0){
                                            ?>
                                            <p class="description" style="text-align: center;"><b>
                                            Jūs užsiregistravote į šį renginį.
                                            </b></p>
                                            <form action="process.php" method="POST" class="description">
                                                <p>
                                                    <input type="hidden" name="unregisterfromevent" value="1"/>
                                                    <input type="hidden" name="eventid" value="<?php echo $result['id'];?>"/>
                                                    <input class="btn btn-submit" style="margin-top: 0px; padding: 0px 0px;" type="submit" value="Išsiregistruoti"/>
                                                </p>
                                            </form>
                                        <?php
                                        }
                                        else if ($session->isModerator()){
                                            ?>
                                            <br><p><a class="description" href="redaguotistovykla.php?id=<?php echo $result['id'];?>">Redaguoti stovyklą</a></p>
                                            <?php
                                        }
                                        //Checks if there is still left some place to register
                                        else if ($registeredUsersCount['count'] < $result['capacity']){
                                            ?>
                                            <form action="process.php" method="POST" class="description">
                                                <p>
                                                    <input type="hidden" name="registertoevent" value="1"/>
                                                    <input type="hidden" name="eventid" value="<?php echo $result['id'];?>"/>
                                                    <input class="btn btn-submit btn-submit-register" type="submit" value="Registruotis"/>
                                                </p>
                                            </form>
                                            <?php
                                        }
                                        else{
                                            ?>
                                            <br><p class="description" style="text-align: center;"><b>Vietų nebėra.</b></p>
                                            <?php
                                        }
                                    }
                                    else {
                                    ?>
                                        <br><p class="description" style="text-align: center;"><b>Norint užsiregistruoti į stovyklą - prisijunkite.</b></p>
                                    <?php
                                    }
                                    ?>
                            </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="cut cut-bottom">
        </div>
    </section>
    <div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                            echo "<p style=\"text-align: center;\"><h5 class=\"regular white\">Jūsų užklausos forma buvo sėkmingai išsiųsta</h5></p><br><br>";
                        } else {
                            echo "<div align=\"center\">";
                            ?>
                            <form action="process.php" method="POST" class="contact-form">
                                <div class="col-sm-6 text-center-mobile">
                                    <p class="regular white">El. paštas</p>
                                    <p><input name="email" type="text" class="form-control input-narrow" placeholder="Jūsų el. paštas" value="<?php echo $form->value("email"); ?>" required="true">
                                    <?php echo $form->error("email"); ?></p>
                                </div>
                                <div class="col-sm-6 text-center-mobile">
                                    <p class="regular white">Tema</p>
                                    <p><input name="heading" type="text" class="form-control input-narrow" placeholder="Tema" value="<?php echo $form->value("heading"); ?>" required="true">
                                    <?php echo $form->error("heading"); ?></p>
                                </div>
                                <div class="col-sm-12 text-center-mobile">
                                    <p class="regular white">Žinutė</p>
                                    <p><textarea rows="10" name="message" type="text" class="contact-form" required="true"><?php echo $form->value("message"); ?></textarea></p>
                                    <?php echo $form->error("message"); ?></p>
                                </div>
                                <div class="text-center-mobile">
                                    <input type="hidden" name="contactform" value="1"/>
                                <input type="submit" class="btn btn-form" value="Siųsti"/>
                                </div>

                            </form>
                            <?php 
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
