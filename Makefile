SIZE_ULTRA_BIG=+W1280 +H768
#SIZE_ULTRA_BIG=+W640 +H384
SIZE_BIG=+W450 +H338
SIZE_SML=+W220 +H165
OPTIONS=+A0.3 +Q9 +D1 #+AM2 +A

all: demo demo-small rotate eur nld holidays

debug:
	povray -Idebug.pov +K125 -Ooutput/debug ${SIZE_BIG} ${OPTIONS}

rotate: rotate-sml rotate-big

rotate-sml:
	povray -Iearth.pov Final_Frame=9 +KI0 +KF360 -Ooutput/earth_s ${SIZE_SML} ${OPTIONS}

rotate-big:
	povray -Iearth.pov Final_Frame=9 +KI0 +KF360 -Ooutput/earth ${SIZE_BIG} ${OPTIONS}

demo:
	povray -Iearth.pov +K100 -Ooutput/demo ${SIZE_ULTRA_BIG} ${OPTIONS}

demo-small:
	povray -Iearth.pov +K100 -Ooutput/demo_s ${SIZE_BIG} ${OPTIONS}

yearinedits-full: yearinedits-redaction-bot yearinedits-full-1 yearinedits-full-2

yearinedits-redaction-bot: yearinedits-redaction-bot-1 yearinedits-redaction-bot-2

yearinedits-redaction-bot-1:
	povray -Iyearinedits1.pov Final_Frame=1465 +KI0 +KF366 Subset_Start_Frame=750 Subset_End_Frame=880 -Ooutput/yearinedits-1- ${SIZE_ULTRA_BIG} ${OPTIONS}

yearinedits-redaction-bot-2:
	povray -Iyearinedits2.pov Final_Frame=1465 +KI0 +KF366 Subset_Start_Frame=750 Subset_End_Frame=880 -Ooutput/yearinedits-2- ${SIZE_ULTRA_BIG} ${OPTIONS}

yearinedits-full-1:
	povray -Iyearinedits1.pov Final_Frame=1465 +KI0 +KF366 Subset_Start_Frame=1 Subset_End_Frame=350 -Ooutput/yearinedits-1- ${SIZE_ULTRA_BIG} ${OPTIONS} &
	povray -Iyearinedits1.pov Final_Frame=1465 +KI0 +KF366 Subset_Start_Frame=351 Subset_End_Frame=749 -Ooutput/yearinedits-1- ${SIZE_ULTRA_BIG} ${OPTIONS} &
	povray -Iyearinedits1.pov Final_Frame=1465 +KI0 +KF366 Subset_Start_Frame=881 Subset_End_Frame=1161 -Ooutput/yearinedits-1- ${SIZE_ULTRA_BIG} ${OPTIONS} &
	povray -Iyearinedits1.pov Final_Frame=1465 +KI0 +KF366 Subset_Start_Frame=1162 Subset_End_Frame=1466 -Ooutput/yearinedits-1- ${SIZE_ULTRA_BIG} ${OPTIONS} &

yearinedits-full-2:
	povray -Iyearinedits2.pov Final_Frame=1465 +KI0 +KF366 Subset_Start_Frame=1 Subset_End_Frame=350 -Ooutput/yearinedits-2- ${SIZE_ULTRA_BIG} ${OPTIONS} &
	povray -Iyearinedits2.pov Final_Frame=1465 +KI0 +KF366 Subset_Start_Frame=351 Subset_End_Frame=749 -Ooutput/yearinedits-2- ${SIZE_ULTRA_BIG} ${OPTIONS} &
	povray -Iyearinedits2.pov Final_Frame=1465 +KI0 +KF366 Subset_Start_Frame=881 Subset_End_Frame=1161 -Ooutput/yearinedits-2- ${SIZE_ULTRA_BIG} ${OPTIONS} &
	povray -Iyearinedits2.pov Final_Frame=1465 +KI0 +KF366 Subset_Start_Frame=1162 Subset_End_Frame=1466 -Ooutput/yearinedits-2- ${SIZE_ULTRA_BIG} ${OPTIONS} &

yearinedits-fix-up:
	povray -Iyearinedits1.pov Final_Frame=1465 +KI0 +KF366 Subset_Start_Frame=1256 Subset_End_Frame=1466 -Ooutput/yearinedits-1- ${SIZE_ULTRA_BIG} ${OPTIONS} &
	povray -Iyearinedits2.pov Final_Frame=1465 +KI0 +KF366 Subset_Start_Frame=1256 Subset_End_Frame=1466 -Ooutput/yearinedits-2- ${SIZE_ULTRA_BIG} ${OPTIONS} &

nld:
	povray -Inld.pov +K25 -Ooutput/nld ${SIZE_BIG} ${OPTIONS}

eur: eur1 eur2

eur1:
	povray -Ieur1.pov +K25 -Ooutput/eur1 ${SIZE_BIG} ${OPTIONS}

eur2:
	povray -Ieur2.pov +K40 -Ooutput/eur2 ${SIZE_ULTRA_BIG} ${OPTIONS}
	povray -Ieur2.pov +K40 -Ooutput/eur2_s ${SIZE_XML} ${OPTIONS}

holidays: finland usa bc ca ez norway

finland:
	povray -Ifinland.pov +K30 -Ooutput/finnor ${SIZE_BIG} ${OPTIONS}
	povray -Ifinland.pov +K30 -Ooutput/finnor_s ${SIZE_SML} ${OPTIONS}

bc:
	povray -Ibc.pov +K165 -Ooutput/bc ${SIZE_ULTRA_BIG} ${OPTIONS}

usa:
	povray -Iusa.pov +K160 -Ooutput/usa ${SIZE_BIG} ${OPTIONS}

ca:
	povray -Ica.pov +K126 -Ooutput/ca ${SIZE_BIG} ${OPTIONS}

ez:
	povray -Iez.pov +K30 -Ooutput/ez ${SIZE_BIG} ${OPTIONS}

norway:
	povray -Inorway.pov +K43 -Ooutput/norway ${SIZE_BIG} ${OPTIONS}



use10:
	rm earthmap.png
	ln -s earthmap10k.png earthmap.png

use2:
	rm earthmap.png
	ln -s earthmap2k.png earthmap.png



clean:
	rm -rf output/*png output/*ppm

install:
	cp output/*png /home/httpd/html/www.derickrethans.nl/html/images/travel
