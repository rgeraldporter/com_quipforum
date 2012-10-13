<?php
/*
*
*	Quip Forum for Joomla!
*	(c) 2010-2012 Robert Gerald Porter
*
*	Author: 	Robert Gerald Porter <rob@robporter.ca>
*	Version: 	0.3
*   License: 	GPL v3.0
*
*   This extension is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   This extension is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details <http://www.gnu.org/licenses/>.
*	
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.model');
jimport( 'joomla.access.access' );

class QuipForumModelList extends JModel
{

	public $data	= null;
	
	public function __construct()
	{
        
        parent::__construct();
 
        $mainframe = JFactory::getApplication();
        $option = JRequest::getCmd('option');
 
        $filter_order     = $mainframe->getUserStateFromRequest($option.'filter_order', 'filter_order', 'ordering', 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($option.'filter_order_Dir', 'filter_order_Dir', 'asc', 'word');
 
        $this->setState('filter_order', $filter_order);
        $this->setState('filter_order_Dir', $filter_order_Dir);
        
	}
	
	private function _buildContentOrderBy()
	{
    	
    	$mainframe = JFactory::getApplication();
    	$option = JRequest::getCmd('option');

            $orderby = '';
            $filter_order     = $this->getState('filter_order');
            $filter_order_Dir = $this->getState('filter_order_Dir');

            /* Error handling is never a bad thing*/
            if(!empty($filter_order) && !empty($filter_order_Dir) ){
                    $orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
            }

            return $orderby;
	}
	
	
	public function getBoardList()
	{
	
		$query = "SELECT * FROM #__quipforum_boards ".$this->_buildContentOrderBy();
		$this->data = $this->_getList($query);		

		return $this->data;
	
	}
	



}