<?php
/**
 * Functions
 * 
 * @author Maxime Raoust
 * @package Manialib
 */

/**
 * Autoloading function
 */
function __autoload($className)
{
	if (autoload_recursive($className, APP_FRAMEWORK_LIBRARIES_PATH))
	{
		return true;
	}
	elseif(autoload_recursive($className, APP_LIBRARIES_PATH))
	{
		return true;
	}
	elseif(autoload_recursive($className, APP_FRAMEWORK_GUI_TOOLKIT_PATH))
	{
		return true;
	}
	return false;
}

/**
 * Recursive autoloading
 */
function autoload_recursive($className, $path)
{
	if(file_exists($rpath = $path . "/" . $className . ".class.php"))
	{
		require_once($rpath);
		return true;
	}
	$return = false;
	if ($handle = opendir($path))
	{
		while (false !== ($file = readdir($handle)))
		{
			if (strcasecmp(substr($file, 0, 1), "."))
			{
				if(is_dir($path . $file))
				{
					if(autoload_recursive($className, $path.$file.'/'))
					{
						return true;
					}
				}
			}
		}
		closedir($handle);
	}
	return $return;
} 

/**
 * Prints a line
 * 
 * @param string
 */
function println($string)
{
	echo $string."\n";
}

/**
 * Writes a message in the debug log file
 */
function debuglog($msg, $addDate = true, $log = APP_DEBUG_LOG)
{
	if ($addDate)
	{
		$msg = date('d/m/y H:i:s') . " $msg\n";
	}
	file_put_contents($log, $msg, FILE_APPEND);
}

/**
 * "Safely" get an element from an array.
 */
function array_get($array, $key, $default = null)
{
	if (isset ($array[$key]))
	{
		return $array[$key];
	}
	return $default;
}

/**
 * Protect TM styles to put a formatted text in the middle of a sentence
 */
function protectStyles($string)
{
	return "\$<$string\$>";
}

/**
 * Unprotect the TM styles
 */
function unprotectStyles($string)
{
	return str_replace(array (
		'$<',
		'$>'
	), "", $string);
}

/**
 * Remove the TM font styles (wide, bold, underline)
 */
function stripWideFonts($str)
{
	return str_replace(array (
		'$w',
		'$o',
		'$s'
	), "", $str);
}

/**
 * Remove the TM links
 */
function stripLinks($str)
{
	return preg_replace('/\\$[hlp](.*?)(?:\\[.*?\\](.*?))*(?:\\$[hlp]|$)/ixu', '$1$2', $str);
}

/**
 * Include all the files of the given directory
 */
function require_once_dir($directory_path)
{
	if ($handle = opendir($directory_path))
	{
		while (false !== ($file = readdir($handle)))
		{
			if (strcasecmp(substr($file, 0, 1), "."))
			{
				require_once ($directory_path . $file);
			}
		}
		closedir($handle);
	}
}

/**
 * Safe division. Returns 0 if the denominator is 0
 */
function safe_div($numerator, $denominator)
{
	if (!$denominator)
	{
		return 0;
	}
	return $numerator / $denominator;
}

/**
 * Format a short english date from a timestamp
 */
function formatDate($timestamp)
{
	return date('l jS \of F Y', $timestamp);
}

/**
 * Format a long english date+time from a timestamp
 */
function formatLongDate($timestamp)
{
	return date('l jS \of F Y @ h:i A ', $timestamp).TIMEZONE_NAME;
}

?>