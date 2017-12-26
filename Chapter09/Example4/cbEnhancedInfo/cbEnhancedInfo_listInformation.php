<?php
/*******************************************************************************

 File:         cbEnhancedInfo_listInformation.php
 Author:       Thomas Urban
 Language:     PHP
 Encoding:     UTF-8
 Status:       -
 License:      GPLv2
 
*******************************************************************************/

$dir = dirname(__FILE__);
$mainDir = preg_replace("@plugins.cbEnhancedInfo@","",$dir);
chdir($mainDir);
include_once("./include/auth.php");
$_SESSION['custom']=false;

switch (get_request_var('drp_action') ) {
	case '1':
		form_delete();
		break;
	default:
		top_header();
		form_display();
		bottom_footer();
		break;
}


function form_delete() {
    global $colors, $hash_type_names;
    
    /* loop through each of the selected tasks and delete them*/
	while (list($var,$val) = each($_POST)) {
		if (ereg("^chk_([0-9]+)$", $var, $matches)) {
			/* ================= input validation ================= */
			input_validate_input_number($matches[1]);
			/* ==================================================== */
            db_execute("DELETE FROM `plugin_cbEnhancedInfo_dataTable` where `Id`='" . $matches[1] . "'");
        }
	}
    header("Location: cbEnhancedInfo_listInformation.php");
}

function form_display() {
    global $colors, $hash_type_names;
    print "<font size=+1>cbEnhancedInfo - Enhanced Information Items</font><br>\n";
    print "<hr>\n";
	
    $where_clause = "";
 
    // Take care of the sorting, did the user select any column
    // to be sorted ?
    if ( isset_request_var('sort_column') )
    {
	// Did the user select a column that is actually sortable ?
        if (
            ( get_request_var("sort_column") == 'Id' )
            || ( get_request_var("sort_column") == 'hostId' )
            || ( get_request_var("sort_column") == 'contactAddress' )
           )
        {
	    // What direction should the table be sorted, ascending or
            // descending ?
            if (
                ( get_request_var("sort_direction") == 'ASC' )
                || ( get_request_var("sort_direction") == 'DESC' )
            )
            {
		// Finally, we can build the sort order sql statement
                $where_clause  .= ' ORDER BY ' .
                    get_nfilter_request_var("sort_column") .
                    ' ' .get_nfilter_request_var("sort_direction");
            }
        }
    }
    // Select all data items from the table. The data will be stored
    // in an array. Note the $where_clause being used
    $a_enhancedInfos = db_fetch_assoc("
        SELECT
          `plugin_cbEnhancedInfo_dataTable`.`Id`,
          `host`.`description` as hostDescription,
          `plugin_cbEnhancedInfo_dataTable`.`contactAddress`,
          `plugin_cbEnhancedInfo_dataTable`.`additionalInformation`
        FROM
          `plugin_cbEnhancedInfo_dataTable` INNER JOIN
          `host` ON `plugin_cbEnhancedInfo_dataTable`.`hostId` = `host`.`Id`
	$where_clause
    ");

    // Start the web form
    print "<form name=chk method=POST action=cbEnhancedInfo_listInformation.php>\n";

    // Print a nice looking html start box :-)
    html_start_box("<strong>Enhanced Information Items</strong>", "100%", $colors["header"], "3", "center", "cbEnhancedInfo_addInformation.php?action=add");

    if ( sizeof( $a_enhancedInfos ) > 0 ) 
    {
	// The table needs some menu, this will also be used for the sorting
        $menu_text = array(
            "Id" => array("Id", "ASC"),
            "hostId" => array("Host", "ASC"),
            "contactAddress" => array("Contact Address", "ASC")
        );
    
	// The html header will contain a checkbox, so the end-user can
	// select all items on the table at once.
        html_header_sort_checkbox($menu_text, get_nfilter_request_var("sort_column"), get_nfilter_request_var("sort_direction"));
    
	// This variable will be used to create te alternate colored
	// rows on the table
        $i = 0;
    
	// Let’s cycle through the items !
        foreach ($a_enhancedInfos as $a_enhancedInfo)
        {
             // Data presentation is missing here
        }
        html_end_box(false);

	// Let's define some actions for the user:
	$task_actions = array(
	    1 => "Delete"
	);
	draw_actions_dropdown($task_actions);
    }
    else
    {
	// Hm, we didn't find any items ? Let's notify our user !
	print "<tr><td><em>No enhanced information records exist</em></td></tr>";
        html_end_box(false);
    }
    
    print "</form>";
}    


?>
