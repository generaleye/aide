<?php
include_once('include/Config.php');
include_once('include/DbHandlerForWeb.php');
include_once('include/functions.php');

//make sure user is logged in, function will redirect use if not logged in
login_required();

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

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <!-- jQuery Version 1.11.1 -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Notify Core JavaScript -->
    <script src="js/.js"></script>

    <!-- Custom CSS -->
    <style>
        
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
                    <a href="editprovider.php">Edit Profile</a>
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

<div class="outer">
    <div class="middle">
        <div class="wrapper">
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover table-responsive">
                    <thead><tr><td>S/N</td><td>Name</td><td>Address</td><td>Date</td><td>Request Status</td><td>Service Status</td></tr></thead>
                    <tbody>
                        <tr><?php
                            $db = new DbHandlerForWeb();
                            $requestArr = $db->getProvidersRequests($_SESSION['email']);
                            //print_r($requestArr);
                            for ($i=0;$i<=$requestArr['count']-1;$i++) {
                                echo '<td>'.($i+1).'</td>
                                        <td>'.strtoupper($requestArr['requests'][$i]['last_name']).' '.$requestArr['requests'][$i]['first_name'].'</td>
                                        <td><a href="./requestdetails.php?request='.$requestArr['requests'][$i]['request_id'].'">View Details</a></td>
                                        <td>'.$requestArr['requests'][$i]['request_created_time'].'</td>
                                        <td>'.$requestArr['requests'][$i]['request_statuses_name'].'</td>
                                        <td>'.$requestArr['requests'][$i]['service_statuses_name'].'</td></tr>';
                            }
                            ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



</body>

</html>
