<?php
/**
 * Attendance-List Component Site meldungform Controller
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
class AttlistControllerMeldungForm extends JControllerForm
{
	/**
	 * Method to check out an item for editing and redirect to the edit form.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	public function edit($key = NULL, $urlVar = NULL)
	{
		$app = JFactory::getApplication();

		// Get the previous edit id (if any) and the current edit id.
		$previousId = (int) $app->getUserState('com_attlist.edit.meldung.id');
		$editId     = $app->input->getInt('id', 0);

		// Set the user id for the user to edit in the session.
		$app->setUserState('com_attlist.edit.meldung.id', $editId);

		// Get the model.
		$model = $this->getModel('MeldungForm', 'AttlistModel');

		// Check out the item
		if ($editId)
		{
			$model->checkout($editId);
		}

		// Check in the previous user.
		if ($previousId)
		{
			$model->checkin($previousId);
		}

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=com_attlist&view=meldungform&layout=edit', false));
	}

	/**
	 * Method to save a user's profile data.
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since  1.6
	 */
	public function save($key = NULL, $urlVar = NULL)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app   = JFactory::getApplication();
		$model = $this->getModel('MeldungForm', 'AttlistModel');

		// Get the user data.
		$data = JFactory::getApplication()->input->get('jform', array(), 'array');

		// Validate the posted data.
		$form = $model->getForm();

		if (!$form)
		{
			throw new Exception($model->getError(), 500);
		}

		// Validate the posted data.
		$data = $model->validate($form, $data);

		// Check for errors.
		if ($data === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			$input = $app->input;
			$jform = $input->get('jform', array(), 'ARRAY');

			// Save the data in the session.
			$app->setUserState('com_attlist.edit.meldung.data', $jform);

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_attlist.edit.meldung.id');
			$this->setRedirect(JRoute::_('index.php?option=com_attlist&view=meldungform&layout=edit&id=' . $id, false));

			$this->redirect();
		}

		// Attempt to save the data.
		$return = $model->save($data);

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_attlist.edit.meldung.data', $data);

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_attlist.edit.meldung.id');
			$menu = $app->getMenu()->getActive();
			$this->setMessage(JText::sprintf('Save failed', $model->getError()), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_attlist&view=meldungform&layout=edit&id=' . $id, false));
		}

		// Check in the profile.
		if ($return)
		{
			$model->checkin($return);
		}

		// Clear the profile id from the session.
		$app->setUserState('com_attlist.edit.meldung.id', null);

		// Redirect to the list screen.
		$this->setMessage(JText::sprintf('COM_ATTLIST_THANKS',$data['name']).' '.JText::_('COM_ATTLIST_OPTION_'.$data['present'].'_SAVED_SUCCESSFULLY'), 'message');
		$menu = JFactory::getApplication()->getMenu();
		$item = $menu->getActive();
		$config = JFactory::getConfig();
		$sef = $config->get('sef');
		if (empty($item->link) || empty($item->id)) {
			$url  = 'index.php?option=com_attlist&view=meldungform';
		} elseif ($sef) {
			$url  = 'index.php?option=com_attlist&Itemid=' . $item->id;
		} else {
			$url  = $item->link . '&Itemid=' . $item->id;
		}
		$this->setRedirect(JRoute::_($url, false));

		// Flush the data from the session.
		$app->setUserState('com_attlist.edit.meldung.data', null);
	}

	/**
	 * Method to abort current operation
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function cancel($key = NULL)
	{
		$app = JFactory::getApplication();

		// Get the current edit id.
		$editId = (int) $app->getUserState('com_attlist.edit.meldung.id');

		// Get the model.
		$model = $this->getModel('MeldungForm', 'AttlistModel');

		// Check in the item
		if ($editId)
		{
			$model->checkin($editId);
		}

		$menu = JFactory::getApplication()->getMenu();
		$item = $menu->getActive();
		$config = JFactory::getConfig();
		$sef = $config->get('sef');
		if (empty($item->link) || empty($item->id)) {
			$url  = 'index.php?option=com_attlist&view=meldungform';
		} elseif ($sef) {
			$url  = 'index.php?option=com_attlist&Itemid=' . $item->id;
		} else {
			$url  = $item->link . '&Itemid=' . $item->id;
		}
		//$url  = (empty($item->link) ? 'index.php?option=com_attlist&view=meldungen' : $item->link);
		$this->setRedirect(JRoute::_($url, false));
	}

	/**
	 * Method to remove data
	 *
	 * @return void
	 *
	 * @throws Exception
     *
     * @since 1.6
	 */
	public function remove()
    {
        $app   = JFactory::getApplication();
        $model = $this->getModel('MeldungForm', 'AttlistModel');
        $pk    = $app->input->getInt('id');

        // Attempt to save the data
        try
        {
            $return = $model->delete($pk);

            // Check in the profile
            $model->checkin($return);

            // Clear the profile id from the session.
            $app->setUserState('com_attlist.edit.meldung.id', null);

            $menu = $app->getMenu();
            $item = $menu->getActive();
            $url = (empty($item->link) ? 'index.php?option=com_attlist&view=meldungen' : $item->link);

            // Redirect to the list screen
            $this->setMessage(JText::_('COM_EXAMPLE_ITEM_DELETED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_($url, false));

            // Flush the data from the session.
            $app->setUserState('com_attlist.edit.meldung.data', null);
        }
        catch (Exception $e)
        {
            $errorType = ($e->getCode() == '404') ? 'error' : 'warning';
            $this->setMessage($e->getMessage(), $errorType);
            $this->setRedirect('index.php?option=com_attlist&view=meldungen');
        }
    }

    public function checkCall()
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
