<?php
/**
 * @version    1.0.0
 * @package    Com_Attlist
 * @author     Manuel Häusler <tech.spuur@quickline.com>
 * @copyright  2020 Manuel Häusler
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Meldung controller class.
 *
 * @since  1.6
 */
class AttlistControllerMeldung extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'meldungen';
		parent::__construct();
	}
}
