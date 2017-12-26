<?php
/*
 +-------------------------------------------------------------------------+
 | Copyright (C) 2004-2010 The Cacti Group                                 |
 |                                                                         |
 | This program is free software; you can redistribute it and/or           |
 | modify it under the terms of the GNU General Public License             |
 | as published by the Free Software Foundation; either version 2          |
 | of the License, or (at your option) any later version.                  |
 |                                                                         |
 | This program is distributed in the hope that it will be useful,         |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
 | GNU General Public License for more details.                            |
 +-------------------------------------------------------------------------+
 | Cacti: The Complete RRDTool-based Graphing Solution                     |
 +-------------------------------------------------------------------------+
 | This code is designed, written, and maintained by the Cacti Group. See  |
 | about.php and/or the AUTHORS file for specific developer information.   |
 +-------------------------------------------------------------------------+
 | http://www.cacti.net/                                                   |
 +-------------------------------------------------------------------------+
*/

/* do NOT run this script through a web browser */
if (!isset($_SERVER["argv"][0]) || isset($_SERVER['REQUEST_METHOD'])  || isset($_SERVER['REMOTE_ADDR'])) {
        die("<br><strong>This script is only meant to run at the command line.</strong>");
}

if (empty($_SERVER["argv"][1])) {
        print "Syntax:\n php import_devices.php <import file>\n\n";
        exit;
}


$no_http_headers = true;

include(dirname(__FILE__) . "/../include/global.php");
include_once($config["base_path"] . "/lib/auth.php");

$import_file = $_SERVER["argv"][1];
$dir = dirname(__FILE__);

print "Cacti Device Import Utility\n";
print "Import File: ". $import_file . "\n";

/* Check if the import file exists */
if ( file_exists( $import_file ) ) {
   print "\nImporting Devices...\n";
   // read in the import file
   $lines = file( $import_file );
   foreach ($lines as $line)
   {
      // cycle through the file
      $line = rtrim ($line); // remove the line ending character
      $data = preg_split("/;/",$line);  // split the line data at the ";"
      $device_description = $data[0];
      $device_ip = $data[1];
      $device_snmp_version = $data[2];
      $device_snmp_community = $data[3];
      $device_template = $data[4];
      if ( preg_match("/^\d+$/",$device_template) == 0 ) {
        $device_template = 2; // Generic SNMP-enabled device
      }
      $command = "php $dir/add_device.php ".
                 "--ip=\"$device_ip\" ".
                 "--description=\"$device_description\" ".
                 "--version=$device_snmp_version ".
                 "--community=$device_snmp_community ".
                 "--template=$device_template";
      $return_code = `$command`;
      print $return_code;
   }
}
else {
  die("Error: Import file [$import_file] does not exist!\n\n");
}


?>
