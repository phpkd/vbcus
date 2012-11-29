<?php
/*==================================================================================*\
|| ################################################################################ ||
|| # Product Name: vB Username Change Manager               Version: 1.0.0 Beta.1 # ||
|| # Licence Number: {LicenceNumber}
|| # ---------------------------------------------------------------------------- # ||
|| # 																			  # ||
|| #          Copyright ©2005-2008 PHP KingDom, Ltd. All Rights Reserved.         # ||
|| #       This file may not be redistributed in whole or significant part.       # ||
|| # 																			  # ||
|| # ------------- vB Username Change Manager IS NOT FREE SOFTWARE -------------- # ||
|| #           http://www.phpkd.org | http://www.phpkd.org/license.html           # ||
|| ################################################################################ ||
\*==================================================================================*/

// ######################## SET PHP ENVIRONMENT ###########################
error_reporting(E_ALL & ~E_NOTICE);

// ##################### DEFINE IMPORTANT CONSTANTS #######################
define('CVS_REVISION', '$RCSfile$ - $Revision: 10000 $');

// #################### PRE-CACHE TEMPLATES AND DATA ######################
$phrasegroups = array('cpuser', 'user');
$specialtemplates = array();

// ########################## REQUIRE BACK-END ############################
require_once('./global.php');

// ######################## CHECK ADMIN PERMISSIONS #######################
if (!can_administer('canadminusers'))
{
	print_cp_no_permission();
}

// ############################# LOG ACTION ###############################
log_admin_action();

// ########################################################################
// ######################### START MAIN SCRIPT ############################
// ########################################################################

// #############################################################################
print_cp_header($vbphrase['user_manager']);
// #############################################################################

if (empty($_REQUEST['do']))
{
	$_REQUEST['do'] = 'intro';
}

// ########################## Start intro ###########################
if ($_REQUEST['do'] == 'intro')
{
	print_form_header('phpkd_chusrn', 'view');
	print_table_header($vbphrase['phpkd_chusrn_history']);
	print_cells_row(array($vbphrase['phpkd_chusrn_log_entries_sorting'],
		"<div align=\"$stylevar[left]\" id=\"ctrl_sort\">
			<select name=\"action\" id=\"sel_action_1\" tabindex=\"1\" class=\"bginput\" title=\"name=&quot;action&quot;\">
				<optgroup label=\"" . $vbphrase['phpkd_chusrn_log_entries_sorting_action'] . "\">
					" . construct_select_options(array('all' => $vbphrase['phpkd_chusrn_log_entries_sorting_action_all'], 'waiting' => $vbphrase['phpkd_chusrn_log_entries_sorting_waiting'], 'approved' => $vbphrase['phpkd_chusrn_log_entries_sorting_approved'], 'auto_approved' => $vbphrase['phpkd_chusrn_log_entries_sorting_action_auto_approved'], 'denied' => $vbphrase['phpkd_chusrn_log_entries_sorting_action_denied']), 'all') . "
				</optgroup>
			</select>
			<select name=\"orderby\" id=\"sel_orderby_2\" tabindex=\"1\" class=\"bginput\" title=\"name=&quot;orderby&quot;\">
				<optgroup label=\"" . $vbphrase['phpkd_chusrn_log_entries_sorting_orderby'] . "\">
					" . construct_select_options(array('id' => $vbphrase['phpkd_chusrn_log_entries_sorting_orderby_id'], 'userid' => $vbphrase['phpkd_chusrn_log_entries_sorting_orderby_userid'], 'oldusergroupid' => $vbphrase['phpkd_chusrn_log_entries_sorting_orderby_oldusergroupid'], 'newusergroupid' => $vbphrase['phpkd_chusrn_log_entries_sorting_orderby_newusergroupid'], 'oldusername' => $vbphrase['phpkd_chusrn_log_entries_sorting_orderby_oldusername'], 'newusername' => $vbphrase['phpkd_chusrn_log_entries_sorting_orderby_newusername'], 'action' => $vbphrase['phpkd_chusrn_log_entries_sorting_orderby_action'], 'request_dateline' => $vbphrase['phpkd_chusrn_log_entries_sorting_orderby_request_dateline'], 'action_dateline' => $vbphrase['phpkd_chusrn_log_entries_sorting_orderby_action_dateline'], 'processedby' => $vbphrase['phpkd_chusrn_log_entries_sorting_orderby_processedby']), 'id') . "
				</optgroup>
			</select>
			<select name=\"direction\" id=\"sel_direction_3\" tabindex=\"1\" class=\"bginput\" title=\"name=&quot;direction&quot;\">
				<optgroup label=\"" . $vbphrase['phpkd_chusrn_log_entries_sorting_direction'] . "\">
					" . construct_select_options(array('ASC' => $vbphrase['phpkd_chusrn_log_entries_sorting_direction_asc'], 'DESC' => $vbphrase['phpkd_chusrn_log_entries_sorting_direction_desc']), 'ASC') . "
				</optgroup>
			</select>
			<select name=\"perpage\" id=\"sel_perpage_4\" tabindex=\"1\" class=\"bginput\" title=\"name=&quot;perpage&quot;\">
				<optgroup label=\"" . $vbphrase['phpkd_chusrn_log_entries_sorting_perpage'] . "\">
					" . construct_select_options(array(5 => 5, 10 => 10, 15 => 15, 20 => 20, 25 => 25, 30 => 30, 40 => 40, 50 => 50, 100 => 100), 15) . "
				</optgroup>
			</select>
		</div>"
	), false, '', -2);
	print_cells_row(array($vbphrase['username'],
		"<div align=\"$stylevar[left]\" id=\"ctrl_username\">
			<input type=\"text\" class=\"bginput\" name=\"username\" tabindex=\"1\" size=\"35\" />
			<select name=\"usernametype\" id=\"sel_usernametype_5\" tabindex=\"1\" class=\"bginput\" title=\"name=&quot;usernametype&quot;\">
				<optgroup label=\"" . $vbphrase['phpkd_chusrn_log_entries_sorting_usernametype'] . "\">
					" . construct_select_options(array('oldusername' => $vbphrase['phpkd_chusrn_log_entries_sorting_usernametype_oldusername'], 'newusername' => $vbphrase['phpkd_chusrn_log_entries_sorting_usernametype_newusername']), 'newusername') . "
				</optgroup>
			</select>
			<input type=\"checkbox\" name=\"exact\" value=\"1\" title=\"name=&quot;exact&quot;\" />&nbsp;$vbphrase[phpkd_chusrn_log_entries_sorting_exactusername]
		</div>"
	), false, '', -2);
	print_time_row($vbphrase['phpkd_chusrn_log_entries_sorting_dateline'], 'dateline', iif($vbulletin->GPC['dateline'], $vbulletin->GPC['dateline'], TIMENOW - 3600 * 24 * 365), 0);
	print_submit_row($vbphrase['find']);
	print_table_footer();
}

// #############################################################################
// process usergroup join requests
if ($_POST['do'] == 'processrequests')
{
	$vbulletin->input->clean_array_gpc('p', array(
		'request' => TYPE_ARRAY_INT
	));

	// check we have some results to process
	if (empty($vbulletin->GPC['request']))
	{
		print_stop_message('no_matches_found');
	}

	$auth = array();

	// sort the requests according to the action specified
	foreach($vbulletin->GPC['request'] AS $requestid => $action)
	{
		switch($action)
		{
			case -1:	// this request will be ignored
				unset($vbulletin->GPC['request']["$requestid"]);
				break;
			case  1:	// this request will be authorized
				$auth[] = $requestid;
				break;
			case  0:	// this request will be denied
				// do nothing - this request will be zapped at the end of this segment
				break;
		}
	}

	// #################################################
	// ############ Process Approved Requests ##########
	// #################################################
	if (!empty($auth))
	{
		$authlogs = $db->query_read("
			SELECT history.*, user.email
			FROM " . TABLE_PREFIX . "phpkd_usernamehistory AS history
			LEFT JOIN " . TABLE_PREFIX . "user AS user USING (userid)
			WHERE id IN (" . implode(', ', $auth) . ")
		");

		// instanciate User data manager class
		$userdata =& datamanager_init('user', $vbulletin, ERRTYPE_CP);
		$userdata->set_existing($vbulletin->userinfo);

		while ($authlog = $db->fetch_array($authlogs))
		{
			$info = fetch_userinfo($authlog['userid']);
			$userdata->set_existing($info);

			// set the username field to be updated
			$userdata->set('username', $authlog['newusername']);


			// #########################################
			// ####### ## Users Notifications ## #######
			// #########################################
			if ($vbulletin->options['phpkd_chusrn_users_notifications'] == 'pm' OR $vbulletin->options['phpkd_chusrn_users_notifications'] == 'both')
			{
				// Sending PM Notifications to staff about the username changes/requests to be reviewed & processed
				$searchfor = array("{oldusername}", "{newusername}", "{date}", "{comment}", "{contactuslink}", "{bbtitle}", "{bburl}");
				$replacewith   = array($authlog['oldusername'], $authlog['newusername'], vbdate($vbulletin->options['dateformat']), $vbphrase['phpkd_chusrn_subaction_nots'] . $vbphrase['phpkd_chusrn_subaction_passactivation'], $vbulletin->options['bburl'] . '/' . $vbulletin->options['contactuslink'], $vbulletin->options['bbtitle'], $vbulletin->options['bburl']); 
				$pm['subject'] = @str_replace($searchfor, $replacewith, $vbphrase['phpkd_chusrn_acp_report_notification_passactivation_subject']); 
				$pm['message'] = @str_replace($searchfor, $replacewith, $vbphrase['phpkd_chusrn_acp_report_notification_passactivation_body']); 

				$fromuser = fetch_userinfo($vbulletin->options['phpkd_chusrn_report_notification_sender']);
				$pm['fromuserid'] = $fromuser['userid'];
				$pm['fromusername'] = $fromuser['username'];

				// create the DM to do error checking and insert the new PM
				$pmdm_users =& datamanager_init('PM', $vbulletin, ERRTYPE_SILENT);

				$pmdm_users->set_info('savecopy',      $vbulletin->options['phpkd_chusrn_pm_savecopy']);
				$pmdm_users->set_info('receipt',       $vbulletin->options['phpkd_chusrn_pm_receipt']);
				$pmdm_users->set_info('cantrackpm',    $cantrackpm);
				if ($vbulletin->options['phpkd_chusrn_pm_overridequota'])
				{
					$pmdm_users->overridequota = true;
				}
				$pmdm_users->set('fromuserid', $pm['fromuserid']);
				$pmdm_users->set('fromusername', $pm['fromusername']);
				$pmdm_users->setr('title', $pm['subject']);
				$pmdm_users->set_recipients($authlog['oldusername'], $permissions, 'cc');
				$pmdm_users->setr('message', $pm['message']);
				$pmdm_users->setr('iconid', $vbulletin->options['phpkd_chusrn_pm_iconid']);
				$pmdm_users->set('dateline', TIMENOW);
				$pmdm_users->setr('showsignature', $vbulletin->options['phpkd_chusrn_pm_signature']);
				$pmdm_users->set('allowsmilie', $vbulletin->options['phpkd_chusrn_pm_disablesmilies'] ? 0 : 1);
				$pmdm_users->save();
			}

			// E-Mailing
			if ($vbulletin->options['phpkd_chusrn_users_notifications'] == 'email' OR $vbulletin->options['phpkd_chusrn_users_notifications'] == 'both')
			{
				eval(fetch_email_phrases('phpkd_chusrn_report_passactivation'));
				vbmail($info['email'], $subject, $message, true);
			}


			// save the data
			$userdata->save();

			// update processed requests
			$updateQuery = "
				UPDATE " . TABLE_PREFIX . "phpkd_usernamehistory
				SET action = 'approved', action_dateline = '" . TIMENOW . "', processedby = '" . $vbulletin->userinfo['userid'] . "'
				WHERE userid = '" . $authlog['userid'] . "'
					AND id = '" . $authlog['id'] . "' 
			";
			$db->query_write($updateQuery);
		}
	}


	// #################################################
	// ############ Process Denied Requests ############
	// #################################################
	function notauth($allreqs, $authrequests)
	{
		foreach ($authrequests AS $authrequestid => $authrequest)
		{
			if (array_key_exists($authrequest, $allreqs))
			{
				unset($allreqs[$authrequest]);
			}
			
		}

		return $allreqs;
	}

	$notauth = notauth($vbulletin->GPC['request'], $auth);

	if (is_array($notauth) AND !empty($notauth))
	{
		$notauthlogs = $db->query_read("
			SELECT history.*, user.email
			FROM " . TABLE_PREFIX . "phpkd_usernamehistory AS history
			LEFT JOIN " . TABLE_PREFIX . "user AS user USING (userid)
			WHERE id IN (" . implode(', ', array_keys($notauth)) . ")
		");

		while ($notauthlog = $db->fetch_array($notauthlogs))
		{
			$info = fetch_userinfo($notauthlog['userid']);

			// #########################################
			// ####### ## Users Notifications ## #######
			// #########################################
			if ($vbulletin->options['phpkd_chusrn_users_notifications'] == 'pm' OR $vbulletin->options['phpkd_chusrn_users_notifications'] == 'both')
			{
				// Sending PM Notifications to staff about the username changes/requests to be reviewed & processed
				$searchfor = array("{oldusername}", "{newusername}", "{date}", "{comment}", "{contactuslink}", "{bbtitle}", "{bburl}");
				$replacewith   = array($notauthlog['oldusername'], $notauthlog['newusername'], vbdate($vbulletin->options['dateformat']), $vbphrase['phpkd_chusrn_subaction_nots'] . $vbphrase['phpkd_chusrn_subaction_denied'], $vbulletin->options['bburl'] . '/' . $vbulletin->options['contactuslink'], $vbulletin->options['bbtitle'], $vbulletin->options['bburl']); 
				$pm['subject'] = @str_replace($searchfor, $replacewith, $vbphrase['phpkd_chusrn_acp_report_notification_denied_subject']); 
				$pm['message'] = @str_replace($searchfor, $replacewith, $vbphrase['phpkd_chusrn_acp_report_notification_denied_body']); 

				$fromuser = fetch_userinfo($vbulletin->options['phpkd_chusrn_report_notification_sender']);
				$pm['fromuserid'] = $fromuser['userid'];
				$pm['fromusername'] = $fromuser['username'];

				// create the DM to do error checking and insert the new PM
				$pmdm_users =& datamanager_init('PM', $vbulletin, ERRTYPE_SILENT);

				$pmdm_users->set_info('savecopy',      $vbulletin->options['phpkd_chusrn_pm_savecopy']);
				$pmdm_users->set_info('receipt',       $vbulletin->options['phpkd_chusrn_pm_receipt']);
				$pmdm_users->set_info('cantrackpm',    $cantrackpm);
				if ($vbulletin->options['phpkd_chusrn_pm_overridequota'])
				{
					$pmdm_users->overridequota = true;
				}
				$pmdm_users->set('fromuserid', $pm['fromuserid']);
				$pmdm_users->set('fromusername', $pm['fromusername']);
				$pmdm_users->setr('title', $pm['subject']);
				$pmdm_users->set_recipients($notauthlog['oldusername'], $permissions, 'cc');
				$pmdm_users->setr('message', $pm['message']);
				$pmdm_users->setr('iconid', $vbulletin->options['phpkd_chusrn_pm_iconid']);
				$pmdm_users->set('dateline', TIMENOW);
				$pmdm_users->setr('showsignature', $vbulletin->options['phpkd_chusrn_pm_signature']);
				$pmdm_users->set('allowsmilie', $vbulletin->options['phpkd_chusrn_pm_disablesmilies'] ? 0 : 1);
				$pmdm_users->save();
			}

			// E-Mailing
			if ($vbulletin->options['phpkd_chusrn_users_notifications'] == 'email' OR $vbulletin->options['phpkd_chusrn_users_notifications'] == 'both')
			{
				eval(fetch_email_phrases('phpkd_chusrn_report_passactivation'));
				vbmail($info['email'], $subject, $message, true);
			}


			// update processed requests
			$updateQuery = "
				UPDATE " . TABLE_PREFIX . "phpkd_usernamehistory
				SET action = 'denied', action_dateline = '" . TIMENOW . "', processedby = '" . $vbulletin->userinfo['userid'] . "'
				WHERE userid = '" . $notauthlog['userid'] . "'
					AND id = '" . $notauthlog['id'] . "' 
			";
			$db->query_write($updateQuery);
		}
	}

	// and finally jump back to the requests screen
	$_REQUEST['do'] = 'view';
}

// ######################## Start List Logs #########################
if ($_REQUEST['do'] == 'view')
{
	$vbulletin->input->clean_array_gpc('r', array(
		'action'        => TYPE_STR,
		'orderby'       => TYPE_STR,
		'direction'     => TYPE_STR,
		'perpage'       => TYPE_INT,
		'pagenumber'    => TYPE_INT,
		'username'      => TYPE_STR,
		'usernametype'  => TYPE_STR,
		'exact'         => TYPE_UINT,
		'dateline'      => TYPE_ARRAY_UINT,
		'date'          => TYPE_INT
	));

	$sqlconds = "WHERE 1 = 1 ";
	if ($vbulletin->GPC['action'] AND $vbulletin->GPC['action'] != 'all')
	{
		$sqlconds .= " AND history.action = '" . $vbulletin->GPC['action'] . "'";
	}

	if ($vbulletin->GPC['username'])
	{
		switch ($vbulletin->GPC['usernametype'])
		{
			case 'oldusername':
			case 'newusername':
				$usernametype = $vbulletin->GPC['usernametype'];
			default:
				$usernametype = 'newusername';
		}

		if ($vbulletin->GPC['exact'])
		{
			$sqlconds .= " AND history." . $vbulletin->GPC['usernametype'] . " = '" . $vbulletin->db->escape_string(htmlspecialchars_uni($vbulletin->GPC['username'])) . "'";
		}
		else
		{
			$sqlconds .= " AND history." . $vbulletin->GPC['usernametype'] . " LIKE '%" . $vbulletin->db->escape_string_like(htmlspecialchars_uni($vbulletin->GPC['username'])) . "%'";
		}
	}

	if ($vbulletin->GPC['dateline'])
	{
		$vbulletin->GPC['date'] = mktime(0, 0, 0, $vbulletin->GPC['dateline']['month'], $vbulletin->GPC['dateline']['day'], $vbulletin->GPC['dateline']['year']);
		$sqlconds .= " AND history.request_dateline > '" . $vbulletin->GPC['date'] . "'";
	}
	elseif ($vbulletin->GPC['date'])
	{
		$sqlconds .= " AND history.request_dateline > '" . $vbulletin->GPC['date'] . "'";
	}

	if ($vbulletin->GPC['orderby'])
	{
		$order = $vbulletin->GPC['orderby'];
	}
	else
	{
		$order = "action_dateline";
	}

	if ($vbulletin->GPC['direction'])
	{
		$direction = $vbulletin->GPC['direction'];
	}
	else
	{
		$direction = "ASC";
	}

	if ($vbulletin->GPC['perpage'] < 1)
	{
		$vbulletin->GPC['perpage'] = 15;
	}

	if ($vbulletin->GPC['pagenumber'] < 1)
	{
		$vbulletin->GPC['pagenumber'] = 1;
	}

	$startat = ($vbulletin->GPC['pagenumber'] - 1) * $vbulletin->GPC['perpage'];
	$counter = $db->query_first("SELECT COUNT(*) AS total FROM " . TABLE_PREFIX . "phpkd_usernamehistory AS history $sqlconds");
	$totalpages = ceil($counter['total'] / $vbulletin->GPC['perpage']);

	$logs = $db->query_read("
		SELECT history.*
		FROM " . TABLE_PREFIX . "phpkd_usernamehistory AS history
		$sqlconds
		ORDER BY $order $direction
		LIMIT $startat, " .  $vbulletin->GPC['perpage']
	);

	if ($db->num_rows($logs))
	{
		if ($vbulletin->GPC['pagenumber'] != 1)
		{
			$prv = $vbulletin->GPC['pagenumber'] - 1;
			$firstpage = "<input type=\"button\" class=\"button\" value=\"&laquo; " . $vbphrase['first_page'] .
				"\" tabindex=\"1\" onclick=\"window.location='phpkd_chusrn.php?" . $vbulletin->session->vars['sessionurl'] .
				"do=view&action=" . $vbulletin->GPC['action'] .
				"&username=" . $vbulletin->db->escape_string(htmlspecialchars_uni($vbulletin->GPC['username'])) .
				"&usernametype=" . $vbulletin->GPC['usernametype'] .
				"&exact=" . $vbulletin->GPC['exact'] .
				"&date=" . $vbulletin->GPC['date'] .
				"&orderby=" . $vbulletin->GPC['orderby'] .
				"&direction=" . $vbulletin->GPC['direction'] .
				"&pp=" . $vbulletin->GPC['perpage'] .
				"&page=1'\">";

			$prevpage = "<input type=\"button\" class=\"button\" value=\"&lt; " . $vbphrase['prev_page'] .
				"\" tabindex=\"1\" onclick=\"window.location='phpkd_chusrn.php?" . $vbulletin->session->vars['sessionurl'] .
				"do=view&action=" . $vbulletin->GPC['action'] .
				"&username=" . $vbulletin->db->escape_string(htmlspecialchars_uni($vbulletin->GPC['username'])) .
				"&usernametype=" . $vbulletin->GPC['usernametype'] .
				"&exact=" . $vbulletin->GPC['exact'] .
				"&date=" . $vbulletin->GPC['date'] .
				"&orderby=" . $vbulletin->GPC['orderby'] .
				"&direction=" . $vbulletin->GPC['direction'] .
				"&pp=" . $vbulletin->GPC['perpage'] .
				"&page=$prv'\">";
		}

		if ($vbulletin->GPC['pagenumber'] != $totalpages)
		{
			$nxt = $vbulletin->GPC['pagenumber'] + 1;
			$nextpage = "<input type=\"button\" class=\"button\" value=\"" . $vbphrase['next_page'] .
				" &gt;\" tabindex=\"1\" onclick=\"window.location='phpkd_chusrn.php?" .
				$vbulletin->session->vars['sessionurl'] .
				"do=view&action=" . $vbulletin->GPC['action'] .
				"&username=" . $vbulletin->db->escape_string(htmlspecialchars_uni($vbulletin->GPC['username'])) .
				"&usernametype=" . $vbulletin->GPC['usernametype'] .
				"&exact=" . $vbulletin->GPC['exact'] .
				"&date=" . $vbulletin->GPC['date'] .
				"&orderby=" . $vbulletin->GPC['orderby'] .
				"&direction=" . $vbulletin->GPC['direction'] .
				"&pp=" . $vbulletin->GPC['perpage'] .
				"&page=$nxt'\">";

			$lastpage = "<input type=\"button\" class=\"button\" value=\"" . $vbphrase['last_page'] .
				" &raquo;\" tabindex=\"1\" onclick=\"window.location='phpkd_chusrn.php?" . $vbulletin->session->vars['sessionurl'] .
				"do=view&action=" . $vbulletin->GPC['action'] .
				"&username=" . $vbulletin->db->escape_string(htmlspecialchars_uni($vbulletin->GPC['username'])) .
				"&usernametype=" . $vbulletin->GPC['usernametype'] .
				"&exact=" . $vbulletin->GPC['exact'] .
				"&date=" . $vbulletin->GPC['date'] .
				"&orderby=" . $vbulletin->GPC['orderby'] .
				"&direction=" . $vbulletin->GPC['direction'] .
				"&pp=" . $vbulletin->GPC['perpage'] .
				"&page=$totalpages'\">";
		}

		print_form_header('phpkd_chusrn', 'processrequests');
		construct_hidden_code('action', $vbulletin->GPC['action']);
		construct_hidden_code('orderby', $vbulletin->GPC['orderby']);
		construct_hidden_code('direction', $vbulletin->GPC['direction']);
		construct_hidden_code('perpage', $vbulletin->GPC['perpage']);
		construct_hidden_code('pagenumber', $vbulletin->GPC['pagenumber']);
		construct_hidden_code('username', $vbulletin->GPC['username']);
		construct_hidden_code('usernametype', $vbulletin->GPC['usernametype']);
		construct_hidden_code('exact', $vbulletin->GPC['exact']);
		construct_hidden_code('date', $vbulletin->GPC['date']);
		//print_description_row(construct_link_code($vbphrase['restart'], "phpkd_chusrn.php?" . $vbulletin->session->vars['sessionurl']), 0, 11, 'thead', $stylevar['right']);
		print_table_header(construct_phrase($vbphrase['phpkd_chusrn_log_viewer_page_x_y_there_are_z_total_log_entries'], vb_number_format($vbulletin->GPC['pagenumber']), vb_number_format($totalpages), vb_number_format($counter['total'])), 11);

		$headings = array();
		$headings[] = "<a href='phpkd_chusrn.php?" . $vbulletin->session->vars['sessionurl'] . "do=view&action=" . $vbulletin->GPC['action'] . "&username=" . $vbulletin->GPC['username'] . "&usernametype=" . $vbulletin->GPC['usernametype'] . "&exact=" . $vbulletin->GPC['exact'] . "&date=" . $vbulletin->GPC['date'] . "&orderby=id&direction=" . $vbulletin->GPC['direction'] . "&pp=" . $vbulletin->GPC['perpage'] . "&page=" . $vbulletin->GPC['pagenumber'] . "' title='" . $vbphrase['phpkd_chusrn_headings_order_by_logid_title'] . "'>" . $vbphrase['phpkd_chusrn_headings_order_by_logid'] . "</a>";
		$headings[] = "<a href='phpkd_chusrn.php?" . $vbulletin->session->vars['sessionurl'] . "do=view&action=" . $vbulletin->GPC['action'] . "&username=" . $vbulletin->GPC['username'] . "&usernametype=" . $vbulletin->GPC['usernametype'] . "&exact=" . $vbulletin->GPC['exact'] . "&date=" . $vbulletin->GPC['date'] . "&orderby=userid&direction=" . $vbulletin->GPC['direction'] . "&pp=" . $vbulletin->GPC['perpage'] . "&page=" . $vbulletin->GPC['pagenumber'] . "' title='" . $vbphrase['phpkd_chusrn_headings_order_by_userid_title'] . "'>" . $vbphrase['phpkd_chusrn_headings_order_by_userid'] . "</a>";
		$headings[] = "<a href='phpkd_chusrn.php?" . $vbulletin->session->vars['sessionurl'] . "do=view&action=" . $vbulletin->GPC['action'] . "&username=" . $vbulletin->GPC['username'] . "&usernametype=" . $vbulletin->GPC['usernametype'] . "&exact=" . $vbulletin->GPC['exact'] . "&date=" . $vbulletin->GPC['date'] . "&orderby=oldusername&direction=" . $vbulletin->GPC['direction'] . "&pp=" . $vbulletin->GPC['perpage'] . "&page=" . $vbulletin->GPC['pagenumber'] . "' title='" . $vbphrase['phpkd_chusrn_headings_order_by_oldusername_title'] . "'>" . $vbphrase['phpkd_chusrn_headings_order_by_oldusername'] . "</a>";
		$headings[] = "<a href='phpkd_chusrn.php?" . $vbulletin->session->vars['sessionurl'] . "do=view&action=" . $vbulletin->GPC['action'] . "&username=" . $vbulletin->GPC['username'] . "&usernametype=" . $vbulletin->GPC['usernametype'] . "&exact=" . $vbulletin->GPC['exact'] . "&date=" . $vbulletin->GPC['date'] . "&orderby=newusername&direction=" . $vbulletin->GPC['direction'] . "&pp=" . $vbulletin->GPC['perpage'] . "&page=" . $vbulletin->GPC['pagenumber'] . "' title='" . $vbphrase['phpkd_chusrn_headings_order_by_newusername_title'] . "'>" . $vbphrase['phpkd_chusrn_headings_order_by_newusername'] . "</a>";
		$headings[] = "<a href='phpkd_chusrn.php?" . $vbulletin->session->vars['sessionurl'] . "do=view&action=" . $vbulletin->GPC['action'] . "&username=" . $vbulletin->GPC['username'] . "&usernametype=" . $vbulletin->GPC['usernametype'] . "&exact=" . $vbulletin->GPC['exact'] . "&date=" . $vbulletin->GPC['date'] . "&orderby=action&direction=" . $vbulletin->GPC['direction'] . "&pp=" . $vbulletin->GPC['perpage'] . "&page=" . $vbulletin->GPC['pagenumber'] . "' title='" . $vbphrase['phpkd_chusrn_headings_order_by_action_title'] . "'>" . $vbphrase['phpkd_chusrn_headings_order_by_action'] . "</a>";
		$headings[] = "<a href='phpkd_chusrn.php?" . $vbulletin->session->vars['sessionurl'] . "do=view&action=" . $vbulletin->GPC['action'] . "&username=" . $vbulletin->GPC['username'] . "&usernametype=" . $vbulletin->GPC['usernametype'] . "&exact=" . $vbulletin->GPC['exact'] . "&date=" . $vbulletin->GPC['date'] . "&orderby=request_dateline&direction=" . $vbulletin->GPC['direction'] . "&pp=" . $vbulletin->GPC['perpage'] . "&page=" . $vbulletin->GPC['pagenumber'] . "' title='" . $vbphrase['phpkd_chusrn_headings_order_by_processedby_title'] . "'>" . $vbphrase['phpkd_chusrn_headings_order_by_processedby'] . "</a>";
		$headings[] = "<a href='phpkd_chusrn.php?" . $vbulletin->session->vars['sessionurl'] . "do=view&action=" . $vbulletin->GPC['action'] . "&username=" . $vbulletin->GPC['username'] . "&usernametype=" . $vbulletin->GPC['usernametype'] . "&exact=" . $vbulletin->GPC['exact'] . "&date=" . $vbulletin->GPC['date'] . "&orderby=action_dateline&direction=" . $vbulletin->GPC['direction'] . "&pp=" . $vbulletin->GPC['perpage'] . "&page=" . $vbulletin->GPC['pagenumber'] . "' title='" . $vbphrase['phpkd_chusrn_headings_order_by_request_dateline_title'] . "'>" . $vbphrase['phpkd_chusrn_headings_order_by_request_dateline'] . "</a>";
		$headings[] = "<a href='phpkd_chusrn.php?" . $vbulletin->session->vars['sessionurl'] . "do=view&action=" . $vbulletin->GPC['action'] . "&username=" . $vbulletin->GPC['username'] . "&usernametype=" . $vbulletin->GPC['usernametype'] . "&exact=" . $vbulletin->GPC['exact'] . "&date=" . $vbulletin->GPC['date'] . "&orderby=processedby&direction=" . $vbulletin->GPC['direction'] . "&pp=" . $vbulletin->GPC['perpage'] . "&page=" . $vbulletin->GPC['pagenumber'] . "' title='" . $vbphrase['phpkd_chusrn_headings_order_by_action_dateline_title'] . "'>" . $vbphrase['phpkd_chusrn_headings_order_by_action_dateline'] . "</a>";

		if ($vbulletin->GPC['action'] != 'approved' AND $vbulletin->GPC['action'] != 'auto_approved' AND $vbulletin->GPC['action'] != 'denied')
		{
			$headings[] = '<input type="button" value="' . $vbphrase['accept'] . '" onclick="js_check_all_option(this.form, 1);" class="button" title="' . $vbphrase['check_all'] . '" />';
			$headings[] = '<input type="button" value=" ' . $vbphrase['deny'] . ' " onclick="js_check_all_option(this.form, 0);" class="button" title="' . $vbphrase['check_all'] . '" />';
			$headings[] = '<input type="button" value="' . $vbphrase['ignore'] . '" onclick="js_check_all_option(this.form, -1);" class="button" title="' . $vbphrase['check_all'] . '" />';
		}

		print_cells_row($headings, 1);

		while ($log = $db->fetch_array($logs))
		{
			$cell = array();
			$cell[] = $log['id'];
			$cell[] = $log['userid'];
			$cell[] = iif($log['action'] != 'approved' AND $log['action'] != 'auto_approved', "<a href=\"user.php?" . $vbulletin->session->vars['sessionurl'] . "do=edit&u=$log[userid]\">$log[oldusername]</a>", $log['oldusername']);
			$cell[] = iif($log['action'] != 'waiting' AND $log['action'] != 'denied', "<a href=\"user.php?" . $vbulletin->session->vars['sessionurl'] . "do=edit&u=$log[userid]\">$log[newusername]</a>", $log['newusername']);

			switch ($log['action'])
			{
				case 'waiting':
					$action = $vbphrase['phpkd_chusrn_action_waiting'];
					break;
				case 'approved':
					$action = $vbphrase['phpkd_chusrn_action_approved'];
					break;
				case 'auto_approved':
					$action = $vbphrase['phpkd_chusrn_action_auto_approved'];
					break;
				case 'denied':
					$action = $vbphrase['phpkd_chusrn_action_denied'];
					break;
				default:
					$action = $vbphrase['phpkd_chusrn_action_waiting'];
			}

			$cell[] = "<span title=\"" . $vbphrase[$log['comment']] . "\">$action</span>";
			$log['processedby'] = fetch_userinfo($log['processedby']);
			$processedby = "<a href=\"user.php?" . $vbulletin->session->vars['sessionurl'] . "do=edit&u=" . $log['processedby']['userid'] . "\">" . $log['processedby']['musername'] . "</a>";
			$cell[] = iif(!empty($log['processedby']), "<a href=\"user.php?" . $vbulletin->session->vars['sessionurl'] . "do=edit&u=$log[processedby]\">$processedby</a>", $vbphrase['n_a']);
			$cell[] = '<span class="smallfont">' . vbdate($vbulletin->options['dateformat'], $log['request_dateline'], 1) . '</span>';
			$cell[] = iif(!empty($log['action_dateline']), "<span class=\"smallfont\">" . vbdate($vbulletin->options['dateformat'], $log['action_dateline'], 1) . "</span>", $vbphrase['n_a']);

			if ($vbulletin->GPC['action'] != 'approved' AND $vbulletin->GPC['action'] != 'auto_approved' AND $vbulletin->GPC['action'] != 'denied' AND $log['action'] != 'approved' AND $log['action'] != 'auto_approved' AND $log['action'] != 'denied')
			{
				$cell[] = '<label for="a' . $log['id'] . '" class="smallfont">' . $vbphrase['accept'] . '<input type="radio" name="request[' . $log['id'] . ']" value="1" id="a' . $log['id'] . '" tabindex="1" /></label>';
				$cell[] = '<label for="d' . $log['id'] . '" class="smallfont">' . $vbphrase['deny'] . '<input type="radio" name="request[' . $log['id'] . ']" value="0" id="d' . $log['id'] . '" tabindex="1" /></label>';
				$cell[] = '<label for="i' . $log['id'] . '" class="smallfont">' . $vbphrase['ignore'] . '<input type="radio" name="request[' . $log['id'] . ']" value="-1" id="i' . $log['id'] . '" tabindex="1" checked="checked" /></label>';
			}
			elseif ($vbulletin->GPC['action'] == 'all' AND ($log['action'] == 'approved' OR $log['action'] == 'auto_approved' OR $log['action'] == 'denied'))
			{
				$cell[] = "";
				$cell[] = "";
				$cell[] = "";
			}

			print_cells_row($cell);
		}

		print_submit_row($vbphrase['process'], $vbphrase['reset'], 11, "", "$firstpage $prevpage &nbsp; $nextpage $lastpage");
	}
	else
	{
		print_stop_message('phpkd_chusrn_no_log_entries_matched_your_query');
	}
}

print_cp_footer();

/*==================================================================================*\
|| ################################################################################ ||
|| # Downloaded: {Downloaded}
|| # CVS: $RCSfile$ - $Revision: 10000 $
|| ################################################################################ ||
\*==================================================================================*/
?>