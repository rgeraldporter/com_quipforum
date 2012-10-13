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
?>

<h1>Settings. These don't work. Yet.</h1>

<ul><li><a href='<?php echo JRoute::_('index.php?option=com_quipforum&view=board&id='.$this->boardId); ?>'>&gt; Return to the forum</a></li></ul>

<form action='<? echo JRoute::_('index.php'); ?>' method="post" name='settings' id='settings'>

<fieldset><legend>Post Prefix</legend>
<textarea type='text' name='post_prefix' class='qforum-settings-prefix'><?php echo $this->userSettings->post_prefix; ?></textarea>
</fieldset>
<fieldset>
<legend>Signature</legend>
<textarea type='text' name='post_sig' class='qforum-settings-suffix'><?php echo $this->userSettings->post_sig; ?></textarea>
</fieldset>
<fieldset>
<legend>Post Template</legend>
<textarea type='text' name='post_template' class='qforum-settings-template'><?php echo $this->userSettings->post_template; ?></textarea>
</fieldset>

<fieldset>
<legend>Profile Name Colours</legend>
<input type='text' name='colours' placeholder='colors go here' class='qforum-settings-user-colour' value='<?php echo $this->userSettings->colours; ?>' />
</fieldset>

<fieldset>
<legend>Avicon</legend>
<input type='text' name='icon' placeholder='icon file name' class='qforum-settings-user-icon' value='<?php echo $this->userSettings->icon; ?>' />
</fieldset>

<fieldset>
<legend>Ignore users</legend>
<input type='text' name='ignore_list' placeholder='list of user ids to ignore'  class='qforum-settings-user-ignore' value='<?php echo $this->userSettings->ignore_list; ?>' />
</fieldset>

<fieldset>
<legend>Worksafe Options</legend>
<input type='checkbox' name='no_smileys' id='no_smileys' <?php echo $this->checks->no_smileys; ?> /><label for='no_smileys'>Show smileys as text codes</label><br />
<input type='checkbox' name='no_icons' id='no_icons' <?php echo $this->checks->no_icons; ?> /><label for='no_icons'>Show icons as text codes</label><br />
<input type='checkbox' name='no_colours' id='no_colours' <?php echo $this->checks->no_colours; ?> /><label for='no_colours'>Supress username colours</label><br />
</fieldset>

<fieldset>
<legend>Notification Options</legend>
<input type='checkbox' name='flag_unread' id='flag_unread' <?php echo $this->checks->flag_unread; ?> /><label for='flag_unread'>Flag all unread new posts</label><br />
<input type='checkbox' name='email_me' id='email_me' <?php echo $this->checks->email_me; ?> /><label for='email_me'>Automatically check the "email me any replies" checkbox on all your posts</label><br />
</fieldset>

<fieldset>
<legend>Visual Options</legend>
<input type='checkbox' name='line' id='line' <?php echo $this->checks->line; ?> /><label for='line'>Place a line between each seperate discussion thread</label><br />
</fieldset>


<input type='hidden' name='option' value='com_quipforum' />
<input type='hidden' name='view' value='settings' />
<input type='hidden' name='task' value='save_settings' />

<div id='qforum-submit-post-container'><button class="qforum-submit-post" type="submit">Save Settings</button></div>

</form>