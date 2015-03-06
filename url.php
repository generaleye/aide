<?php
require_once('./include/GoogleUrlApi.php');
// Create instance with key
$key = 'AIzaSyDsIoSE8N4_r4t3A46s5_EYAY3NYVWpCKs';
$googer = new GoogleURLAPI($key);

// Test: Shorten a URL
$shortDWName = $googer->shorten("http://localhost/aide/map.php?lat=40.74844&long=-73.985664");
echo $shortDWName." "; // returns http://goo.gl/i002

// Test: Expand a URL
$longDWName = $googer->expand($shortDWName);
echo $longDWName; // returns http://davidwalsh.name

?>