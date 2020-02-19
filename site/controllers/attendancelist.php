<?php

/**
 * Attendance-List Component Site attendancelist Controller
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

// No direct access
defined('_JEXEC') or die;

/**
 * Meldung controller class.
 *
 * @since  1.6
 */
class AttlistControllerAttendancelist extends JControllerForm
{

	public function remove()
	{
		$jinput = JFactory::getApplication()->input;
		$currentUri = (string)JUri::getInstance();

		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
		$app = JFactory::getApplication(); 
		$input = $app->input; 
		$model = $this->getModel('attform');


		// Check that this user is allowed to delete records
		if (!JFactory::getUser()->authorise( "core.delete", "com_attlist"))
		{
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->setHeader('status', 403, true);

			return;
		}

		// get the data from the HTTP POST request
		//$data  = $_POST;
		$data['eventDate'] = $jinput->post->get('eventDate');
		$data['eventTitle'] = $jinput->post->get('eventTitle');
		$data['eventCat'] = $jinput->post->get('eventCat');

		// set up context for saving form data
		$context = "$this->option.edit.$this->context";

		// // Attempt to delete the data.
		if (!$model->deleteMeldung($data))
		{
		// Handle the case where the save failed
            
			// Save the data in the session.
			$app->setUserState($context . '.data', $data);

			// Redirect back to the edit screen.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_DELETE_FAILED; '.$model->deleteMeldung($data), $model->getError()));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect($currentUri);

			return false;
		}

		// clear the data in the form
		$app->setUserState($context . '.data', null);

		// set up the redirect back to the same form
        $this->setRedirect(
            $currentUri,
            JText::_('1Meldungen erfolgreich gelöscht; '.$data['eventCat'])
		);
	}

	
	public function pdfDownload()
	{
		if (!JSession::checkToken('get')) 
        {
            echo new JResponseJson(null, JText::_('JINVALID_TOKEN'), true);
        }
        else 
        {
            parent::display();
        }
	}

}