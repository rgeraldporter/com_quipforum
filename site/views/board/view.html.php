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

jimport('joomla.application.component.view');

require_once (JPATH_COMPONENT.DS.'helpers'.DS.'threadweaver'.'.php');
require_once (JPATH_COMPONENT.DS.'helpers'.DS.'boardinfo'.'.php');



class QuipForumViewBoard extends JView 
{
    

        public $boardThreads 	= null;
        public $boardId			= 1;
        public $pageNav			= null;
        public $board			= null;
        public $threadMarkup;
        public $jsonLog;
        public $preBoardMarkup;
        public $adminOptions;
        public $userPanel;
        public $userData;
        public $adminPostTrash;
        public $userAccessLevel;
        public $userOptions;
        public $settingsOptions;
        public $postReadClearOptions;
        

        public function display($tpl = null) 
        {
					
			$this->board = new QuipForumBoardInfo;
			
			$session =& JFactory::getSession();
			
			if(!$this->boardId = JRequest::getInt('id'))
				$this->boardId = $session->get('quipforum_board_id','1'); # will need to change to first available published forum. Redirect??
			
			$this->userAccessLevel = $this->get('UserAccessLevel');
			$this->userData = JFactory::getUser();
			
			if(!$this->userAccessLevel)
			{
				return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			}
			
			if(!$this->board->data[$this->boardId]->published)
			{
				return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			}
			
			if($this->userData->id)
			{
			
				$this->userPanel = "Welcome back, ".$this->userData->name;
				$this->settingsOptions = " <div class='qforum-index-settings'>
				<a href='".JRoute::_('index.php?option=com_quipforum&view=settings')."'><s> Settings</s></a>
				</div>";
				
				$userSettings = comQuipForumHelper::getUserSettings();
				
				if($userSettings->flags->flag_unread)
				{
					
					$this->get('BoardRead');
					$this->get('PostsRead');
					
					$this->postReadClearOptions = " <div class='qforum-board-clear-unread'>
						<a href='".JRoute::_('index.php?option=com_quipforum&view=board&task=clear_unread')."'>Clear All Unread Flags</a>
						</div>";
				}
			
			}
			
			$this->preBoardMarkup = $this->get('PreBoardMarkup');
			
          	$this->boardThreads	= $this->get('BoardThreads');
          	$this->threadMarkup = $this->get('ThreadMarkup');
          	


			$this->pageNav = comQuipForumHelper::buildPageNav();
			
			$session->set( 'quipforum_board_id', $this->boardId );
			
			$document =& JFactory::getDocument();
			$document->addStyleSheet("components/com_quipforum/assets/css/board.css",'text/css',"screen");
			
			$this->threadMarkup = comQuipForumHelper::icons($this->threadMarkup);
			$this->threadMarkup = comQuipForumHelper::smilies($this->threadMarkup);


			
			if ($this->userData->authorise('core.manage', 'com_quipforum') || $this->userAccessLevel > 3) {
				
				$this->adminOptions = "";
				
				// takes a long time for some reason.
				/*if($this->adminPostTrash = $this->get('PostTrashDB'))
				{
				
					$this->adminOptions .= "There are deleted posts in the trash. Empty it.";
				}*/
				
				$this->jsonLog = $this->get('JsonLog');
	
	        }
	        
	        switch($this->userAccessLevel)
	        {
	        
	        	case 1: 
	        		
	        		$this->postOptions = " <div class='qforum-add-post'>
	        		Cannot Add New Post
	        		</div>";
	        	
	        	break;
	        	
	        	case 2:
	        	case 3:
	        	case 4:
	        	
	        		$this->postOptions = " <div class='qforum-add-post'>
	        		<a href='".JRoute::_('index.php?option=com_quipforum&view=add')."'>New Post</a>
	        		</div>";
	        	
	        
	        }
			

            parent::display($tpl);
            
        }
}
