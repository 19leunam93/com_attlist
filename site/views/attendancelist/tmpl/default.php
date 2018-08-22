<?php 
/**
 * Attendance-List Component Site default VIEW
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


// Create a List of event dates
// Create a List of event titles
$events = array();
$event_titles = array();
foreach ($meldungen as $key => $entry) {
	if ($meldungen[$key]['state'] == 1) {
		if (!in_array($meldungen[$key]['event_date'], $events)) {
		array_push($events, $meldungen[$key]['event_date']);
		}
		if (!in_array($meldungen[$key]['event_title'], $event_titles)) {
			array_push($event_titles, $meldungen[$key]['event_title']);
		}
	}
}


// Create Tables out of fetched Data from Database
$tables = array();
$statistics = array();

foreach ($events as $eventNumb => $value) {
	
	$tableX = '';	
	foreach ($meldungen as $key => $entry) {

		if ($events[$eventNumb] == $meldungen[$key]['event_date']) {

			$tableX .= '<tr>
							<td>'.$meldungen[$key]['name'].'</td>
							<td>'.$this->meldungArt($meldungen[$key]['present']).'</td>
							<td>'.$this->dateFormat($meldungen[$key]['creation_date']).'</td>
						</tr>';

			// Count Meldungen			
			$statistics[$eventNumb]['Meldungen']++;

			// Count of Abmeldungen
			if ($meldungen[$key]['present'] == 0) {
				$statistics[$eventNumb]['Abmeldungen']++;
			}
		}
	}
	array_push($tables, $tableX);
}

// get Name of selected category
$catName = array_search($this->viewParams['item_cat'], array_column($this->categories, 'id'));



// Create HTML-Code
?>
<h1><?php echo (JText::_('COM_ATTLIST_CALL_PL').' '.JText::_('COM_ATTLIST_FOR_VIEW_ATTLIST').' '); echo $this->categories[$catName]['title']; ?></h1>
<br />

<?php if (count($events) == 0 || is_null($events)) {
	echo '<p>'.JText::_("COM_ATTLIST_NO_ITEMS_VIEW_ATTLIST").'...</p>';
} ?>


<?php foreach ($tables as $key => $value) { ?>

	<div id="accordionTable<?php echo $key;?>" class="accordion">
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
			

		</div>
		<br />

		<div id="collapse<?php echo $key;?>" class="collapse show" data-parent="#accordionTable<?php echo $key;?>">
			
			<table border="1">
				<tr class="table_heading">
					<th width="30%"><?php echo JText::_('COM_ATTLIST_NAME');?></th>
					<th width="30%"><?php echo JText::_('COM_ATTLIST_CALL_SING');?></th>
					<th width="40%"><?php echo JText::_('COM_ATTLIST_SUBMIT_DATE');?></th>
				</tr>
				<?php echo $tables[$key]; ?>
			</table>
			<p><?php echo(JText::_('COM_ATTLIST_NUMBEROF_VIEW_ATTLIST').' '.JText::_('COM_ATTLIST_CALL_PL').': '); if ($statistics[$key]['Meldungen'] == null) {echo '0';}else{echo $statistics[$key]['Meldungen'];} ?></p>
			<p><?php echo(JText::_('COM_ATTLIST_NUMBEROF_VIEW_ATTLIST').' '.JText::_('COM_ATTLIST_ABSENT_PL').': '); if ($statistics[$key]['Abmeldungen'] == null) {echo '0';}else{echo $statistics[$key]['Abmeldungen'];} ?></p>
		</div>
	</div>
	<br />
	<br />
<?php };?>


<script>

	if (<?php echo count($events);?> != <?php echo count($event_titles);?>) {
		console.log("<?php echo JText::_('COM_ATTLIST_NAMING_PROBLEM_VIEW_ATTLIST');?>");
	}

	/**
	 * Generic submit form
	 *
	 * @param  {String}  task      The given task
	 * @param  {String}  formID	   The id of the form element
	 * @param  {node}    form      The form element
	 * @param  {bool}    validate  The form element
	 *
	 * @returns  {void}
	 */
	function AttlistSubmitForm(task, formID, form, validate) {

		if (!form) {
			form = document.getElementById(formID);
		}

		if (task) {
			form.task.value = task;
		}

		// Toggle HTML5 validation
		form.noValidate = !validate;

		if (!validate) {
			form.setAttribute('novalidate', '');
		} else if ( form.hasAttribute('novalidate') ) {
			form.removeAttribute('novalidate');
		}

		// Submit the form.
		// Create the input type="submit"
		var button = document.createElement('input');
		button.style.display = 'none';
		button.type = 'submit';

		// Append it and click it
		form.appendChild(button).click();

		// If "submit" was prevented, make sure we don't get a build up of buttons
		form.removeChild(button);
	};

	/**
	 * Ajax request of form data to download a pdf
	 *
	 * @param  {String}  task      The given task
	 * @param  {String}  formID	   The id of the form element
	 * @param  {node}    form      The form element
	 * @param  {bool}    validate  The form element
	 *
	 * @returns  {void}
	 */
	function pdfAjax(formID, pdfName) {

		// catch form data
		var formData = new FormData(document.getElementById(formID));
		formData.append('[token]', '1');
		formData.append('task', 'pdfDownload');
		formData.append('format', 'json');

		// Ajax request for generating the PDF
		var xhr=new XMLHttpRequest();
		xhr.open("POST", "<?php echo JRoute::_('index.php?option=com_attlist&view=attendancelist'); ?>", true);
		xhr.responseType = 'blob';

		xhr.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var blob = xhr.response;
			    var link = document.createElement('a');
			    document.body.appendChild(link);
			    link.href = URL.createObjectURL(blob);
			    link.download=pdfName;
				link.style = "display: none";
			    link.click();
			} else if (this.status == 403) {
				alert("Response from server: 403 - Access denied");
			}
		};
		xhr.send(formData);
	};

	/**
	 * Change Icon of the accordion
	 *
	 * @param  {String}  id   The id of the element of which icon has to be changed
	 *
	 * @returns  {void}
	 */
	function changeIcon(id) {
		jQuery("#"+id).toggleClass("icon-arrow-down");
		jQuery("#"+id).toggleClass("icon-arrow-up");
	};


	jQuery(document).ready(function(){
				

	});
</script>
