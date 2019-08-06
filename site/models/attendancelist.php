<?php
/**
 * Attendance-List Component Site attendancelist Model
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


defined('_JEXEC') or die;

use Joomla\CMS\Factory;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Attlist records.
 *
 * @since  1.6
 */
class AttlistModelAttendancelist extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see        JController
	 * @since      1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'state', 'a.state',
				'name', 'a.name',
				'present', 'a.present',
				'event_date', 'a.event_date',
				'creation_date', 'a.creation_date',
				'catid', 'a.catid',
				'created_by', 'a.created_by',
			);
		}

		parent::__construct($config);
	}

        
        
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since    1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
            

            // List state information.
            parent::populateState($ordering, $direction);

            $app = Factory::getApplication();

            $ordering  = $app->getUserStateFromRequest($this->context . '.ordercol', 'filter_order', $ordering);
            $direction = $app->getUserStateFromRequest($this->context . '.orderdirn', 'filter_order_Dir', $ordering);

            $this->setState('list.ordering', $ordering);
            $this->setState('list.direction', $direction);

            $start = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0, 'int');
            $limit = $app->getUserStateFromRequest($this->context . '.limit', 'limit', 0, 'int');

            if ($limit == 0)
            {
                $limit = $app->get('list_limit', 0);
            }

            $this->setState('list.limit', $limit);
            $this->setState('list.start', $start);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
            // Create a new query object.
            $db    = $this->getDbo();
            $query = $db->getQuery(true);

            // Select the required fields from the table.
            $query->select(
                        $this->getState(
                                'list.select', 'DISTINCT a.*'
                        )
                );

            $query->from('`#__attlist_item` AS a');
            

		// Join over the created by field 'created_by'
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');
            
		if (!Factory::getUser()->authorise('core.edit', 'com_attlist'))
		{
			$query->where('a.state = 1');
		}

            // Filter by search in title
            $search = $this->getState('filter.search');

            if (!empty($search))
            {
                if (stripos($search, 'id:') === 0)
                {
                    $query->where('a.id = ' . (int) substr($search, 3));
                }
                else
                {
                    $search = $db->Quote('%' . $db->escape($search, true) . '%');
                }
            }
            

		// Filtering event_date
		// Checking "_dateformat"
		$filter_event_date_from = $this->state->get("filter.event_date_from_dateformat");
		$filter_Qevent_date_from = (!empty($filter_event_date_from)) ? $this->isValidDate($filter_event_date_from) : null;

		if ($filter_Qevent_date_from != null)
		{
			$query->where("a.event_date >= '" . $db->escape($filter_Qevent_date_from) . "'");
		}

		$filter_event_date_to = $this->state->get("filter.event_date_to_dateformat");
		$filter_Qevent_date_to = (!empty($filter_event_date_to)) ? $this->isValidDate($filter_event_date_to) : null ;

		if ($filter_Qevent_date_to != null)
		{
			$query->where("a.event_date <= '" . $db->escape($filter_Qevent_date_to) . "'");
		}

		// Filtering catid
		$filter_catid = $this->state->get("filter.catid");

		if ($filter_catid)
		{
			$query->where("a.`catid` = '".$db->escape($filter_catid)."'");
		}

            // Add the list ordering clause.
            $orderCol  = $this->state->get('list.ordering');
            $orderDirn = $this->state->get('list.direction');

            if ($orderCol && $orderDirn)
            {
                $query->order($db->escape($orderCol . ' ' . $orderDirn));
            }

            return $query;
	}

	/**
	 * Method to get an array of data items
	 *
	 * @return  mixed An array of data on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
		
		foreach ($items as $item)
		{
				$item->present = empty($item->present) ? '' : JText::_('COM_ATTLIST_MELDUNGEN_PRESENT_OPTION_' . strtoupper($item->present));

		if (isset($item->catid) && $item->catid != '')
		{

			$db    = Factory::getDbo();
			$query = $db->getQuery(true);

			$query
				->select($db->quoteName('title'))
				->from($db->quoteName('#__categories'))
				->where('FIND_IN_SET(' . $db->quoteName('id') . ', ' . $db->quote($item->catid) . ')');

			$db->setQuery($query);

			$result = $db->loadColumn();

			$item->catid = !empty($result) ? implode(', ', $result) : '';
		}
		}

		return $items;
	}

	/**
	 * Method to get items of a specified category
	 *
	 * @param   string  $category     specifies the chosen category
	 *
	 * @return  associative Array     Array with a list of all "Meldungen" of the specified category
	 */
	public function getMeldungen()
	{
		$app = Factory::getApplication('com_attlist');

		// Load the view parameters.
        $viewParams       = $app->getParams();
        $viewParams_array = $viewParams->toArray();
        // get selected category
		$category = $viewParams_array['item_cat'];

		// Obtain a database connection
        $db = JFactory::getDbo();
        //Retrieve the shout
        $query = $db->getQuery(true)
                    ->select(array('state','name', 'present', 'note','event_date', 'event_title','creation_date'))
                    ->from($db->quoteName('#__attlist_item'))
                    ->where($db->quoteName('catid') . '=' . $category)
                    ->order('present DESC')
                    ->order('event_date DESC');
        // Prepare the query
        $db->setQuery($query);
        // Load the row.
        $result = $db->loadAssocList();
        // Return the Hello
        return $result;
	}

	/**
     * Get the all the Parameters set in the settings of the view
     *
     * @param   string  $name  Name ot the requested parameter
     *
     * @return  string  The content of the requested parameter, if parameter exists. Else: false
     *
     */
    public function getViewParams()
    {
        $app = Factory::getApplication('com_attlist');

        // Load the parameters.
        $params       = $app->getParams();
        $params_array = $params->toArray();

        if (isset($params_array['view_type'])) {
            $viewtype = $params_array['view_type'];
        } else {
            $viewtype = 'viewtype-Parameter does not exist';
        }

        if (isset($params_array['item_cat'])) {
            $category = $params_array['item_cat'];
        } else {
            $category = 'category-Parameter does not exist';
        }

        if (isset($params_array['item_dateformat'])) {
            $dateformat = $params_array['item_dateformat'];
        } else {
            $dateformat = 'dateformat-Parameter does not exist';
        }

        if (isset($params_array['item_span'])) {
            $itemspan = $params_array['item_span'];
        } else {
            $itemspan = 'item_span-Parameter does not exist';
        }

        if (isset($params_array['title_span'])) {
            $titlespan = $params_array['title_span'];
        } else {
            $titlespan = 'title_span-Parameter does not exist';
        }

        return array('item_cat'=>$category,'item_dateformat'=>$dateformat,'view_type'=>$viewtype, 'item_span'=>$itemspan, 'title_span'=>$titlespan);
    }

	/**
	 * Overrides the default function to check Date fields format, identified by
	 * "_dateformat" suffix, and erases the field if it's not correct.
	 *
	 * @return void
	 */
	protected function loadFormData()
	{
		$app              = Factory::getApplication();
		$filters          = $app->getUserState($this->context . '.filter', array());
		$error_dateformat = false;

		foreach ($filters as $key => $value)
		{
			if (strpos($key, '_dateformat') && !empty($value) && $this->isValidDate($value) == null)
			{
				$filters[$key]    = '';
				$error_dateformat = true;
			}
		}

		if ($error_dateformat)
		{
			$app->enqueueMessage(JText::_("COM_ATTLIST_SEARCH_FILTER_DATE_FORMAT"), "warning");
			$app->setUserState($this->context . '.filter', $filters);
		}

		return parent::loadFormData();
	}

	/**
	 * Checks if a given date is valid and in a specified format (YYYY-MM-DD)
	 *
	 * @param   string  $date  Date to be checked
	 *
	 * @return bool
	 */
	private function isValidDate($date)
	{
		$date = str_replace('/', '-', $date);
		return (date_create($date)) ? Factory::getDate($date)->format("Y-m-d") : null;
	}

	/**
     * Get the all categories of the attlist component
     *
     *
     * @return  string  The content of the requested parameter, if parameter exists. Else: false
     *
     */
    public function getCategories()
    {
        $app = Factory::getApplication('com_attlist');

        // Obtain a database connection
        $db = JFactory::getDbo();
        //Retrieve the shout
        $query = $db->getQuery(true)
                    ->select(array('id', 'title'))
                    ->from($db->quoteName('#__categories'))
                    ->where($db->quoteName('extension') . ' = ' . $db->quote('com_attlist.meldungen'))
                    ->order('id ASC');
        // Prepare the query
        $db->setQuery($query);
        // Load the categories.
        $category = $db->loadAssocList();
        // Return the Categories
        return $category;
    }
}	
