<?php
include_once('include/Config.php');
include_once('include/functions.php');

//make sure user is logged in, function will redirect use if not logged in
login_required();


if(isset($_GET['request']) && !(empty($_GET['request']))) {
    $req = $_GET['request'];
    $db = new DbHandlerForWeb();
    $requestArr = $db->getRequest($req,$_SESSION['email']);
    $providerLoc = $db->getProvidersLocation($_SESSION['email']);
    //print_r($providerLoc);
    //print_r($requestArr);
    if(count($requestArr) != 1) {
        $lat = $requestArr['latitude'];
        $lon = $requestArr['longitude'];
        $fname = $requestArr['first_name'];
        $lname = $requestArr['last_name'];
        $address = $requestArr['address'];
        $phone = $requestArr['phone_number'];

        $pLat = $providerLoc['latitude'];
        $pLon = $providerLoc['longitude'];
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
                <li>
                    <a href="dashboard.php">Dashboard</a>
                </li>
                <li>
                    <a href="./index.php?logout">Logout</a>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>



<div id="container" style="padding-top: 70px">

    <div class="col-md-12">
        <div class="col-md-offset-1 col-md-10 col-md-offset-1">
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
                        <p class="info-header">Name:<h5 class="center-text"><?php echo strtoupper($requestArr['last_name']).' '.$requestArr['first_name']; ?></h5></p>
                    </div>
                    <hr/>
                    <div class="row">
                        <p class="info-header">Address:<h5 class="center-text"><?php echo $requestArr['address'] ?></h5></p>
                    </div>
                    <hr/>
                    <div class="row">
                        <p class="info-header">Phone Number:<h5 class="center-text"><?php echo $requestArr['phone_number']; ?></h5></p>
                    </div>
                </div>

        </div>
        <div class="row">
            <div class="col-md-8 col-xs-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div id="new-button-div" class="btn-group" style="padding:30px">
                                <?php
                                if (intval($requestArr['statuses_request_status_id'])==1) {
                                    echo '<button id="approve" class="btn btn-success" >Approve</button>
                                                    <button id="decline" class="btn btn-danger" >Decline</button>';
                                }
                                ?>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" >
                        <div class="row">
                            <div id="old-button-div" class="btn-group" style="padding:30px;<?php if (intval($requestArr['statuses_request_status_id'])==2) {echo ""; } else {echo "display:none;";} ?>">
                                <?php
                                if (!((intval($requestArr['statuses_service_status_id'])==1) || (intval($requestArr['statuses_request_status_id'])==4) || (intval($requestArr['statuses_service_status_id'])==4))) {
                                    echo '<button class="btn btn-default" >Send a message</button>
                                        <button id="complete" class="btn btn-success" >Completed</button>
                                        <button id="abort" class="btn btn-danger" >Abort</button>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12 border-left" style="margin-bottom:20px">
                <?php
                $kins = $db->getKinsForProviders($requestArr['user_id']);
                //var_dump($kins);
                for ($i=0;$i<=$kins['count']-1;$i++) {
                    echo '<div class="center-text border-top"><h4 class="info-header">('.($i+1).') Next of Kin:</h4>
                                        <p>Name: <span>'.strtoupper($kins['kins'][$i]['last_name']).' '.$kins['kins'][$i]['first_name'].'</span></p>
                                        <p>Address: <span>'.$kins['kins'][$i]['address'].'</span></p>
                                        <p>Phone Number:  <span>'.$kins['kins'][$i]['phone_number'].'</span></p>
                                        </div>';
                }
                ?>
            </div>
        </div>
        </div>
    </div>

</div>
<script type="text/javascript">
    $(document).ready(function(){
        $("button#approve").click(function(){
            $.ajax({
                type: "POST",
                url: "include/DbHandlerForWeb.php",
                timeout: 20000,
                data: "request=<?php echo $requestArr['request_id']; ?>&provider=<?php echo $_SESSION['email']; ?>&methods=approveRequest",
                success: function(){
                    $('#new-button-div').css('display','none');
                    $('#old-button-div').css('display','block');
                },
                error: function(xhr, desc, err){
                    alert("Failure! Please Try Again");
                    console.log(xhr);
                    console.log("|Details: " + desc + "|Error: " + err);
                }
            });
        });

        $("button#decline").click(function(){
            $.ajax({
                type: "POST",
                url: "include/DbHandlerForWeb.php",
                timeout: 20000,
                data: "request=<?php echo $requestArr['request_id']; ?>&provider=<?php echo $_SESSION['email']; ?>&methods=declineRequest",
                success: function(){
                    $('#new-button-div').css('display','none');
                },
                error: function(xhr, desc, err){
                    alert("Failure! Please Try Again");
                    console.log(xhr);
                    console.log("|Details: " + desc + "|Error: " + err);
                }
            });
        });

        $("button#abort").click(function(){
            $.ajax({
                type: "POST",
                url: "include/DbHandlerForWeb.php",
                timeout: 20000,
                data: "request=<?php echo $requestArr['request_id']; ?>&provider=<?php echo $_SESSION['email']; ?>&methods=abortRequest",
                success: function(data){
                    $('#old-button-div').css('display','none');
                },
                error: function(xhr, desc, err){
                    alert("Failure! Please Try Again");
                    console.log(xhr);
                    console.log("|Details: " + desc + "|Error: " + err);
                }
            });
        });

        $("button#complete").click(function(){
            $.ajax({
                type: "POST",
                url: "include/DbHandlerForWeb.php",
                timeout: 20000,
                data: "request=<?php echo $requestArr['request_id']; ?>&methods=completeRequest",
                success: function(data){
                    console.log(data);
                    $('#old-button-div').css('display','none');
                },
                error: function(xhr, desc, err){
                    alert("Failure! Please Try Again");
                    console.log(xhr);
                    console.log("|Details: " + desc + "|Error: " + err);
                }
            });
        });
    });
</script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAq-qALg5CO4EPfaV0kgcqTjlCp9oVriVc">
</script>
<script type="text/javascript">
    var directionsDisplay;
    var directionsService = new google.maps.DirectionsService();

    function initialize() {
        directionsDisplay = new google.maps.DirectionsRenderer();

        //var chicago = new google.maps.LatLng(41.850033, -87.6500523);

        var a = ["<?php echo $lat?>","<?php echo $lon?>"];
        var myLatLng = new google.maps.LatLng(a[0], a[1]);
        var mapOptions = {
            center: myLatLng,
            zoom: 15
        };
        var map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);

        directionsDisplay.setMap(map);

//        var marker = new google.maps.Marker({
//            position: myLatLng,
//            map:map,
//            title:"Last Known Location"
//        });

//        function calcRoute() {
//            var start = document.getElementById('start').value;
//            var end = document.getElementById('end').value;
        var chicago = new google.maps.LatLng("<?php echo $pLat; ?>","<?php echo $pLon; ?>");

        //console.log("knk"+"<?php echo $pLat; ?>","<?php echo $pLon; ?>");
        var a = ["<?php echo $lat?>","<?php echo $lon?>"];
        var myLatLng = new google.maps.LatLng(a[0], a[1]);
            var request = {
                origin: chicago,
                destination: myLatLng,
                travelMode: google.maps.TravelMode.DRIVING
            };
            directionsService.route(request, function(response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(response);
                    //console.log("successsss");
                    var leg = response.routes[ 0 ].legs[ 0 ];
                    makeMarker( leg.start_location, map, "title 1" );
                    makeMarker( leg.end_location, map, 'title 2' );
                }
            });
    }
    function makeMarker( position, map, title ) {
        new google.maps.Marker({
            position: position,
            map: map,
            title: title
        });
    }
//        }

    google.maps.event.addDomListener(window, 'load', initialize);
</script>


</body>

</html>
