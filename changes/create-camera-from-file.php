<?php
include 'interpolate.php';
include 'spline.php';

$f = file($argv[1]);
$fadeOut = false;

if (isset($argv[2])) {
	$fadeOut = true;
}

foreach ($f as $line) {
	preg_match( "/([0-9]+)\s+([0-9-]+)\s+([0-9-]+)\s+([0-9-]+)/", $line, $m);

	if ((($m[1] < 1361) || !$fadeOut) && $m[4] != 0) {
		$coordsZ[$m[1]] = $m[4];
	}
}

if ($fadeOut) {
	$coordsZ[1460] = 80000;
}

$cZ = new CubicSplines;
$cZ->setInitCoords($coordsZ, 1, 0, 1460);
$Z = $cZ->processCoords();

for( $i = 0; $i <= 1460; $i++ )
{
//	echo "#if ((clock*4) = $i)\nlocation <0, 0, {$Z[$i]}>\nlook_at <0,0,0>\n#end\n";
	echo $i, ',', $Z[$i], "\n";
}
