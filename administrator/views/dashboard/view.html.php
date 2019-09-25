<?php

/**
 * @version    CVS: 1.2.0
 * @package    Com_Attlist
 * @author     Manuel Häusler <tech.spuur@quickline.com>
 * @copyright  2018 Manuel Häusler
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Attlist.
 *
 * @since  1.6
 */
class AttlistViewDashboard extends JViewLegacy
{

	/**
	 * Display the Dashboard view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		//$this->pagination = $this->get('Pagination');

		// Check for errors.
		if (!empty($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		AttlistHelper::addSubmenu('dashboard');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = AttlistHelper::getActions();

		JToolBarHelper::title(JText::_('COM_ATTLIST').' - '.JText::_('COM_ATTLIST_DASHBOARD'), 'dashboard.png');		

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_attlist');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_attlist&view=meldungen');
	}

}
