<?php
/**
 * @author Maxime Raoust
 * @copyright 2009-2010 NADEO 
 * @package ManiaLib
 */


/**
 * GUI Toolkit
 * Manialink GUI Toolkit main class
 * @package ManiaLib
 * @subpackage GUIToolkit
 */
abstract class ManiaLib_Gui_Manialink
{
	/**#@+
	 * @ignore
	 */
	public static $domDocument;
	public static $parentNodes;
	public static $parentLayouts;
	public static $linksEnabled = true;
	
	public static $dicosURL;
	public static $imagesURL;
	public static $mediaURL;
	
	protected static $dicos = array();
	/**#@-*/
	
	/**
	 * Loads the Manialink GUI toolkit. This should be called before doing
	 * anything with the toolkit.
	 * @param bool Whether you want to create the root "<manialink>" element in the XML
	 * @param int The timeout value in seconds. Use 0 if you have dynamic pages to avoid caching
	 */
	final public static function load($createManialinkElement = true, $timeoutValue=0)
	{
		// Load the config
		$config = ManiaLib_Config_Loader::$config;
		if($config->application)
		{
			self::$dicosURL = $config->application->dicosURL;
			self::$imagesURL = $config->application->imagesURL;
			self::$mediaURL = $config->application->mediaURL;
		}
		
		// Load the XML object
		self::$domDocument = new DOMDocument('1.0', 'utf-8');
		self::$parentNodes = array();
		self::$parentLayouts = array();

		if($createManialinkElement)
		{
			$manialink = self::$domDocument->createElement('manialink');
			self::$domDocument->appendChild($manialink);
			self::$parentNodes[] = $manialink;
				
			$timeout = self::$domDocument->createElement('timeout');
			$manialink->appendChild($timeout);
			$timeout->nodeValue = $timeoutValue;
		}
		else
		{
			$frame = self::$domDocument->createElement('frame');
			self::$domDocument->appendChild($frame);
			self::$parentNodes[] = $frame;
		}
	}

	/**
	 * Renders the Manialink
	 * @param boolean Wehther you want to return the XML instead of printing it
	 */
	final public static function render($return = false)
	{
		if(self::$dicos)
		{
			array_map(array('Manialink', 'includeManialink'), self::$dicos);
		}
		if($return)
		{
			return self::$domDocument->saveXML();
		}
		else
		{
			header('Content-Type: text/xml; charset=utf-8');
			echo self::$domDocument->saveXML();
		}
	}

	/**
	 * Creates a new Manialink frame, with an optionnal associated layout
	 *
	 * @param float X position
	 * @param float Y position
	 * @param float Z position
	 * @param float Scale (default is null or 1)
	 * @param ManiaLib_Gui_Layouts_AbstractLayout The optionnal layout associated with the frame. If
	 * you pass a layout object, all the items inside the frame will be
	 * positionned using constraints defined by the layout
	 */
	final public static function beginFrame($x=0, $y=0, $z=0, $scale=null, ManiaLib_Gui_Layouts_AbstractLayout $layout=null)
	{
		// Update parent layout
		$parentLayout = end(self::$parentLayouts);
		if($parentLayout instanceof ManiaLib_Gui_Layouts_AbstractLayout)
		{
			// If we have a current layout, we have a container size to deal with
			if($layout instanceof ManiaLib_Gui_Layouts_AbstractLayout)
			{
				$ui = new ManiaLib_Gui_Elements_Spacer($layout->getSizeX(), $layout->getSizeY());
				$ui->setPosition($x, $y, $z);

				$parentLayout->preFilter($ui);
				$x += $parentLayout->xIndex;
				$y += $parentLayout->yIndex;
				$z += $parentLayout->zIndex;
				$parentLayout->postFilter($ui);
			}
		}

		// Create DOM element
		$frame = self::$domDocument->createElement('frame');
		if($x || $y || $z)
		{
			$frame->setAttribute('posn', $x.' '.$y.' '.$z);
		}
		end(self::$parentNodes)->appendChild($frame);
		if($scale)
		{
			$frame->setAttribute('scale', $scale);
		}

		// Update stacks
		self::$parentNodes[] = $frame;
		self::$parentLayouts[] = $layout;
	}

	/**
	 * Closes the current Manialink frame
	 */
	final public static function endFrame()
	{
		if(!end(self::$parentNodes)->hasChildNodes())
		{
			end(self::$parentNodes)->nodeValue = ' ';
		}
		array_pop(self::$parentNodes);
		array_pop(self::$parentLayouts);
	}
	
	/**
	 * Redirects the user to the specified Manialink
	 */
	final public static function redirect($link, $render = true)
	{
		self::$domDocument = new DOMDocument('1.0', 'utf-8');
		self::$parentNodes = array();
		self::$parentLayouts = array();

		$redirect = self::$domDocument->createElement('redirect');
		$redirect->appendChild(self::$domDocument->createTextNode($link));
		self::$domDocument->appendChild($redirect);
		self::$parentNodes[] = $redirect;

		if($render)
		{
			if(ob_get_contents())
			{
				ob_clean();
			}
			header('Content-Type: text/xml; charset=utf-8');
			echo self::$domDocument->saveXML();
			exit;
		}
		else
		{
			return self::$domDocument->saveXML();
		}
	}

	/**
	 * Append some XML code to the document
	 * @param string Some XML code
	 */
	static function appendXML($XML)
	{
		$doc = new DOMDocument('1.0', 'utf-8');
		$doc->loadXML($XML);
		$node = self::$domDocument->importNode($doc->firstChild, true);
		end(self::$parentNodes)->appendChild($node);
	}

	/**
	 * Disable all Manialinks, URLs and actions of GUIElement objects as long as it is disabled
	 */
	static function disableLinks()
	{
		self::$linksEnabled = false;
	}

	/**
	 * Enable links
	 */
	static function enableLinks()
	{
		self::$linksEnabled = true;
	}
	
	/**
	 * Shortcut for including files in manialinks
	 */
	static function includeManialink($url)
	{
		$ui = new ManiaLib_Gui_Elements_IncludeManialink();
		$ui->setUrl($url);
		$ui->save();
	}
	
	/**
	 * Add a dictionnary file, will be included when rendering
	 */
	static function addDico($url)
	{
		self::$dicos[] = $url;
	}
}

?>