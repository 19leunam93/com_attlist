<?php
/**
 * Attendance-List Component Site medungform VIEW
 * 
 * @package    Attlist
 * @subpackage com_attlist
 * @version    1.1.0
 *
 * @author     Manuel Haeusler <tech.spuur@quickline.com>
 * @copyright  2018 Manuel Haeusler
 * @license    GNU/GPL, see LICENSE.php
 *
 * com_attlist is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

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

	<div class="controls">{easycalccheckplus}</div>

			<div class="control-group">
				<div class="controls">

					<?php if ($this->canSave): ?>
						<button type="submit" class="validate btn btn-primary">
							<?php echo JText::_('JSUBMIT'); ?>
						</button>
					<?php endif; ?>
					<a class="btn"
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

<script>
	jQuery(document).ready(function(){

		// insert category in hidden form field
		jQuery("#jform_catid").val("<?php echo $this->viewParams['item_cat']; ?>");

		// insert eventtitle in hidden form field
		var title = jQuery("span[id*='<?php echo $this->viewParams['title_span'];?>']").text();
		jQuery("#jform_event_title").val(title);

		var date_format = jQuery("#jform_event_date_btn").attr("data-dayformat");
		var date = jQuery("span[id*='<?php echo $this->viewParams['item_span'];?>']").text();
		var date_corrected = "";

		if (<?php echo $this->viewParams['item_dateformat']; ?> == 1) {
			date_corrected = date;
		} else if (<?php echo $this->viewParams['item_dateformat']; ?> == 0){
			//split date to parts (dd.mm.YYYY)
			var parts = date.split(".");
			date_corrected = parts[2] + "-" + parts[1] + "-" + parts[0];

		} else if (<?php echo $this->viewParams['item_dateformat']; ?> == 2){
			//split date to parts (mm/dd/YYYY)
			var parts = date.split("/");
			date_corrected = String(parts[2]) + "-" + String(parts[0]) + "-" + String(parts[1]);

		} else {
			var message1 = {};
			message1.warning = [];
			message1.warning.push('<?php echo JText::_('COM_ATTLIST_DATEFORMAT_ITEM_VIEW_FORM');?>');
			Joomla.renderMessages(message1);
		}

		if (date_format == '%Y-%m-%d') {

			setTimeout(function(){
				jQuery("#jform_event_date").attr("data-alt-value",date_corrected);
				jQuery("#jform_event_date").attr("data-local-value",date_corrected);
				jQuery("#jform_event_date").val(date_corrected);
			},500);	

		} else {
			var message2 = {};
			message2.warning = [];
			message2.warning.push('<?php echo JText::_('COM_ATTLIST_DATEFORMAT_ITEM_VIEW_FORM');?>');
			Joomla.renderMessages(message2);
		}

		if (date.length == null || date.length == 0) {
			var message3 = {};
			message3.warning = [];
			message3.warning.push('<?php echo JText::_('COM_ATTLIST_WARNING_EVENTDATE_VIEW_MELDUNGFORM');?>');
			Joomla.renderMessages(message3);
			
			console.log('<?php echo JText::_('COM_ATTLIST_LOG_EVENTDATE_VIEW_MELDUNGFORM');?>');
		}

		if (title.length == null || title.length == 0) {
			var message4 = {};
			message4.warning = [];
			message4.warning.push('<?php echo JText::_('COM_ATTLIST_WARNING_EVENTTITLE_VIEW_MELDUNGFORM');?>');
			Joomla.renderMessages(message4);
			
			console.log('<?php echo JText::_('COM_ATTLIST_LOG_EVENTTITLE_VIEW_MELDUNGFORM');?>');
		}
	});
</script>
