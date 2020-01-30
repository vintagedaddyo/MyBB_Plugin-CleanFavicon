<?php
/*
 * MyBB: Clean Favicon
 *
 * File: cleanfavicon.php
 * 
 * Authors: Kapsonfire, Vintagedaddyo
 *
 * MyBB Version: 1.8
 *
 * Plugin Version: 1.0
 *
 * License: GNU GPL 
 * 
 */ 

// Disallow direct access to this file for security reasons

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// Add Hooks

$plugins->add_hook('global_end','cleanfavicon_hook');

// Plugin Information

function cleanfavicon_info()
{
    global $lang;

    // Load Language
    
    $lang->load("cleanfavicon");
        $lang->cleanfavicon_description = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="float:right;">' .
        '<input type="hidden" name="cmd" value="_s-xclick">' . 
        '<input type="hidden" name="hosted_button_id" value="AZE6ZNZPBPVUL">' .
        '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">' .
        '<img alt="" border="0" src="https://www.paypalobjects.com/pl_PL/i/scr/pixel.gif" width="1" height="1">' .
        '</form>' . $lang->cleanfavicon_description;

    return array(
        'name' => $lang->cleanfavicon_name,
        'description' => $lang->cleanfavicon_description,
        'website' => $lang->cleanfavicon_website,
        'author' => $lang->cleanfavicon_author,
        'authorsite' => $lang->cleanfavicon_authorsite,
        'version' => $lang->cleanfavicon_version,
        'guid' => $lang->cleanfavicon_guid,
        'compatibility' => $lang->cleanfavicon_compatibility
    );
}

// Plugin Activation

function cleanfavicon_activate()
{
	
	global $db, $lang;
	
    // Load Language
    
    $lang->load("cleanfavicon");
       		
	require '../inc/adminfunctions_templates.php';
	
	$query  = $db->simple_select("settinggroups","COUNT(*) as rows");
	
	$drows   = $db->fetch_field($query,"rows");
	
	// Setting group
	
	$iad_group = array('name' => 'cleanfavicon','title' => $lang->cleanfavicon_setting_group_title,'description' => $lang->cleanfavicon_setting_group_description,'disporder' =>$drows + 1,'isdefault' => '0',);
	$db->insert_query('settinggroups',$iad_group);
	
	$gid = $db->insert_id();
	
	//  Setting 1 (onoff)
	
	$setting1	   = array('name' => 'CLEAN-FAV-Activate',
						   'title' => $lang->cleanfavicon_setting_1_title,
						   'description' => $lang->cleanfavicon_setting_1_description,
						   'optionscode' => 'onoff',
						   'value' => '0',
						   'disporder' => 1,
						   'gid' => intval($gid),);

	// Setting 2 (icon location)
					   
	$setting2	   = array('name' => 'CLEAN-FAV-Location',
						   'title' => $lang->cleanfavicon_setting_2_title,
						   'description' => $lang->cleanfavicon_setting_2_description,
						   'optionscode' => 'text',
						   'value' => 'images/favicon.ico',
						   'disporder' => 2,
						   'gid' => intval($gid),);

	   $db->insert_query('settings',$setting1);
	
    $db->insert_query('settings',$setting2);
    
	rebuild_settings();
						       
}

// Plugin Deactivation

function cleanfavicon_deactivate()
{
	
    global $db;
    
	require MYBB_ROOT.'/inc/adminfunctions_templates.php';
	
	
    $db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='".$lang->cleanfavicon_setting_group_title."'");
    
    $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('CLEAN-FAV-Activate', 'cleanfavicon')");
    
    $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('CLEAN-FAV-Location', 'cleanfavicon')");
    
    $db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name='cleanfavicon'");
    
	rebuild_settings();
	
}

// Valid Icon

function CLEANValidIcon($str)
{
	
	$str=strtolower($str);
	
	if($str=='ico'||$str=='png'||$str=='gif') return true;
	return false;
	
}

// Icon Format

function CLEANGetIconFormat($str)
{
	
	$str=strtolower($str);
	
	if($str=='ico')
	{
		
		return 'image/x-icon';
		
	}
	
	else if($str=='png')
	{
		
		return 'image/png';
		
	}
	
	else if($str=='gif')
	{
		
		return 'image/gif';
		
	}
	
	return '';
	
	
}

// Hook Plugin

function cleanfavicon_hook()
{
	global $headerinclude, $mybb;
	
	$str = explode('.',$mybb->settings['CLEAN-FAV-Location']);
	
	$for = $str[count($str)-1];	
	if(intval($mybb->settings['CLEAN-FAV-Activate']))
	{
		
		if(CLEANValidIcon($for))
		{
			
			$headerinclude .= '<link type="'.CLEANGetIconFormat($for).'" rel="icon" href="'.$mybb->settings['CLEAN-FAV-Location'].'" />';
			
		}
		
		else
		{
			
			$headerinclude .= '<!--- INVALID FAVICON !--->';
			
		}
	}
	
}

?>