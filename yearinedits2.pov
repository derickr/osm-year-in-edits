// ==== Standard POV-Ray Includes ====
#include "colors.inc"	// Standard Color definitions
#include "textures.inc"	// Standard Texture definitions

#declare s=30;
#declare p=0.3;


#declare BlueAtm =
sphere {
  <0, 0, 0> 1
  pigment {color rgbt<0.46, 0.46, 1, 0.80>}
  interior { ior 1.000277 }
}

#declare SmallPoint =
sphere {
  <0, 0, 0> 1
  pigment {color rgbt<1, 1, 0, 0>}
}

camera{
#if (clock > 313)
 location <0 ,0, min(80000, max(18000, 18000 + ((clock-313) * 2232))) > // 0, 20000, -40000
 look_at  <max(-25000, min(0, 0 - ((clock-313) * 900))), max(-16666, min(0, 0 - ((clock-313) * 600))),0>
#else
 #if (clock < 200)
  location <0 ,0, 18000> // 0, 20000, -40000
 #else
  #if (clock > 290)
   location <0 ,0, 18000> // 0, 20000, -40000
  #else
   location <0 ,0, 16000 + 2000 * cos(((clock-200) * 4) * (pi / 180))>
   #warning concat("Clock: ", str(clock-200, 0, 2), " CAM: ", str(15000 + 3000 * cos(((clock-200) * 4) * (pi / 180)), 9, 2), " COS: ", str(cos(((clock-200) * 4) * (pi / 180)), 5, 3),"\n")
  #end
 #end
 look_at  <0,0,0>
#end
  rotate <0,-90,0>
  right x*1280/768
}


// create a regular point light source
light_source
{
  0*x // light's position (translated below)
  color red 1.0  green 1.0  blue 0.9  // light's color
  looks_like {sphere {0*x, 696265 pigment {color rgb <1, 0.5, 0.5>} finish {ambient 1}}}
  translate <1000000, 0, -100000>
  rotate <0,-120 - clock / 3, 23>
}

#declare Indexes = 256;  // number of entries in the gif color map

#declare T = 1.5;        // controls how fast the clouds become
                        // opaque towards the center (white areas)
                        // T=1 -> linear
                        // T<1 -> less transparency on the edges
                        // T>1 -> more transparency on the edges (better)


#declare Earth=
union {
      sphere {0, 6379
        pigment {color rgb <0.8 0.8 0.8>}
        finish {ambient 0 diffuse 1}
      }
      sphere {0, 6380
        pigment {image_map {png "earthmap.png" map_type 1 interpolate 2 transmit all 0.5}}
        finish {ambient 0 diffuse 1}
      }

#warning concat("Clock is:",str(clock, 4, 4),"\n")
#warning concat("Value is:",str(clock * 4, 4, 4),"\n")
#if ((10000 + clock * 4) <= 11466)
      sphere {0, 6380.00001
        pigment {image_map {concat("/backup/osm/year-in-edits-2012/changes/0", str(10000 + clock * 4, 0, 0), ".png") map_type 1 interpolate 2}}
        finish {ambient 2 diffuse 1}
      }
#end

#if ((clock * 4) >= 770)
	#if ((clock * 4) <= 851)
		#include concat("/backup/osm/year-in-edits-2012/changes/povs/log", str(clock * 4, 0, 0), ".pov")
	#end
#end

#if ((clock * 4) >= 1020)
	#if ((clock * 4) <= 1120)
		#include concat("/backup/osm/year-in-edits-2012/changes/povs/odbl", str(clock * 4, 0, 0), ".pov")
	#end
#end

	object {BlueAtm scale 6700}
	sphere {
	   <0,0,0>, 1
	   hollow
	   pigment {
		  image_map {
			 png "starmap.png"
			 map_type 1 // Spherical
			 once
			 transmit all 0.4
		  }
	   }
	   finish {
		  ambient 4 // Inverse of earlier global ambient level
		  diffuse 0
	   }
	   scale 2500000 // Depends on model units
	}
}

object {Earth rotate <0,160-clock,-5 + 30*sin((clock)/35)>}

