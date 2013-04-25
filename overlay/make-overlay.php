<?php
require dirname( __FILE__ ) . '/../../osm-tools/lib/convert.php';
define('WIDTH', 1280);
define('HEIGHT', 720);
define('YEAR', 2013);
date_default_timezone_set('UTC');

function drawAxis($img)
{
	$white = imagecolorallocate($img, 255, 255, 255);

	$x = 100;
	$y = HEIGHT - 32;
	$sdate = strtotime( YEAR . "-01-01 00:00 UTC" );

	for ( $i = 0; $i < 366; $i++ )
	{
		imagesetpixel($img, $x + $i, $y, $white);
		if (date('d', $sdate) == 1) {
			imageline($img, $x + $i, $y - 1, $x + $i, $y - 5, $white);
			imagestring($img, 5, $x + $i + 12, $y - 20, substr(date('M', $sdate), 0, 1), $white);
		}
		$sdate += 86400;
	}
	imageline($img, $x + 365, $y, $x + 365, $y - 5, $white);

}

function drawProgress($img, $days)
{
	$needle = imagecolorallocate($img, 255,   0,   0);
	$red    = imagecolorallocate($img, 200,   0,   0);
	$yellow = imagecolorallocate($img, 200, 200,   0);
	$green  = imagecolorallocate($img,   0, 200,   0);

	$x = 100;
	$y = HEIGHT - 32;

	$redDayStart = 0;
	$redDayEnd   = $days;

	imagefilledrectangle($img, $x + $redDayStart, $y + 2, $x + $redDayEnd, $y + 10, $red);
/*
	if ($days > 91) {
		$yellowDayStart = 92;
		$yellowDayEnd   = min( 255, $days );
	
		imagefilledrectangle($img, $x + $yellowDayStart, $y + 2, $x + $yellowDayEnd, $y + 10, $yellow);

		if ($days > 255) {
			$greenDayStart = 256;
			$greenDayEnd   = $days;
	
			imagefilledrectangle($img, $x + $greenDayStart, $y + 2, $x + $greenDayEnd, $y + 10, $green);
		}
	}
*/
	imageline($img, $x + $days, $y - 20, $x + $days, $y + 10, $needle);
}

$start = 0;
$end   = 365 * 4;

for ($i = $start; $i < $end; $i++)
{
	$picf = sprintf("overlay-%06d.png", 10000 + $i);

	$q = imagecreatetruecolor(WIDTH, HEIGHT);
	imagealphablending($q, true);
	$black = imagecolorallocatealpha($q, 0, 0, 0, 200);
	imagecolortransparent($q, $black);
	imagefilledrectangle($q, 0, 0, WIDTH, HEIGHT, $black);

	drawAxis( $q );
	drawProgress( $q, $i / 4);

	$white = imagecolorallocate($q, 255, 255, 255);
	imagepng($q, $picf);
	`convert $picf $picf`;

	echo $picf, "\n";
}
