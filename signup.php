<?php
include_once('include/functions.php');

if (isset($_POST['register'])) {
    if ($_POST['password']==$_POST['password2']) {
        register($_POST['name'],$_POST['email'],$_POST['password'],$_POST['phone'],$_POST['service'],$_POST['address'],$_POST['latitude'],$_POST['longitude']);
    } else {
        $_SESSION['error'] = 'Passwords do not Match';
    }
} elseif (isset($_POST['login'])) {
    login($_POST['email'],$_POST['password'],$_POST['typer']);
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

    <style>
        #myMap {
            height: 350px;
            width: 680px;
        }
    </style>
<!--    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>-->
<!--    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>-->
<!--    <script type="text/javascript">-->
<!--        var map;-->
<!--        var marker;-->
<!--        var myLatlng = new google.maps.LatLng(7.517734199217642,4.526349925848308);-->
<!--        var geocoder = new google.maps.Geocoder();-->
<!--        var infowindow = new google.maps.InfoWindow();-->
<!--        function initialize(){-->
<!--            var mapOptions = {-->
<!--                zoom: 18,-->
<!--                center: myLatlng,-->
<!--                mapTypeId: google.maps.MapTypeId.ROADMAP-->
<!--            };-->
<!---->
<!--            map = new google.maps.Map(document.getElementById("myMap"), mapOptions);-->
<!---->
<!--            marker = new google.maps.Marker({-->
<!--                map: map,-->
<!--                position: myLatlng,-->
<!--                draggable: true-->
<!--            });-->
<!---->
<!--            geocoder.geocode({'latLng': myLatlng }, function(results, status) {-->
<!--                if (status == google.maps.GeocoderStatus.OK) {-->
<!--                    if (results[0]) {-->
<!--                        $('#address').val(results[0].formatted_address);-->
<!--                        $('#latitude').val(marker.getPosition().lat());-->
<!--                        $('#longitude').val(marker.getPosition().lng());-->
<!--                        infowindow.setContent(results[0].formatted_address);-->
<!--                        infowindow.open(map, marker);-->
<!--                    }-->
<!--                }-->
<!--            });-->
<!---->
<!---->
<!--            google.maps.event.addListener(marker, 'dragend', function() {-->
<!---->
<!--                geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {-->
<!--                    if (status == google.maps.GeocoderStatus.OK) {-->
<!--                        if (results[0]) {-->
<!--                            $('#address').val(results[0].formatted_address);-->
<!--                            $('#latitude').val(marker.getPosition().lat());-->
<!--                            $('#longitude').val(marker.getPosition().lng());-->
<!--                            infowindow.setContent(results[0].formatted_address);-->
<!--                            infowindow.open(map, marker);-->
<!--                        }-->
<!--                    }-->
<!--                });-->
<!--            });-->
<!---->
<!--        }-->
<!---->
<!--        google.maps.event.addDomListener(window, 'load', initialize);-->
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
                <li>
                    <a href="#">About</a>
                </li>
                <li>
                    <a href="#">Services</a>
                </li>
                <li>
                    <a href="#">Register / Login</a>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>


<div class="container center-block">

    <div role="tabpanel" class="col-md-12">
        <p><?php echo messages();?></p>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active col-md-6"><a href="#login" aria-controls="login" role="tab" data-toggle="tab">Login</a></li>
            <li role="presentation" class="col-md-6"><a href="#register" aria-controls="register" role="tab" data-toggle="tab">Register</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="login">
                <div class="col-md-6">
                    <form class="form-signin" role='form' method="post" action="">
                        <h2 class="form-signin-heading text-center">Sign in</h2>
                        <label for="inputEmail" class="sr-only">Email address</label>
                        <input type="email" name="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
                        <label for="inputPassword" class="sr-only">Password</label>
                        <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
                        <div class="radio">
                            <label><input name="typer" type="radio" value="user"> User</label>&nbsp;
                            <label><input name="typer" type="radio" value="provider"> Service Provider</label>
                        </div>
                        <button class="btn btn-lg btn-primary btn-block" name="login" type="submit">Sign in</button>
                    </form>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="register">
                <div class="col-md-6">
                    <form class="form-signin" role='form' method="post" action="">
                        <h2 class="form-signin-heading text-center">Register as a Service Provider</h2>
                        <label for="inputName" class="sr-only">Name</label>
                        <input type="text" name="name" id="inputName" class="form-control" placeholder="Name" required autofocus>
                        <label for="inputEmail" class="sr-only">Email address</label>
                        <input type="email" name="email" id="inputEmail" class="form-control" placeholder="Email address" required>
                        <label for="inputPassword" class="sr-only">Password</label>
                        <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
                        <label for="inputPasswordTwo" class="sr-only">Retype Password</label>
                        <input type="password" name="password2" id="inputPasswordTwo" class="form-control" placeholder="Retype Password" required>
                        <label for="inputPhone" class="sr-only">Phone Number</label>
                        <input type="tel" name="phone" id="inputPhone" class="form-control" placeholder="Phone Number" required>

                        <label for="">Type of Service Offered: &nbsp;</label>
                        <label>Fire <input name="service" type="radio" value="1"></label>
                        <label>Theft <input name="service" type="radio" value="2"></label>
                        <label>Medical <input name="service" type="radio" value="3"></label>
                        <label>Auto-Repair <input name="service" type="radio" value="4"></label>

<!--                    <div id="myMap" class="col-md-12"></div><br/>-->
                        <input type="text" name="address" id="address" class="form-control" placeholder="Address" />
                        <input type="text" name="latitude" id="latitude" placeholder="Latitude" readonly/>
                        <input type="text" name="longitude" id="longitude" placeholder="Longitude" readonly/>
                        <button class="btn btn-lg btn-primary btn-block" type="submit" name="register">Register</button>
                    </form>
                </div>
            </div>
<!--            <div role="tabpanel" class="tab-pane" id="messages">...</div>-->
<!--            <div role="tabpanel" class="tab-pane" id="settings">...</div>-->
        </div>

    </div>




</div>


</body>

</html>
