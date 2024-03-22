<?php

include './classes/Dbh.class.php';
include './classes/Link.class.php';

// Get the visitor's IP address
// if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
//     $ip = $_SERVER['HTTP_CLIENT_IP'];
// } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
//     $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
// } else {
//     $ip = $_SERVER['REMOTE_ADDR'];
// }

// echo $ip;
$ip = '41.44.144.143';

// Function to get the country and country code using MaxMind GeoIP database
function getCountry($ip)
{
    // Path to the GeoIP database file
    $database = './data/GeoLite2-Country.mmdb';

    // Load the GeoIP2 database
    require_once 'vendor/autoload.php'; // Adjust this path based on your setup

    // Include necessary GeoIP2 classes
    require_once 'vendor/geoip2/geoip2/src/Database/Reader.php';
    require_once 'vendor/geoip2/geoip2/src/Exception/AddressNotFoundException.php';

    // Create a GeoIP2 Reader object
    $reader = new GeoIp2\Database\Reader($database);

    try {
        // Get the country information based on the IP address
        $record = $reader->country($ip);
        $country = $record->country->name;
        $countryCode = $record->country->isoCode;

        return array($country, $countryCode);
    } catch (GeoIp2\Exception\AddressNotFoundException $e) {
        // Handle AddressNotFoundException if necessary
        return array('Unknown', 'Unknown');
    }
}

// Call the function to get the country and country code
list($country, $countryCode) = getCountry($ip);

$link = new Link();
$fetchedLink = $link->getLink(filter_var($_GET['link'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$link->addAccess($fetchedLink['ID'], $ip, $country, $countryCode);

if ($fetchedLink) {
    header('Location: ' . $fetchedLink['destination']);
    exit;
} else {
    header('Location: index.php');
    exit;
}