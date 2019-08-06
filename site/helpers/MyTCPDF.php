<?php
//create Header and Footer
class MYPDF extends TCPDF {

	protected $params;
	protected $viewParams;

	
	//header
	public function Header() {
		$this->params  = JFactory::getApplication()->getParams('com_attlist');

		//set logo position due to settings
		if ($this->params->get('pdf_logo') != '') {
			$img_size = getimagesize(JPATH_SITE . '/' . $this->params->get('pdf_logo'));
		} else {
			$img_size = getimagesize(JPATH_SITE . '/images/joomla_black.png');
		}
		$img_height = 18;
		$img_width = ($img_height/$img_size[1]) * $img_size[0];
		$cor_Y = 15;
		switch ($this->params->get('place_logo')) {
			case 0:
				$cor_X = 15;
				break;
			case 1:
				$cor_X = 115 - ($img_width/2);
				break;
			case 2:
				$cor_X = 200 - $img_width;
				break;
			default:
				$cor_X = 15;
				break;
		}

		//Logo
		if ($this->params->get('pdf_logo') != '') {
			$logo_file = JPATH_SITE . '/' . $this->params->get('pdf_logo');
			$this->Image($logo_file, $cor_X, $cor_Y, '', $img_height, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
		} else {
			$logo_file = JPATH_SITE . '/images/joomla_black.png';
			$this->Image($logo_file, $cor_X, $cor_Y, '', $img_height, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		}
		//Line
		$this->Line(15,38,200,38);
	}
	//footer
	public function Footer() {
		$this->params  = JFactory::getApplication()->getParams('com_attlist');

		if ($this->params->get('pdf_source') != '') {
			$source = $this->params->get('pdf_source');
		} else {
			$source = JURI::base();
		}
		
		// Position at 15 mm from bottom
      		$this->SetY(-15);
      		$this->SetFont('helvetica', 'I', 8);
      		$this->Cell(0, 10, JText::_('COM_ATTLIST_PRINT_DATE_PDF').': '.date("d.m.Y, H:i \U\H\R"), 0, false, 'L', 0, '', 0, false, 'T', 'M');
      		$this->Cell(0, 10, JText::_('COM_ATTLIST_PRINT_SOURCE_PDF').': '.$source, 0, false, 'R', 0, '', 0, false, 'T', 'M');
	}
}