<?php

/*
Output array of Positions as Cruiser-File, archived in a Zip file.
If more than 99 points are given, more than 1 cruiser-file will be created


fOutPutCruiser($alldata,$finallabel)
$alldata = array(
		array(
		 'paesse_GPS_lat_dec' => "12.212",
		 'paesse_GPS_lon_dec' => "4.121"
		),
		array(
		 'paesse_GPS_lat_dec' => "32.212",
		 'paesse_GPS_lon_dec' => "14.121"
		),	 
	)

*/

/**
 * 
 * Jetzt zum Format (alles, was Sie ändern dürfen, ist rot):
{"route":{"coords":["49.79752,9.94514","49.79856,9.9403","49.79518,9.92668"],"v":1,"settings":{"VT":1,"BE":0,"FR":0,"ROUND":0,"RT":3,"SR":0,"HOV":0,"HW":0,"TR":0,"CU":1}}}
                     RRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRR                                        R                                             R      R          
Erklärung der notwendigen Parameter:
"route": The main dictionary for a route
    "coords": An array of route-point coordinates. Each latitude / longitude should have 5 fraction digits.
    "binary": (optional) Base64 encoded binary "Beacon serialized targets"
    "v": Version of the QR-code format as Integer
    "settings": Dictionary for route settings
        "VT": VehicleType  …
        "RT": RouteType …
        "HW": Highways, …
        "TR": Tollroads, values are: 0=allow, 1=avoid, 2=forbid (nur 0 und 2 nutzen!)
        "FR": Ferries, values are: 0=allow, 1=avoid, 2=forbid (nur 0 und 2 nutzen!)
        "SR": Service roads, …
        "HOV": HOV lanes, …
        "CU": curvyness, values are: 0=less curvy ... 5=very curvy  -> bitte nur so nutzen: 1=Autobahn erlaubt, 2=Autobahn verboten
        "BE": bending, …
        "ROUND": round trip, …
Die wichtigen Parameter sind also FR, TR, CU
 * 
{"route":{"coords":["49.79752,9.94514","49.79856,9.9403","49.79518,9.92668"],"v":1,"settings":{"VT":1,"BE":0,"FR":0,"ROUND":0,"RT":3,"SR":0,"HOV":0,"HW":0,"TR":0,"CU":1}}}
 */


function createCruiserString($pointsLatLon){
                        $RT = array(
                            "route" => array(
                                "coords" => $pointsLatLon,
                                "v"     => 1,
                                "settings" => array(
                                "VT" => 1,
                                "BE" => 0,
                                "FR" => 2,
                                "ROUND" => 0,
                                "RT" => 3,
                                "SR" => 0,
                                "HOV" => 0,
                                "HW" => 0,
                                "TR" => 0,
                                "CU" => 2
                                )    
                            )
                        );

    
$tmp = json_encode($RT);
$tmp = str_replace('\\','',$tmp);
return $tmp;    
}


////////////////////
function createCruiserStrings($pointsLatLon){
    $ret = array();   
    $cnt = 0;
    $retIndex = 0;
    $maxinfile = 99;
    for ($x=0;$x<count($pointsLatLon);$x++){
        $cnt++;
        //echo "$cnt --_".$pointsLatLon[$x]."<br>";
        $latlon[] = $pointsLatLon[$x];
        if (($cnt == $maxinfile) or ($x == count($pointsLatLon)-1)){
            $ret[] = createCruiserString($latlon);
            $cnt = 0;
            $latlon = array();
        }
    }
    return $ret;
}
///////////////////






function fOutPutCruiser($alldata,$finallabel){
    for ($x = 0; $x < count($alldata);$x++){
        $co[] = $alldata[$x]['paesse_GPS_lat_dec'] . ","  .$alldata[$x]['paesse_GPS_lon_dec'];
    }
    $results = createCruiserStrings($co);
    $zip = new ZipArchive;
    $zipname = $finallabel . '_cruiser.zip';
    $tmpfilename = tempnam("tmp","dd_s_");
    $res = $zip->open($tmpfilename, ZipArchive::CREATE);
if ($res === TRUE) {

    $single = true;
    if (count($results) > 1) $single = false;
    for  ($x=0 ; $x < count($results); $x++){
        $cfn = $finallabel;
        if (!$single){
            $cfn .= "_teil_" . str_pad($x +1,2, '0', STR_PAD_LEFT);
        }
        $cfn .= '.cruiser';
        $zip->addFromString($cfn, $results[$x]);
    }
    $zip->close();
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$zipname);
        header('Content-Length: ' . filesize($tmpfilename));
        ob_end_clean();
        readfile($tmpfilename);
        unlink($tmpfilename);
} else {
    echo 'Fehler';
}        
} 


