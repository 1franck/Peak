<?php

/* Example usage: */

require('bwt.class.php');
require('mtf.class.php');

$original = "Hello World, this is my test message.";

$bwt = new BWT();
$mtf = new MTF();

$encoded = $mtf->encode( $bwt->transform( $original ) );

$decoded = $bwt->inverse( $mtf->decode( $encoded ) );

print $decoded;

print $encoded;

file_put_contents('test.bz2',$encoded);

?>