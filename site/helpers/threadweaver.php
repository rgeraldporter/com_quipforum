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

class QuipForumThreadWeaver
{

    public 		$threadMarkup		; 		
    protected 	$replies 			= array();
    protected 	$threadPostData		;
    protected 	$threadCount		= 0;
    public 		$jsonThreads		= null;
    public		$encoded			= null;
    protected 	$encoded_gzip		= null;
    

   
	public function __construct() 
	{	
	} 
	
	public function weaveThread($id)
	{
	
		unset($this->jsonThreads);
	
		$this->threadMarkup = "";
	
		if(!$id)
		{
			JError::raiseWarning('Thread Weave Error', JText::_('Missing "id" for a requested thread weave.'));
			return;
		}
		
		$this->buildThreadMarkup($id);  
		//$this->threadMarkup = gzdeflate($this->threadMarkup, 6);
		
		$this->encoded = json_encode($this->jsonThreads);
		
		$this->encoded_gzip = gzdeflate($this->encoded, 6);
		
		$this->saveWovenThreadDB($id);	
		
		
	
	}
	
	public function clearVars()
	{
	
		unset($this->threadMarkup);
		unset($this->threadPostData);
		unset($this->threadCount);
		unset($this->replies);
	}
   
    
	protected function buildThreadMarkup($id)
	{

		$linksMarkup = null;

		
		$this->getThreadPostDataDB($id);
	
		# build our array of replies to build thread
		foreach((array)$this->threadPostData as $k => $v)
			$this->replies[$v->parent_id][] = $v;

		foreach((array)$this->replies[0] as $k=>$v)
		{

			$this->jsonThreads[$v->id] = $v;
			
			if($this->jsonThreads[$v->id]->links)
				$this->jsonThreads[$v->id]->link_urls = $this->getPostLinks($v->id);
			

			# recursive function -- weaves children
			if(array_key_exists($v->id, $this->replies))
			{	
				$this->jsonThreads[$v->id]->childReplies = $this->weaveChildren($v->id);
				
			}
		
		}	
	
	}
	
	protected function weaveChildren($post_id)
	{
	

		$childReplies = null;
	
		foreach((array)$this->replies[$post_id] as $k=>$v)
		{
		
		
			$childReplies[$v->id] = $v;
			
			if($childReplies[$v->id]->links)
				$childReplies[$v->id]->link_urls = $this->getPostLinks($v->id);

			if(array_key_exists($v->id, $this->replies))
			{	

				$childReplies[$v->id]->childReplies = $this->weaveChildren($v->id);

				
			}	
			
		
		}
		
		return $childReplies;
	
	}
	

    protected function saveWovenThreadDB($id)
    {
    		
		$start = "";
		$limit = "";
		$order = "";
		
		$db = &JFactory::getDBO();
		$now = JFactory::getDate();
		$mysql_datetime = $now->toMySQL(); 
		
		# existing threads
		if($this->checkThreadExist($id))
		{
		
			$where = "#__quipforum_threads.id = '".$id."'";
	
			$query = comQuipForumHelper::buildQuery
			(
				"UPDATE #__quipforum_threads ".
				"SET ".
				"#__quipforum_threads.thread_cache =  ".$db->quote($this->encoded_gzip)." ",
				$start, $limit, $where, $order
			);
			
		}
		# new threads
		else
		{
		
			$where = "";
		
			$query = comQuipForumHelper::buildQuery
			(
				"INSERT INTO #__quipforum_threads ".
				"(#__quipforum_threads.thread_cache) ".
				"VALUES ".
				"(".$db->quote($this->encoded_gzip).")",
				$start, $limit, $where, $order
			);		
						
		}
		
		$db->setQuery($query);
		$result = $db->query();


    }
    
    
    protected function checkThreadExist($id)
    {
	    
		$start = "";
		$limit = "";
		$where = "#__quipforum_threads.id = '".$id."'";
		$order = "";
		
		$db = &JFactory::getDBO();
	
		$query = comQuipForumHelper::buildQuery
		(
			"SELECT #__quipforum_threads.id ".
			"FROM ".
			"#__quipforum_threads ",
			$start, $limit, $where, $order
		);
		
		$db->setQuery($query);
		
		return $db->loadResult();
    	
    }
    
    protected function getThreadPostDataDB($id)
    {
    
   		$start = "";
   		$limit = "";
   		$where = "#__quipforum_posts.thread_id = '".$id."' AND #__quipforum_posts.trashed <> '1' ";
   		$order = "#__quipforum_posts.post_date DESC";
   	
   		$query = comQuipForumHelper::buildQuery
   		(
   			"SELECT #__quipforum_posts.id, ".
   			"#__quipforum_posts.user_id, ".
   			"#__quipforum_posts.parent_id, ".
   			"#__quipforum_posts.post_date, ".
   			"#__quipforum_posts.user_alt_name, ".
   			"#__quipforum_posts.no_text, ".
   			"#__quipforum_posts.links, ".
   			"#__quipforum_posts.subject ".
   			"FROM #__quipforum_posts",
   			$start, $limit, $where, $order
   		);
   		
   		$db = &JFactory::getDBO();
   		
   		$db->setQuery($query);
   		$this->threadPostData = $db->loadObjectList();
    
    }
    
    protected function getPostLinks($id)
    {
    
    	$linkMarkup = null;
    
    	$linkList = $this->getPostLinksDB($id);
    	
    	return $linkList;
    
    }
    
    
    protected function getPostLinksDB($id)
    {
	    
		$start = "";
		$limit = "";
		$where = "#__quipforum_links.post_id = '".$id."'";
		$order = "";
	
		$query = comQuipForumHelper::buildQuery
		(
			"SELECT #__quipforum_links.id, ".
			"#__quipforum_links.url ".
			"FROM #__quipforum_links",
			$start, $limit, $where, $order
		);
		
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		return $db->loadObjectList();    
    
    }
    

}