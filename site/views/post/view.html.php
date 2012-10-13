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
require_once (JPATH_COMPONENT.DS.'helpers'.DS.'helper'.'.php');


class QuipForumViewPost extends JView 
{
    

        public $postData 	= null;
        public $userData	= null;
        public $adminOptions;
        public $jsonLog;
        public $adminLog;
        public $userAccessLevel		= 0;
        public $replyAllowed		= 0;

        public function display($tpl = null) 
        {
			
			
          	$this->postData	= $this->get('PostData');
          	$this->boardData = $this->get('BoardData');
          	$this->userAccessLevel = $this->get('UserAccessLevel');
          	
          	if(!$this->userAccessLevel)
          	{
          		return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
          	}
          	
          	$this->threadMarkup = $this->get('ThreadMarkup');

          	$document =& JFactory::getDocument();
          	
          	$document->addStyleSheet("components/com_quipforum/assets/css/board.css",'text/css',"screen");
          	$document->addStyleSheet("components/com_quipforum/assets/css/post.css",'text/css',"screen");
          	
  			$this->userData = JFactory::getUser();
  			
  			if($this->userData->id == $this->postData->user_id && $this->userData->id)
  				$this->adminOptions .= "<li><a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$this->postData->id.'&task=edit')."'>Edit Post</a></li>";
  			
  			
  			if($this->postData->id == $this->postData->thread_id && $this->userAccessLevel > 2 && $this->userData->id)
  				$this->adminOptions .= "<li><a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$this->postData->id.'&task=request_sticky')."'>Request Sticky</a></li>";
  			
  			
  			if ($this->userData->authorise('core.manage', 'com_quipforum') || $this->userAccessLevel > 3 ) {
  				
  				if($this->postData->id == $this->postData->thread_id)
  				{
  					
  					$this->adminOptions .= "<li><a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$this->postData->id.'&task=toggle_sticky')."'>Toggle Sticky</a></li>";
  					$this->adminOptions .= "<li><a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$this->postData->id.'&task=toggle_announcement')."'>Toggle Announcement</a></li>";
  					
  				}
  				
  				$this->adminOptions .= "<li><a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$this->postData->id.'&task=edit')."'>Edit Post (as Admin)</a></li>";
  				$this->adminOptions .= "<li><a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$this->postData->id.'&task=delete')."'>Delete Post</a></li>";
  				$this->adminOptions .= "<li><a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$this->postData->id.'&task=clear_all')."'>Clear All Flags</a></li>";
  				
  				$this->jsonLog = $this->get('JsonLog');
  	
  	        }
  	        
  	        if($this->userData->id)
  	        {
  	        
  	        	$userSettings = comQuipForumHelper::getUserSettings();
  	        	
  	        	if($userSettings->flags->flag_unread)
  	        	{
  	        		$this->get('PostRead');  	        	
  	        	}
  	        
	  	        $this->adminOptions .= "<li><a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$this->postData->thread_id.'&task=watch_thread&post_return_id='.$this->postData->id)."'>Toggle Thread Watch Status</a></li>";
	  	        $this->adminOptions .= "<li><a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$this->postData->id.'&task=favourite')."'>Toggle Favourite</a></li>";
	  	        $this->adminOptions .= "<li><a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$this->postData->id.'&task=off_topic')."'>Flag as Off-Topic</a></li>";
	  	        $this->adminOptions .= "<li><a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$this->postData->id.'&task=report_spam')."'>Report as Spam</a></li>";
	  	        $this->adminOptions .= "<li><a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$this->postData->id.'&task=report_offensive')."'>Report Offensive Material</a></li>";
	  	
	  		}
	  		
	  		if($this->userAccessLevel > 1)
	  		{
	  			$this->adminOptions .= " <li><a href='#reply'> Reply</a></li>";
	  			$this->replyAllowed = 1;
	  		}
	  			
	  		if($this->userAccessLevel > 3 || $this->userData->authorise('core.manage', 'com_quipforum'))
  				$this->adminLog = comQuipForumHelper::getLog($this->postData->id);
  	
            parent::display($tpl);
            
            
        }
}
