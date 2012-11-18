<?php
$g = glob( 'redaction-bot/*.log' );
$c = 0;
foreach ( $g as $file )
{
	$f = fopen( $file, 'r' );
	$first = fread( $f, 4096 );
	preg_match( '/created on (.*) by logger/', $first, $m );
	$start = canonicalize_time( $m[1] );
	if ( preg_match( '/Processing region {:id=>"(.*)", :lat=>(.*), :lon=>(.*)}/', $first, $m ) )
	{
		$map[floor($start)][(int) $m[2]][(int) $m[3]] = $start - floor($start);
	}

	$c++;
	if ( $c % 1000 == 0 )
	{
		echo $c, "\n";
	}
}

file_put_contents( 'logs.json', '<?php return ' . var_export( $map, true ) . ';' );

function canonicalize_time( $time )
{
	$begin = strtotime( "2012-01-01 00:00 UTC" );
	$ts = strtotime( $time );
	$idx = ($ts - $begin) / (3600 * 6);
	return $idx;
}
