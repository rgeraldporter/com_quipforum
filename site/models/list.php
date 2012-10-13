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

jimport('joomla.application.component.helper');
jimport('joomla.application.component.modelitem');
jimport( 'joomla.access.access' );

class QuipForumModelList extends JModelItem
{

        protected 	$forumObjectList; 		
		public 		$userData;
		protected 	$userGroups;
		protected 	$boardAccessList;
        
        public function getForumObjectList()
        {
        
        	$this->userData = JFactory::getUser();
        	$this->userGroups = JAccess::getGroupsByUser($this->userData->id);
        	
        	$this->getForumObjectListDB();
        	$this->boardAccessList = comQuipForumHelper::getBoardAccessListDB($this->userGroups);
        	
        	foreach((array)$this->forumObjectList as $kboard=>$vboard)
        	{
      			$readAccess = 0;
        		foreach((array)$this->userGroups as $kgroup=>$vgroup)
        		{
        			
        			if(@$this->boardAccessList[$vboard->id][$vgroup] > 0)
        				$readAccess = 1;
        		}
        		if(!$readAccess)
        			unset($this->forumObjectList[$kboard]);
        	}
        	
			return $this->forumObjectList;
        
        }
       
        
        protected function getForumObjectListDB($order = null)
        {
        		
        		$start = "";
        		$limit = "";
        		$where = "published = '1' ";
        
        		if($order == null)
        			$order = "#__quipforum_boards.ordering";
        	
        		$query = comQuipForumHelper::buildQuery
        		(
        			"SELECT #__quipforum_boards.id, ".
        			"#__quipforum_boards.topic, ".
        			"#__quipforum_boards.description, ".
        			"#__quipforum_boards.tag, ".
        			"#__quipforum_boards.thread_count ".
        			"FROM #__quipforum_boards",
        			$start, $limit, $where, $order
        		);
        		
        		$db = &JFactory::getDBO();
        		
        		$db->setQuery($query);
        		$this->forumObjectList = $db->loadObjectList();
        		

        	
        }
}
