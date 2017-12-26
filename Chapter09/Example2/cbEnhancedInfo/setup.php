<?php
/*******************************************************************************

 File:         setup.php
 Author:       Thomas Urban
 Language:     PHP
 Encoding:     UTF-8
 Status:       -
 License:      GPLv2
 
*******************************************************************************/

function plugin_cbEnhancedInfo_install () {
    api_plugin_register_hook('cbEnhancedInfo',
			     'draw_navigation_text',
			     'cbEnhancedInfo_draw_navigation_text',
			     'setup.php');
    api_plugin_register_hook('cbEnhancedInfo',
			     'config_arrays', 
			     'cbEnhancedInfo_config_arrays',
			     'setup.php');
    api_plugin_register_hook('cbEnhancedInfo',
			     'config_settings',
			     'cbEnhancedInfo_config_settings',
			     'setup.php');
    api_plugin_register_hook('cbEnhancedInfo',
			     'config_form',
			     'cbEnhancedInfo_config_form',
			     'setup.php');
    api_plugin_register_hook('cbEnhancedInfo',
			     'console_after',
			     'cbEnhancedInfo_console_after',
			     'setup.php');
    api_plugin_register_hook('cbEnhancedInfo',
			     'tree_after',
			     'cbEnhancedInfo_tree_after',
			     'setup.php');
    api_plugin_register_hook('cbEnhancedInfo',
			     'api_device_save',
			     'cbEnhancedInfo_api_device_save',
			     'setup.php');
	/* The realm permission are missing here --->*/
	/* <--- */
    cbEnhancedInfo_setup_table_new ();
}

function cbEnhancedInfo_draw_navigation_text ( $nav ) {    
    // Report Scheduler
    $nav["cbEnhancedInfo_listInformation.php:"] = array(
	"title" => "Enhanced Information List",
	"mapping" => "index.php:",
	"url" => "cbEnhancedInfo_listInformation.php",
	"level" => "1"
    );
    $nav["cbEnhancedInfo_addInformation.php:add"] = array(
	"title" => "(Add)",
	"mapping" => "index.php:,?",
	"url" => "cbEnhancedInfo_addInformation",
	"level" => "2"
    );
    $nav["cbEnhancedInfo_addInformation.php:update"] = array(
	"title" => "(Edit)",
	"mapping" => "index.php:,?",
	"url" => "cbEnhancedInfo_addInformation.php",
	"level" => "2"
    );
    return $nav;
}

function cbEnhancedInfo_config_form() {
    global $fields_host_edit;

   $fields_host_edit2 = $fields_host_edit;
   $fields_host_edit3 = array();
   foreach ($fields_host_edit2 as $f => $a) {
 	 $fields_host_edit3[$f] = $a;
	 if ($f == 'disabled') {
	    $fields_host_edit3["ebEnhancedInfo_serial"] = array(
		 "method" => "textbox",
		 "friendly_name" => "Serial Number ",
		 "description" => "The serial number of this device.",
		 "value" => "|arg1:ebEnhancedInfo_serial|",
		 "max_length" => "255",
		 "form_id" => false
	    );
	    $fields_host_edit3["ebEnhancedInfo_warranty"] = array(
		  "method" => "textbox",
		  "friendly_name" => "Warranty",
		  "description" => "The end date of the warranty of this device",
		  "value" => "|arg1:ebEnhancedInfo_warranty|",
		  "max_length" => "255",
		  "form_id" => false
	    );	    
	 } // end $f == disabled
   } // end foreach
   $fields_host_edit = $fields_host_edit3;
}


function cbEnhancedInfo_api_device_save ($save) {
        if (isset($_POST['ebEnhancedInfo_serial'])) {
                $save["ebEnhancedInfo_serial"] = form_input_validate($_POST['ebEnhancedInfo_serial'], "ebEnhancedInfo_serial", "", true, 255);
        } else {
                $save['ebEnhancedInfo_serial'] = form_input_validate('', "ebEnhancedInfo_serial", "", true, 3);
	}
        if (isset($_POST['ebEnhancedInfo_warranty'])) {
                $save["ebEnhancedInfo_warranty"] = form_input_validate($_POST['ebEnhancedInfo_warranty'], "ebEnhancedInfo_warranty", "", true, 255);
        } else {
                $save['ebEnhancedInfo_warranty'] = form_input_validate('', "ebEnhancedInfo_warranty", "", true, 3);
	}
    return $save;
}

    
function plugin_cbEnhancedInfo_uninstall () {
	// Do any extra Uninstall stuff here
}


function plugin_cbEnhancedInfo_check_config () {
	// Here we will check to ensure everything is configured
	cbEnhancedInfo_check_upgrade();

	return true;
}

function plugin_cbEnhancedInfo_upgrade () {
	// Here we will upgrade to the newest version
	cbEnhancedInfo_check_upgrade ();
	return false;
}

function plugin_cbEnhancedInfo_version () {
	return cbEnhancedInfo_version();
}

function cbEnhancedInfo_check_upgrade () {
	// We will only run this on pages which really need that data ...
	$files = array('cbEnhancedInfo_listInformation.php');
	if (isset($_SERVER['PHP_SELF']) && !in_array(basename($_SERVER['PHP_SELF']), $files))
		return;
	
	$current = cbEnhancedInfo_version ();
	$current = $current['version'];
	$old = db_fetch_cell("SELECT version FROM plugin_config WHERE directory='cbEnhancedInfo'");
	if ($current != $old) {
		cbEnhancedInfo_setup_table( $old );
	}
}

function cbEnhancedInfo_check_dependencies() {
    global $plugins, $config;
    return true;
}


function cbEnhancedInfo_setup_table_new () {
    global $config, $database_default;
    include_once($config["library_path"] . "/database.php");

    // Check if the cbEnhancedInfo tables are present
    $s_sql	= 'show tables from `' . $database_default . '`';
    $result = db_fetch_assoc( $s_sql ) or die ( mysql_error() );
    $a_tables = array();

    foreach($result as $index => $array) {
	    foreach($array as $table) {
		    $a_tables[] = $table;
	    }
    }

    /* The additional columns are missing here --->*/
	/* <--- */

   
    if (!in_array('plugin_cbEnhancedInfo_dataTable', $a_tables)) {
	    // Create Report Schedule Table
	    $data = array();
	    $data['columns'][] = array('name' => 'Id',
				       'type' => 'mediumint(25)',
				       'unsigned' => 'unsigned',
				       'NULL' => false,
				       'auto_increment' => true);
	    $data['columns'][] = array('name' => 'hostId',
				       'type' => 'mediumint(25)',
				       'unsigned' => 'unsigned',
				       'NULL' => false,
				       'default' => '0');
	    $data['columns'][] = array('name' => 'contactAddress',
				       'type' => 'varchar(1024)',
				       'NULL' => false);
	    $data['columns'][] = array('name' => 'additionalInformation',
				       'type' => 'text',
				       'NULL' => true);
	    $data['primary'] = 'Id';
	    $data['keys'][] = array('name' => 'hostId', 'columns' => 'hostId');
	    $data['type'] = 'MyISAM';
	    $data['comment'] = 'cbEnhancedInfo Data Table';
	    api_plugin_db_table_create ('cbEnhancedInfo', 'plugin_cbEnhancedInfo_dataTable', $data);
    }
}
	
function cbEnhancedInfo_config_settings () {
	global $tabs, $settings;
	$tabs["misc"] = "Misc";
	
	$temp = array(
            "cbEnhancedInfo_header" => array(
		"friendly_name" => "cbEnhancedInfo Plugin",
		"method" => "spacer",
		),
	    "cbEnhancedInfo_showInfo" => array(
                "friendly_name" => "Display enhanced information a the tree view",
                "description" => "This will display enhanced information after the tree view graph.",
                "method" => "checkbox",
                "max_length" => "255"
	        ),
	);

        if (isset($settings["misc"]))
                $settings["misc"] = array_merge($settings["misc"], $temp);
        else
                $settings["misc"] = $temp;
}


function cbEnhancedInfo_tree_after ($param)
{
    global $config, $database_default;
    include_once($config["library_path"] . "/database.php");

    // Only show the enhanced information if it is enabled in the settings
    if ( read_config_option('cbEnhancedInfo_showInfo') ) {
	
	// Get the parameters
	preg_match("/^(.+),(\d+)$/",$param,$hit);
	
	// Check if there are some parameters
	if ( isset ( $hit[1] ) )
	{    
	    $host_name = $hit[1];
	    $host_leaf_id = $hit[2];
	    
	    // Retrieve the host id
	    $host_id = db_fetch_cell("SELECT host_id FROM graph_tree_items WHERE id=$host_leaf_id");
	    
	    // Retrieve the enhanced information for that host from the table
	    $host_contactAddress = db_fetch_cell("SELECT contactAddress FROM plugin_cbEnhancedInfo_dataTable WHERE hostId=$host_id");
	    $host_additionalInformation = db_fetch_cell("SELECT additionalInformation FROM plugin_cbEnhancedInfo_dataTable WHERE hostId=$host_id");
    
	    // Retrieve the host specific information from the host table
	    $host_serial = db_fetch_cell("SELECT ebEnhancedInfo_serial FROM host WHERE id=$host_id");
	    $host_warranty = db_fetch_cell("SELECT ebEnhancedInfo_warranty FROM host WHERE id=$host_id");
	    
	    ?>    
	    <tr bgcolor='#6d88ad'>
		<tr bgcolor='#a9b7cb'>
		    <td colspan='3' class='textHeaderDark'>
			    <strong>Enhanced Information</strong>
		    </td>
		</tr>			
		<tr align='center' style='background-color: #f9f9f9;'>
		    <td align='center'>
	    <?php
	    
	    print "<table>\n";
	    print "	<tr>\n";
	    print " 	  <td align=left><b>Contact Address</b></td>\n";
	    print " 	  <td align=left>".$host_contactAddress."</td>\n";
	    print "	</tr>\n";
	    print "	<tr>\n";
	    print " 	  <td align=left><b>Serial</b></td>\n";
	    print " 	  <td align=left>".$host_serial."</td>\n";
	    print "	</tr>\n";
	    print "	<tr>\n";
	    print " 	  <td align=left><b>Warranty</b></td>\n";
	    print " 	  <td align=left>".$host_warranty."</td>\n";
	    print "	</tr>\n";
	    print "	<tr>\n";
	    print " 	  <td align=left><b>Aditional Information</b></td>\n";
	    print " 	  <td align=left>".$host_additionalInformation."</td>\n";
	    print "	</tr>\n";
	    print "</table>\n";

	    print "</td></tr></tr>";
	}
    } 
    return $param;
}


function cbEnhancedInfo_config_arrays () {
	global $menu;

	$temp = array(
		'plugins/cbEnhancedInfo/cbEnhancedInfo_listInformation.php' => 'Enhanced Info'
	);
        
   	if (isset($menu['CactiBook Plugins'])) {
		$menu['CactiBook Plugins'] = array_merge($temp, $menu['cbPlugins']);
	} else {
		$menu['CactiBook Plugins'] = $temp;
	}
}

function cbEnhancedInfo_version () {
	global $config;
	$info = parse_ini_file($config['base_path'] . '/plugins/cbEnhancedInfo/INFO', true);
	return $info['info'];
}

function cbEnhancedInfo_setup_table ( $old_version ) {
    global $config;
}




?>
