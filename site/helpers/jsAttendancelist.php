<?php 
/**
 * Attendance-List javascript-Helper for attendancelist view
 * 
 * @package    Attlist
 * @subpackage com_attlist
 * @version    1.4.0
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
?>

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

		jQuery("[id*='deleteForm_']").submit(function() {
			return confirm("<?php echo JText::_("COM_ATTLIST_DELETE_CONFIRM_VIEW_ATTLIST"); ?>");
		});
		
		/**
		 * Get Date of the current Event. View the correct Attendencelist according to that Event.
		 * Only if visitor view is selected
		 *
		 */
		<?php if ($this->viewParams['view_type'] == 1) : ?>
			var title = jQuery("span[id*='<?php echo $this->viewParams['title_span'];?>']").text();
			var date = jQuery("span[id*='<?php echo $this->viewParams['item_span'];?>']").text();
			var date_corrected = "";

			// create the corrected date (YYYY-mm-dd)
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
				// render a Message, that the File formats do not match
				var message1 = {};
				message1.warning = [];
				message1.warning.push('<?php echo JText::_('COM_ATTLIST_DATEFORMAT_ITEM_VIEW_FORM');?>');
				Joomla.renderMessages(message1);
			}

			// render a Message, if the event date is not found
			if (date.length == null || date.length == 0) {
				var message3 = {};
				message3.warning = [];
				message3.warning.push('<?php echo JText::_('COM_ATTLIST_WARNING_EVENTDATE_VIEW_MELDUNGFORM');?>');
				Joomla.renderMessages(message3);
				
				console.log('<?php echo JText::_('COM_ATTLIST_LOG_EVENTDATE_VIEW_MELDUNGFORM');?>');
			}

			// render a Message, if the event title is not found
			if (title.length == null || title.length == 0) {
				var message4 = {};
				message4.warning = [];
				message4.warning.push('<?php echo JText::_('COM_ATTLIST_WARNING_EVENTTITLE_VIEW_MELDUNGFORM');?>');
				Joomla.renderMessages(message4);
				
				console.log('<?php echo JText::_('COM_ATTLIST_LOG_EVENTTITLE_VIEW_MELDUNGFORM');?>');
			}

			// make the table with the correct event date visible again
			jQuery('div[eventdate=' + date_corrected + ']').removeClass('hidden');
		<?php endif; ?>
	});
</script>
