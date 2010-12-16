<?php

/**
 * Audio player
 * @package ManiaLib
 * @subpackage GUIToolkit
 */
class ManiaLib_Gui_Elements_Audio extends ManiaLib_Gui_Elements_Music
{
	/**#@+
	 * @ignore 
	 */
	protected $xmlTagName = 'music';
	protected $posX = 0;
	protected $posY = 0;
	protected $posZ = 0;
	protected $play;
	protected $looping = 0;
	/**#@-*/

	/**
	 * Autoplay the data when it's done loading
	 */
	function autoPlay()
	{
		$this->play = 1;
	}

	/**
	 * Loop when the end of the data is reached
	 */
	function enableLooping()
	{
		$this->looping = 1;
	}

	/**
	 * Returns whether auto playing is enabled
	 * @return boolean
	 */
	function getAutoPlay()
	{
		return $this->play;
	}

	/**
	 * Returns whether looping is enabled
	 * @return boolean
	 */
	function getLooping()
	{
		return $this->looping;
	}

	/**
	 * @ignore 
	 */
	protected function postFilter()
	{
		parent::postFilter();
		if($this->play !== null)
			$this->xml->setAttribute('play', $this->play);
		if($this->looping !== null)
			$this->xml->setAttribute('looping', $this->looping);
	}
}

?>