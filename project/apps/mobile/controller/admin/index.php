<?php
/**
 * 手机版
 *
 * @aca 手机版
 */
final class controller_admin_index extends mobile_controller_abstract
{
	function __construct(& $app)
	{
		parent::__construct($app);
        if (!license('mobile')) cmstop::licenseFailure();
	}
}