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
                            <li><a href="stovyklos.php">Visos stovyklos</a></li>
                            <li class="active"><a href="#myevents">Mano stovyklos</a></li>
                            <!--<li><a href="manopaskyra.php">Mano paskyra</a></li>-->
                            <li><a href="manopaskyra.php">Sveiki, <b><?php echo $session->username; ?></b></a></li>
                            <li><a href="process.php" class="btn btn-sm btn-blue">Atsijungti</a></li>
                        </ul>
                </div>
                <!-- /.navbar-collapse -->
            </div>
            <!-- /.container-fluid -->
	</nav>
	<section id="myevents" class="section section-padded">
		<div class="container">
                    <div class="row text-center title">
                            <h2>Mano stovyklos</h2>
                    </div>
                    <?php
                    if (isset($_SESSION['unregisterfromevent'])) {
                        unset($_SESSION['unregisterfromevent']);
                        echo "<br><br><p style=\"text-align: center;\"><b>$session->username</b>, sėkmingai išsiregistravote iš stovyklos.<p>";
                    }
                    ?>
                    <div class="row events">
                        <div class="center-block col-md-10" style="float: none;">
                            <div class="account">
                                <?php 
                                $username = $database->getUserName($session->username);
                                
                                ?>
                                <p><b><?php echo $username['name']; ?></b>, čia pateikiamos visos tavo stovyklos:</p><br>
                                <?php
                                $events = $database->getUserEventsData($session->username);
                                if ($events == NULL){
                                    ?>
                                    <p>Deja, nesi užsiregistravęs nei vienoje stovykloje. <a href="stovyklos.php">Visas stovyklas gali rasti čia.</a></p>
                                <?php
                                }
                                else {
                                ?>
                                <table class="table table-hover">
                                    <thead>
                                        <tr class='rowToClick'>
                                            <th>Stovyklos pavadinimas</th>
                                            <th>Data</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while($result = mysqli_fetch_array($events)) {
                                            ?>
                                            <tr>
                                                <td class="toggle"> <?php echo $result['title'];?> </td>
                                                <td> <?php echo $result['date'];?> </td>
                                                <td>
                                                    <form action="process.php" method="POST" class="description">
                                                        <p>
                                                            <input type="hidden" name="unregisterfromevent" value="1"/>
                                                            <input type="hidden" name="eventid" value="<?php echo $result['id'];?>"/>
                                                            <input class="btn btn-submit" style="margin-top: 0px; padding: 0px 0px;" type="submit" value="Išsiregistruoti"/>
                                                        </p>
                                                    </form>
                                                </td>
                                            </tr>
                                            <tr class="showhide" style="display: none;">
                                                <td colspan="3">
                                                    <div class="camppreview">
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
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <?php
                                }
                                ?>
                            </div>
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