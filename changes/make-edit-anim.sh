#!/bin/sh
mkdir -p tmp

for i in 01*png; do
	if [ ! -f tmp/$i.jpg ]; then
		echo $i
		convert -scale 1280x768 -gravity center -extent 1280x768 -background Black $i tmp-$i-s.png
		composite -quality 100 -dissolve 100 ../overlay/overlay-$i tmp-$i-s.png tmp/$i.jpg
		rm tmp-$i-s.png
	fi
done

mencoder "mf://tmp/*.jpg" -mf fps=24 -o changes-2012.avi -ovc lavc -lavcopts vcodec=msmpeg4v2:vbitrate=16000
