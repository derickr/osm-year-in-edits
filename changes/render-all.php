<?php
require dirname( __FILE__ ) . '/../../osm-tools/lib/convert.php';
define('WIDTH', 1440 * 8);
define('HEIGHT', 720 * 8);
define("YEAR", 2013);
date_default_timezone_set('UTC');
ini_set( 'memory_limit', '4G' );
$forceParse = false;
$force = false;
$forceRender = false;
$mask = false;

if ( $argc == 2 )
{
	$mask = imagecreatefrompng( $argv[2] );
}

@mkdir( 'json-tmp', 0777, true );

function createNewFrame()
{
	$img = imagecreatetruecolor(WIDTH, HEIGHT);

	$black  = imagecolorallocate($img, 0, 0, 0);

	imagecolortransparent($img, $black);
	imagefilledrectangle($img, 0, 0, WIDTH, HEIGHT, $black);

	return $img;
}

function parseFile( $input, $output )
{
	$info = array();

	if ( !file_exists( $input ) )
	{
		goto end;
	}

	$f = simplexml_load_file("compress.zlib://$input");

	foreach ( $f->delete as $delete )
	{
		foreach ( $delete->node as $node )
		{
			$lon = (int) ((WIDTH/360) * ((float) $node['lon'] + 180));
			$lat = (int) ((HEIGHT/180) * ((0 - (float) $node['lat']) + 90));
			$info[$lon][$lat] = 'D';
		}
	}

	foreach ( $f->modify as $modify )
	{
		foreach ( $modify->node as $node )
		{
			$lon = (int) ((WIDTH/360) * ((float) $node['lon'] + 180));
			$lat = (int) ((HEIGHT/180) * ((0 - (float) $node['lat']) + 90));
			$info[$lon][$lat] = 'M';
		}
	}

	foreach ( $f->create as $create )
	{
		foreach ( $create->node as $node )
		{
			$lon = (int) ((WIDTH/360) * ((float) $node['lon'] + 180));
			$lat = (int) ((HEIGHT/180) * ((0 - (float) $node['lat']) + 90));
			$info[$lon][$lat] = 'C';
		}
	}

end:
	file_put_contents( $output, json_encode( $info ) );
}



function nameMap( $i )
{
	// jan 1, 0 = 2657
	if ($i >= 0 && $i <= 366 * 24) {
		$off = $i + 2657;
		return array( 'dir' => 'odbl', 'series' => (int) $off / 1000, 'seq' => $off % 1000 );
	}
	return false;
}

$globalData = array();

for ($i = 0; $i < 366 * 24; $i += 6)
{
	$filename = array();

	for ( $j = 0; $j < 6; $j++ )
	{
		$c = $i + $j;
		$finfo = nameMap( $c );
		$fn = sprintf("%s/%03d/%03d.osc.gz", $finfo['dir'], $finfo['series'], $finfo['seq']);
		if ( $finfo && ( $finfo['dir'] == 'dummy' || file_exists( $fn ) ) )
		{
			$ts = date_create( YEAR . "-01-01 00:00 UTC " )->modify( "+ $c hours" );
			$parsedFileName = sprintf( 'change-%05d.json', $c );
			echo $ts->format( 'Y-m-d H' ), ": " . $fn, "\n";

			/* Parse file if necessary */
			if ( $forceParse || !file_exists( 'json-tmp/' . $parsedFileName ) )
			{
				echo "- Parsing $fn into $parsedFileName\n";
				parseFile( $fn, 'json-tmp/' . $parsedFileName );
			}
			$filename[$j] = $parsedFileName;
		}
	}

	/* Merge last 6 into 1 */
	$mergedFileName = sprintf( 'merged-%05d.json', $i / 6 );
	if ( count( $filename ) == 6 && ( $force || !file_exists( 'json-tmp/' . $mergedFileName ) ) )
	{
		echo "- Merging into $mergedFileName\n";
		$data = array();

		for ( $j = 0; $j < 6; $j++ )
		{
			$fData = (array) json_decode( file_get_contents( 'json-tmp/' . $filename[$j] ) );
			foreach ( $fData as $x => $value )
			{
				foreach ( (array) $value as $y => $op )
				{
					$x = (int) $x;
					$y = (int) $y;
					if ( !isset( $data[$x][$y] ) ) {
						$data[$x][$y] = $op;
					} else {
						switch ( $op ) {
							case 'C':
								$data[$x][$y] = $op;
								break;
							case 'M':
								if ( $data[$x][$y] != 'C' ) {
									$data[$x][$y] = $op;
								}
						}
					}
				}
			}
		}
		file_put_contents( 'json-tmp/' . $mergedFileName, serialize( $data ) );
	}

	if ( count( $filename ) != 6 )
	{
		die( "Nothing left, only " . count( $filename ) . " items left\n" );
	}

	/* Update global array with latest merged data */
	$globalFileName = sprintf( 'global-%05d.json', 10000 + ( $i / 6 ) );
	$previousGlobalFileName = sprintf( 'global-%05d.json', 9999 + ( $i / 6 ) );
	if ( /* $forceRender ||*/ !file_exists( 'json-tmp/' . $globalFileName ) )
	{
		echo "- Updating globals into $globalFileName\n";

		$globalData = unserialize( file_get_contents( 'json-tmp/' . $previousGlobalFileName ) );

		/* Decay current data */
		foreach ( $globalData as $x => $xs )
		{
			foreach ( $xs as $y => $value )
			{
				if ( in_array( $value, array( 'C', 'D', 'M' ) ) )
				{
					$globalData[$x][$y] = 100;
				}
				else if ( $globalData[$x][$y] > 50 )
				{
					$globalData[$x][$y] -= 0.5;
				}
			}
		}

		$mergedData = unserialize( file_get_contents( 'json-tmp/' . $mergedFileName ) );
		foreach ( $mergedData as $x => $value )
		{
			foreach ( $value as $y => $op )
			{
				$globalData[$x][$y] = $op;
			}
		}
		file_put_contents( 'json-tmp/' . $globalFileName, serialize( $globalData ) );
	}

	/* Render image */
	$imageFileName = sprintf( '%06d.png', 10000 + ( $i / 6 ) );
	if ( $forceRender || !file_exists( $imageFileName ) )
	{
		echo "- Rendering image $imageFileName\n";
		$globalData = unserialize( file_get_contents( 'json-tmp/' . $globalFileName ) );
		$img = createNewFrame();
		$modify = imagecolorallocate($img, 255, 162, 0 );
		$create = imagecolorallocate($img, 0, 255, 42 );
		$delete = imagecolorallocate($img, 255, 100, 100);

		for ( $j = 50; $j <= 100; $j++ )
		{
			$decay[$j] = imagecolorallocatealpha( $img, (($j - 50) * 5) / 1.5, $j / 1.5, (250 - (($j - 50) * 2.5)) / 1.5, (100-$j) );
		}

		foreach ( $globalData as $x => $xs )
		{
			foreach ( $xs as $y => $value )
			{
				switch ( $value )
				{
					case 'C': $color = $create; break;
					case 'M': $color = $modify; break;
					case 'D': $color = $delete; break;
					default:
						$color = $decay[(int) $value];
				}
				if ( $x < WIDTH && $y < HEIGHT )
				{
					$maskC = 1;
					if ( $GLOBALS['mask'] )
					{
						$maskC = imagecolorat( $GLOBALS['mask'], $x, $y );
					}
					if ($maskC != 0)
					{
						imagesetpixel( $img, $x, $y, $color );
					}
				}
			}
		}
		imagepng( $img, $imageFileName );
		`convert $imageFileName $imageFileName`;
	}
}
