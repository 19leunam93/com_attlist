<?php 

/**
 * @version    1.3.0
 * @package    Com_Attlist
 * @author     Manuel Häusler <tech.spuur@quickline.com>
 * @copyright  2020 Manuel Häusler
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;



?>

<?php if (!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif; ?>
		<img style="float: right; text-align: right;" src="<?php echo JURI::root().'/media/com_attlist/img/logo.jpg'; ?>">
		<h1><strong><?php echo JText::_('COM_CONFIG_COMPONENT_FIELDSET_LABEL'); ?> - <?php echo JText::_('COM_ATTLIST'); ?><strong></h1>
		<?php echo JText::_('COM_ATTLIST_COMPONENT_DESC'); ?>
		<br />
		<p>Author: Manuel Häusler, <a href="https://tech.spuur.ch">tech.spuur.ch</a></p>
		<br />
		<a href="https://tech.spuur.ch/extensions/com-attlist" class="btn btn-success"><?php echo JText::_('COM_ATTLIST_COMPONENT_WEBSITE'); ?></a>
		<br />
	</div>

