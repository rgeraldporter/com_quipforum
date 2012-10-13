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

<script type="text/javascript">
	function validateForm( frm ) {
		var valid = document.formvalidator.isValid(frm);
		if (valid == false) {
			// do field validation
			if (frm.name.invalid) {
				alert( "Please enter a user name" );
			} else if (frm.f_subject.invalid) {
				alert( "Please enter a subject summary" );
			}
			return false;
		} else {
			frm.submit();
		}
	}
</script>

<h1><? echo $this->boardData->topic; ?></h1>

<form action='<? echo JRoute::_('index.php'); ?>' class="form-validate" method='post' name='addpost' id='addpost'>
	<div id='forum-post-options-bar'>
	
	<div id='forum-post-username'>
	<fieldset>
	<legend>Name</legend>
	<input type='text' value='<?php echo $this->userData->name; ?>' name='user_alt_name' id='forum-name' class='validate-name' style='' />
	</fieldset>
	</div>
	
	</div>
	<div id='forum-post-textarea'>
	<fieldset>
	<legend>Subject &amp; Body</legend>
	<input type='text' name='subject' style='width:600px;' id='forum-subject' placeholder='Subject' class='validate-subject' maxlength='120' value='<?php echo $this->postData->subject; ?>' /><br />
<?
		$editor = "";
		$editor =& JFactory::getEditor();
		echo $editor->display('body', $this->postData->body, '600', '400', '60', '20', false);

?>
	<div id='qforum-submit-post-container'><button class="qforum-submit-post" type="submit">Submit Post</button></div>
	<input type='hidden' name='user_id' value='<?php echo $this->userData->id; ?>' />
	<input type='hidden' name='parent_id' value='<?php echo $this->postData->parent_id; ?>' />
	<input type='hidden' name='thread_id' value='<?php echo $this->postData->thread_id; ?>' />
	<input type='hidden' name='reference_key_id' value='<?php echo $this->postData->reference_key_id; ?>' />
	<input type='hidden' name='view' value='post' />
	<input type='hidden' name='task' value='save' />
	<input type='hidden' name='id' value='<?php echo $this->postData->id; ?>' />
	<input type='hidden' name='option' value='com_quipforum' />
	<input type='hidden' name='board_id' value='<?php echo $this->boardId; ?>' />
	<?php echo JHTML::_('form.token'); ?>
	</fieldset>
	</div>
</form>

