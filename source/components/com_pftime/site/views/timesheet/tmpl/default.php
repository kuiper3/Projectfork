<?php
/**
 * @package      Projectfork
 * @subpackage   Timetracking
 *
 * @author       Tobias Kuhn (eaxs)
 * @copyright    Copyright (C) 2006-2012 Tobias Kuhn. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl.html GNU/GPL, see LICENSE.txt
 */

defined('_JEXEC') or die();


JHtml::_('pfhtml.script.listform');

$list_order = $this->escape($this->state->get('list.ordering'));
$list_dir   = $this->escape($this->state->get('list.direction'));
$user       = JFactory::getUser();
$uid        = $user->get('id');

$list_total_time = 0;
$list_total_billable = 0.00;

// Calculate un/billable time percentage
$billable_percent   = ($this->total_time == 0) ? 0 : round($this->total_time_billable * (100 / $this->total_time));
$unbillable_percent = ($this->total_time == 0) ? 0 : round($this->total_time_unbillable * (100 / $this->total_time));

$filter_in = ($this->state->get('filter.isset') ? 'in ' : '');
?>
<div id="projectfork" class="category-list<?php echo $this->pageclass_sfx;?> view-timesheet">

    <?php if ($this->params->get('show_page_heading', 1)) : ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php endif; ?>

    <div class="clearfix"></div>

    <div class="cat-items">

        <form name="adminForm" id="adminForm" action="<?php echo JRoute::_(PFtimeHelperRoute::getTimesheetRoute()); ?>" method="post">
            <div class="btn-toolbar btn-toolbar-top">
                <?php echo $this->toolbar; ?>
                <div class="filter-project btn-group">
                    <?php echo JHtml::_('pfhtml.project.filter');?>
                </div>
            </div>

            <div class="clearfix"> </div>

            <div class="<?php echo $filter_in;?>collapse" id="filters">
                <div class="well btn-toolbar">
                    <div class="filter-search btn-group pull-left">
                        <input type="text" name="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
                    </div>
                    <div class="filter-search-buttons btn-group pull-left">
                        <button type="submit" class="btn" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                        <button type="button" class="btn" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
                    </div>

                    <div class="clearfix"> </div>
                    <hr />

                    <?php if ($this->access->get('time.edit.state') || $this->access->get('time.edit')) : ?>
                        <div class="filter-published btn-group">
                            <select name="filter_published" class="inputbox input-medium" onchange="this.form.submit()">
                                <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
                                <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <?php if (intval($this->state->get('filter.project')) > 0) : ?>
                        <div class="filter-author btn-group">
                            <select id="filter_author" name="filter_author" class="inputbox input-medium" onchange="this.form.submit()">
                                <option value=""><?php echo JText::_('JOPTION_SELECT_AUTHOR');?></option>
                                <?php echo JHtml::_('select.options', $this->authors, 'value', 'text', $this->state->get('filter.author'), true);?>
                            </select>
                        </div>
                        <div class="filter-task btn-group">
                            <select id="filter_task" name="filter_task" class="inputbox input-medium" onchange="this.form.submit()">
                                <option value=""><?php echo JText::_('COM_PROJECTFORK_OPTION_SELECT_TASK');?></option>
                                <?php echo JHtml::_('select.options', $this->tasks, 'value', 'text', $this->state->get('filter.task'), true);?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <div class="clearfix"> </div>
                </div>
            </div>

            <?php if (intval($this->state->get('filter.project')) > 0) : ?>
                <h3><?php echo PFApplicationHelper::getActiveProjectTitle();?></h3>

                <div class="row-fluid">
                	<div class="span3">
                		<div class="thumbnail thumbnail-timesheet">
                			<h6><?php echo JText::_('COM_PROJECTFORK_TIME_TRACKING_TOTAL_HOURS');?></h6>
                			<h1><?php echo JHtml::_('time.format', $this->total_time, 'decimal');?></h1>
                			<h5><?php echo JText::_('COM_PROJECTFORK_TIME_TRACKING_ESTIMATED');?> (<?php echo JHtml::_('time.format', $this->total_estimated_time, 'decimal');?>)</h5>
                		</div>
                	</div>
                	<div class="span6">
                		<div class="thumbnail thumbnail-timesheet">
                			<h6><?php echo JText::_('COM_PROJECTFORK_TIME_TRACKING_TOTAL_HOURS');?></h6>
                			<div class="row-fluid">
                				<div class="span6">
                					<div class="progress progress-success">
    									<div class="bar" style="width: <?php echo $billable_percent;?>%;"></div>
    								</div>
    								<div class="progress">
    									<div class="bar" style="width: <?php echo $unbillable_percent;?>%;"></div>
    								</div>
                				</div>
                				<div class="span6">
                					<h2>
                                        <?php echo JHtml::_('time.format', $this->total_time_billable, 'decimal');?>
                                        <span class="label label-success"><?php echo JText::_('COM_PROJECTFORK_TIME_TRACKING_BILLABLE');?></span>
                                    </h2>
                					<h2>
                                        <?php echo JHtml::_('time.format', $this->total_time_unbillable, 'decimal');?>
                                        <span class="label label-info"><?php echo JText::_('COM_PROJECTFORK_TIME_TRACKING_UNBILLABLE');?></span>
                                    </h2>
                				</div>
                			</div>
                		</div>
                	</div>
                	<div class="span3">
                		<div class="thumbnail thumbnail-timesheet">
                			<h6><?php echo JText::_('COM_PROJECTFORK_TIME_TRACKING_BILLABLE_TOTAL');?></h6>
                			<h2><?php echo JHtml::_('pfhtml.format.money', $this->total_billable);?></h2>
                			<h5><?php echo JText::_('COM_PROJECTFORK_TIME_TRACKING_ESTIMATED');?> (<?php echo JHtml::_('pfhtml.format.money', $this->total_estimated_cost);?>)</h5>
                		</div>
                	</div>
                </div>
            <?php endif; ?>

            <hr />

            <table class="table table-striped">
            	<thead>
            		<tr>
            			<th width="1%"></th>
                        <th><?php echo JText::_('JGRID_HEADING_TASK');?></th>
            			<th width="10%"><?php echo JText::_('COM_PROJECTFORK_TIME_TRACKING_TIME');?></th>
            			<th width="10%"></th>
            			<th width="10%"><?php echo JText::_('JGRID_HEADING_AUTHOR');?></th>
            			<th width="10%"><?php echo JText::_('JGRID_HEADING_DATE');?></th>
            			<th width="10%"><?php echo JText::_('COM_PROJECTFORK_TIME_TRACKING_RATE');?></th>
            			<th width="10%"><?php echo JText::_('COM_PROJECTFORK_TIME_TRACKING_BILLABLE');?></th>
            		</tr>
            	</thead>
            	<tbody>
			        <?php
			        $k = 0;
			        foreach($this->items AS $i => $item) :
			            $access = PFtimeHelper::getActions($item->id);

			            $can_create   = $access->get('core.create');
			            $can_edit     = $access->get('core.edit');
			            $can_change   = $access->get('core.edit.state');
			            $can_edit_own = ($access->get('core.edit.own') && $item->created_by == $uid);

                        if ($item->log_time > 0) {
                            $list_total_time += (int) $item->log_time;
                        }

                        if ((float) $item->billable_total > 0.00) {
                            $list_total_billable += (float) $item->billable_total;
                        }

                        $percentage = ($item->estimate == 0) ? 0 : round($item->log_time * (100 / $item->estimate));
                        $percentage_class = 'progress';

                        if ($percentage > 100) {
                            $percentage = 100;
                            $percentage_class .= ' progress-info';
                        }
                        else {
                            $percentage_class .= ($item->billable == 1) ? ' progress-success' : '';
                        }

                        $exists = ((int) $item->task_exists > 0);
			        ?>
			        <tr>
                        <td>
                            <?php echo JHtml::_('pf.html.id', $i, $item->id); ?>
			        		<?php
                            $this->menu->start(array('class' => 'btn-mini', 'pull' => 'right'));
                            $this->menu->itemEdit('form', $item->id, ($can_edit || $can_edit_own));
                            $this->menu->itemTrash('timesheet', $i, $can_change);
                            $this->menu->end();

                            echo $this->menu->render(array('class' => 'btn-mini'));
	                        ?>
			        	</td>
			        	<td>
                            <?php if ($exists) : ?>
                                <a href="<?php echo JRoute::_(PFtasksHelperRoute::getTaskRoute($item->task_slug, $item->project_slug, $item->milestone_slug, $item->list_slug));?>"
                                    rel="popover"
                                    title="<?php echo $this->escape($item->task_title); ?>"
                                    data-content="<?php echo $this->escape($item->description); ?>"
                                >
                                    <?php echo $this->escape($item->task_title); ?>
                                </a>
                            <?php else : ?>
                                <?php echo $this->escape($item->task_title); ?>
                            <?php endif; ?>
			        	</td>
			        	<td>
			        		<?php echo JHtml::_('time.format', $item->log_time); ?>
			        	</td>
			        	<td>
							<div class="<?php echo $percentage_class;?>">
								<div class="bar" style="width: <?php echo $percentage;?>%;"></div>
							</div>
			        	</td>
			        	<td>
			        		<?php echo $item->author_name; ?>
			        	</td>
			        	<td>
			        		<?php echo JHtml::_('date', $item->log_date, JText::_('DATE_FORMAT_LC4')); ?>
			        	</td>
			        	<td>
                            <?php echo JHtml::_('pfhtml.format.money', $item->rate);?>
			        	</td>
			        	<td>
                            <?php echo JHtml::_('pfhtml.format.money', $item->billable_total);?>
			        	</td>
			        </tr>

			        <?php
			        $k = 1 - $k;
			        endforeach;
			        ?>
            	</tbody>
            	<tfoot>
            		<tr>
            			<th><?php echo JText::_('COM_PROJECTFORK_TIME_TRACKING_TOTALS');?></th>
            			<th></th>
            			<th><?php echo JHtml::_('time.format', $list_total_time); ?></th>
            			<th></th>
            			<th></th>
            			<th></th>
	            		<th></th>
	            		<th><?php echo JHtml::_('pfhtml.format.money', $list_total_billable);?></th>
            		</tr>
            	</tfoot>
            </table>

            <div class="filters btn-toolbar">
                <div class="btn-group filter-order">
                    <select name="filter_order" class="inputbox input-medium" onchange="this.form.submit()">
                        <?php echo JHtml::_('select.options', $this->sort_options, 'value', 'text', $list_order, true);?>
                    </select>
                </div>
                <div class="btn-group folder-order-dir">
                    <select name="filter_order_Dir" class="inputbox input-medium" onchange="this.form.submit()">
                        <?php echo JHtml::_('select.options', $this->order_options, 'value', 'text', $list_dir, true);?>
                    </select>
                </div>
                <div class="btn-group display-limit">
                    <?php echo $this->pagination->getLimitBox(); ?>
                </div>
                <?php if ($this->pagination->get('pages.total') > 1) : ?>
                    <div class="btn-group pagination">
                        <p class="counter"><?php echo $this->pagination->getPagesCounter(); ?></p>
                        <?php echo $this->pagination->getPagesLinks(); ?>
                    </div>
                <?php endif; ?>
            </div>

            <input type="hidden" id="boxchecked" name="boxchecked" value="0" />
            <input type="hidden" name="task" value="" />
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
</div>
