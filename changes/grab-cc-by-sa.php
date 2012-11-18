<?php
$base = 'http://planet.openstreetmap.org/cc-by-sa/hour-replicate/000/';
$baseFile = '/backup/osm/year-in-edits-2012/changes/cc-by-sa/';
$start = 18;
$end   = 20;

for ($i = $start; $i <= $end; $i++) {
	$d = sprintf("%03d", $i);
	@mkdir( $baseFile . $d );
	chdir( $baseFile . $d );

	for ($j = 0; $j < 1000; $j++) {
		$f = sprintf("%03d.osc.gz", $j);
		if (!file_exists($baseFile . $d . '/' . $f)) {
			$contents = file_get_contents( $base . $d . '/' . $f );
			if (strlen( $contents) > 1) {
				file_put_contents($baseFile . $d . '/' . $f, $contents);
				echo $baseFile . $d . '/' . $f, "\n";
			} else {
				exit("DONE $i:$j\n");
			}
		}
	}
}
