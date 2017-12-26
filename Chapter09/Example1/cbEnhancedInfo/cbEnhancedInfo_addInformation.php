<?php
/*******************************************************************************

 File:         cbEnhancedInfo_addInformation.php
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
include_once("./lib/data_query.php");
$_SESSION['custom']=false;

switch (get_request_var('action') ) {
    case 'save':
	form_save( get_request_var('dataId') );
	break;
    default:
    top_header();
	form_display( get_request_var('dataId') );
	bottom_footer();
    break;
}

function form_save( $dataId ) {
    global $hash_type_names;
    if ( isset_request_var('hostId') ) { $s_dataHostId= get_nfilter_request_var('hostId'); }
	if ( isset_request_var('contactAddress') ) { $s_dataContactAddress = get_nfilter_request_var('contactAddress'); }
	if ( isset_request_var('additionalInformation') ) { $s_dataAdditionalInformation =  get_nfilter_request_var('additionalInformation'); }

    if ( ( isset_request_var('contactAddress') ) && (isset_request_var('save_component_import') ) ) {
        db_execute_prepared("
	    INSERT INTO `plugin_cbEnhancedInfo_dataTable`
		(`hostId`, `contactAddress`, `additionalInformation`)
	    VALUES
		(?, ?, ?)
	", array($s_dataHostId,$s_dataContactAddress,$s_dataAdditionalInformation));
    }

    if ( (isset_request_var('contactAddress')) && (isset_request_var('update_component_import') ) ) {
        db_execute_prepared("
	    UPDATE `plugin_cbEnhancedInfo_dataTable`
	    Set
		hostId=?,
		contactAddress=?,
		additionalInformation=?
	    WHERE
		Id=?
	", array($s_dataHostId,$s_dataContactAddress,$s_dataAdditionalInformation,$dataId) );
    }
    header("Location: cbEnhancedInfo_listInformation.php");

}

function form_display( $dataId ) {
    global $hash_type_names, $config;
    
    $i_defaultHostId = 0;
    $s_defaultContactAddress = '';
    $s_defaultAdditionalInformation = '';
	
    if ( $dataId > 0 )
    {
        $a_items = db_fetch_assoc("
	    SELECT
		`plugin_cbEnhancedInfo_dataTable`.`Id`,
		`plugin_cbEnhancedInfo_dataTable`.`hostId`,
		`plugin_cbEnhancedInfo_dataTable`.`contactAddress`,
		`plugin_cbEnhancedInfo_dataTable`.`additionalInformation`
	    FROM
		`plugin_cbEnhancedInfo_dataTable`
	    WHERE Id='$dataId'
	");
        foreach ($a_items as $a_item)
        {
	    $i_defaultHostId =  $a_item['hostId'];
	    $s_defaultContactAddress = $a_item['contactAddress'];
	    $s_defaultAdditionalInformation = $a_item['additionalInformation'];
	}
    }
    
    print "<font size=+1>cbEnhancedInfo - Add Enhanced Information Data</font><br>\n";
    print "<hr>\n";

	?>
	<form method="post" action="cbEnhancedInfo_addInformation.php" enctype="multipart/form-data">
	<?php

	if ( $dataId > 0 ) {
	    html_start_box("<strong>Enhanced Information Data</strong> [update]", "100%", $colors["header"], "3", "center", "");
	}
	else {
	    html_start_box("<strong>Enhanced Information Data</strong> [new]", "100%", $colors["header"], "3", "center", "");		
	}

	form_alternate_row_color($colors["form_alternate1"],$colors["form_alternate2"],0); ?>
	    <td width="50%">
		<font class="textEditTitle">Device Name</font><br>
		The device this data set is for.
	    </td>
	    <td>
		<?php
		$a_hosts = db_fetch_assoc("
		    SELECT
			id,
			CONCAT(description,' [',hostname,'] ') as name
		    FROM
			host
		;");
		form_dropdown("hostId",$a_hosts, "name", "id", $i_defaultHostId, "" ,$i_defaultHostId ,"","");
		?>
	    </td>
	</tr>

	<?php form_alternate_row_color($colors["form_alternate1"],$colors["form_alternate2"],1); ?>
	    <td width="50%">
		<font class="textEditTitle">Contact Address</font><br>
		A name, email or any other contact information.
	    </td>
	    <td>
		<?php  form_text_box("contactAddress","",$s_defaultContactAddress,255); ?>
	    </td>
	</tr>

	<?php form_alternate_row_color($colors["form_alternate1"],$colors["form_alternate2"],0); ?>
	    <td width="50%">
		<font class="textEditTitle">Additional Information</font><br>
		Some additional information for this device.
	    </td>
	    <td>
		<?php form_text_area("additionalInformation",$s_defaultAdditionalInformation,5,50,""); ?>
	    </td>		
	</tr>			

	<?php

    if ( $dataId > 0)
    {
    	form_hidden_box("update_component_import","1","");
        form_hidden_box("dataId",$dataId,"");
    }
    else
    {
    	form_hidden_box("save_component_import","1","");
    }

    html_end_box();
    form_save_button("cbEnhancedInfo_listInformation.php", "save");
	
}

bottom_footer();
?>
