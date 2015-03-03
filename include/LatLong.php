<?php
/**
 * Created by PhpStorm.
 * User: Generaleye
 * Date: 2/27/15
 * Time: 12:23 PM
 */

class LatLong {
    function __construct($lat,$lon) {
        $this->setLatitude($lat);
        $this->setLongitude($lon);
    }

    private $latitude;          // latitude of centre of bounding circle in degrees
    private $longitude;         // longitude of centre of bounding circle in degrees
    private $radius;            // radius of bounding circle in kilometers
    private static $R = 6371;   // earth's mean radius, km

    private $maxLat;
    private $minLat;
    private $maxLon;
    private $minLon;
    public $response = array();

    public static function getR() {
        return self::$R;
    }

    public static function setR($R) {
        self::$R = $R;
    }

    public function getLatitude() {
        return $this->latitude;
    }

    public function setLatitude($latitude) {
        $this->latitude = $latitude;
    }

    public function getLongitude() {
        return $this->longitude;
    }

    public function setLongitude($longitude) {
        $this->longitude = $longitude;
    }

    public function getRadius() {
        return $this->radius;
    }

    public function setRadius($radius) {
        $this->radius = $radius;
    }

    public function getMaxLat() {
        return $this->maxLat;
    }

    public function setMaxLat() {
        $this->maxLat = $this->latitude + rad2deg($this->radius / self::$R);
    }

    public function getMaxLon() {
        return $this->maxLon;
    }

    public function setMaxLon() {
        $this->maxLon = $this->latitude - rad2deg($this->radius / self::$R);
    }

    public function getMinLat() {
        return $this->minLat;
    }

    public function setMinLat() {
        $this->minLat = $this->longitude + rad2deg($this->radius / self::$R / cos(deg2rad($this->latitude)));
    }

    public function getMinLon() {
        return $this->minLon;
    }

    public function setMinLon() {
        $this->minLon = $this->longitude - rad2deg($this->radius / self::$R / cos(deg2rad($this->latitude)));
    }

    public function calculateMinMax($rad) {
        $this->setRadius($rad);
        $this->setMaxLat();
        $this->setMaxLon();
        $this->setMinLat();
        $this->setMinLon();
    }

    public function getResult($radius) {
        $this->calculateMinMax($radius);
        $this->response["maxLat"] = $this->getMaxLat();
        $this->response["maxLon"] = $this->getMaxLon();
        $this->response["minLat"] = $this->getMinLat();
        $this->response["minLon"] = $this->getMinLon();
        return $this->response;
    }

}
