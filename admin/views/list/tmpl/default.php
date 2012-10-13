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
$option = JRequest::getCmd('option');
//$order = JHTML::_('grid.order', $i, $row->ordering);
?>

<form action='<?php echo JRoute::_( 'index.php' );?>' method='post' name='adminForm'>
<table class='adminlist'>
<thead>
<tr>
<th width='20'>
	<input type='checkbox' name='toggle' value='' onclick='checkAll(<?php echo count($this->rows); ?>)' />
</th>

<th class='title'><?php echo JHTML::_('grid.sort', JText::_('QFORUM_TOPIC'), 'topic', $this->lists['order_Dir'], $this->lists['order']); ?></th>
<th width='5%' nowrap='nowrap'><?php echo JText::_('QFORUM_TAG'); ?></th>
<th width='5%' nowrap='nowrap'><?php echo JHTML::_('grid.sort', JText::_('QFORUM_THREAD_COUNT'), 'thread_count', $this->lists['order_Dir'], $this->lists['order']); ?></th>
<th width='5%' nowrap='nowrap'><?php echo JHTML::_('grid.sort', JText::_('PUBLISHED'), 'published', $this->lists['order_Dir'], $this->lists['order']); ?></th>
<th width='5%' nowrap='nowrap'><?php echo JHTML::_('grid.sort', JText::_('ORDER'), 'ordering', $this->lists['order_Dir'], $this->lists['order']); ?></th>
<th width='5%' nowrap='nowrap'><?php echo JHTML::_('grid.sort', JText::_('ID'), 'id', $this->lists['order_Dir'], $this->lists['order']); ?></th>
</tr>
</thead>

<?php

jimport('joomla.filter.output');

$k = 0;

for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];
	$checked = JHTML::_('grid.id', $i, $row->id);
	$published = JHTML::_('grid.published', $row, $i);

	$link = JFilterOutput::ampReplace('index.php?option=' . $option . '&view=board&task=edit&cid[]='.$row->id);
	?>
	
	<tr class='<?php echo "row$k"; ?>'>
	<td>
		<?php echo $checked; ?>
	</td>
	
	<td>
		<a href='<?php echo $link; ?>'><?php echo $row->topic; ?></a> <span style='color:#888;'></span>
	</td>
	<td>
		 #<?php echo $row->tag; ?>
	</td>
	<td>
		 <?php echo $row->thread_count; ?>
	</td>
	
	<td align='center'>
		 <?php echo $published; ?>
	</td>
	
	<td>
		<?php echo $row->ordering;  ?>
	</td>
	<td align='center'>
		<?php echo $row->id; ?>
	</td>
	</tr>
	
	<?php
	$k = 1 - $k;
	
}
?>
</table>
<input type="hidden" name="option" value="<?php echo $option; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="list" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<?php echo JHTML::_('form.token'); ?>
</form>