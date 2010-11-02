<?php 
/**
 * @author Maxime Raoust
 * @copyright 2009-2010 NADEO 
 */

require_once(APP_FRAMEWORK_LIBRARIES_PATH.'Database.php');
require_once(APP_MVC_MODELS_PATH.'Shout.class.php');

/**
 * Shout data access
 */
abstract class ShoutDAO
{
	/**
	 * Get the (10 by default) latest shouts
	 * @return array[Shout]
	 */
	static function getAll($offset=0, $length=10)
	{
		$db = DatabaseConnection::getInstance();
		$result = $db->execute(
			'SELECT * '.
			'FROM ManiaLib_Shouts '.
			'ORDER BY datePosted DESC '.
			DatabaseTools::getLimitString($offset, $length));
		$shouts = array();
		{
			while($shout = $result->fetchObject('Shout'))
			{
				$shouts[] = $shout;
			}
		}
		return array_reverse($shouts);
	}
	
	static function save(Shout $shout)
	{
		$newShout = $shout->id==null;
		$db = DatabaseConnection::getInstance();
		
		$values = array(
			'login' => $db->quote($shout->login), 
			'nickname' => $db->quote($shout->nickname),
			'message' => $db->quote($shout->message));
		
		if(!$newShout)
			$values['id'] = (int)$shout->id;
		
		$result = $db->execute(
			'INSERT INTO ManiaLib_Shouts '.
			DatabaseTools::getValuesString($values).' '.
			'ON DUPLICATE KEY UPDATE '.
			DatabaseTools::getOnDuplicateKeyUpdateValuesString(array_keys($values)));
			
		if($newShout)
		{
			$shout->id = $db->insertID();
		}
	}
}

?>