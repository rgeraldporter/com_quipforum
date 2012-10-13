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

jimport('joomla.application.component.controller');

require_once (JPATH_COMPONENT.DS.'helpers'.DS.'threadweaver'.'.php');
require_once (JPATH_COMPONENT.DS.'helpers'.DS.'boardinfo'.'.php');

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_quipforum'.DS.'tables');

class blankUserSettingsFlags
{

	public $no_smileys			= false;
	public $no_icons 			= false;
	public $no_colours			= false;
	public $flag_unread			= true;
	public $email_me			= false;
	public $line				= true;

}

class QuipForumController extends JController
{

	public function clear_unread()
	{
		
		$userData = JFactory::getUser();
		$userSettings = comQuipForumHelper::getUserSettings();
		$session =& JFactory::getSession();
		$boardId = $session->get('quipforum_board_id','0'); 

		
		if(!$userData->id || !$userSettings->flags->flag_unread) 
		{
			return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$query = 	
			"DELETE FROM #__quipforum_boards_read ".
			"WHERE #__quipforum_boards_read.user_id = '".$userData->id."' ".
			"AND #__quipforum_boards_read.board_id = '".$boardId."' ";
		
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		$query = 	
			"DELETE #__quipforum_posts_read.* FROM #__quipforum_posts_read ".
			"LEFT JOIN #__quipforum_post_references ".
			"ON #__quipforum_post_references.id = #__quipforum_posts_read.post_id ".
			"WHERE #__quipforum_post_references.board_id = '".$boardId."' ".
			"AND #__quipforum_posts_read.user_id = '".$userData->id."' ";
		
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		
		$query = 	
			"INSERT INTO #__quipforum_boards_read ".
			"(user_id, board_id, datetime) ".
			"VALUES ('".$userData->id."','".$boardId."', '".comQuipForumHelper::sqlDateTime()."' )";
		
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		$this->setRedirect(JRoute::_('index.php?option=com_quipforum&view=board&id='.$boardId),'Unread posts cleared.');
		
	}

	public function save_settings()
	{
	
		$userData = JFactory::getUser();
		
		if(!$userData->id) 
		{
			return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$userSettings =& JTable::getInstance('usersettings', 'Table');

		$userSettings->user_id = $userData->id;
		$userSettings->load();
		
		if(!$userSettings->flags)
			$new = 1;
		
		if(!$userSettings->bind(JRequest::get('post')))
		{
			JError::raiseError(500, $rowPost->getError());
		}
		
		$userSettings->flags = new blankUserSettingsFlags;
		
		$userSettings->flags->no_smileys = JRequest::getVar('no_smileys');
		$userSettings->flags->no_icons = JRequest::getVar('no_icons');
		$userSettings->flags->no_colours = JRequest::getVar('no_colours');
		$userSettings->flags->flag_unread = JRequest::getVar('flag_unread');
		$userSettings->flags->email_me = JRequest::getVar('email_me');
		$userSettings->flags->line = JRequest::getVar('line');
		
		$userSettings->flags = json_encode($userSettings->flags);
		

		if($new)
		{
			$db = &JFactory::getDBO();
	
			$query = 
				"INSERT INTO #__quipforum_user_settings ".
				"(post_prefix, post_sig, post_template, colours, icon, ignore_list, flags, user_id) ".
				"VALUES ('".$userSettings->post_prefix."','".$userSettings->post_sig."','".$userSettings->post_template."',".
				"'".$userSettings->colours."','".$userSettings->icon."','".$userSettings->ignore_list."','".$userSettings->flags."', '".$userData->id."') ";
				
			$db->setQuery($query);
			$db->loadObjectList();
		}
		else
		{
			if(!$userSettings->store())
			{
				JError::raiseError(500, $rowPost->getError());
			}
		}
	
		$this->setRedirect(JRoute::_('index.php?option=com_quipforum&view=settings'),'Settings were saved.');
	
	}

	public function clear_all()
	{

		$userData = JFactory::getUser();
		
		$session =& JFactory::getSession();
		
		$boardId = $session->get('quipforum_board_id','0'); 
		
		$userAccessLevel = comQuipForumHelper::getUserAccessLevel($boardId);
		
		if (!$userData->authorise('core.manage', 'com_quipforum') && $userAccessLevel < 4) {
		
			return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				
		}
	
		$option = JRequest::getCmd('option');
		$id = JRequest::getVar('id');
		$db = &JFactory::getDBO();

		$query = 
			"DELETE FROM #__quipforum_flagged_posts ".
			"WHERE post_id = '".$id."' ".
			"AND (type <> 'favourite' AND type <> 'watch_thread' AND type <> 'collapse') ";
			
		$db->setQuery($query);
		$db->loadObjectList();

		comQuipForumHelper::logIt(" <span class='qforum-log-clear-all'>All flags cleared by ".$userData->name."(".$userData->id.") at ".comQuipForumHelper::sqlDateTime().".</span>", $id);

		$this->setRedirect(JRoute::_('index.php?option='.$option.'&view=post&id='.$id),'All flags related to this post have been cleared.');
	
	
	}
	
		
	public function collapse()
	{


		$userData = JFactory::getUser();
		
		$option = JRequest::getCmd('option');
		$id = JRequest::getVar('id');
		
		$session =& JFactory::getSession();
		
		$boardId = $session->get('quipforum_board_id','0'); 
		
		$userAccessLevel = comQuipForumHelper::getUserAccessLevel($boardId);
		
		if ($userAccessLevel < 1) {
		
			return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				
		}
		
		$query = 	
			"SELECT #__quipforum_flagged_posts.* ".
			"FROM #__quipforum_flagged_posts ".
			"WHERE #__quipforum_flagged_posts.post_id = '".$id."' ".
			"AND #__quipforum_flagged_posts.type = 'collapse' ".
			"AND #__quipforum_flagged_posts.user_id = '".$userData->id."'";
		
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		if(!@$result[0])
		{
			$query = 
				"INSERT INTO #__quipforum_flagged_posts ".
				"(post_id, type, user_id) ".
				"VALUES ".
				"('".$id."', 'collapse', '".$userData->id."') ";
				
			$db->setQuery($query);
			$db->loadObjectList();
			$status = "collapsed";
		}
		else
		{
			$query = 
				"DELETE FROM #__quipforum_flagged_posts ".
				"WHERE ".
				"#__quipforum_flagged_posts.id = '".$result[0]->id."' ";
				
			$db->setQuery($query);
			$db->loadObjectList();
			$status = "uncollapsed";
		}
		
		comQuipForumHelper::logIt(" <span class='qforum-log-collapse'>".$userData->name."(".$userData->id.") has ".$status." this post (at ".comQuipForumHelper::sqlDateTime().").</span>", $id);

		$this->setRedirect(JRoute::_('index.php?option='.$option.'&view=board'),'Thread collapsed.');
		

	}
			
		
	public function favourite()
	{


		$userData 		= JFactory::getUser();	
		$option 		= JRequest::getCmd('option');
		$id 			= JRequest::getVar('id');
		$session 		=& JFactory::getSession();
		
		$boardId 			= $session->get('quipforum_board_id','0'); 
		$userAccessLevel 	= comQuipForumHelper::getUserAccessLevel($boardId);
		
		if ($userAccessLevel < 1) {
		
			return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				
		}
		
		$query = 	
			"SELECT #__quipforum_flagged_posts.* ".
			"FROM #__quipforum_flagged_posts ".
			"WHERE #__quipforum_flagged_posts.post_id = '".$id."' ".
			"AND #__quipforum_flagged_posts.type = 'favourite' ".
			"AND #__quipforum_flagged_posts.user_id = '".$userData->id."'";
		
		$db 		= &JFactory::getDBO();
		
		$db->setQuery($query);
		
		$result 	= $db->loadObjectList();
		
		if( !isset($result[0]) )
		{
		
			$query = 
				"INSERT INTO #__quipforum_flagged_posts ".
				"(post_id, type, user_id) ".
				"VALUES ".
				"('".$id."', 'favourite', '".$userData->id."') ";
				
			$db->setQuery($query);
			$db->loadObjectList();
			
			$status 	= "added this post to their favourites";
			
		}
		else
		{
		
			$query = 
				"DELETE FROM #__quipforum_flagged_posts ".
				"WHERE ".
				"#__quipforum_flagged_posts.id = '".$result[0]->id."' ";
				
			$db->setQuery($query);
			$db->loadObjectList();
			
			$status 	= "removed this post from their favorites";
			
		}
		
		comQuipForumHelper::logIt(" <span class='qforum-log-favourite'>".$userData->name."(".$userData->id.") has ".$status." (at ".comQuipForumHelper::sqlDateTime().").</span>", $id);

		$this->setRedirect(JRoute::_('index.php?option='.$option.'&view=post&id='.$id),'Thread favourite status toggled.');
		

	}
			
	
	
	public function watch_thread()
	{


		$userData = JFactory::getUser();
		
		$option = JRequest::getCmd('option');
		$id = JRequest::getVar('id');
		$post_return_id = JRequest::getVar('post_return_id');
		
		$session =& JFactory::getSession();
		
		$boardId = $session->get('quipforum_board_id','0'); 
		
		$userAccessLevel = comQuipForumHelper::getUserAccessLevel($boardId);
		
		if ($userAccessLevel < 1) {
		
			return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				
		}
		
		$query = 	
			"SELECT #__quipforum_flagged_posts.* ".
			"FROM #__quipforum_flagged_posts ".
			"WHERE #__quipforum_flagged_posts.post_id = '".$id."' ".
			"AND #__quipforum_flagged_posts.type = 'watch_thread' ".
			"AND #__quipforum_flagged_posts.user_id = '".$userData->id."'";
		
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		if(!@$result[0])
		{
			$query = 
				"INSERT INTO #__quipforum_flagged_posts ".
				"(post_id, type, user_id) ".
				"VALUES ".
				"('".$id."', 'watch_thread', '".$userData->id."') ";
				
			$db->setQuery($query);
			$db->loadObjectList();
			$status = "now watching";
		}
		else
		{
			$query = 
				"DELETE FROM #__quipforum_flagged_posts ".
				"WHERE ".
				"#__quipforum_flagged_posts.id = '".$result[0]->id."' ";
				
			$db->setQuery($query);
			$db->loadObjectList();
			$status = "no longer watching";
		}
		
		comQuipForumHelper::logIt(" <span class='qforum-log-watch'>".$userData->name."(".$userData->id.") is ".$status." this thread (at ".comQuipForumHelper::sqlDateTime().").</span>", $id);

		$this->setRedirect(JRoute::_('index.php?option='.$option.'&view=post&id='.$post_return_id),'Thread watch status toggled.');
		

	}
		

	public function off_topic()
	{

		$userData = JFactory::getUser();
	
		$option = JRequest::getCmd('option');
		$id = JRequest::getVar('id');
		$session =& JFactory::getSession();
		$db = &JFactory::getDBO();
		$boardId = $session->get('quipforum_board_id','0'); 
		
		$userAccessLevel = comQuipForumHelper::getUserAccessLevel($boardId);
		
		if ($userAccessLevel < 2) {
		
			return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				
		}
		
		$query = 	
			"SELECT #__quipforum_flagged_posts.* ".
			"FROM #__quipforum_flagged_posts ".
			"WHERE #__quipforum_flagged_posts.post_id = '".$id."' ".
			"AND #__quipforum_flagged_posts.type = 'off_topic' ".
			"AND #__quipforum_flagged_posts.board_id = '".$boardId."'";
		
		
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		if(@$result[0])
		{
			$query = 
				"UPDATE #__quipforum_flagged_posts ".
				"SET votes = votes + 1 ".
				"WHERE post_id = '".$id."' ";
				
			$db->setQuery($query);
			$db->loadObjectList();
		}
		else	
		{	
	
			$query = 
				"INSERT INTO #__quipforum_flagged_posts ".
				"(post_id, type, board_id) ".
				"VALUES ".
				"('".$id."', 'off_topic', '".$boardId."') ";
				
			$db->setQuery($query);
			$db->loadObjectList();
		}

		comQuipForumHelper::logIt(" <span class='qforum-log-off-topic'>Reported as off-topic by ".$userData->name."(".$userData->id.") at ".comQuipForumHelper::sqlDateTime().".</span>", $id);

		$this->setRedirect(JRoute::_('index.php?option='.$option.'&view=post&id='.$id),'Post has been reported as off-topic.');
	
	
	}
	
	public function report_offensive()
	{

		$userData = JFactory::getUser();
	
		$option = JRequest::getCmd('option');
		$id = JRequest::getVar('id');
		$session =& JFactory::getSession();
		$db = &JFactory::getDBO();
		$boardId = $session->get('quipforum_board_id','0'); 
		
		$userAccessLevel = comQuipForumHelper::getUserAccessLevel($boardId);
		
		if ($userAccessLevel < 2) {
		
			return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				
		}
		
		
		$query = 	
			"SELECT #__quipforum_flagged_posts.* ".
			"FROM #__quipforum_flagged_posts ".
			"WHERE #__quipforum_flagged_posts.post_id = '".$id."' ".
			"AND #__quipforum_flagged_posts.type = 'report_offensive' ".
			"AND #__quipforum_flagged_posts.board_id = '".$boardId."'";
		
		
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		if(@$result[0])
		{
			$query = 
				"UPDATE #__quipforum_flagged_posts ".
				"SET votes = votes + 1 ".
				"WHERE post_id = '".$id."' ";
				
			$db->setQuery($query);
			$db->loadObjectList();
		}
		else	
		{	
	
			$query = 
				"INSERT INTO #__quipforum_flagged_posts ".
				"(post_id, type, board_id) ".
				"VALUES ".
				"('".$id."', 'report_offensive', '".$boardId."') ";
				
			$db->setQuery($query);
			$db->loadObjectList();
			
		}


		comQuipForumHelper::logIt(" <span class='qforum-log-report-offensive'>Reported as offensive by ".$userData->name."(".$userData->id.") at ".comQuipForumHelper::sqlDateTime().".</span>", $id);

		$this->setRedirect(JRoute::_('index.php?option='.$option.'&view=post&id='.$id),'Post has been reported as offensive.');
	
	
	}
	
	

	public function report_spam()
	{

		$userData = JFactory::getUser();
	
		$option = JRequest::getCmd('option');
		$id = JRequest::getVar('id');
		$session =& JFactory::getSession();
		$db = &JFactory::getDBO();
		$boardId = $session->get('quipforum_board_id','0'); 
		
		$userAccessLevel = comQuipForumHelper::getUserAccessLevel($boardId);
		
		if ($userAccessLevel < 2) {
		
			return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				
		}
		
		$query = 	
			"SELECT #__quipforum_flagged_posts.* ".
			"FROM #__quipforum_flagged_posts ".
			"WHERE #__quipforum_flagged_posts.post_id = '".$id."' ".
			"AND #__quipforum_flagged_posts.type = 'report_spam' ".
			"AND #__quipforum_flagged_posts.board_id = '".$boardId."'";
		
		
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		if(@$result[0])
		{
			$query = 
				"UPDATE #__quipforum_flagged_posts ".
				"SET votes = votes + 1 ".
				"WHERE post_id = '".$id."' ";
				
			$db->setQuery($query);
			$db->loadObjectList();
		}
		else	
		{		
	
			$query = 
				"INSERT INTO #__quipforum_flagged_posts ".
				"(post_id, type, board_id) ".
				"VALUES ".
				"('".$id."', 'report_spam', '".$boardId."') ";
				
			$db->setQuery($query);
			$db->loadObjectList();
		}


		comQuipForumHelper::logIt(" <span class='qforum-log-report-spam'>Reported as spam by ".$userData->name."(".$userData->id.") at ".comQuipForumHelper::sqlDateTime().".</span>", $id);

		$this->setRedirect(JRoute::_('index.php?option='.$option.'&view=post&id='.$id),'Post has been reported as spam.');
	
	
	}


	public function request_sticky()
	{

		$userData = JFactory::getUser();
		
	
		$option = JRequest::getCmd('option');
		$id = JRequest::getVar('id');
		$session =& JFactory::getSession();
		
		$boardId = $session->get('quipforum_board_id','0'); 
		
		$userAccessLevel = comQuipForumHelper::getUserAccessLevel($boardId);
		
		if ($userAccessLevel < 2) {
		
			return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				
		}
		
		$db = &JFactory::getDBO();
		
		$query = 	
			"SELECT #__quipforum_flagged_posts.* ".
			"FROM #__quipforum_flagged_posts ".
			"WHERE #__quipforum_flagged_posts.post_id = '".$id."' ".
			"AND #__quipforum_flagged_posts.type = 'request_sticky' ".
			"AND #__quipforum_flagged_posts.board_id = '".$boardId."'";
		
		
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		if(@$result[0])
		{
			$query = 
				"UPDATE #__quipforum_flagged_posts ".
				"SET votes = votes + 1 ".
				"WHERE post_id = '".$id."' ";
				
			$db->setQuery($query);
			$db->loadObjectList();
		}
		else	
		{		
	
			$query = 
				"INSERT INTO #__quipforum_flagged_posts ".
				"(post_id, type, board_id) ".
				"VALUES ".
				"('".$id."', 'request_sticky', '".$boardId."') ";
				
			$db->setQuery($query);
			$db->loadObjectList();
		}


		comQuipForumHelper::logIt(" <span class='qforum-log-report-spam'>Requested for sticky by ".$userData->name."(".$userData->id.") at ".comQuipForumHelper::sqlDateTime().".</span>", $id);

		$this->setRedirect(JRoute::_('index.php?option='.$option.'&view=post&id='.$id),'Sticky status for post has been requested.');

	}

	public function toggle_sticky()
	{
	
		$userData = JFactory::getUser();
		$session =& JFactory::getSession();
		$boardId = $session->get('quipforum_board_id','0'); 
		
		$userAccessLevel = comQuipForumHelper::getUserAccessLevel($boardId);
		
		if (!$userData->authorise('core.manage', 'com_quipforum') && $userAccessLevel < 4) {
		
			return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				
		}
	
		$option = JRequest::getCmd('option');
		$id = JRequest::getVar('id');

		
		$query = 	
			"SELECT #__quipforum_flagged_posts.* ".
			"FROM #__quipforum_flagged_posts ".
			"WHERE #__quipforum_flagged_posts.post_id = '".$id."' ".
			"AND #__quipforum_flagged_posts.type = 'sticky' ".
			"AND #__quipforum_flagged_posts.board_id = '".$boardId."'";
		
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		if(!@$result[0])
		{
			$query = 
				"INSERT INTO #__quipforum_flagged_posts ".
				"(post_id, type, board_id) ".
				"VALUES ".
				"('".$id."', 'sticky', '".$boardId."') ";
				
			$db->setQuery($query);
			$db->loadObjectList();
		}
		else
		{
			$query = 
				"DELETE FROM #__quipforum_flagged_posts ".
				"WHERE ".
				"#__quipforum_flagged_posts.id = '".$result[0]->id."' ";
				
			$db->setQuery($query);
			$db->loadObjectList();
		}
		
		comQuipForumHelper::logIt(" <span class='qforum-log-sticky'>Toggled sticky status: ".$userData->name."(".$userData->id.") at ".comQuipForumHelper::sqlDateTime().".</span>", $id);

		$this->setRedirect(JRoute::_('index.php?option='.$option.'&view=post&id='.$id),'Sticky status has been toggled!');
		
	
	}
	
	public function toggle_announcement()
	{
	
		$userData = JFactory::getUser();
		
		$session =& JFactory::getSession();
		$boardId = $session->get('quipforum_board_id','0'); 
		
		$userAccessLevel = comQuipForumHelper::getUserAccessLevel($boardId);
		
		if (!$userData->authorise('core.manage', 'com_quipforum') && $userAccessLevel < 4) {
		
			return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				
		}
	
		$id = JRequest::getVar('id');
		
		$session =& JFactory::getSession();
		
		$query = 	
			"SELECT #__quipforum_flagged_posts.* ".
			"FROM #__quipforum_flagged_posts ".
			"WHERE #__quipforum_flagged_posts.post_id = '".$id."' ".
			"AND #__quipforum_flagged_posts.type = 'announcement' ".
			"AND #__quipforum_flagged_posts.board_id = '".$boardId."'";
		
		$db = &JFactory::getDBO();
		
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		if(!@$result[0])
		{
			$query = 
				"INSERT INTO #__quipforum_flagged_posts ".
				"(post_id, type, board_id) ".
				"VALUES ".
				"('".$id."', 'announcement', '".$boardId."') ";
				
			$db->setQuery($query);
			$db->loadObjectList();
		}
		else
		{
			$query = 
				"DELETE FROM #__quipforum_flagged_posts ".
				"WHERE ".
				"#__quipforum_flagged_posts.id = '".$result[0]->id."' ";
				
			$db->setQuery($query);
			$db->loadObjectList();
		}
		
		comQuipForumHelper::logIt(" <span class='qforum-log-announcement'>Toggled announcement status: ".$userData->name."(".$userData->id.") at ".comQuipForumHelper::sqlDateTime().".</span>", $id);

		$this->setRedirect(JRoute::_('index.php?option='.$option.'&view=post&id='.$id),'Announcement status has been toggled!');
		
	
	}
	
	public function edit()
	{
	
		JRequest::setVar('view', 'add');
		$this->display();
		
	}
	
	public function delete()
	{
	
		$userData = JFactory::getUser();
		$session =& JFactory::getSession();
		$boardId = $session->get('quipforum_board_id','0'); 
		
		$userAccessLevel = comQuipForumHelper::getUserAccessLevel($boardId);
		
		if (!$userData->authorise('core.manage', 'com_quipforum') && $userAccessLevel < 4) 
		{		
			return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));				
		}
		
		$id = JRequest::getVar('id');
		
		$db = &JFactory::getDBO();
		
		$rowPost =& JTable::getInstance('posts', 'Table');
		
		$rowPost->load($id);
		
		$thread_id = $rowPost->thread_id;
		
		$rowPost->trashed = 1;
			
		if(!$rowPost->store())
		{
			JError::raiseError(500, $rowPost->getError());
		}
		
		/*$query = 
			"DELETE FROM #__quipforum_posts ".
			"WHERE id = '".$id."' ";
			
		$db->setQuery($query);
		$db->loadObjectList();*/
		
		if($id == $thread_id)
		{
			
			/*$query = 
				"DELETE FROM #__quipforum_post_references ".
				"WHERE id = '".$id."' ";
				
			$db->setQuery($query);
			$db->loadObjectList();*/
			
			$query = 
				"DELETE FROM #__quipforum_threads ".
				"WHERE id = '".$id."' ";
				
			$db->setQuery($query);
			$db->loadObjectList();
		}
		else
		{
			
			$threadWeaver = new QuipForumThreadWeaver;
			$threadWeaver->weaveThread($thread_id);
		}

		$this->setRedirect(JRoute::_('index.php?option='.$option.'&view=board&id='.$boardId),'Post has been deleted.');
		
	}

	public function save()
	{
	
		$option = JRequest::getCmd('option');
		$userData = JFactory::getUser();
		$userAccessLevel = comQuipForumHelper::getUserAccessLevel(JRequest::getVar('board_id'));
		
		if($userAccessLevel < 2)
		{
			return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
		}
	
	
		JRequest::checkToken() or jexit('Invalid Token');
		
		$rowPost =& JTable::getInstance('posts', 'Table');
		
		if(!$rowPost->bind(JRequest::get('post')))
		{
			JError::raiseError(500, $rowPost->getError());
		}
		
		if($userAccessLevel == 2 && !$rowPost->thread_id)
		{
			return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$rowPost->ip_address = $_SERVER['REMOTE_ADDR'];
		$rowPost->post_date = comQuipForumHelper::sqlDateTime();
		
		if(!trim(strip_tags($rowPost->body)))
			$rowPost->no_text = 1;
		
		if(!$rowPost->id)
			$status = "created";
		else
			$status = "edited";
		
		if(!$rowPost->store())
		{
			JError::raiseError(500, $rowPost->getError());
		}
		
		$urls = array();
		
		if($urls = comQuipForumHelper::parseUrlsFromText($rowPost->body))
		{
			$noTextTester = $rowPost->body;
			foreach((array)$urls as $key=>$value)
			{
				$noTextTester = str_replace($value, "", $noTextTester);
				$rowLinks =& JTable::getInstance('links', 'Table');
				$rowLinks->url = $value;
				$rowLinks->post_id = $rowPost->id;
				$rowLinks->user_id = $rowPost->user_id;
				if(!$rowLinks->store())
				{
					JError::raiseError(500, $rowLinks->getError());
				}
				$rowPost->links++;
			} 
			
			if(!trim(strip_tags($noTextTester)))
				$rowPost->no_text = 1;
			
			# update with # of links
			if(!$rowPost->store())
			{
				JError::raiseError(500, $rowPost->getError());
			}
		}	
		
		
		if(!$parent_id = JRequest::getVar('parent_id'))
		{
			
			$rowPost->thread_id = $rowPost->id;	

			$rowPostRefs =& JTable::getInstance('postreferences','Table');			
			$rowPostRefs->load($rowPost->reference_key_id);
			$rowPostRefs->board_id = JRequest::getVar('board_id');
			$rowPostRefs->id = $rowPost->id;
			
			if(!$rowPostRefs->store())
			{
				JError::raiseError(500, $rowPostRefs->getError());
			}
			
			$rowPost->reference_key_id = $rowPostRefs->key_id; 
			
			if(!$rowPost->store())
			{
				JError::raiseError(500, $rowPost->getError());
			}
			
		}
		
		$threadWeaver = new QuipForumThreadWeaver;
		
		$threadWeaver->weaveThread($rowPost->thread_id);
		
		if($userData->id)
			comQuipForumHelper::logIt(" <span class='qforum-log-post-".$status."'>Post ".$status." by ".$userData->name."(".$userData->id.") at ".$rowPost->post_date.".</span>", $rowPost->id);
		else
			comQuipForumHelper::logIt(" <span class='qforum-log-post-".$status."'>Post ".$status." by ".$rowPost->user_alt_name."(guest from IP: ".$rowPost->ip_address.") at ".$rowPost->post_date.".</span>", $rowPost->id);
		
		$this->setRedirect(JRoute::_('index.php?option='.$option.'&view=post&id='.$rowPost->id),'Post saved, here it is!');
	
	}
	
}
