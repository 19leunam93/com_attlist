<?php

/**
 * @version    1.3.0
 * @package    Com_Attlist
 * @author     Manuel Häusler <tech.spuur@quickline.com>
 * @copyright  2020 Manuel Häusler
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Attlist records.
 *
 * @since  1.6
 */
class AttlistModelMeldungen extends JModelList
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
				'id', 'a.`id`',
				'catid', 'a.`catid`',
				'state', 'a.`state`',
				'name', 'a.`name`',
				'present', 'a.`present`',
				'event_date', 'a.`event_date`',
				'event_title', 'a.`event_title`',
				'creation_date', 'a.`creation_date`',
				'created_by', 'a.`created_by`',
				'note', 'a.`note`',
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
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);
		// Filtering event_date
		$this->setState('filter.event_date.from', $app->getUserStateFromRequest($this->context.'.filter.event_date.from', 'filter_from_event_date', '', 'string'));
		$this->setState('filter.event_date.to', $app->getUserStateFromRequest($this->context.'.filter.event_date.to', 'filter_to_event_date', '', 'string'));

		// Filtering catid
		$this->setState('filter.catid', $app->getUserStateFromRequest($this->context.'.filter.catid', 'filter_catid', '', 'string'));


		// Load the parameters.
		$params = JComponentHelper::getParams('com_attlist');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.id', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return   string A store id.
	 *
	 * @since    1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

                
                    return parent::getStoreId($id);
                
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
                

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');
                

		// Filter by published state
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
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
		$filter_event_date_from = $this->state->get("filter.event_date.from");

		if ($filter_event_date_from !== null && !empty($filter_event_date_from))
		{
			$query->where("a.`event_date` >= '".$db->escape($filter_event_date_from)."'");
		}
		$filter_event_date_to = $this->state->get("filter.event_date.to");

		if ($filter_event_date_to !== null  && !empty($filter_event_date_to))
		{
			$query->where("a.`event_date` <= '".$db->escape($filter_event_date_to)."'");
		}

		// Filtering catid
		$filter_catid = $this->state->get("filter.catid");

		if ($filter_catid !== null && !empty($filter_catid))
		{
			$query->where("a.`catid` = '".$db->escape($filter_catid)."'");
		}
		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', "a.id");
		$orderDirn = $this->state->get('list.direction', "ASC");

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
                
		foreach ($items as $oneItem)
		{
					$oneItem->present = ($oneItem->present == '') ? '' : JText::_('COM_ATTLIST_OPTION_' . strtoupper($oneItem->present));

			if (isset($oneItem->catid))
			{
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);

				$query
					->select($db->quoteName('title'))
					->from($db->quoteName('#__categories'))
					->where('FIND_IN_SET(' . $db->quoteName('id') . ', ' . $db->quote($oneItem->catid) . ')');

				$db->setQuery($query);
				$result = $db->loadColumn();

				$oneItem->catid = !empty($result) ? implode(', ', $result) : '';
			}
		}

		return $items;
	}
}
