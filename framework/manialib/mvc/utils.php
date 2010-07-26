<?php 
/**
 * MVC framework utilities
 * @author Maxime Raoust
 */

function autoload_mvc_framework($className)
{
	if(file_exists($path = APP_MVC_FILTERS_PATH.$className.'.class.php'))
	{
		require_once($path);
		return true;
	}
	if(file_exists($path = APP_MVC_FRAMEWORK_FILTERS_PATH.$className.'.class.php'))
	{
		require_once($path);
		return true;
	}
	if(file_exists($path = APP_MVC_FRAMEWORK_LIBRARIES_PATH.$className.'.class.php'))
	{
		require_once($path);
		return true;
	}
	if(file_exists($path = APP_MVC_FRAMEWORK_EXCEPTIONS_PATH.$className.'.class.php'))
	{
		require_once($path);
		return true;
	}
	return false;
}

?>