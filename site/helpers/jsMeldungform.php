<?php 
/**
 * Attendance-List javascript-Helper for meldungform view
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
	jQuery(document).ready(function(){

		// form submit
		jQuery("#form-meldung").submit(function(event){
		    event.preventDefault();

		  // catch form data
			var formData = new FormData(document.getElementById('form-meldung'));
			formData.append('[token]', '1');
			formData.append('task', 'checkCall');
			formData.append('format', 'json');

			var form = jQuery("#form-meldung");
    	var url_save = form.attr('action');

    	// Ajax request for check if user already passed a call
    	var xhr=new XMLHttpRequest();
			xhr.open("POST", "<?php echo JRoute::_('index.php?option=com_attlist&view=meldungform'); ?>", true);
			xhr.overrideMimeType('text/plain; charset=utf8');

			xhr.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {

				var response = JSON.parse(xhr.response);
				response = response.data;
				if (response.count != 0) {
					if (response.count == 1) {
						var option = '<?php echo JText::_('COM_ATTLIST_OPTION_0_ADJ');?>';

						if (response.last_option == 1) {
							option = '<?php echo JText::_('COM_ATTLIST_OPTION_1_ADJ');?>';
						}

						if ( confirm(response.name+', <?php echo JText::_('COM_ATTLIST_CHECK_2_VIEW_FORM');?> '+option+' <?php echo JText::_('COM_ATTLIST_TO_THAT_EVENT');?> <?php echo JText::_('COM_ATTLIST_CHECK_3_VIEW_FORM');?>'))
						{
							document.getElementById('form-meldung').submit();
						}
						else
						{
							location.href = "<?php echo str_replace('&amp;','&',JRoute::_('index.php?option=com_attlist&task=meldungform.cancel')); ?>";
						}

					} else {

						if ( confirm(response.name+', <?php echo JText::_('COM_ATTLIST_CHECK_1_VIEW_FORM');?> '+response.count+' <?php echo JText::_('COM_ATTLIST_CALL_PL');?> <?php echo JText::_('COM_ATTLIST_TO_THAT_EVENT');?> <?php echo JText::_('COM_ATTLIST_CHECK_3_VIEW_FORM');?>'))
						{
							document.getElementById('form-meldung').submit();
						}
						else
						{
							location.href = "<?php echo str_replace('&amp;','&',JRoute::_('index.php?option=com_attlist&task=meldungform.cancel')); ?>";
						}
					}

				} else {
					document.getElementById('form-meldung').submit();
				}

			} else if (this.status == 403) {
				alert("Response from server: 403 - Access denied");
			}
		};
		xhr.send(formData);
		console.log(formData.get("jform[event_title]"));    
		});

		// insert category in hidden form field
		jQuery("#jform_catid").val("<?php echo $this->viewParams['item_cat']; ?>");

		// insert eventtitle in hidden form field
		var title = jQuery("span[id*='<?php echo $this->viewParams['title_span'];?>']").text();
		jQuery("#jform_event_title").val(title);

		var date_format = jQuery("#jform_event_date_btn").attr("data-dayformat");
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

		if (date_format == '%Y-%m-%d') {

			setTimeout(function(){
				jQuery("#jform_event_date").attr("data-alt-value",date_corrected);
				jQuery("#jform_event_date").attr("data-local-value",date_corrected);
				jQuery("#jform_event_date").val(date_corrected);
			},500);	

		} else {
			// render a Message, that the File formats do not match
			var message2 = {};
			message2.warning = [];
			message2.warning.push('<?php echo JText::_('COM_ATTLIST_DATEFORMAT_ITEM_VIEW_FORM');?>');
			Joomla.renderMessages(message2);
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

	});
</script>
