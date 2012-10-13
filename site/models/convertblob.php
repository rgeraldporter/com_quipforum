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

class QuipForumModelConvertblob extends JModelItem
{

	public $boardData;
	public $log;
	public $tableToConvert;

	public function getBlobConversion()
	{
	
		$task = JRequest::getVar('task');
		
		
		if($task == "threads")
		{
		
			$this->getThreadBlobConversionDB();
			$this->convertThreadStrings();
			$this->saveThreadBlobConversionDB();		
		
		}
		else if($task == "mergetables")
		{
			
			$this->getTableConversionDB();
			$this->convertRows();
			$this->saveTableConversionDB();	
		
		}
		else if($task == "links")
		{
			
			$this->getLinksConversionDB();
			$this->saveLinksConversionDB();	
		
		}
		else
		{
			$this->getBlobConversionDB();
			$this->convertLongStrings();
			$this->saveBlobConversionDB();
		}
		
		return $this->log;
	
	}
	


	
	protected function saveLinksConversionDB()
	{		
		$start = "";
		$limit = "";
		$order = "";
		
		$db = &JFactory::getDBO();
		
		# existing threads

		
		foreach($this->boardData as $k=>$v)
		{
		
			$where = "#__quipforum_posts.id = '".$v->post_id."'";
	
			$query = comQuipForumHelper::buildQuery
			(
				"UPDATE #__quipforum_posts ".
				"SET ".
				"#__quipforum_posts.links = '1'",
				$start, $limit, $where, $order
			);

			
			$db->setQuery($query);

			$result = $db->query();
			
			echo "Link from post #".$v->post_id." recorded.";
			
		}
			
	
		
	
	}
	

	protected function getLinksConversionDB()
	{
	
		$start = "";
		$limit = "";
		$where = "";
		$order = "";
	
		$query = comQuipForumHelper::buildQuery
		(
			"SELECT #__quipforum_links.url, ".
			"#__quipforum_links.post_id ".
			"FROM #__quipforum_links
				",
			$start, $limit, $where, $order
		);

		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$this->boardData = $db->loadObjectList();
		

	}


	
	protected function saveTableConversionDB()
	{		
		$start = "";
		$limit = "";
		$order = "";
		
		$db = &JFactory::getDBO();
		
		# existing threads

		
		foreach($this->boardData as $k=>$v)
		{
		
			$where = "#__quipforum_posts.id = '".$v->id."'";
	
			$query = comQuipForumHelper::buildQuery
			(
				"UPDATE #__quipforum_posts ".
				"SET ".
				"#__quipforum_posts.body = ".$db->quote($v->body)." , ".
				"#__quipforum_posts.subject = ".$db->quote($v->subject)." , ".
				"#__quipforum_posts.board_id = ".$db->quote($v->board_id)." , ".
				"#__quipforum_posts.bodyblob = ".$db->quote($v->bodyblob)." , ".
				"#__quipforum_posts.compressed = ".$db->quote($v->compressed)." , ".
				"#__quipforum_posts.converted =  1 ",
				$start, $limit, $where, $order
			);

			
			$db->setQuery($query);

			$result = $db->query();
			
		}
			
	
		
	
	}
	
	protected function convertRows()
	{
	
	
		foreach((array) $this->boardData as $k=>$v)
		{
		
			$this->boardData[$k]->body = $v->oldbody;
			$this->boardData[$k]->bodyblob = $v->oldbodyblob;
			$this->boardData[$k]->board_id = $v->oldboard_id;
			$this->boardData[$k]->compressed = $v->compressed;
			$this->boardData[$k]->subject = $v->oldsubject;
			
			$this->log .= " Entry #".$v->id." converted. <br />";
			
		}
		
	
	}
	
	

	protected function getTableConversionDB()
	{
	
		$start = "";
		$limit = "25000";
		$where = "#__quipforum_posts.converted = '0'";
		$order = "";
	
		$query = comQuipForumHelper::buildQuery
		(
			"SELECT #__quipforum_posts.*, ".
			"#__quipforum_posts_ref.board_id AS oldboard_id, ".
			"#__quipforum_post_text.body AS oldbody, ".
			"#__quipforum_post_text.subject AS oldsubject, ".
			"#__quipforum_post_text.compressed AS oldcompressed, ".
			"#__quipforum_post_text.bodyblob AS oldbodyblob ".
			"FROM #__quipforum_posts 
			LEFT JOIN
				#__quipforum_posts_ref 
			ON
				#__quipforum_posts_ref.id =
				#__quipforum_posts.id 
			LEFT JOIN
				#__quipforum_post_text 
			ON
				#__quipforum_post_text.id =
				#__quipforum_posts.id ",
			$start, $limit, $where, $order
		);

		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$this->boardData = $db->loadObjectList();
		

	}
	
	
	
	protected function saveThreadBlobConversionDB()
	{		
		$start = "";
		$limit = "";
		$order = "";
		
		$db = &JFactory::getDBO();
		
		# existing threads

		
		foreach($this->boardData as $k=>$v)
		{
		
			$where = "#__quipforum_post_threads.id = '".$v->id."'";
	
			$query = comQuipForumHelper::buildQuery
			(
				"UPDATE #__quipforum_post_threads ".
				"SET ".
				"#__quipforum_post_threads.thread_cache = ".$db->quote($v->thread_cache)." , ".
				"#__quipforum_post_threads.threadblob = ".$db->quote($v->threadblob)." , ".
				"#__quipforum_post_threads.converted =  ".$db->quote($v->converted)." ",
				$start, $limit, $where, $order
			);

			
			$db->setQuery($query);

			$result = $db->query();
			
		}
			
	}
	
	protected function convertThreadStrings()
	{
	
		foreach((array)$this->boardData as $k=>$v)
		{
		
			if($v->thread_cache)
				$this->boardData[$k]->threadblob = gzdeflate($v->thread_cache,6);
				
			$this->boardData[$k]->converted = 1;
			$this->boardData[$k]->thread_cache = "";
			
			if($this->boardData[$k]->threadblob)
				$this->log .= " Thread ".$v->id." compressed. <br />";
			else
				$this->log .= " Thread ".$v->id." was empty. <br />";
		
		}
		
		//echo $this->log;
		//die();
	
	}

	protected function getThreadBlobConversionDB()
	{
	
		$start = "";
		$limit = "7500";
		$where = "#__quipforum_post_threads.converted = '0'";
		$order = "";
	
		$query = comQuipForumHelper::buildQuery
		(
			"SELECT #__quipforum_post_threads.thread_cache, ".
			"#__quipforum_post_threads.threadblob, ".
			"#__quipforum_post_threads.converted, ".
			"#__quipforum_post_threads.id ".
			"FROM #__quipforum_post_threads ",
			$start, $limit, $where, $order
		);

		
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$this->boardData = $db->loadObjectList();
		

	}
	
	
	
	protected function saveBlobConversionDB()
	{		
		$start = "";
		$limit = "";
		$order = "";
		
		$db = &JFactory::getDBO();
		
		# existing threads

		
		foreach($this->boardData as $k=>$v)
		{
		
			$where = "#__text_duplicate.id = '".$v->id."'";
	
			$query = comQuipForumHelper::buildQuery
			(
				"UPDATE #__text_duplicate ".
				"SET ".
				"#__text_duplicate.body = ".$db->quote($v->body)." , ".
				"#__text_duplicate.bodyblob = ".$db->quote($v->bodyblob)." , ".
				"#__text_duplicate.checked =  ".$db->quote($v->checked).", ".
				"#__text_duplicate.compressed =  ".$db->quote($v->compressed)." ",
				$start, $limit, $where, $order
			);

			
			$db->setQuery($query);
			$result = $db->query();
			
		}
			
	}
	
	protected function convertLongStrings()
	{
	
		foreach((array)$this->boardData as $k=>$v)
		{
		
			if(strlen($v->body) > 256)
			{
				$this->boardData[$k]->bodyblob = gzdeflate($v->body,6);
				$this->boardData[$k]->compressed = 1;
				$this->boardData[$k]->body = "";
			}
				
			$this->boardData[$k]->checked = 1;
			
			if($this->boardData[$k]->bodyblob)
				$this->log .= " String ".$v->id." compressed. <br />";
			else
				$this->log .= " String ".$v->id." retained. <br />";
		
		}
	
	}

	protected function getBlobConversionDB()
	{
	
		$start = "";
		$limit = "15000";
		$where = "#__text_duplicate.checked = '0'";
		$order = "";
	
		$query = comQuipForumHelper::buildQuery
		(
			"SELECT #__text_duplicate.body, ".
			"#__text_duplicate.id, ".
			"#__text_duplicate.checked, ".
			"#__text_duplicate.compressed, ".
			"#__text_duplicate.bodyblob ".
			"FROM #__text_duplicate ",
			$start, $limit, $where, $order
		);

		
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$this->boardData = $db->loadObjectList();
		

	}

}