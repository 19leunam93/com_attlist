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

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_attlist'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Attlist', JPATH_COMPONENT_ADMINISTRATOR);
JLoader::register('AttlistHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'attlist.php');

$controller = JControllerLegacy::getInstance('Attlist');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
