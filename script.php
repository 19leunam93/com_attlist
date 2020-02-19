<?php
/**
 * @version    1.3.0
 * @package    Com_Attlist
 * @author     Manuel Häusler <tech.spuur@quickline.com>
 * @copyright  2020 Manuel Häusler
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Install method
 * is called by the installer of Joomla!
 *
 * @return  void
 * @since   1.0.0
 */
class com_attlistInstallerScript
{
  /**
   * This method is called after a component is installed.
   *
   * @param  \stdClass $parent - Parent object calling this method.
   *
   * @return void
   */
  public function install($parent)
  {
  	?>

      <div class="hero-unit">
        <img src="../media/com_attlist/img/logo.jpg" alt="AttendanceList Logo" width="128" height="128">
        <p></p>
        <div class="alert alert-success">
          <h3><?php echo JText::sprintf('COM_ATTLIST_INSTALL_TXT', $parent->get('manifest')->version);?></h3>
          <p>
            <a title="Dashboard" class="btn btn-primary" onclick="location.href='index.php?option=com_attlist'; return false;" href="#">
              <?php echo JText::_('COM_ATTLIST_DASHBOARD');?></a>
            <a title="Options" class="btn" onclick="location.href='index.php?option=com_config&view=component&component=com_attlist'; return false;" href="#">
              <?php echo JText::_('JOPTIONS');?></a>
          </p>
        </div>
      </div>

    <?php
  }

  /**
   * This method is called after a component is uninstalled.
   *
   * @param  \stdClass $parent - Parent object calling this method.
   *
   * @return void
   */
  public function uninstall($parent)
  {
    ?>

      <div class="hero-unit">
        <div class="alert alert-success">
          <h3><?php echo JText::_('COM_ATTLIST_UNINSTALL_TXT');?></h3>
        </div>
      </div>

    <?php
  }

  /**
   * This method is called after a component is updated.
   *
   * @param  \stdClass $parent - Parent object calling object.
   *
   * @return void
   */
  public function update($parent)
  {
    ?>

      <div class="hero-unit">
        <img src="../media/com_attlist/img/logo.jpg" alt="AttendanceList Logo" width="128" height="128">
        <p></p>
        <div class="alert alert-success">
          <h3><?php echo JText::sprintf('COM_ATTLIST_UPDATE_TXT', $parent->get('manifest')->version);?></h3>
          <p>
            <a title="Changelog" class="btn btn-info" onclick="location.href='https://github.com/Elfangor93/com_attlist#changelog'; return false;" href="#">
              <i class="icon-list"></i> Changelog
            </a>
          </p>
          <p>
            <a title="Dashboard" class="btn btn-primary" onclick="location.href='index.php?option=com_attlist'; return false;" href="#">
              <?php echo JText::_('COM_ATTLIST_DASHBOARD');?></a>
            <a title="Options" class="btn" onclick="location.href='index.php?option=com_config&view=component&component=com_attlist'; return false;" href="#">
              <?php echo JText::_('JOPTIONS');?></a>
          </p>
        </div>
      </div>

    <?php
  }

  /**
   * Runs just before any installation action is performed on the component.
   * Verifications and pre-requisites should run in this function.
   *
   * @param  string    $type   - Type of PreFlight action. Possible values are:
   *                           - * install
   *                           - * update
   *                           - * discover_install
   * @param  \stdClass $parent - Parent object calling object.
   *
   * @return void
   */
  public function preflight($type, $parent)
  {

  }

  /**
   * Runs right after any installation action is performed on the component.
   *
   * @param  string    $type   - Type of PostFlight action. Possible values are:
   *                           - * install
   *                           - * update
   *                           - * discover_install
   * @param  \stdClass $parent - Parent object calling object.
   *
   * @return void
   */
  function postflight($type, $parent)
  {

  }
}