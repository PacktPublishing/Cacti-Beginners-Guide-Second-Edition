<?php
/*

 Cacti User Import Script
 Book: "Cacti Beginner's Guide - Second Edition"
 Chapter 4 - "User Management"
 Version 1.1
 Based on the copy_user.php script
 
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
if (!isset($_SERVER['argv'][0]) || isset($_SERVER['REQUEST_METHOD'])  || isset($_SERVER['REMOTE_ADDR'])) {
        die('<br><strong>This script is only meant to run at the command line.</strong>');
}

$no_http_headers = true;
$script_version = '1.1';

include(dirname(__FILE__) . '/../include/global.php');
include_once($config['base_path'] . '/lib/auth.php');

if (empty($_SERVER['argv'][1]) ){
        display_help();
        exit;
} else {
        switch($_SERVER['argv'][1]) {
                case '--help':
                case '-H':
                case '-h':
                        display_help();
                        exit;
                case '--version':
                case '-V':
                case '-v':
                        display_version();
                        exit;
        }
}

$import_file = $_SERVER["argv"][1];
$template_user = $_SERVER["argv"][2];
$realm_id = $_SERVER["argv"][3];

// Realm Id can be: 0 - Local, 1 - LDAP, 2 - Web Auth
if ( ( $realm_id < 0 ) || ( $realm_id > 2  ) ) {
   // The realm id will be local unless a valid id was given
   $realm_id = 0;
}

print "\nIt is highly recommended that you use the web interface to copy users as this script will only copy Local Cacti users.\n\n";
print "Cacti User Copy Utility\n";
print "Template User: " . $template_user . "\n";
print "Realm: ". $auth_realms[$realm_id] . "\n";



/* Check that user exists - the template user must be setup as user with the local authentication set ( realm = 0 ) */
$user_auth = db_fetch_row("SELECT * FROM user_auth WHERE username = '" . $template_user . "' AND realm = 0");
if (! isset($user_auth)) {
        die("Error: Template user does not exist!\n\n");
}

if ( file_exists( $import_file ) ) {
   print "\nCopying/Creating User...\n";
   // read in the import file
   $lines = file( $import_file );
   foreach ($lines as $line)
   {
      // cycle through the file
      $line = rtrim ($line); // remove the line ending character
      $data = preg_split("/;/",$line);  // split the line data at the ";"
      $new_user_username = $data[0];
      $new_user_fullname = $data[1];

      // Check if the target username already exists in the database
	  $user_auth = db_fetch_row("SELECT * FROM user_auth WHERE username = '" . $new_user_username . "' AND realm = 0");
	  if (! isset($user_auth)) {
	     print "Error: User [$new_user_username] already exists !\n";
      }
      else {
        // The target username does not exist so we can proceed
        print "New User: " . $new_user_username . "\n";
        if (user_copy($template_user, $new_user_username) === false) {
          print "Error: User [$new_user_username] not copied!\n\n";
        }
        else {
          // The user exists, so we can add/change the full name
          db_execute("UPDATE user_auth SET full_name = '".$new_user_fullname."' WHERE username='".$new_user_username."'");

          // And adapt the Realm ID
          db_execute("UPDATE user_auth SET realm=".$realm_id." WHERE username='".$new_user_username."'");
        }
      }
   }
   print "User copied...\n";
}
else {
  die("Error: Import file [$import_file] does not exist!\n\n");
}

function display_help() {
	print "Syntax:\n";
	print " php import_user.php <import file> <template user> <realm id>\n";
	print "\n";
	print "<import file> content (Example):\n";
	print "  user1;User Name1\n";
	print "  user2;User Name2\n";
	print "  user3;User Name3\n";
	print "  user4;User Name4\n";
	print "\n";
	print "<template user>:\n";
	print "  The username of the cacti user to use as the template Example: guest\n";
	print "\n";
	print "<realm id>:\n";
	print "  0 – Local Authentication\n";
	print "  1 – LDAP Authentication\n";
	print "   2 – Web Authentication\n";
}

function display_version() {
	print "import_user.php - Version $script_version\n";
}
