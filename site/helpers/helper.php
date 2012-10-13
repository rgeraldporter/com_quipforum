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

#
# @static
# Our Helper class
#

class comQuipForumHelper
{

	public static function buildQuery($query, $start="", $limit="", $where="", $order="")
	{
	
		$query_lim = "";
		
		if($where)
			$query .= " WHERE ".$where;

		if($order)
			$query .= " ORDER BY ".$order." ";

		if($limit)
			$query_lim = " LIMIT ".$limit. " ";

		if($start && $limit)
			$query_lim = " LIMIT ".$start.", ".$limit." ";

			
		$query .= $query_lim;

		return $query;
		
	}
	
	
	public static function isArray($unknown)
	{
		
		if ( (array) $unknown !== $unknown ) 
		    return FALSE;
		else
		    return TRUE;
		
	}
	
	public static function getBoardAccessListDB($userGroups)
	{
	
		$boardAccessList = array();
		$start = "";
		$limit = "";
		$where = "#__quipforum_board_access.viewlevel_id IN (".implode(",",$userGroups).") ";
		$order = "";
	
		$query = comQuipForumHelper::buildQuery
		(
			"SELECT #__quipforum_board_access.* ".
			"FROM #__quipforum_board_access",
			$start, $limit, $where, $order
		);
		
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		
		foreach((array)$db->loadObjectList() as $k=>$v)
		{
			$boardAccessList[$v->board_id][$v->viewlevel_id] = $v->access_level;
		}
	
		return $boardAccessList;
	
	}
	
	
	        
    public static function getUserAccessLevel($board_id)
    {
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
	        
	
	public static function buildPageNav()
	{
	
		$page = JRequest::getInt('page');
		$sort = JRequest::getVar('sort');
		$id	= JRequest::getInt('id');
		$previousPageLink = "";
		$nextPageLink = "";
		
		
		if($page < 1 || !$page)
			$page = 1;
			
		$previousPage = $page - 1;
		$nextPage = $page + 1;
		$pageHTML = " (".$page.") ";
		
		if($previousPage)
			$previousPageLink = "<a href='".JRoute::_('index.php?option=com_quipforum&view=board&id='.$id.'&page='.$previousPage.'&sort='.$sort)."'> Previous Page</a>";
		
		$nextPageLink = "<a href='".JRoute::_('index.php?option=com_quipforum&view=board&id='.$id.'&page='.$nextPage.'&sort='.$sort)."'> Next Page</a>";

		return $previousPageLink.$pageHTML.$nextPageLink;
	
	}	
	
	public static function getUserSettings()
	{
	
		$userData = JFactory::getUser();
		$userSettings =& JTable::getInstance('usersettings', 'Table');
		$userSettings->load($userData->id);
		//$this->userSettings = $this->get('UserSettings');
		
		if(!$userSettings->flags)
			$userSettings->flags = new blankUserSettingsFlags;
		else
			$userSettings->flags = json_decode($userSettings->flags);
	
		return $userSettings;
	
	}


	public static function smilies($text)
	{
		$text = str_replace(":P", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/emoticon_tongue.png',':P')."</span>", $text);
		$text = str_replace(":p", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/emoticon_tongue.png',':p')."</span>", $text);
		$text = str_replace(":-P", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/emoticon_tongue.png',':-P')."</span>", $text);
		$text = str_replace(":)", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/emoticon_smile.png',':)')."</span>", $text);
		$text = str_replace(":-)", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/emoticon_smile.png',':-)')."</span>", $text);
		$text = str_replace(":(", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/emoticon_unhappy.png',':(')."</span>", $text);
		$text = str_replace(":-(", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/emoticon_unhappy.png',':-(')."</span>", $text);
        $text = str_replace("&gt;:D", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/emoticon_evilgrin.png','*evilgrin*')."</span>", $text);
        $text = str_replace(":D", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/emoticon_grin.png',':D')."</span>", $text);
        $text = str_replace(":-D", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/emoticon_grin.png',':-D')."</span>", $text);
        $text = str_replace(" 8D", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/emoticon_happy.png',' 8D')."</span>", $text);
        $text = str_replace(":O", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/emoticon_surprised.png',':O')."</span>", $text);
        $text = str_replace(":-O", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/emoticon_surprised.png',':-O')."</span>", $text);
        $text = str_replace(";)", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/emoticon_wink.png',';)')."</span>", $text);
        $text = str_replace(";-)", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/emoticon_wink.png',';-)')."</span>", $text);
        $text = str_replace("6-)&gt;", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/smiley3.png','*piratey*')."</span>", $text);
        $text = str_replace("6-)", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/smiley4.png','6-)')."</span>", $text);
        $text = str_replace("8)", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/nerd.jpg','8)')."</span>", $text);
      
      return $text;
	}
	


	public static function icons($text)
	{
	
      $text = str_replace("-bug-", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/bug.png','-bug-')."</span>", $text);
      $text = str_replace("*bug*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/bug.png','*bug*')."</span>", $text);
      $text = str_replace("*squish*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/bug_delete.png','*squish*')."</span>", $text);
      $text = str_replace("*music*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/music.png','*music*')."</span>", $text);
      $text = str_replace("POLL:", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/chart_bar.png','POLL: ')."</span>", $text);
      $text = str_replace("Poll:", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/chart_bar.png','Poll: ')."</span>", $text);
      $text = str_replace("*book*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/book.png','*book*')."</span>", $text);
      $text = str_replace("*film*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/film.png','*film*')."</span>", $text);
      $text = str_replace("*tv*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/television.png','*tv*')."</span>", $text);
      $text = str_replace("*album*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/cd.png','*album*')."</span>", $text);
      $text = str_replace("*game*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/controller.png','*game*')."</span>", $text);
      $text = str_replace("*drink*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/drink.png','*drink*')."</span>", $text);
      $text = str_replace("*idea*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/lightbulb.png','*idea*')."</span>", $text);
      $text = str_replace("*8ball*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/sport_8ball.png','*8ball*')."</span>", $text);
      $text = str_replace("*hivemind*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/tinyhive.png','*hivemind*')."</span>", $text);
	  $text = str_replace("*poop*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/icon_shit.gif','*poop*')."</span>", $text);
	  $text = str_replace("*heart*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/heart.png','*heart*')."</span>", $text);
	  $text = str_replace("*award*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/rosette.png','*award*')."</span>", $text);
	  $text = str_replace("*bomb*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/bomb.png','*bomb*')."</span>", $text);
	  $text = str_replace("*cake*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/cake.png','*cake*')."</span>", $text);
	  $text = str_replace("*coffee*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/cup.png','*coffee*')."</span>", $text);
	  $text = str_replace("*stop*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/stop.png','*stop*')."</span>", $text);
	  $text = str_replace("*shamrock*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/shamrock.gif', '*shamrock*')."</span>", $text);
	  $text = str_replace("*youtube*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/youtube.png','*youtube*')."</span>", $text);
      $text = str_replace("*suggestion*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/lightbulb.png','*suggestion*')."</span>", $text);
	  $text = str_replace("*photos*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/photos.png','*photos*')."</span>", $text);
	  $text = str_replace("*photo*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/photo.png','*photo*')."</span>", $text);
	  $text = str_replace("*redpill*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/pill.png','*redpill*')."</span>", $text);
	  $text = str_replace("*flickr*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/flickr.png','*flickr*')."</span>", $text);
	  $text = str_replace("*picasa*", "<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/picasa.png','*picasa*')."</span>", $text);
	  $text = str_replace("*help*","<span class='qforum-emoticon'>".JHTML::_('image','components/com_quipforum/icons/16x16/help.png','*help*')."</span>", $text);
	
	  return $text;
	
	}


	public static function parseUrlsFromText($text)
	{
		$urls = array();
		
		$text = ereg_replace( "www\.", "http://www.", $text );
		// eliminate duplicates after force
		$text = ereg_replace( "http://http://www\.", "http://www.", $text );
		$text = ereg_replace( "https://http://www\.", "https://www.", $text );
	
		$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
		
		preg_match_all($reg_exUrl, $text, $matches);
		$usedPatterns = array();
		
		foreach($matches[0] as $pattern){
		    if(!array_key_exists($pattern, $usedPatterns)){
		        $usedPatterns[$pattern]=true;
		        $urls[]=$pattern;
		    }
		}
		
		return $urls;
		
	}
	
	public static function sqlDateTime()
	{
	
		return date("Y-m-d H:i:s", time());
	
	}
	
	public static function logIt($text, $post_id)
	{
	
		$rowLog =& JTable::getInstance('postlogs','Table');	
		
		$rowLog->post_id = $post_id;
		$rowLog->log = $text;
		
		if(!$rowLog->store())
		{
			JError::raiseError(500, $rowLog->getError());
		}
	
	
	}
	
	public static function getLog($post_id)
	{
		
		$log = null;
		
		$start = "";
		$limit = "";
		$where = "post_id = '".$post_id."' ";
		$order = "";
	
		$query = comQuipForumHelper::buildQuery
		(
			"SELECT #__quipforum_post_logs.log ".
			"FROM #__quipforum_post_logs ",
			$start, $limit, $where, $order
		);
		
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$logs = $db->loadObjectList();
		
		foreach((array)$logs as $k=>$v)
		{
		
			$log .= " <li>".$v->log."</li>";
		
		}
		
		$log = "<h3>Post activity log</h3> <ul>".$log."</ul>";

		return $log;
	
	}
	
	
}