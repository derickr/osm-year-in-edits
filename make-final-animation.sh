#!/bin/sh

cd output

for i in `php -r 'echo join(" ", range(0, 100));'`; do
	nr=`php -r "echo min(100, 200 - ($i * 2));"`
	fn=`php -r "printf('%04d', $i);"`
	composite -quality 100 -size 1280x768 -dissolve ${nr} /backup/osm/year-in-edits-2012/overlay/title-2012.png xc:black -alpha set tmp-yearinedits-0-$fn.jpg
	echo yearinedits-0-$fn.png
done

for i in yearinedits-1-000*png; do
	base=`echo $i | sed 's/yearinedits-1-//'`
	nr=`echo $i | sed 's/yearinedits-1-000//'`
	count=`php -r "echo ((int) \"${nr}\" - 1) * 10;"`;

	composite -quality 100 -size 1280x768 -blend ${count} $i xc:black -alpha set /tmp/tmp-x.png
	composite -quality 100 -dissolve 100 /backup/osm/year-in-edits-2012/overlay/overlay-01$base /tmp/tmp-x.png tmp-$i.jpg
	echo $i
done

for i in yearinedits-1-00[123456789]*.png yearinedits-1-0[123456789]*png yearinedits-1-1[0123]*.png yearinedits-1-14[01234]*; do
	base=`echo $i | sed 's/yearinedits-1-//'`

	composite -quality 100 -dissolve 100 /backup/osm/year-in-edits-2012/overlay/overlay-01$base $i tmp-$i.jpg
	echo $i
done

for i in yearinedits-1-145*png; do
	base=`echo $i | sed 's/yearinedits-1-//'`
	nr=`echo $i | sed 's/yearinedits-1-145//'`
	count=`php -r "echo 100 - ((int) \"${nr}\" - 1) * 10;"`;

	composite -quality 100 -size 1280x768 -blend ${count} $i xc:black -alpha set /tmp/tmp-x.png
	composite -quality 100 -dissolve 100 /backup/osm/year-in-edits-2012/overlay/overlay-01$base /tmp/tmp-x.png tmp-$i.jpg
	echo $i
done

for i in yearinedits-2-000*png; do
	base=`echo $i | sed 's/yearinedits-2-//'`
	nr=`echo $i | sed 's/yearinedits-2-000//'`
	count=`php -r "echo ((int) \"${nr}\" - 1) * 10;"`;

	composite -quality 100 -size 1280x768 -blend ${count} $i xc:black -alpha set /tmp/tmp-x.png
	composite -quality 100 -dissolve 100 /backup/osm/year-in-edits-2012/overlay/overlay-01$base /tmp/tmp-x.png tmp-$i.jpg
	echo $i
done

for i in yearinedits-2-00[123456789]*.png yearinedits-2-0[123456789]*png yearinedits-2-1[012]*.png; do
	base=`echo $i | sed 's/yearinedits-2-//'`

	composite -quality 100 -dissolve 100 /backup/osm/year-in-edits-2012/overlay/overlay-01$base $i tmp-$i.jpg
	echo $i
done

for i in yearinedits-2-13*png; do
	base=`echo $i | sed 's/yearinedits-2-//'`
	nr=`echo $i | sed 's/yearinedits-2-13//'`
	count=`php -r "echo max(0, -100 + (int) \"${nr}\" * 2);"`;
	revcount=`php -r "echo max(0, 100 - (int) \"${nr}\" * 3);"`;

	composite -quality 100 -dissolve ${revcount} /backup/osm/year-in-edits-2012/overlay/overlay-01$base $i /tmp/tmp-x.png
	composite -quality 100 -dissolve ${count} /backup/osm/year-in-edits-2012/overlay/final.png /tmp/tmp-x.png tmp-$i.jpg
	echo $i
done
for i in yearinedits-2-14*png; do
	base=`echo $i | sed 's/yearinedits-2-//'`
	nr=`echo $i | sed 's/yearinedits-2-14//'`
	count=`php -r "echo min(98, (int) \"${nr}\" * 4);"`;

	composite -quality 100 -dissolve 100 /backup/osm/year-in-edits-2012/overlay/final.png $i /tmp/tmp-x.png
	composite -quality 100 -dissolve ${count} /backup/osm/year-in-edits-2012/overlay/end.png /tmp/tmp-x.png tmp-$i.jpg
	echo $i
done

mencoder "mf://tmp-yearinedits-0-*.jpg" -mf fps=24 -o test1.avi -ovc lavc -lavcopts vcodec=msmpeg4v2:vbitrate=16000
mencoder "mf://tmp-yearinedits-[12]-*.jpg" -mf fps=24 -o test2.avi -ovc lavc -lavcopts vcodec=msmpeg4v2:vbitrate=16000
mencoder -oac copy -ovc copy -o test.avi test1.avi test2.avi 
mencoder -ovc copy -audiofile /backup/osm/year-in-edits-2012/Butterfly_Tea_-_Andoria_Main_title.mp3 -oac copy test.avi -o yearofedits2012.avi 
