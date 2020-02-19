<?php 
/**
 * Attendance-List Component Site default VIEW
 * 
 * @package    Attlist
 * @subpackage com_attlist
 * @version    1.1.1
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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

// Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_attlist', JPATH_SITE);
$doc = JFactory::getDocument();

// Import CSS
$doc->addStyleSheet($this->baseurl.'/media/jui/css/icomoon.css');
$doc->addStyleSheet($this->baseurl. '/media/com_attlist/css/front_form.css');

$user    = JFactory::getUser();
$canEdit = AttlistHelpersAttlist::canUserEdit($this->item, $user);
$meldungen = $this->meldungen;

// Create a List of event dates and event titles
list($events, $event_titles) = $this->createEventlists();

// Create Tables out of fetched Data from Database
list($tables, $statistics) = $this->createTableentrys($events);

// Create Title
list($catName, $moduletitle) = $this->createModuletitle();


// Create HTML-Code
?>
<h2><?php echo ($moduletitle); ?></h2>

<?php if (count($events) == 0 || is_null($events)) {
	echo '<p>'.JText::_("COM_ATTLIST_NO_ITEMS_VIEW_ATTLIST").'...</p>';
} ?>

<?php foreach ($tables as $key => $value) { ?>

	<div id="accordionTable<?php echo $key;?>" class="attlist accordion <?php if ($this->viewParams['view_type'] == 1){echo 'hidden';}; ?>" eventdate="<?php  echo $events[$key];?>">
		<div id="headingTable<?php echo $key;?>" class="card-header">
			<div>
				<button class="btn att-header-element att-float" type="button" data-toggle="collapse" data-target="#collapse<?php echo $key;?>" aria-expanded="false" aria-controls="collapse<?php echo $key;?>" onclick="changeIcon('accIcon-<?php echo $key;?>')"><span id="accIcon-<?php echo $key;?>" class="icon-arrow-down"></span></button>
				<h2 class="att-header-element att-float">
					<?php if (count($events) == count($event_titles)) {
							echo $event_titles[$key];
						  } else {
						  	echo JText::_("COM_ATTLIST_EVENT");
						  }?>
					vom <?php echo $this->dateFormat($events[$key]);?>
				</h2>
			</div>			

			<?php if ($this->viewParams['view_type'] == 0) : ?>
				<div>				
					<form action="<?php echo JRoute::_('index.php?option=com_attlist&view=attendancelist'); ?>"
			    method="post" enctype="multipart/form-data" name="deleteForm_<?php echo $key;?>" id="deleteForm_<?php echo $key;?>" class="att-header-element att-float">
				    	<button type="button" class="btn" onclick="AttlistSubmitForm('attendancelist.remove', 'deleteForm_<?php echo $key;?>')">
							<span class="icon-delete"></span><?php echo JText::_('JACTION_DELETE') ?>
						</button>
						<input type="hidden" name="eventDate" value="<?php  echo $events[$key];?>">
						<input type="hidden" name="eventTitle" value="<?php  echo $event_titles[$key];?>">
						<input type="hidden" name="eventCat" value="<?php  echo $this->viewParams['item_cat'];?>">
						<input type="hidden" name="task">
						<?php echo JHtml::_('form.token'); ?>
					</form>

					<form id="form_PDF_<?php echo $key;?>" method="post" action="" class="att-header-element">
						<input id="token" type="hidden" name="<?php echo JSession::getFormToken(); ?>" value="1" />
						<input type="hidden" name="eventDate" value="<?php echo $this->dateFormat($events[$key]); ?>">
						<input type="hidden" name="eventTitle" value="<?php echo $event_titles[$key]; ?>">
						<input type="hidden" name="eventCat" value="<?php  echo $this->categories[$catName]['title'];?>">
						<input type="hidden" name="eventStat_Numb" value="<?php echo $statistics[$key]['Meldungen']; ?>">
						<input type="hidden" name="eventStat_Ab" value="<?php echo $statistics[$key]['Abmeldungen']; ?>">
						<textarea form="form_PDF_<?php echo $key;?>" name="html_table" readonly style="display: none;">
							<?php echo $tables[$key]; ?>
						</textarea>
						<button type="button" class="btn btn-primary" onclick="pdfAjax('form_PDF_<?php echo $key;?>', '<?php echo JText::_('COM_ATTLIST_TITLE_VIEW_ATTLIST');?>_<?php echo $events[$key];?>.pdf');">
							PDF
						</button>
					</form>
				</div>
			<?php endif; ?>		

		</div>
		<br />

		<div id="collapse<?php echo $key;?>" class="collapse show" data-parent="#accordionTable<?php echo $key;?>">
			
			<table border="1" class="attlist">
				<tr class="table_heading">
					<th width="20%"><?php echo JText::_('COM_ATTLIST_NAME');?></th>
					<th width="20%"><?php echo JText::_('COM_ATTLIST_CALL_SING');?></th>
					<?php if ($this->viewParams['view_type'] == 0) : ?>
						<th width="30%"><?php echo JText::_('COM_ATTLIST_NOTE');?></th>
					<?php endif ; ?>
					<th width="30%"><?php echo JText::_('COM_ATTLIST_SUBMIT_DATE');?></th>
				</tr>
				<?php echo $tables[$key]; ?>
			</table>
			<?php if ($this->viewParams['view_type'] == 0) : ?>
				<p><?php echo(JText::_('COM_ATTLIST_NUMBEROF_VIEW_ATTLIST').' '.JText::_('COM_ATTLIST_CALL_PL').': '); if ($statistics[$key]['Meldungen'] == null) {echo '0';}else{echo $statistics[$key]['Meldungen'];} ?></p>
				<p><?php echo(JText::_('COM_ATTLIST_NUMBEROF_VIEW_ATTLIST').' '.JText::_('COM_ATTLIST_ABSENT_PL').': '); if ($statistics[$key]['Abmeldungen'] == null) {echo '0';}else{echo $statistics[$key]['Abmeldungen'];} ?></p>
			<?php endif; ?>
		</div>
	</div>
<?php };

require(JPATH_COMPONENT_SITE . '/helpers/jsAttendancelist.php'); ?>
