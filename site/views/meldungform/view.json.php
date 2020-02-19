<?php
/**
 * Attendance-List Component Site form View.Json
 * 
 * @package    Attlist
 * @subpackage com_attlist
 * @version    1.3.0
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
 
// No direct access to this file
defined('_JEXEC') or die;
 
class AttlistViewMeldungform extends JViewLegacy
{
	/**
	 * This display function returns in json format the Helloworld greetings
	 *   found within the latitude and longitude boundaries of the map.
	 * These bounds are provided in the parameters
	 *   minlat, minlng, maxlat, maxlng
	 */

	function display($tpl = null)
	{
		//echo new JResponseJson('Ajax from Meldungform successful');

		function getMeldungen($catid) {

	        // Obtain a database connection
	        $db = JFactory::getDbo();
	        //Retrieve the shout
	        $query = $db->getQuery(true)
	                    ->select(array('state','name', 'event_date', 'event_title'))
	                    ->from($db->quoteName('#__attlist_item'))
	                    ->where($db->quoteName('catid') . ' = ' . (int)$catid);
	        // Prepare the query  
	        $db->setQuery($query);
	        // Load the row.
	        $result = $db->loadAssocList();
	        return $result;
		}

		$jinput = JFactory::getApplication()->input;

		// get the data from the HTTP POST request
		$df  = $jinput->post->get('jform');
		$data['name'] = (string)$_POST['jform']['name'];
		$data['present'] = $df[3];
		$data['event_date'] = $df[4];
		$data['creation_date'] = $df[5];
		$data['event_title'] = (string)$_POST['jform']['event_title'];
		$data['catid'] = $df[7];

		$meldungen = array();
		foreach (getMeldungen($data['catid']) as $key => $meldung) {
			if ($meldung['name'] == $data['name'] && $meldung['event_date'] == $data['event_date'] && $meldung['event_title'] == $data['event_title']) {
				array_push($meldungen, $meldung);
			}
		}
		
		// create the response
		$response['name'] = $data['name'];
		$response['count'] = count($meldungen);
		
		echo new JResponseJson($response);


		// $model = $this->getModel();
		// if ($mapbounds)
		// {
		// 	$records = $model->getMapSearchResults($mapbounds);
		// 	if ($records) 
		// 	{
		// 		echo new JResponseJson($records);
		// 	}
		// 	else
		// 	{
		// 		echo new JResponseJson(null, JText::_('COM_HELLOWORLD_ERROR_NO_RECORDS'), true);
		// 	}
		// }
		// else 
		// {
		// 	$records = array();
		// 	echo new JResponseJson(null, JText::_('COM_HELLOWORLD_ERROR_NO_MAP_BOUNDS'), true);
		// }
	}
}