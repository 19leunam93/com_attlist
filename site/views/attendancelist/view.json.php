<?php
/**
 * Attendance-List Component Site attendancelist View.Json
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
 
// No direct access to this file
defined('_JEXEC') or die;
 
class AttlistViewAttendancelist extends JViewLegacy
{
	/**
	 * This display function returns in json format the Helloworld greetings
	 *   found within the latitude and longitude boundaries of the map.
	 * These bounds are provided in the parameters
	 *   minlat, minlng, maxlat, maxlng
	 */

	function display($tpl = null)
	{

		// echo new JResponseJson('Ajax successful');

		$jinput = JFactory::getApplication()->input;
		$params = JFactory::getApplication()->getParams();

		// get the data from the HTTP POST request
		//$data  = $_POST;
		$data['eventDate'] = $jinput->post->get('eventDate');
		$data['eventTitle'] = $jinput->post->get('eventTitle');
		$data['eventCat'] = $jinput->post->get('eventCat');
		$data['eventStat_Numb'] = $jinput->post->get('eventStat_Numb');
		if ($data['eventStat_Numb'] == null) {
			$data['eventStat_Numb'] = '0';
		}
		$data['eventStat_Ab'] = $jinput->post->get('eventStat_Ab');
		if ($data['eventStat_Ab'] == null) {
			$data['eventStat_Ab'] = '0';
		}
		$table = $jinput->post->get('html_table', null, 'RAW');

		//generate PDF
		require_once(JPATH_COMPONENT_SITE . '/helpers/TCPDF/tcpdf.php');
		require_once(JPATH_COMPONENT_SITE . '/helpers/MyTCPDF.php');

		$obj_pdf = new MYPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$obj_pdf->SetCreator(PDF_CREATOR);
		$obj_pdf->SetTitle('Meldungen für ' . $data['eventCat']);
		$obj_pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
		$obj_pdf->SetFooterData(array(0,64,0), array(0,64,128));
		$obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$obj_pdf->SetDefaultMonospacedFont('helvetica');
		$obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$obj_pdf->SetMargins(PDF_MARGIN_LEFT, '50', PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$obj_pdf->setPrintHeader(true);
		$obj_pdf->setPrintFooter(true);
		$obj_pdf->SetAutoPageBreak(TRUE, 10);
		$obj_pdf->SetFont('helvetica', '', 11);
		$obj_pdf->AddPage('P', 'A4');  // L: Landscape  P: Portrait

		// Create HTML content 
		$content = '<h1>'.JText::_('COM_ATTLIST_CALL_PL').' '.JText::_('COM_ATTLIST_FOR_VIEW_ATTLIST').' ' . $data['eventCat'] . '</h1><br />';
		$content .= '<p style="height: 100px;"><p>';
		$content .= '<h2>' . $data['eventTitle'] . ' vom ' . $data['eventDate'] . ':</h2><br />';
		$content .= '<table border="1" cellpadding="2">
		            <tr class="table_heading" style="font-weight: bold;">
		                <th width="20%">'.JText::_('COM_ATTLIST_NAME').'</th>
		                <th width="20%">'.JText::_('COM_ATTLIST_CALL_SING').'</th>
		                <th width="30%">'.JText::_('COM_ATTLIST_NOTE').'</th>
		                <th width="30%">'.JText::_('COM_ATTLIST_SUBMIT_DATE').'</th> 
		            </tr>';
		$content .= $table . '</table>';
		$content .= '<p>'.JText::_('COM_ATTLIST_NUMBEROF_VIEW_ATTLIST').' '.JText::_('COM_ATTLIST_CALL_PL').': ' . $data['eventStat_Numb'] . '</p>';
		$content .= '<p>'.JText::_('COM_ATTLIST_NUMBEROF_VIEW_ATTLIST').' '.JText::_('COM_ATTLIST_ABSENT_PL').': ' . $data['eventStat_Ab'] . '</p>';
		$content .= '<p style="height: 100px;"><p>';

		//paste content into the PDF
		//$obj_pdf->setCellHeightRatio(1.25);
		$obj_pdf->writeHTML($content, true, false, true, false, '');
		//output the PDF
		ob_end_clean();
		echo new JResponseJson($obj_pdf->Output(JText::_('COM_ATTLIST_TITLE_VIEW_ATTLIST').'.pdf', 'I'));


		// $model = $this->getModel();
		// if ($mapbounds)
		// {
		// 	$records = $model->getMapSearchResults($mapbounds);
		// 	if ($records) 
		// 	{
		// 		echo new JResponseJson($records);
		// 	}
		// 	else
		// 	{
		// 		echo new JResponseJson(null, JText::_('COM_HELLOWORLD_ERROR_NO_RECORDS'), true);
		// 	}
		// }
		// else 
		// {
		// 	$records = array();
		// 	echo new JResponseJson(null, JText::_('COM_HELLOWORLD_ERROR_NO_MAP_BOUNDS'), true);
		// }
	}
}