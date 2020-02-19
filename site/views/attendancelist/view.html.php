<?php 
/**
 * Attendance-List Component Site attendancelist View.Html
 * 
 * @package    Attlist
 * @subpackage com_attlist
 * @version    1.0.0
 *
 * @author     Manuel Haeusler <tech.spuur@quickline.com>
 * @copyright  2020 Manuel Haeusler
 * @license    GNU/GPL, see LICENSE.php
 *
 * com_attlist is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

jimport('joomla.application.component.view');

/**
 * View to edit
 *
 * @since  1.6
 */
class AttlistViewAttendancelist extends JViewLegacy
{
	protected $state;

	protected $item;

	protected $viewParams;

	protected $params;

	protected $canSave;

	protected $meldungen;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$app  = Factory::getApplication();
		$user = Factory::getUser();

		$this->state   = $this->get('State');
		$this->item    = $this->get('Item');
		$this->params  = $app->getParams('com_attlist');
		$this->canSave = $this->get('CanSave');
		$this->viewParams = $this->get('ViewParams');
		$this->meldungen = $this->get('Meldungen');
		$this->categories = $this->get('Categories');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function _prepareDocument()
	{
		$app   = Factory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_ATTLIST_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}


	/**
	 * Gibt die Art der Meldung zurück
	 *
	 * @param   int  $present  Art der Meldung
	 *
	 * @return  str  Name der Meldung
	 *
	 */
	public function meldungArt($present)
	{
		if ($present == 0) {
			$meldung = JText::_("COM_ATTLIST_OPTION_0");
		} elseif ($present == 1) {
			$meldung = JText::_("COM_ATTLIST_OPTION_1");
		} else {
			$meldung = JText::_("COM_ATTLIST_UNKNOWN");
		}

		return $meldung;
	}


	/**
	 * Gibt das korrekte Datumsformat zurück
	 *
	 * @param   str  $date  Datum
	 *
	 * @return  str  Datum im richtigen Format
	 *
	 */
	public function dateFormat($date)
	{
		$app  = Factory::getApplication();
		$this->viewParams = $this->get('ViewParams');
		$param = $this->viewParams['item_dateformat'];

		$t = explode(' ', $date);
		if (isset($t[1])) {
			$time = $t[1];
		} else {
			$time = '';
		}
		
		$d = explode('-', $t[0]);
		$year = $d[0];
		$month = $d[1];
		$day = $d[2];

		if ($param == 0) {
			$newDate = $day.'.'.$month.'.'.$year.' '.$time;
		} elseif ($param == 1) {
			$newDate = $date;
		} elseif ($param == 2) {
			$newDate = $month.'/'.$day.'/'.$year.' '.$time;
		} else {
			$newDate = JText::_("COM_ATTLIST_UNKNOWN");
		}

		return $newDate;
	}

	/**
	 * Create a List of event dates and List of event titles
	 *
	 *
	 * @return  array  List of the two lists
	 *
	 */
	public function createEventlists()
	{	 
		$events = array();
		$event_titles = array();
		foreach ($this->meldungen as $key => $entry) {
			if ($this->meldungen[$key]['state'] == 1) {
				if (!in_array($this->meldungen[$key]['event_date'], $events)) {
				array_push($events, $this->meldungen[$key]['event_date']);
				}
				if (!in_array($this->meldungen[$key]['event_title'], $event_titles)) {
					array_push($event_titles, $this->meldungen[$key]['event_title']);
				}
			}
		}

		return array($events, $event_titles);
	}

	/**
	 * Create Tables out of fetched Data from Database
	 *
	 * @param   array  $events  List of events
	 *
	 * @return  array  List of all the entrys for the table and the statistics
	 *
	 */
	public function createTableentrys($events)
	{	 
		$tables = array();
		$statistics = array();
		foreach ($events as $eventNumb => $value) {
			$statistics[$eventNumb]['Meldungen'] = 0;
			$statistics[$eventNumb]['Abmeldungen'] = 0;
			$tableX = '';	
			foreach ($this->meldungen as $key => $entry) {

				if ($events[$eventNumb] == $this->meldungen[$key]['event_date']) {
					if ($this->meldungen[$key]['present'] == 1 || $this->viewParams['view_type'] == 0) {
						$tableX .= '<tr>
										<td>'.$this->meldungen[$key]['name'].'</td>
										<td>'.$this->meldungArt($this->meldungen[$key]['present']).'</td>';
						if ($this->viewParams['view_type'] == 0) {
						$tableX .=		'<td>'.$this->meldungen[$key]['note'].'</td>';
						}				
						$tableX .=		'<td>'.$this->dateFormat($this->meldungen[$key]['creation_date']).'</td>
									</tr>';
					}

					// Count Meldungen
					$statistics[$eventNumb]['Meldungen'] = $statistics[$eventNumb]['Meldungen'] + 1;				

					// Count of Abmeldungen
					if ($this->meldungen[$key]['present'] == 0) {
						$statistics[$eventNumb]['Abmeldungen'] = $statistics[$eventNumb]['Abmeldungen'] + 1;
					}
				}
			}
			array_push($tables, $tableX);
		}

		return array($tables, $statistics);
	}

	/**
	 * Erstellt den Titel
	 *
	 *
	 * @return  array  Liste von 
	 *
	 */
	public function createModuletitle()
	{
		$catName = array_search($this->viewParams['item_cat'], array_column($this->categories, 'id'));

		// create the title
		if ($this->viewParams['view_type'] == 0) {
			$moduletitle = JText::_('COM_ATTLIST_CALL_PL').' '.JText::_('COM_ATTLIST_FOR_VIEW_ATTLIST').' '.$this->categories[$catName]['title'];
		} elseif ($this->viewParams['view_type'] == 1) {
			$moduletitle = JText::_('COM_ATTLIST_RECEIVED_VIEW_ATTLIST').' '.JText::_('COM_ATTLIST_PRESENT_PL');
		} else {
			$moduletitle = 'No view type specified';
		}

		return array($catName, $moduletitle);
	}

}
