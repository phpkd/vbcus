<?php
/*==================================================================================*\
|| ################################################################################ ||
|| # Product Name: vB Username Change Manager               Version: 1.0.0 Beta.1 # ||
|| # Licence Number: {LicenceNumber}
|| # ---------------------------------------------------------------------------- # ||
|| # 																			  # ||
|| #          Copyright �2005-2008 PHP KingDom, Ltd. All Rights Reserved.         # ||
|| #       This file may not be redistributed in whole or significant part.       # ||
|| # 																			  # ||
|| # ------------- vB Username Change Manager IS NOT FREE SOFTWARE -------------- # ||
|| #           http://www.phpkd.org | http://www.phpkd.org/license.html           # ||
|| ################################################################################ ||
\*==================================================================================*/

class vBulletinHook_phpkd_chusrn extends vBulletinHook
{
	var $last_called = '';

	function vBulletinHook_phpkd_chusrn(&$pluginlist, &$hookusage)
	{
		$this->pluginlist =& $pluginlist;
		$this->hookusage =& $hookusage;
	}

	function &fetch_hook_object($hookname)
	{
		$this->last_called = $hookname;
		return parent::fetch_hook_object($hookname);
	}
}

/*==================================================================================*\
|| ################################################################################ ||
|| # Downloaded: {Downloaded}
|| # CVS: $RCSfile$ - $Revision: 10000 $
|| ################################################################################ ||
\*==================================================================================*/
?>