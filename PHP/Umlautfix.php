<?php 


/**
 *
 *  MySQLI Implementierung von 
 * ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
 * Alle kaputten Umlaute reparieren bei Umstellung von ISO->UTF8 
 * Source: http://xhtmlforum.de/66480-kleines-skript-alle-umlaute-der-datenbank.html
 * 
 * @project        - 
 * @author        Boris Bojic <bojic@devshack.biz> 
 * @copyright    Copyright (c) 2011, Boris Bojic (DevShack) 
 * @version        Fri, 23 Dec 2011 13:47:11 +0100 
 * @updated        - 

 * |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| 
 * 
 * Mehere DBs auf einen Rutsch möglich
 */

header('Content-Type: text/html; charset=utf-8'); 

mb_internal_encoding('UTF-8'); 

$dbconnect = array();
$db = array(); 

$db[0]['host']        = "dbhost1"; 
$db[0]['uname']    = "dbuser1"; 
$db[0]['password']    = "dbpass1"; 
$db[0]['database']    = "database1"; 

$db[1]['host']        = "dbhost2"; 
$db[1]['uname']    = "dbuser2"; 
$db[1]['password']    = "dbpass2"; 
$db[1]['database']    = "database2"; 

foreach ($db as $database){

    dbwork($database);

}

/**
 * 
 */
function dbwork($db){
    global $dbconnect;
    $dbconnect = mysqli_connect($db['host'], $db['uname'], $db['password'], $db['database']) or die ("Konnte keine Verbindung zur Datenbank aufnehmen!"); 
    mysqli_set_charset($dbconnect,'utf8'); 
    echo '<pre>'; 

    $tablesArray = getTables($db); 
     
    // Alle Spalten pro Tabelle ermitteln und durcharbeiten 
    foreach($tablesArray AS $table){ 
         
        $affectedRows = 0; 
        $spalten = getColumns($table); 
    
        echo "Tabelle: " . $table . "<br />"; 
         
    
        foreach($spalten AS $spalte){ 
         
            echo "...Spalte: " . $spalte . "<br />"; 
         
            $query = ' 
                UPDATE `' . $table . '` SET 
                  `' . $spalte . '` = REPLACE(`' . $spalte . '`,"ÃŸ", "ß"), 
                  `' . $spalte . '` = REPLACE(`' . $spalte . '`, "Ã¤", "ä"), 
                  `' . $spalte . '` = REPLACE(`' . $spalte . '`, "Ã¼", "ü"), 
                  `' . $spalte . '` = REPLACE(`' . $spalte . '`, "Ã¶", "ö"), 
                  `' . $spalte . '` = REPLACE(`' . $spalte . '`, "Ã„", "Ä"), 
                  `' . $spalte . '` = REPLACE(`' . $spalte . '`, "Ãœ", "Ü"), 
                  `' . $spalte . '` = REPLACE(`' . $spalte . '`, "Ã–", "Ö"), 
                  `' . $spalte . '` = REPLACE(`' . $spalte . '`, "â‚¬", "€") 
            '; 
         
            echo $query . "\n";
            mysqli_query($dbconnect,$query) OR die(mysqli_error($dbconnect) . $query); 
            $affectedRows += mysqli_affected_rows($dbconnect); 
         
        } 
    
         
        echo "Tabelle " . $table . " aktualisiert, Datensätze: " . $affectedRows . "<br /><br />"; 
         
    }

}
/**
 * 
 */






function getTables($db){ 
    global $dbconnect;
    $result = mysqli_query($dbconnect,"SHOW TABLES FROM " . $db['database']); 
     
    while($row = mysqli_fetch_row($result)){ 
        $res[] = $row[0]; 
    } 

    return $res; 
     
} 

function getColumns($table){ 
    global $dbconnect;
    $table = mysqli_real_escape_string($dbconnect,$table); 

    $mysqlres = mysqli_query($dbconnect,"SHOW COLUMNS FROM " . $table); 
    while($row = mysqli_fetch_row($mysqlres)){ 
        $res[] = $row[0]; 
    } 

    return $res; 
} 

// Alle Tabellen ermitteln 

