<?php
/**
 * Attendance-List Component Site medungform VIEW
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

// load view specific helper
//require(JPATH_COMPONENT_SITE . '/helpers/viewMeldungform.php');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

// Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_attlist', JPATH_SITE);
$doc = JFactory::getDocument();
$doc->addScript(JUri::base() . '/media/com_attlist/js/form.js');

$user    = JFactory::getUser();
$canEdit = AttlistHelpersAttlist::canUserEdit($this->item, $user);

?>

<div class="meldung-edit front-end-edit">
	<?php if (!$canEdit) : ?>
		<h3>
			<?php throw new Exception(JText::_('COM_ATTLIST_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
		</h3>
	<?php else : ?>
		<?php if (!empty($this->item->id)): ?>
			<h3><?php echo JText::sprintf('COM_ATTLIST_EDIT_ITEM_TITLE', $this->item->id); ?></h3>
		<?php else: ?>
			<h3><?php echo (JText::_('COM_ATTLIST_ADD_ITEM').' '.JText::_('COM_ATTLIST_CALL_SING')); ?></h3>
		<?php endif; ?>

		<form id="form-meldung"
			  action="<?php echo JRoute::_('index.php?option=com_attlist&task=meldung.save'); ?>"
			  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
			
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />

	<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />

	<?php echo $this->form->renderField('name'); ?>

	<?php echo $this->form->renderField('present'); ?>

	<div <?php if ($this->viewParams['show_note'] == 0) {echo 'style="display: none;"';} ?>>
	<?php echo $this->form->renderField('note'); ?>
	</div>
	
	<div style="display: none;">
	<?php echo $this->form->renderField('event_date'); ?>
	</div>

	<?php echo $this->form->getInput('creation_date'); ?>

	<div style="display: none;">
	<?php echo $this->form->renderField('event_title'); ?>
	</div>

	<div style="display: none;">
	<?php echo $this->form->renderField('catid'); ?>
	</div>

	<?php echo $this->form->renderField('created_by'); ?>
	
	<?php if (JPluginHelper::isEnabled('system', 'easycalccheckplus')) : ?>
		<div class="controls">{easycalccheckplus}</div>
	<?php endif ; ?>	

			<div class="control-group">
				<div class="controls">

					<?php if ($this->canSave): ?>
						<button type="submit" class="validate btn btn-primary">
							<?php echo JText::_('JSUBMIT'); ?>
						</button>
					<?php endif; ?>
					<a class="btn"
					   id="cancelButton"
					   href="<?php echo JRoute::_('index.php?option=com_attlist&task=meldungform.cancel'); ?>"
					   title="<?php echo JText::_('JCANCEL'); ?>">
						<?php echo JText::_('JCANCEL'); ?>
					</a>
				</div>
			</div>

			<input type="hidden" name="option" value="com_attlist"/>
			<input type="hidden" name="task"
				   value="meldungform.save"/>
			<?php echo JHtml::_('form.token'); ?>
		</form>
	<?php endif; ?>
</div>

<?php require(JPATH_COMPONENT_SITE . '/helpers/jsMeldungform.php'); ?>

