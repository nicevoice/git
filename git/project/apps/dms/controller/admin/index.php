<?php
/**
 * 数据中心
 *
 * @aca 数据中心
 */
final class controller_admin_index extends dms_controller_abstract
{
	function __construct(&$app)
	{
		parent::__construct($app);
		if (!license('dms')) cmstop::licenseFailure();
	}
}