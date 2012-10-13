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

class QuipForumModelBoard extends JModelItem
{

    protected 	$boardThreads; 		
    protected 	$threadMarkup;
    protected 	$replies;
    public 		$jsonLog;
    protected	$preBoardData;
    public		$preBoardMarkup;
    public 		$boardId;
    public 		$postsRead;
    public 		$boardRead;

    
    public function getJsonLog()
    {
    
    	return $this->jsonLog;
    }
    
    public function getBoardThreads()
    {
    	
    	$board_id = JRequest::getInt('id');
    	$sort = JRequest::getVar('sort');
    	$page = JRequest::getInt('page');
    	
    	if($page < 1)
    		$page = 1;
    	
    	
    	$this->getBoardThreadsDB($sort, $board_id, $page);
    			
    	return $this->boardThreads;
    
    }
    
    public function getPostsRead()
    {
    
    	$board_id = JRequest::getInt('id');
    	$userData = JFactory::getUser();
    	
		$where = "#__quipforum_posts_read.user_id = '".$userData->id."' ";
		$order = "";
		$start = "";
		$limit = "";
	
		$query = comQuipForumHelper::buildQuery
		(
			"SELECT #__quipforum_posts_read.* ".
			"FROM #__quipforum_posts_read ",
			$start, $limit, $where, $order
		);

		$db = &JFactory::getDBO();
		$db->setQuery($query);
		
		foreach((array)@$db->loadObjectList() as $k=>$v)
		{
			$this->postsRead[$v->post_id] = 1;
		}
		// might want to LIMIT 2000 to this one later, or cross reference with post_ref

    }

    public function getBoardRead()
    {
    
    	$board_id = JRequest::getInt('id');
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

    public function getPostTrashDB()
    {
    
    	$board_id = JRequest::getInt('id');
    	
		$where = "#__quipforum_posts.trashed = '1' AND #__quipforum_post_references.board_id = '".$board_id."'";
		$order = "#__quipforum_posts.post_date DESC";
		$start = "";
		$limit = "";
	
		$query = comQuipForumHelper::buildQuery
		(
			"SELECT #__quipforum_posts.id, ".
			"#__quipforum_threads.thread_cache ".
			"FROM #__quipforum_posts ".
			"LEFT JOIN #__quipforum_threads ".
			"ON #__quipforum_posts.id = ".
			"#__quipforum_threads.id ".
			"LEFT JOIN #__quipforum_post_references 
			ON
				#__quipforum_posts.id =
				#__quipforum_post_references.id ",
			$start, $limit, $where, $order
		);



		$db = &JFactory::getDBO();
	
		$db->setQuery($query);
		
		return $db->loadObjectList();

    }
    
    public function getUserAccessLevel()
    {
    	$board_id = JRequest::getInt('id');
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
    
	        
    protected function getBoardThreadsDB($sort = "normal", $board_id = 1, $page = 1)
    {

		$limit = "50";
		$start = ($page * $limit) - $limit;

		
		switch($sort)
		{

			
			case "bump":
			
				$where = "#__quipforum_posts.parent_id = '0' AND #__quipforum_post_references.board_id = '".$board_id."' AND #__quipforum_posts.trashed <> '1' ";
				$order = "#__quipforum_posts.thread_modified DESC";
			
				$query = comQuipForumHelper::buildQuery
				(
					"SELECT #__quipforum_post_references.id, ".
					"#__quipforum_threads.thread_cache ".
					"FROM #__quipforum_post_references ".
					"LEFT JOIN #__quipforum_threads ".
					"ON #__quipforum_post_references.id = ".
					"#__quipforum_threads.id ".
					"LEFT JOIN #__quipforum_posts 
					ON
						#__quipforum_posts.id =
						#__quipforum_post_references.id ",
					$start, $limit, $where, $order
				);
			
			break;
			
			default:

				$where = "#__quipforum_posts.parent_id = '0' AND #__quipforum_post_references.board_id = '".$board_id."' AND #__quipforum_posts.trashed <> '1' ";
				$order = "#__quipforum_posts.post_date DESC";
			
				$query = comQuipForumHelper::buildQuery
				(
					"SELECT #__quipforum_post_references.id, ".
					"#__quipforum_threads.thread_cache ".
					"FROM #__quipforum_post_references ".
					"LEFT JOIN #__quipforum_threads ".
					"ON #__quipforum_post_references.id = ".
					"#__quipforum_threads.id ".
					"LEFT JOIN #__quipforum_posts 
					ON
						#__quipforum_posts.id =
						#__quipforum_post_references.id ",
					$start, $limit, $where, $order
				);
		
			break;
		
		}
	
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$this->boardThreads = $db->loadObjectList();

    }
    
    public function getThreadMarkup()
    {
    	
    	$this->buildBoardThreads();
    	
    	return $this->threadMarkup;
    
    }
    
    protected function buildBoardThreads()
    {
    
	    $threadWeaver = new QuipForumThreadWeaver;
	    $boardReadCSSClass = " qforum-post-new-flag";
	    $userData = JFactory::getUser();

	    
	    foreach($this->boardThreads as $k=>$v)
	    {
	    
	    	if(!$v->thread_cache)
	    	{
	    		$threadWeaver->weaveThread($v->id);
	    		$v->jsonThreadCache = $threadWeaver->encoded;
	    		echo " <span>(Thread #".$v->id." was rebuilt.)</span> ";
	    		$threadWeaver->clearVars();
	    	}
	    	else
	    	{
	    	
	    		$v->jsonThreadCache = gzinflate($v->thread_cache);
	    	
	    	}
	    	
	    	$this->jsonLog .= $v->jsonThreadCache;
	    	
	    	$threadPostData = json_decode($v->jsonThreadCache);


	    	foreach((array)$threadPostData as $kkk=>$vvv)
	    	{
	    	
    			$linksMarkup = null;
    			$boardReadClass = "";
    			
	    		if(	
	    			$this->boardRead && 
	    			(strtotime($this->boardRead) < strtotime($vvv->post_date)) &&
	    			(!@$this->postsRead[$vvv->id]) &&
	    			($vvv->user_id != $userData->id)
	    		  )
	    		{
	    			$boardReadClass = $boardReadCSSClass;
	    		}

				if(!$vvv->no_text)
				{
					$a_link = "<a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$vvv->id)."' class='qforum-board-subject".$boardReadClass."'>";
					$a_close = "</a>";
				}
				else
				{
					$a_link = "<span class='qforum-board-subject-no-text".$boardReadClass."'>";
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
	    	
	    	unset($this->replies);
	    	unset($threadPostData);
	    	unset($v->jsonThreadCache);
	    
	    	
	    
	    }
	    
	    
	}
	
	
	protected function weaveChildren($values)
	{
	
		$linksMarkup = null;
		$boardReadCSSClass = " qforum-post-new-flag";
		$boardReadClass = "";
		$userData = JFactory::getUser();
		
			
			if(	
				$this->boardRead && 
				(strtotime($this->boardRead) < strtotime($values->post_date)) &&
				(!@$this->postsRead[$values->id]) &&
				($values->user_id != $userData->id)
			  )
			{
				$boardReadClass = $boardReadCSSClass;
			}
			
			if(!$values->no_text)
			{
				$a_link = "<a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$values->id)."' class='qforum-board-subject".$boardReadClass."'>";
				$a_close = "</a>";
			}
			else
			{
				$a_link = "<span class='qforum-board-subject-no-text".$boardReadClass."'>";
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
	
	public function getPreBoardMarkup()
	{
	
		$this->getPreBoardDB();
		
		foreach((array)$this->preBoardData as $k=>$v)
		{
		
			switch($v->type)
			{
			
				case "sticky":
				
					if(!$v->no_text)
					{
						$a_link = "<a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$v->id)."' class='qforum-board-subject'>";
						$a_close = "</a>";
					}
					else
					{
						$a_link = "<span class='qforum-board-subject-no-text'>";
						$a_close = "</span><span class='qforum-no-text'>NT</span><a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$v->id.'#reply')."' class='qforum-board-subject-reply'>reply</a>";
					}
				
					$bullet = "<div class='qforum-collapse-img'>".JHTML::_('image','components/com_quipforum/icons/16x16/bullet_error.png',null)."</div>";
				
					$this->preBoardMarkup .=" <li id='qforum-thread-".$v->id."' class='qforum-author-".$v->user_id." qforum-thread-sticky'>".$bullet."<span class='qforum-thread-sticky-label'>Sticky: </span>".
						$a_link.$v->subject.$a_close.
						" <span class='qforum-posted-by'>posted by </span><span class='qforum-post-author'>".$v->user_alt_name."</span><span class='qforum-post-date'> on ".$v->post_date."</span></li>";
				
					break;
					
				case "announcement":
				
					if(!$v->no_text)
					{
						$a_link = "<a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$v->id)."' class='qforum-board-subject'>";
						$a_close = "</a>";
					}
					else
					{
						$a_link = "<span class='qforum-board-subject-no-text'>";
						$a_close = "</span><span class='qforum-no-text'>NT</span><a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$v->id.'#reply')."' class='qforum-board-subject-reply'>reply</a>";
					}
				
					$bullet = "<div class='qforum-collapse-img'>".JHTML::_('image','components/com_quipforum/icons/16x16/bullet_star.png',null)."</div>";
				
					$this->preBoardMarkup .=" <li id='qforum-thread-".$v->id."' class='qforum-author-".$v->user_id." qforum-thread-announcement'>".$bullet."<span class='qforum-thread-announcement-label'>Announcement: </span>".
						$a_link.$v->subject.$a_close.
						" <span class='qforum-posted-by'>posted by </span><span class='qforum-post-author'>".$v->user_alt_name."</span><span class='qforum-post-date'> on ".$v->post_date."</span></li>";
				
					break;
					
				case "report_spam":
				case "report_offensive":
				case "off_topic":
				
					if(!$v->no_text)
					{
						$a_link = "<a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$v->id)."' class='qforum-board-subject'>";
						$a_close = "</a>";
					}
					else
					{
						$a_link = "<span class='qforum-board-subject-no-text'>";
						$a_close = "</span><span class='qforum-no-text'>NT</span><a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$v->id.'#reply')."' class='qforum-board-subject-reply'>reply</a>";
					}
				
					$bullet = "<div class='qforum-collapse-img'>".JHTML::_('image','components/com_quipforum/icons/16x16/bullet_star.png',null)."</div>";
				
					$this->preBoardMarkup .=" <li id='qforum-thread-".$v->id."' class='qforum-author-".$v->user_id." qforum-thread-reported'>".$bullet."<span class='qforum-thread-reported-label'>Reported (".
						$v->type."): </span>".
						$a_link.$v->subject.$a_close.
						" <span class='qforum-posted-by'>posted by </span><span class='qforum-post-author'>".$v->user_alt_name."</span><span class='qforum-post-date'> on ".$v->post_date."</span></li>";
				
			
					break;
					
					
				case "request_sticky":
				
					if(!$v->no_text)
					{
						$a_link = "<a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$v->id)."' class='qforum-board-subject'>";
						$a_close = "</a>";
					}
					else
					{
						$a_link = "<span class='qforum-board-subject-no-text'>";
						$a_close = "</span><span class='qforum-no-text'>NT</span><a href='".JRoute::_('index.php?option=com_quipforum&view=post&id='.$v->id.'#reply')."' class='qforum-board-subject-reply'>reply</a>";
					}
				
					$bullet = "<div class='qforum-collapse-img'>".JHTML::_('image','components/com_quipforum/icons/16x16/bullet_star.png',null)."</div>";
				
					$this->preBoardMarkup .=" <li id='qforum-thread-".$v->id."' class='qforum-author-".$v->user_id." qforum-thread-sticky-request'>".$bullet."<span class='qforum-thread-sticky-request-label'>Sticky Requested by User: </span>".
						$a_link.$v->subject.$a_close.
						" <span class='qforum-posted-by'>posted by </span><span class='qforum-post-author'>".$v->user_alt_name."</span><span class='qforum-post-date'> on ".$v->post_date."</span></li>";
				
				
					break;
				
				default:
				
					break;
			
			}
		
		}
		
		
		return $this->preBoardMarkup;
	
	}
	
	protected function getPreBoardDB()
	{
	
		$admin_where = "";
		$userData = JFactory::getUser();
		
		if($userData->authorise('core.manage', 'com_quipforum'))
			$admin_where = "";
		else
			$admin_where = " AND (#__quipforum_flagged_posts.type = 'sticky' OR #__quipforum_flagged_posts.type = 'announcement') ";
	
		$start = "";
		$limit = "";
		$where = "#__quipforum_flagged_posts.board_id = '".JRequest::getInt('id')."'".$admin_where;
		$order = "";
	
		$query = comQuipForumHelper::buildQuery
		(
			"SELECT ".
			"#__quipforum_flagged_posts.*, ".
			"#__quipforum_posts.* ".
			"FROM #__quipforum_flagged_posts ".
			"LEFT JOIN #__quipforum_posts ".
			"ON #__quipforum_posts.id = ".
			"#__quipforum_flagged_posts.post_id ",
			$start, $limit, $where, $order
		);

		
	
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$this->preBoardData = $db->loadObjectList();
	
	}
    
    

}