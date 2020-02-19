<?php
/**
 * Attendance-List Component Site attform Model
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

defined('_JEXEC') or die;


/**
 * HelloWorld Model
 *
 * @since  1.0.5
 */
class AttlistModelAttform extends JModelAdmin
{

	/**
	 * Method to delete records of specific event_date
	 *
	 * @param   array  $form_data   Array of the data from POST request
	 *
	 * @return  true on success, false if not
	 *
	 * @since   1.0.5
	 */
	public function deleteMeldung($form_data)
	{
		// Obtain a database connection
		$db = JFactory::getDbo();

		// delete all custom keys for user 1001.
		$conditions = array(
		    $db->quoteName('event_date') . ' = "' . trim($form_data['eventDate']) . '"', 
		    $db->quoteName('event_title') . ' = "' . trim($form_data['eventTitle']) . '"',
		    $db->quoteName('catid') . ' = ' . trim($form_data['eventCat'])
		);

		//Retrieve the shout
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__attlist_item'));
		$query->where($conditions);
		// Prepare the query
		$db->setQuery($query);
		// Execute deleting
		if ($result = $db->execute() != false) {
			return true;
		} else {
			return false;
		};
	}

	public function getForm($data = array(), $loadData = true)
	{
		return;
	}

}