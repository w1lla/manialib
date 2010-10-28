<?php
/**
 * Error handling features
 * 
 * @author Maxime Raoust
 * @copyright 2009-2010 NADEO 
 * @package ManiaLib
 * @subpackage ErrorHandling
 */

/**
 * Error handling features
 * @package ManiaLib
 * @subpackage ErrorHandling
 * @ignore
 */
abstract class ErrorHandling
{
	/**
	 * Exception handler
	 * 
	 * Converts standard PHP errors into FrameworkErrorException
	 */
	static function exceptionErrorHandler($errno, $errstr, $errfile, $errline) 
	{
    	switch($errno)
    	{
    		case E_USER_WARNING:
    			new FrameworkException($errstr);
    			break;
    		
    		default:
    			// Hack: sometimes classes can't be loaded throuh __autoload
    			require_once(APP_FRAMEWORK_EXCEPTIONS_PATH.'FrameworkErrorException.class.php');
    			throw new FrameworkErrorException(
    				$errstr, 0, $errno, $errfile, $errline);
    	}
	}
	
	/**
	 * Uncaught exceptions handler
	 */
	static function exceptionHandler($exception)
	{
		FrameworkException::handle($exception);
	}
}

?>