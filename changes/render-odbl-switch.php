<?php
require dirname( __FILE__ ) . '/../../osm-tools/lib/convert.php';
define('FACTOR', 4);
define('FACTORP', FACTOR - 1);
define('WIDTH',  360 * FACTOR);
define('HEIGHT', 180 * FACTOR);
date_default_timezone_set('UTC');
$force = true;

mkdir( 'povs', true );

$start = 1020;
$end   = 1120;

for ( $i = $start; $i <= $end; $i++ )
{
	$output = '';
	$j = $start;
	$size = 6381 + (abs($i-$j) * abs($i-$j)) / 2;
	$intensity = ((100 - (abs($i-$j))) / 100);
	$intensity = 0.5 * ($intensity * $intensity);
	$transmit = 1 - $intensity;

$output .= <<<ENDL
    light_source {
		0
		color red 0 green 0 blue {$intensity}
		looks_like {
			sphere {
				<0, 0, 0> 1
				pigment {color rgbt <0, 1, 0.3, 0> transmit {$transmit}} finish {ambient 1}
			}
		}
		scale $size
	}

ENDL;
	file_put_contents( "povs/odbl{$i}.pov", $output );
}
