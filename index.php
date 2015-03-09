<?php
include_once('include/Config.php');
include_once('include/functions.php');

//if logout has been clicked run the logout function which will destroy any active sessions and redirect to the login page
if(isset($_GET['logout'])){
    logout();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Aide</title>
    <link rel="shortcut icon" type="image/ico" href="images/logo.png" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/cover.css" rel="stylesheet">

    <!-- jQuery Version 1.11.1 -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Custom CSS -->
    <style>
    body {
        padding-top: 70px;
         Required padding for .navbar-fixed-top. Remove if using .navbar-static-top. Change if height of navigation changes.
    }
    </style>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

<!--    <style>-->
<!--        #map-canvas {-->
<!--            height: 100%;-->
<!--            margin: 0px;-->
<!--            padding: 0px-->
<!--        }-->
<!--    </style>-->
<!--    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>-->
<!--    <script>-->
<!--        var map;-->
<!--        function initialize() {-->
<!--            var mapOptions = {-->
<!--                zoom: 8,-->
<!--                center: new google.maps.LatLng(-34.397, 150.644)-->
<!--            };-->
<!--            map = new google.maps.Map(document.getElementById('map-canvas'),-->
<!--                mapOptions);-->
<!--        }-->
<!---->
<!--        google.maps.event.addDomListener(window, 'load', initialize);-->
<!---->
<!--    </script>-->
</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <strong><a class="navbar-brand" href="./">Aide</a></strong>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse pull-right" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <?php
                    if(logged_in()) {
                        if(isProvider()) {
                            echo '<li><a href="dashboard.php">Dashboard</a></li>
                                    <li><a href="./index.php?logout">Logout</a></li>';
                        }
                    } else {
                        echo '<li><a href="signup.php">Login / Register</a></li>';
                    }
                    ?>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-offset-1 col-md-10 col-md-offset-1">
                    <img src="logo.png" style="display: block;margin: 0 auto;"/>
                </div>
            </div>
        </div>
    </div>



</body>

</html>
