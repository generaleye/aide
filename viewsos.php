<?php
include_once('include/Config.php');
include_once('include/functions.php');

if(isset($_GET['request']) && !(empty($_GET['request']))) {
    $req = $_GET['request'];
    $db = new DbHandlerForWeb();
    $requestArr = $db->getSOSRequest($req);
//    echo count($requestArr);
    if(count($requestArr) != 1) {
        $lat = $requestArr['latitude'];
        $lon = $requestArr['longitude'];
        $fname = $requestArr['first_name'];
        $lname = $requestArr['last_name'];
        $address = $requestArr['address'];
        $phone = $requestArr['phone_number'];
    }else {
        header('HTTP/1.0 404 Not Found');
        include "./404.php";
        die;
    }
} else {
    header('HTTP/1.0 404 Not Found');
    include "./404.php";
    die;
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
    <link href="css/normalize.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

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
<!--                <li>-->
<!--                    <a href="#">About</a>-->
<!--                </li>-->
<!--                <li>-->
<!--                    <a href="#">Services</a>-->
<!--                </li>-->
                <li>
                    <a href="./signup.php">Login / Register</a>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>



<div id="container">

    <div class="col-md-12">
        <div class="row">
            <div class="col-md-8 col-xs-12">
                <div id="map-canvas" class="center"></div>
            </div>
            <div class="col-md-4 col-xs-12 border-left">
                <div class="row">
                    <img src="badass smiley.jpg" class="img-thumbnail center" width="150px" height="150px" />
                </div>
                <br/>
                <div class="row">
                    <p class="info-header">Name:<h5 class="center-text"><?php echo strtoupper($lname)." ".$fname; ?></h5></p>
                </div>
                <hr/>
                <div class="row">
                    <p class="info-header">Address:<h5 class="center-text"><?php echo $address; ?></h5></p>
                </div>
                <hr/>
                <div class="row">
                    <p class="info-header">Phone Number:<h5 class="center-text"><?php echo $phone; ?></h5></p>
                </div>
            </div>

        </div>
        <p>A user with the name <?php echo strtoupper($lname)." ".$fname; ?>
            sent an SOS message to you via our service. Kindly contact the user immediately if you know this person or send us an email if you do not know this user to stop receiving messages from this user</p>
        <!--                <div class="row">-->
        <!--                    <div class="col-md-8 col-xs-12">-->
        <!--                        <div class="row">-->
        <!--                            <div class="col-md-12">-->
        <!--                                <div class="row">-->
        <!--                                    <div id="new-button-div" class="btn-group" style="padding:30px">-->
        <!--                                        <button class="btn btn-success" >Approve</button>-->
        <!--                                        <button class="btn btn-danger" >Decline</button>-->
        <!--                                    </div>-->
        <!--                                </div>-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                        <div class="row">-->
        <!--                            <div class="col-md-12" >-->
        <!--                                <div class="row">-->
        <!--                                    <div id="old-button-div" class="btn-group" style="padding:30px;display:none;">-->
        <!--                                        <button class="btn btn-default" >Send a message</button>-->
        <!--                                        <button class="btn btn-success" >Completed</button>-->
        <!--                                        <button class="btn btn-danger" >Abort</button>-->
        <!--                                    </div>-->
        <!--                                </div>-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                    <div class="col-md-4 col-xs-12 border-left" style="margin-bottom:20px">-->
        <!--                        <div class="center-text border-top">-->
        <!--                            <h4 class="info-header">Next of Kin:</h4>-->
        <!--                            <p>Name: <span>Burna Bomboy</span></p>-->
        <!--                            <p>Address: <span>Somewhere in lagos</span></p>-->
        <!--                            <p>Phone Number:  <span>0807782921</span></p>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </div>-->
    </div>

</div>
<script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAq-qALg5CO4EPfaV0kgcqTjlCp9oVriVc">
</script>
<script type="text/javascript">
    function initialize() {
        var a = ["<?php echo $lat; ?>","<?php echo $lon; ?>"];
        var myLatLng = new google.maps.LatLng(a[0], a[1]);
        var mapOptions = {
            center: myLatLng,
            zoom: 15
        };
        var map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);

        var marker = new google.maps.Marker({
            position: myLatLng,
            map:map,
            title:"Last Known Location"
        });
    }
    google.maps.event.addDomListener(window, 'load', initialize);
</script>


</body>

</html>
