<?php
require dirname( __FILE__ ) . '/../../osm-tools/lib/convert.php';
define('FACTOR', 4);
define('FACTORP', FACTOR - 1);
define('WIDTH',  360 * FACTOR);
define('HEIGHT', 180 * FACTOR);
date_default_timezone_set('UTC');
$force = true;

mkdir( 'povs', true );

$data = include( 'logs.json' );

$start = 770;
$end   = 826 + 25;

for ( $i = $start; $i <= $end; $i++ )
{
	$output = '';
	for ($j = $i - 25; $j <= $i; $j++) {
		if (isset( $data[$j] ) ) {
			foreach ( $data[$j] as $lat => $longs )
			{
				foreach ( $longs as $lon => $dummy )
				{
					$size = 6381 + (10 * (abs($i-$j) - $dummy) * (abs($i-$j) - $dummy));
					$scale = (((25 - (abs($i-$j))) / 25) * 40) + 0.001;
					$intensity = ((25 - (abs($i-$j))) / 25);
					$intensity = 0.8 * ($intensity * $intensity);

					if ($scale > 20 and (rand(0, 49) == 3)) {
$output .= <<<ENDL
    light_source {
		0
		color red {$intensity} green {$intensity} blue 0
		looks_like {
			sphere {
				<0, 0, 0> 1
				pigment {color rgbt<1, 1, 0, 0>}
			}
		}
		scale $scale
		translate <
			{$size} * sin((-90 - {$lon}) * (pi/180)) * cos((0 - {$lat}) * (pi/180)), 
			{$size} * sin((-90 - {$lon}) * (pi/180)) * sin((0 - {$lat}) * (pi/180)), 
			{$size} * cos((-90 - {$lon}) * (pi/180))
		>
	}

ENDL;
					} else {
$output .= <<<ENDL
    object {
		sphere {
			<0, 0, 0> 1
			pigment {color rgbt<1, 1, 0, 0>}
		}
		scale $scale
		translate <
			{$size} * sin((-90 - {$lon}) * (pi/180)) * cos((0 - {$lat}) * (pi/180)), 
			{$size} * sin((-90 - {$lon}) * (pi/180)) * sin((0 - {$lat}) * (pi/180)), 
			{$size} * cos((-90 - {$lon}) * (pi/180))
		>
	}

ENDL;
					}
				}
			}
		}
	}
	file_put_contents( "povs/log{$i}.pov", $output );
}
