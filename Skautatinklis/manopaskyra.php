<?php
include("include/session.php");
if (!$session->logged_in) {
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
    <?php
    //Gets user info from database
    $userinfo = $database->getUserInfo($session->username);
    switch($_SERVER['QUERY_STRING'])
    {
        case 'editaccount':
        ?>
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
                        <li><a href="manopaskyra.php">Grįžtį į mano paskyrą</a></li>
                        <li class="active"><a>Sveiki, <b><?php echo $session->username; ?></b></a></li>
                        <li><a href="process.php" class="btn btn-sm btn-blue">Atsijungti</a></li>
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </div>
            <!-- /.container-fluid -->
        </nav>
        <section id="manopaskyra" class="section section-padded">
            <div class="container">
                <div class="row text-center title">
                    <h2>Paskyros redagavimas</h2>
                    <h4 class="light muted">Pakeitę duomenis nepamirškite jų išsaugoti</h4>
                </div>
                <div class="row">
                    <div class="center-block col-md-6" style="float: none;">
                        <?php
                        if (isset($_SESSION['subedit'])) {
                            unset($_SESSION['subedit']);
                            echo "<p style=\"text-align: center;\"><b>$session->username</b>, asmeniniai duomenys buvo sėkmingai atnaujinti.</p><br><br>";
                        } else {
                            echo "<div align=\"center\">";
                            if ($form->num_errors > 0) {
                                echo "<font size=\"3\" color=\"#ff0000\">Klaidų: " . $form->num_errors . "</font>";
                            } else {
                                echo "";
                            }
                            ?>
                        <div class="account">
                            <table class="table table-hover">
                                <tbody>
                                    <form action="process.php" method="POST" class="popup-form">
                                        <tr>
                                            <td>Prisijungimo vardas</td>
                                            <td><input name="user" type="text" class="form-control input-narrow" placeholder="Prisijungimo vardas" 
                                                value="<?php  if (!empty($form->value("user"))) { echo $form->value("user"); } else { echo $userinfo['username']; }?>" required="true">
                                            <?php echo $form->error("user"); ?></td>
                                        </tr>
                                        <tr>
                                            <td>El. paštas</td>
                                            <td><input name="email" type="email" class="form-control input-narrow" placeholder="El. paštas" 
                                            value="<?php  if (!empty($form->value("email"))) { echo $form->value("email"); } else { echo $userinfo['email']; }?>" required="true">
                                            <?php echo $form->error("email"); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Vardas</td>
                                            <td><input name="name" type="text" class="form-control input-narrow" placeholder="Jūsų vardas" 
                                            value="<?php  if (!empty($form->value("name"))) { echo $form->value("name"); } else { echo $userinfo['name']; }?>" required="true">
                                            <?php echo $form->error("name"); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Pavardė</td>
                                            <td><input name="surname" type="text" class="form-control input-narrow" placeholder="Pavardė" 
                                            value="<?php  if (!empty($form->value("surname"))) { echo $form->value("surname"); } else { echo $userinfo['surname']; }?>" required="true">
                                            <?php echo $form->error("surname"); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Gimimo data</td>
                                            <td><input name="borndate" type="text" class="form-control input-narrow" placeholder="YYYY-MM-DD" 
                                            value="<?php  if (!empty($form->value("borndate"))) { echo $form->value("borndate"); } else { echo $userinfo['born_date']; }?>" required="true">
                                            <?php echo $form->error("borndate"); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Vadovas</td>
                                            <td><input name="leader" type="text" class="form-control input-narrow" placeholder="Vadovo vardas ir pavardė" 
                                            value="<?php  if (!empty($form->value("leader"))) { echo $form->value("leader"); } else { echo $userinfo['leader']; }?>" >
                                            <?php echo $form->error("leader"); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><input type="hidden" name="subedit" value="1"/>
                                            <input type="submit" class="btn btn-submit" value="Išsaugoti"></td>
                                        </tr>
                                    </form>
                                </tbody>
                            </table>
                        </div>
                        <?php 
                        echo "</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="cut cut-bottom"></div>
        </section>
        <?php
        break;
    case 'editpass':
        ?>
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
                                <li><a href="manopaskyra.php">Grįžtį į mano paskyrą</a></li>
                                <li class="active"><a>Sveiki, <b><?php echo $session->username; ?></b></a></li>
                                <li><a href="process.php" class="btn btn-sm btn-blue">Atsijungti</a></li>
                        </ul>
                </div>
                <!-- /.navbar-collapse -->
            </div>
            <!-- /.container-fluid -->
            </nav>
            <section id="manopaskyra" class="section section-padded">
                    <div class="container">
                        <div class="row text-center title">
                                <h2>Slaptažodžio keitimas</h2>
                                <h4 class="light muted">Suvedę duomenis nepamirškite jų išsaugoti</h4>
                        </div>
                        <div class="row">
                            <div class="center-block col-md-6" style="float: none;">
                                <?php
                                if (isset($_SESSION['subeditpass'])) {
                                    unset($_SESSION['subeditpass']);
                                    echo "<p style=\"text-align: center;\"><b>$session->username</b>, slaptažodis buvo sėkmingai atnaujintas.</p><br><br>";
                                } else {
                                    echo "<div align=\"center\">";
                                    if ($form->num_errors > 0) {
                                        echo "<font size=\"3\" color=\"#ff0000\">Klaidų: " . $form->num_errors . "</font>";
                                    } else {
                                        echo "";
                                    }
                                    ?>
                                <div class="account">
                                    <table class="table table-hover">
                                        <tbody>
                                            <form action="process.php" method="POST" class="popup-form">
                                                <tr>
                                                    <td>Dabartinis slaptažodis</td>
                                                    <td><input name="currpass" type="password" class="form-control input-narrow" placeholder="Dabartinis slaptažodis" value="<?php echo $form->value("currpass"); ?>" required="true">
                                                    <?php echo $form->error("currpass"); ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Naujas slaptažodis</td>
                                                    <td><input name="newpass" type="password" class="form-control input-narrow" placeholder="Naujas slaptažodis" value="<?php echo $form->value("newpass"); ?>" required="true">
                                                    <?php echo $form->error("newpass"); ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Pakartokite naują slaptažodį</td>
                                                    <td><input name="newpass2" type="password" class="form-control input-narrow" placeholder="Naujas slaptažodis" value="<?php echo $form->value("newpass2"); ?>" required="true">
                                                    <?php echo $form->error("newpass2"); ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2"><input type="hidden" name="subeditpass" value="1"/>
                                                    <input type="submit" class="btn btn-submit" value="Išsaugoti"/></td>
                                                </tr>
                                            </form>
                                        </tbody>
                                    </table>
                                </div>
                                <?php 
                                echo "</div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="cut cut-bottom"></div>
            </section>
        <?php
        break;

    default:
    ?>
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
                                    <li><a href="stovyklos.php">Visos sotvyklos</a></li>
                                    <!--<li class="active"><a href="#manopaskyra">Mano paskyra</a></li>-->
                                    <li class="active"><a>Sveiki, <b><?php echo $session->username; ?></b></a></li>
                                    <li><a href="process.php" class="btn btn-sm btn-blue">Atsijungti</a></li>
                            </ul>
                    </div>
                    <!-- /.navbar-collapse -->
            </div>
            <!-- /.container-fluid -->
    </nav>
    <section id="manopaskyra" class="section section-padded">
            <div class="container">
                <div class="row text-center title">
                        <h2>Mano paskyra</h2>
                        <h4 class="light muted">Čia pateikiama visa tavo paskyros informacija</h4>
                </div>
                <div class="row">
                    <div class="center-block col-md-6" style="float: none;">
                        <div class="account">
                            <table class="table table-hover">
                                <tbody>
                                    <tr>
                                        <td>Prisijungimo vardas</td>
                                        <td><b><?php echo $userinfo['username'];?></b></td>
                                    </tr>
                                    <tr>
                                        <td>El. paštas</td>
                                        <td><b><?php echo $userinfo['email'];?></b></td>
                                    </tr>
                                    <tr>
                                        <td>Vardas</td>
                                        <td><b><?php echo $userinfo['name'];?></b></td>
                                    </tr>
                                    <tr>
                                        <td>Pavardė</td>
                                        <td><b><?php echo $userinfo['surname'];?></b></td>
                                    </tr>
                                    <tr>
                                        <td>Gimimo data</td>
                                        <td><b><?php echo $userinfo['born_date'];?></b></td>
                                    </tr>
                                    <tr>
                                        <td>Vadovas</td>
                                        <td><b><?php echo $userinfo['leader'];?></b></td>
                                    </tr>
                                </tbody>

                            </table> 
                            <a href="?editaccount">Keisti duomenis</a><br>
                            <a href="?editpass">Keisti slaptažodį</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="cut cut-bottom"></div>
    </section>
    <?php
            // i.e. no query string.
            break;
    }
    ?>
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
