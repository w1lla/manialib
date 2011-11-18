<?php
/**
 * ManiaLib - Lightweight PHP framework for Manialinks
 * 
 * @copyright   Copyright (c) 2009-2011 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace ManiaLib\Authentication;

/**
 * @deprecated
 * @todo Merge this in ManiaLib\WebServices
 */
class Config extends \ManiaLib\Utils\Singleton
{

	public $username;
	public $password;
	public $scope;
	public $authLog = false;

}

?>