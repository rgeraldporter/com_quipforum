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

jimport('joomla.application.component.modelitem');

require_once (JPATH_COMPONENT.DS.'helpers'.DS.'threadweaver'.'.php');
require_once (JPATH_COMPONENT.DS.'helpers'.DS.'boardinfo'.'.php');

class QuipForumModelPost extends JModelItem
{

    protected 	$boardThreads; 		
    protected 	$boardData; 
    public 		$postData;	
    public 		$threadData;	   
    public 		$threadMarkup;
    public 		$jsonLog;
    public 		$boardRead;

    
    public function getPostData()
    {
    	
    	$id = JRequest::getInt('id');
    	
    	return $this->getPostDataDB($id);
    
    }

	        
    protected function getPostDataDB($id = null)
    {
    		
    	if(!$id)
    		JError::raiseError('404', JText::_('Post not found'));
    			
		$start = "";
		$limit = "";
		$where = "#__quipforum_posts.id = '".$id."'";
		$order = "";
	
		$query = comQuipForumHelper::buildQuery
		(
			"SELECT #__quipforum_posts.id, ".
			"#__quipforum_posts.body, ".
			"#__quipforum_posts.subject, ".
			"#__quipforum_posts.bodyblob, ".
			"#__quipforum_posts.compressed, ".
			"#__quipforum_posts.thread_id, ".
			"#__quipforum_posts.user_id, ".
			"#__quipforum_posts.user_alt_name, ".
			"#__quipforum_posts.wysiwyg, ".
			"#__quipforum_posts.parent_id, ".
			"#__quipforum_posts.no_text, ".
			"#__quipforum_posts.ip_address, ".
			"#__quipforum_posts.thread_modified, ".
			"#__quipforum_posts.post_date ".
			"FROM #__quipforum_posts ",
			$start, $limit, $where, $order
		);
		
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$this->postData = $db->loadObject();
		
		// Now for the board ID code(s) #plural after singular works..
		$start = "";
		$limit = "";
		$where = "#__quipforum_post_references.id = '".$this->postData->thread_id."'";
		$order = "";
	
		$query = comQuipForumHelper::buildQuery
		(
			"SELECT #__quipforum_post_references.board_id ".
			"FROM #__quipforum_post_references ",
			$start, $limit, $where, $order
		);
		
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$this->postData->board_id = $db->loadObject()->board_id;		

    	return $this->postData;
    }
    
    public function getBoardRead()
    {
    
    	$board_id = $this->postData->board_id;
    	$userData = JFactory::getUser();
    	
		$where = "#__quipforum_boards_read.user_id = '".$userData->id."' AND #__quipforum_boards_read.board_id = '".$board_id."'";
		$order = "";
		$start = "";
		$limit = "";
	
		$query = comQuipForumHelper::buildQuery
		(
			"SELECT #__quipforum_boards_read.datetime ".
			"FROM #__quipforum_boards_read ",
			$start, $limit, $where, $order
		);

		$db = &JFactory::getDBO();
		$db->setQuery($query);
		
		$this->boardRead = @$db->loadObject()->datetime;
		
		if(!$this->boardRead)
			$this->boardRead = "2011-01-01 12:00:00"; // later set this to be automatically one month ago

    }

    
    public function getPostRead()
    {
    
    	$this->getBoardRead();
    	
    	if(strtotime($this->boardRead) > strtotime($this->postData->post_date))
    		return;
    		
    	$userData = JFactory::getUser();
    
		$start = "";
		$limit = "";
		$where = "#__quipforum_posts_read.user_id = '".$userData->id."'".
				 " AND #__quipforum_posts_read.post_id = '".JRequest::getInt('id')."' ";
		$order = "";
	
		$query = comQuipForumHelper::buildQuery
		(
			"SELECT #__quipforum_posts_read.id ".
			"FROM #__quipforum_posts_read ",
			$start, $limit, $where, $order
		);

		
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		
		if(!@$db->loadObjectList())
		{
	
			$start = "";
			$limit = "";
			$where = "";
			$order = "";
		
			$query = comQuipForumHelper::buildQuery
			(
				"INSERT INTO #__quipforum_posts_read ".
				"(post_id, user_id, datetime) ".
				"VALUES ('".JRequest::getInt('id')."', '".$userData->id."', '".comQuipForumHelper::sqlDateTime()."') ",
				$start, $limit, $where, $order
			);
			
			$db = &JFactory::getDBO();
			
			$db->setQuery($query);
			$result = @$db->loadObjectList(); // hmm need to figure out the warnings here
		}
		
    
    }
    
        
    public function getUserAccessLevel()
    {
		$board_id = $this->postData->board_id;
    	$userData = JFactory::getUser();
    	$userGroups = JAccess::getGroupsByUser($userData->id);
    	$boardAccessList = comQuipForumHelper::getboardAccessListDB($userGroups);
    	$highestAccessLevel = 0;
    	
    	foreach((array)$userGroups as $kgroup=>$vgroup)
    	{
    		if(@$boardAccessList[$board_id][$vgroup] > $highestAccessLevel)
    		{
    			$highestAccessLevel = $boardAccessList[$board_id][$vgroup];
    		}
    	}

    	return $highestAccessLevel;
    
    }
        


    public function getBoardData()
    {

    	return $this->getBoardDataDB($this->postData->board_id);
    
    }
    
	        
    protected function getBoardDataDB($id = 1)
    {
    
    	
    		
		$start = "";
		$limit = "";
		$where = "#__quipforum_boards.id = '".$id."'";
		$order = "";
	
		$query = comQuipForumHelper::buildQuery
		(
			"SELECT #__quipforum_boards.id, ".
			"#__quipforum_boards.topic, ".
			"#__quipforum_boards.tag, ".
			"#__quipforum_boards.description ".
			"FROM #__quipforum_boards ",
			$start, $limit, $where, $order
		);

		
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$this->boardData = $db->loadObject();
		
    	return $this->boardData;
    }
    
    
    
    public function getThreadMarkup()
    {
    	
    	$this->getThreadDB();
    	$this->buildThreads();
    	
    	return $this->threadMarkup;
    
    }
    
    protected function buildThreads()
    {
    
	    $threadWeaver = new QuipForumThreadWeaver;
			
	    	if(!@$this->threadData[0]->thread_cache)
	    	{
	    		$threadWeaver->weaveThread($this->postData->thread_id);
	    		$this->threadData[0]->jsonThreadCache = $threadWeaver->encoded;
	    		echo " <span>(Thread #".$this->postData->thread_id." was rebuilt.)</span> ";
	    		$threadWeaver->clearVars();
	    	}
	    	else
	    	{
	    	
	    		$this->threadData[0]->jsonThreadCache = gzinflate($this->threadData[0]->thread_cache);
	    	
	    	}
	    	
	    	$this->jsonLog .= $this->threadData[0]->jsonThreadCache;
	    	
	    	$threadPostData = json_decode($this->threadData[0]->jsonThreadCache);


	    	foreach((array)$threadPostData as $kkk=>$vvv)
	    	{
	    	
    			$linksMarkup = null;
	    			

				if(!$vvv->no_text)
				{
					$a_link = "<a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$vvv->id)."' class='qforum-board-subject'>";
					$a_close = "</a>";
				}
				else
				{
					$a_link = "<span class='qforum-board-subject-no-text'>";
					$a_close = "</span><span class='qforum-no-text'>NT</span><a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$vvv->id.'#reply')."' class='qforum-board-subject-reply'>reply</a>";
				}
				
						
				foreach((array)@$vvv->link_urls as $kl=>$vl)
				{
					$linksMarkup .= "<a href='".$vl->url."' target='_blank' class='qforum-post-external-link'>Link...</a>";
				}
	    	
	    		$this->threadMarkup .= 
	    		" <li id='qforum-thread-".$vvv->id."' class='qforum-author-".$vvv->user_id." qforum-thread-parent'>
	    			<div class='qforum-collapse-img'>".JHTML::_('image','components/com_quipforum/icons/16x16/bullet_arrow_down.png',null)."</div> ".$a_link.$vvv->subject.$a_close.$linksMarkup."
	    				<span class='qforum-posted-by'>posted by </span><span class='qforum-post-author'>".$vvv->user_alt_name."</span><span class='qforum-post-date'> on ".$vvv->post_date."</span>";
	    			

	    			
	    		
	    		# recursive function -- weaves children
	    		foreach((array)@$vvv->childReplies as $key=>$values)
	    		{	

	    			$this->threadMarkup .= 
	    				" <ul class='qf-subthread' id='qf-thread-parent-".$vvv->id."'>";
	    			$this->weaveChildren($values);
	    			$this->threadMarkup .= 
	    				" </ul>";
	    			
	    		}
	    		
	    		$this->threadMarkup .= "</li>";
	    	
	    	}	 
	    	
	    
	    
	    
	    
	}
	
	
	protected function weaveChildren($values)
	{
	
		$linksMarkup = null;
		
			
			if(!$values->no_text)
			{
				$a_link = "<a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$values->id)."' class='qforum-board-subject'>";
				$a_close = "</a>";
			}
			else
			{
				$a_link = "<span class='qforum-board-subject-no-text'>";
				$a_close = "</span><span class='qforum-no-text'>NT</span><a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$values->id.'#reply')."' class='qforum-board-subject-reply'>reply</a>";
			}
			
			if(@$values->childReplies)
			{
				$bullet = "<div class='qforum-collapse-img'>".JHTML::_('image','components/com_quipforum/icons/16x16/bullet_arrow_down.png',null)."</div>";
			}
			else
				$bullet = "<div class='qforum-subject-bullet'>".JHTML::_('image','components/com_quipforum/icons/16x16/bullet.png',null)."</div>";
				
			foreach((array)@$values->link_urls as $k=>$v)
			{
				$linksMarkup .= "<a href='".$v->url."' target='_blank' class='qforum-post-external-link'>Link...</a>";
			}
		
			$this->threadMarkup .= 
			" <li id='qforum-thread-".$values->id."' class='qforum-author-".$values->user_id."'>
			".$bullet.
				$a_link.$values->subject.$a_close.$linksMarkup."
					<span class='qforum-posted-by'>posted by </span><span class='qforum-post-author'>".$values->user_alt_name."</span><span class='qforum-post-date'> on ".$values->post_date."</span>";
					
			//$this->threadCount++;	
		
			foreach((array)@$values->childReplies as $k=>$v)
			{	
				$this->threadMarkup .= 
					" <ul class='qf-subthread' id='qf-thread-parent-".$values->id."'>";
				$this->weaveChildren($v);
				$this->threadMarkup .= 
					" </ul>";
				
			}	
			
	
	}
	
	
    protected function getThreadDB()
    {
			
		$query = comQuipForumHelper::buildQuery
		(
			"SELECT ".
			"#__quipforum_threads.thread_cache ".
			"FROM #__quipforum_threads ".
			"WHERE #__quipforum_threads.id = '".$this->postData->thread_id."'"
		);
			
	
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$this->threadData = $db->loadObjectList();

    }
    


}