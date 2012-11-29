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


if (!defined('VB_AREA'))
{
	exit;
}

$hookobj =& vBulletinHook::init();
require_once(DIR . '/includes/phpkd/phpkd_chusrn_functions.php');

switch (strval($hookobj->last_called))
{
	case 'cache_templates':
		{
			if ($vbulletin->options['phpkd_chusrn_active'] AND (THIS_SCRIPT == 'profile' OR THIS_SCRIPT == 'member'))
			{
				if (THIS_SCRIPT == 'profile')
				{
					if ($permissions['changeusernameperms'] & $vbulletin->bf_ugp_changeusernameperms['canchusername'])
					{
						$globaltemplates[] = 'phpkd_chusrn_usercp_nav_username';
						$globaltemplates[] = 'phpkd_chusrn_modifyusername';
						$globaltemplates[] = 'phpkd_chusrn_history_bits';
						$globaltemplates[] = 'phpkd_chusrn_history';
					}
				}
				elseif (THIS_SCRIPT == 'member')
				{
					if (($permissions['changeusernameperms'] & $vbulletin->bf_ugp_changeusernameperms['canviewchusernamelog']) OR ($permissions['changeusernameperms'] & $vbulletin->bf_ugp_changeusernameperms['canviewotherchusernamelog']))
					{
						$globaltemplates[] = 'phpkd_chusrn_history_bits';
						$globaltemplates[] = 'phpkd_chusrn_history';
					}
				}
			}
		}
		break;
	case 'admin_usergroup_edit':
		{
			print_table_header($vbphrase['phpkd_chusrn_perms_1']);
			print_select_row($vbphrase['phpkd_chusrn_move_type_title'] . ' <dfn>' . $vbphrase['phpkd_chusrn_move_type_desc'] . '</dfn>', 'usergroup[chusernamemovetype]', array(0 => $vbphrase['phpkd_chusrn_none'], 1 => $vbphrase['phpkd_chusrn_primary_usergroup'], 2 => $vbphrase['phpkd_chusrn_additional_usergroups']), $usergroup['chusernamemovetype']);
			print_chooser_row($vbphrase['phpkd_chusrn_move_to_title'] . " <dfn>$vbphrase[phpkd_chusrn_move_to_desc]</dfn>", 'usergroup[chusernamemoveugb]', 'usergroup', $usergroup['chusernamemoveugb'], '&nbsp;');
			print_table_break();
			print_column_style_code(array('width: 70%', 'width: 30%'));
		}
		break;
	case 'usercp_nav_start':
		{
			if ($vbulletin->options['phpkd_chusrn_active'] AND ($vbulletin->userinfo['permissions']['changeusernameperms'] & $vbulletin->bf_ugp_changeusernameperms['canchusername']))
			{
				$cells = array_merge($cells, array('username'));
			}
		}
		break;
	case 'usercp_nav_complete':
		{
			if ($vbulletin->options['phpkd_chusrn_active'] AND ($vbulletin->userinfo['permissions']['changeusernameperms'] & $vbulletin->bf_ugp_changeusernameperms['canchusername']))
			{
				$vbulletin->templatecache['USERCP_SHELL'] = str_replace('$vbphrase[edit_email_and_password]</a></td>', fetch_template('phpkd_chusrn_usercp_nav_username'), $vbulletin->templatecache['USERCP_SHELL']);
			}
		}
		break;
	case 'member_complete':
		{
			if ($vbulletin->options['phpkd_chusrn_active'])
			{
				if (($permissions['changeusernameperms'] & $vbulletin->bf_ugp_changeusernameperms['canviewchusernamelog'] AND ($vbulletin->userinfo['userid'] = $vbulletin->GPC['userid'] OR $vbulletin->userinfo['username'] = $vbulletin->GPC['username'])) OR ($permissions['changeusernameperms'] & $vbulletin->bf_ugp_changeusernameperms['canviewotherchusernamelog'] AND ($vbulletin->userinfo['userid'] != $vbulletin->GPC['userid'] OR $vbulletin->userinfo['username'] != $vbulletin->GPC['username'])))
				{
					if ($vbulletin->GPC['username'] != '' AND !$vbulletin->GPC['userid'])
					{
						$phpkd_change_username_where = "username = '" . $db->escape_string($vbulletin->GPC['username']) . "'";
					}
					else
					{
						$phpkd_change_username_where = "userid = '" . $vbulletin->GPC['userid'] . "'";
					}

					// Fetch username history
					$phpkd_username_history = $db->query_read("SELECT * FROM " . TABLE_PREFIX . "phpkd_usernamehistory WHERE $phpkd_change_username_where ORDER BY request_dateline ASC");

					if ($db->num_rows($phpkd_chusrn_history))
					{
						while ($phpkd_chusrn_history_bit = $db->fetch_array($phpkd_chusrn_history))
						{
							$phpkd_chusrn_history_bit['processedby'] = fetch_userinfo($phpkd_chusrn_history_bit['processedby']);
							$processedby = construct_phrase($vbphrase['phpkd_chusrn_processed_by_bit'], $vbulletin->options['bburl'], $phpkd_chusrn_history_bit['processedby']['userid'], $phpkd_chusrn_history_bit['processedby']['musername']);
							$phpkd_chusrn_history_bit['request_dateline'] = vbdate($vbulletin->options['dateformat'], $phpkd_chusrn_history_bit['request_dateline'], 1);
							$phpkd_chusrn_history_bit['action_dateline'] = vbdate($vbulletin->options['dateformat'], $phpkd_chusrn_history_bit['action_dateline'], 1);
							$phpkd_chusrn_history_bit['comment'] = $vbphrase[$phpkd_chusrn_history_bit['comment']];
							$phpkd_chusrn_history_bit['action'] = $vbphrase['phpkd_chusrn_action_' . $phpkd_chusrn_history_bit['action']];
							eval('$phpkd_chusrn_history_bits .= "' . fetch_template('phpkd_chusrn_history_bits') . '";');
						}

						eval('$phpkd_chusrn_history .= "' . fetch_template('phpkd_chusrn_history') . '";');
						$template_hook['memberinfo_pos2'] .= $phpkd_chusrn_history;
					}
				}
			}
		}
		break;
	case 'profile_start':
		{
			if ($_REQUEST['do'] == 'editusername' AND $vbulletin->options['phpkd_chusrn_active'])
			{
				if (!($permissions['changeusernameperms'] & $vbulletin->bf_ugp_changeusernameperms['canchusername']))
				{
					eval(standard_error(fetch_error('canotchusername')));
				}

				if ($permissions['canchusernameminposts'] > 0 AND $vbulletin->userinfo['posts'] < $permissions['canchusernameminposts'])
				{
					eval(standard_error(fetch_error('canotchusername_posts', $vbulletin->userinfo['posts'], $permissions['canchusernameminposts'])));
				}

				if ($permissions['canchusernameminrep'] > 0 AND $vbulletin->userinfo['reputation'] < $permissions['canchusernameminrep'])
				{
					eval(standard_error(fetch_error('canotchusername_reputation', $vbulletin->userinfo['reputation'], $permissions['canchusernameminrep'])));
				}

				$vbulletin->userinfo['regdays'] = floor((TIMENOW - $vbulletin->userinfo['joindate']) / 86400);
				if ($permissions['canchusernameregdays'] > 0 AND $vbulletin->userinfo['regdays'] < $permissions['canchusernameregdays'])
				{
					eval(standard_error(fetch_error('canotchusername_regdays', $vbulletin->userinfo['regdays'], $permissions['canchusernameregdays'])));
				}

				// Fetch username history
				$phpkd_chusrn_history_dateline = $db->query_first("SELECT action_dateline FROM " . TABLE_PREFIX . "phpkd_usernamehistory WHERE userid = '" . $vbulletin->userinfo['userid'] . "' ORDER BY action_dateline DESC");
				if ($phpkd_chusrn_history_dateline['action_dateline'])
				{
					$vbulletin->userinfo['simlperiod'] = floor((TIMENOW - $phpkd_chusrn_history_dateline['action_dateline']) / 86400);;
					if ($permissions['canchusernamesimlperiod'] > 0 AND $vbulletin->userinfo['simlperiod'] < $permissions['canchusernamesimlperiod'])
					{
						eval(standard_error(fetch_error('canotchusername_simlperiod', $vbulletin->userinfo['simlperiod'], $permissions['canchusernamesimlperiod'])));
					}
				}

				($hook = vBulletinHook::fetch_hook('profile_editusername_start')) ? eval($hook) : false;

				// draw cp nav bar
				construct_usercp_nav('username');

				// check for username history
				if (!($permissions['changeusernameperms'] & $vbulletin->bf_ugp_changeusernameperms['canviewchusernamelog']) OR !($permissions['genericoptions'] & $vbulletin->bf_ugp_genericoptions['isnotbannedgroup']))
				{
					$show['canviewchusernamelog'] = false;
				}
				else
				{
					// Fetch username history
					$phpkd_chusrn_history = $db->query_read("SELECT * FROM " . TABLE_PREFIX . "phpkd_usernamehistory WHERE userid = '" . $vbulletin->userinfo['userid'] . "' ORDER BY request_dateline ASC");

					if (!$db->num_rows($phpkd_chusrn_history))
					{
						$show['canviewchusernamelog'] = false;
					}
					else
					{
						$show['canviewchusernamelog'] = true;
						while ($phpkd_chusrn_history_bit = $db->fetch_array($phpkd_chusrn_history))
						{
							$phpkd_chusrn_history_bit['processedby'] = fetch_userinfo($phpkd_chusrn_history_bit['processedby']);
							$processedby = construct_phrase($vbphrase['phpkd_chusrn_processed_by_bit'], $vbulletin->options['bburl'], $phpkd_chusrn_history_bit['processedby']['userid'], $phpkd_chusrn_history_bit['processedby']['musername']);
							$phpkd_chusrn_history_bit['request_dateline'] = vbdate($vbulletin->options['dateformat'], $phpkd_chusrn_history_bit['request_dateline'], 1);
							$phpkd_chusrn_history_bit['action_dateline'] = vbdate($vbulletin->options['dateformat'], $phpkd_chusrn_history_bit['action_dateline'], 1);
							$phpkd_chusrn_history_bit['comment'] = $vbphrase[$phpkd_chusrn_history_bit['comment']];
							$phpkd_chusrn_history_bit['action'] = $vbphrase['phpkd_chusrn_action_' . $phpkd_chusrn_history_bit['action']];
							eval('$phpkd_chusrn_history_bits .= "' . fetch_template('phpkd_chusrn_history_bits') . '";');
						}

						eval('$phpkd_chusrn_history .= "' . fetch_template('phpkd_chusrn_history') . '";');
					}
				}

				$navbits[''] = $vbphrase['phpkd_chusrn_edit_username'];
				$templatename = 'phpkd_chusrn_modifyusername';
			}


			// ############################### start update password ###############################
			if ($_POST['do'] == 'updateusername')
			{
				$vbulletin->input->clean_array_gpc('p', array(
					'currentpassword'        => TYPE_STR,
					'currentpassword_md5'    => TYPE_STR,
					'username'               => TYPE_STR,
					'usernameconfirm'        => TYPE_STR
				));

				// fix extra whitespace and invisible ascii stuff
				$username = trim(preg_replace('#\s+#si', ' ', strip_blank_ascii($vbulletin->GPC['username'], ' ')));

				$username_raw = $username;

				$username = preg_replace(
					'/&#([0-9]+);/ie',
					"convert_unicode_char_to_charset('\\1', \$stylevar['charset'])",
					$username
				);

				// instanciate the data manager class
				$userdata =& datamanager_init('user', $vbulletin, ERRTYPE_STANDARD);
				$userdata->set_existing($vbulletin->userinfo);

				($hook = vBulletinHook::fetch_hook('profile_updateusername_start')) ? eval($hook) : false;

				// validate old password
				if ($userdata->hash_password($userdata->verify_md5($vbulletin->GPC['currentpassword_md5']) ? $vbulletin->GPC['currentpassword_md5'] : $vbulletin->GPC['currentpassword'], $vbulletin->userinfo['salt']) != $vbulletin->userinfo['password'])
				{
					eval(standard_error(fetch_error('badpassword', $vbulletin->options['bburl'], $vbulletin->session->vars['sessionurl'])));
				}

				// update username only if user is not banned and username is changed
				if ($permissions['genericoptions'] & $vbulletin->bf_ugp_genericoptions['isnotbannedgroup'] AND $permissions['changeusernameperms'] & $vbulletin->bf_ugp_changeusernameperms['canchusername'])
				{
					// check that new usernames match
					if ($vbulletin->GPC['username'] != $vbulletin->GPC['usernameconfirm'])
					{
						eval(standard_error(fetch_error('usernamemismatch')));
					}
					else if (strcasecmp($vbulletin->GPC['username'], $vbulletin->userinfo['username']) == '0' OR strcasecmp($vbulletin->GPC['usernameconfirm'], $vbulletin->userinfo['username']) == '0')
					{
						eval(standard_error(fetch_error('usingsameusername')));
					}
					// check if username was previously used
					else if (!$vbulletin->options['phpkd_chusrn_allow_old_usernames'] AND $db->query_first("
						SELECT oldusername FROM " . TABLE_PREFIX . "phpkd_usernamehistory
						WHERE oldusername = '" . $db->escape_string(htmlspecialchars_uni($username)) . "' OR oldusername = '" . $db->escape_string(htmlspecialchars_uni($username_raw)) . "'
					"))
					{
						if (!$vbulletin->options['phpkd_chusrn_allow_own_old_usernames'] AND $db->query_first("
						SELECT oldusername FROM " . TABLE_PREFIX . "phpkd_usernamehistory
						WHERE oldusername = '" . $db->escape_string(htmlspecialchars_uni($username)) . "' OR oldusername = '" . $db->escape_string(htmlspecialchars_uni($username_raw)) . "'
						AND userid = '" . $vbulletin->userinfo['userid'] . "'"))
						{
							eval(standard_error(fetch_error('usernamepreviousused')));
						}
					}
					// check if username was previously denied
					else if (!$vbulletin->options['phpkd_chusrn_allow_denied_to_others'] AND $db->query_first("
						SELECT newusername, action FROM " . TABLE_PREFIX . "phpkd_usernamehistory
						WHERE action = 'denied' AND (newusername = '" . $db->escape_string(htmlspecialchars_uni($username)) . "' OR newusername = '" . $db->escape_string(htmlspecialchars_uni($username_raw)) . "')
					"))
					{
						eval(standard_error(fetch_error('usernamepreviousdenied')));
					}
					// check if username was previously requested & still in the waiting list
					else if ($db->query_first("
						SELECT newusername, action FROM " . TABLE_PREFIX . "phpkd_usernamehistory
						WHERE action = 'waiting' AND (newusername = '" . $db->escape_string(htmlspecialchars_uni($username)) . "' OR newusername = '" . $db->escape_string(htmlspecialchars_uni($username_raw)) . "')
					"))
					{
						eval(standard_error(fetch_error('usernameotherpreviousrequested')));
					}
					// check if username was previously requested & still in the waiting list
					else if ($db->query_first("
						SELECT userid, action FROM " . TABLE_PREFIX . "phpkd_usernamehistory
						WHERE action = 'waiting' AND userid = '" . $vbulletin->userinfo['userid'] . "'
					"))
					{
						eval(standard_error(fetch_error('usernameownpreviousrequested')));
					}
					else if (!$userdata->verify_username($vbulletin->GPC['username']))
					{
						$userdata->error();
					}

					if ($permissions['changeusernameperms'] & $vbulletin->bf_ugp_changeusernameperms['chusernameautoapp'])
					{
						// set the username field to be updated
						$userdata->set('username', $vbulletin->GPC['username']);

						$action = "changed";

						// generate an activation ID if required
						if ($permissions['changeusernameperms'] & $vbulletin->bf_ugp_changeusernameperms['chusernameforcereactivate'] AND $vbulletin->options['verifyemail'] AND !can_moderate())
						{
							$userdata->set('usergroupid', 3);
							$userdata->set_info('override_usergroupid', true);

							$subaction = 'forceactivate';

							// wait lets check if we have an entry first!
							$activation_exists = $db->query_first("SELECT * FROM " . TABLE_PREFIX . "useractivation WHERE userid = " . $vbulletin->userinfo['userid'] . " AND type = 0");

							if (!empty($activation_exists['usergroupid']) AND $vbulletin->userinfo['usergroupid'] == 3)
							{
								$usergroupid = $activation_exists['usergroupid'];
							}
							else
							{
								$usergroupid = $vbulletin->userinfo['usergroupid'];
							}
							$activateid = build_user_activation_id($vbulletin->userinfo['userid'], $usergroupid, 0, 1);

							$username = unhtmlspecialchars($vbulletin->userinfo['username']);
							$userid = $vbulletin->userinfo['userid'];

							eval(fetch_email_phrases('activateaccount_usernamechange'));
							vbmail($vbulletin->userinfo['email'], $subject, $message, true);
						}
						else
						{
							$subaction = 'passactivation';
							if ($vbulletin->userinfo['permissions']['chusernamemovetype'] AND $vbulletin->userinfo['permissions']['chusernamemoveugb'] >= 1)
							{
								if ($vbulletin->userinfo['permissions']['chusernamemovetype'] == 1)
								{
									// set usergroupid
									$userdata->set('usergroupid', $vbulletin->userinfo['permissions']['chusernamemoveugb']);
								}
								elseif ($vbulletin->userinfo['permissions']['chusernamemovetype'] == 2)
								{
									$vbulletin->db->query_write("
										UPDATE " . TABLE_PREFIX . "user
										SET membergroupids = IF(membergroupids= '', '" . $vbulletin->userinfo['permissions']['chusernamemoveugb'] . "', CONCAT(membergroupids, '," . $vbulletin->userinfo['permissions']['chusernamemoveugb'] . "'))
										WHERE userid = " . $vbulletin->userinfo['userid']
									);
								}
							}
						}

						// insert the request
						/*insert query*/
						$db->query_write("
							INSERT INTO " . TABLE_PREFIX . "phpkd_usernamehistory
								(userid,oldusergroupid,newusergroupid,oldusername,newusername,action,request_dateline,action_dateline,processedby,comment)
							VALUES
								('" . $vbulletin->userinfo['userid'] . "', '" . $vbulletin->userinfo['usergroupid'] . "', '" . $vbulletin->userinfo['permissions']['chusernamemoveugb'] . "', '" . $vbulletin->userinfo['username'] . "', '" . $db->escape_string($vbulletin->GPC['username']) . "', 'auto_approved', '" . TIMENOW . "', '" . TIMENOW . "', '" . $vbulletin->userinfo['userid'] . "', 'phpkd_chusrn_subaction_" . $subaction . "')
						");
					}
					else
					{
						$action = "requested";
						$subaction = 'waitingstaff';

						// insert the request
						/*insert query*/
						$db->query_write("
							INSERT INTO " . TABLE_PREFIX . "phpkd_usernamehistory
								(userid,oldusergroupid,newusergroupid,oldusername,newusername,action,request_dateline,action_dateline,processedby,comment)
							VALUES
								('" . $vbulletin->userinfo['userid'] . "', '" . $vbulletin->userinfo['usergroupid'] . "', '" . $vbulletin->userinfo['permissions']['chusernamemoveugb'] . "', '" . $vbulletin->userinfo['username'] . "', '" . $db->escape_string($vbulletin->GPC['username']) . "', 'waiting', '" . TIMENOW . "', '', '', 'phpkd_chusrn_subaction_" . $subaction . "')
						");
					}


					// #########################################
					// ####### ## Staff Notifications ## #######
					// #########################################
					if ($vbulletin->options['phpkd_chusrn_staff_notifications'] == 'pm' OR $vbulletin->options['phpkd_chusrn_staff_notifications'] == 'both')
					{
						// Sending PM Notifications to staff about the username changes/requests to be reviewed & processed
						$searchfor = array("{oldusername}", "{action}", "{newusername}", "{date}", "{profile}", "{comment}", "{bbtitle}", "{bburl}");
						$replacewith   = array($vbulletin->userinfo['username'], $vbphrase['phpkd_chusrn_request_notification_action_' . $action], $vbulletin->GPC['username'], vbdate($vbulletin->options['dateformat']), $vbulletin->options['bburl'] . "/member.php?u=" . $vbulletin->userinfo['userid'], $vbphrase['phpkd_chusrn_subaction_nots'] . $vbphrase['phpkd_chusrn_subaction_' . $subaction], $vbulletin->options['bbtitle'], $vbulletin->options['bburl']); 
						$pm['subject'] = @str_replace($searchfor, $replacewith, $vbphrase['phpkd_chusrn_request_notification_subject']); 
						$pm['message'] = @str_replace($searchfor, $replacewith, $vbphrase['phpkd_chusrn_request_notification_body']); 

						$fromuser = fetch_userinfo($vbulletin->options['phpkd_chusrn_request_notification_sender']);
						$pm['fromuserid'] = $fromuser['userid'];
						$pm['fromusername'] = $fromuser['username'];

						// create the DM to do error checking and insert the new PM
						$pmdm_staff =& datamanager_init('PM', $vbulletin, ERRTYPE_ARRAY);

						$pmdm_staff->set_info('savecopy',      $vbulletin->options['phpkd_chusrn_pm_savecopy']);
						$pmdm_staff->set_info('receipt',       $vbulletin->options['phpkd_chusrn_pm_receipt']);
						$pmdm_staff->set_info('cantrackpm',    $cantrackpm);
						if ($vbulletin->options['phpkd_chusrn_pm_overridequota'])
						{
							$pmdm_staff->overridequota = true;
						}
						$pmdm_staff->set('fromuserid', $pm['fromuserid']);
						$pmdm_staff->set('fromusername', $pm['fromusername']);
						$pmdm_staff->setr('title', $pm['subject']);
						$pmdm_staff->set_recipients($vbulletin->options['phpkd_chusrn_staff_recipients'], $permissions, 'cc');
						$pmdm_staff->setr('message', $pm['message']);
						$pmdm_staff->setr('iconid', $vbulletin->options['phpkd_chusrn_pm_iconid']);
						$pmdm_staff->set('dateline', TIMENOW);
						$pmdm_staff->setr('showsignature', $vbulletin->options['phpkd_chusrn_pm_signature']);
						$pmdm_staff->set('allowsmilie', $vbulletin->options['phpkd_chusrn_pm_disablesmilies'] ? 0 : 1);
						$pmdm_staff->save();
					}


					// E-Mailing
					if ($vbulletin->options['phpkd_chusrn_staff_notifications'] == 'email' OR $vbulletin->options['phpkd_chusrn_staff_notifications'] == 'both')
					{
						$recipients_staff = explode(';', $vbulletin->options['phpkd_chusrn_staff_recipients']);
						foreach ($recipients_staff AS $staffs)
						{
							$staff = fetch_userinfo($staffs);
							eval(fetch_email_phrases('phpkd_chusrn_request_' . $action));
							vbmail($staff['email'], $subject, $message, true);
						}
					}


					// #########################################
					// ####### ## Users Notifications ## #######
					// #########################################
					if ($vbulletin->options['phpkd_chusrn_users_notifications'] == 'pm' OR $vbulletin->options['phpkd_chusrn_users_notifications'] == 'both')
					{
						// Sending PM Notifications to staff about the username changes/requests to be reviewed & processed
						$searchfor = array("{oldusername}", "{newusername}", "{date}", "{comment}", "{contactuslink}", "{bbtitle}", "{bburl}");
						$replacewith   = array($vbulletin->userinfo['username'], $vbulletin->GPC['username'], vbdate($vbulletin->options['dateformat']), $vbphrase['phpkd_chusrn_subaction_nots'] . $vbphrase['phpkd_chusrn_subaction_' . $subaction], $vbulletin->options['bburl'] . '/' . $vbulletin->options['contactuslink'], $vbulletin->options['bbtitle'], $vbulletin->options['bburl']); 
						$pm['subject'] = @str_replace($searchfor, $replacewith, $vbphrase['phpkd_chusrn_report_notification_' . $subaction . '_subject']); 
						$pm['message'] = @str_replace($searchfor, $replacewith, $vbphrase['phpkd_chusrn_report_notification_' . $subaction . '_body']); 

						$fromuser = fetch_userinfo($vbulletin->options['phpkd_chusrn_report_notification_sender']);
						$pm['fromuserid'] = $fromuser['userid'];
						$pm['fromusername'] = $fromuser['username'];

						// create the DM to do error checking and insert the new PM
						$pmdm_users =& datamanager_init('PM', $vbulletin, ERRTYPE_ARRAY);

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
						$pmdm_users->set_recipients($vbulletin->userinfo['username'], $permissions, 'cc');
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
						eval(fetch_email_phrases('phpkd_chusrn_report_' . $subaction));
						vbmail($vbulletin->userinfo['email'], $subject, $message, true);
					}
				}
				else
				{
					$userdata->verify_username($vbulletin->userinfo['username']);
				}

				($hook = vBulletinHook::fetch_hook('profile_updateusername_complete')) ? eval($hook) : false;

				// save the data
				$userdata->save();

				$vbulletin->url = 'usercp.php' . $vbulletin->session->vars['sessionurl_q'];
				switch ($subaction)
				{
					case 'forceactivate':
						eval(print_standard_redirect('redirect_updateusernamethanks_forceactivate', true, true));			
						break;
					case 'passactivation':
						eval(print_standard_redirect('redirect_updateusernamethanks_passactivation'));			
						break;
					case 'waitingstaff':
						eval(print_standard_redirect('redirect_updateusernamethanks_waitingstaff', true, true));			
						break;
					default:
						eval(print_standard_redirect('redirect_updatethanks'));
				}
			}
			else if ($_GET['do'] == 'updateusername')
			{
				// add consistency with previous behavior
				exec_header_redirect('profile.php?do=editusername');
			}
		}
		break;
	default:
		{
			$hookobj = new vBulletinHook_phpkd_chusrn($hookobj->pluginlist, $hookobj->hookusage);
		}
		break;
}

/*==================================================================================*\
|| ################################################################################ ||
|| # Downloaded: {Downloaded}
|| # CVS: $RCSfile$ - $Revision: 10000 $
|| ################################################################################ ||
\*==================================================================================*/
?>