<div class="heading">
	<h3>Email Templates</h3>
</div>
<div class="row-fluid">
	<div class="span12">
		<div class="box">
			<div class="title">
				<h4 class="clearfix">
					<span class="left">Email Templates</span>
					<?php echo $this->Html->link('Add New Template', array('action' => 'add'), array('class' => 'btn btn-primary btn-mini right marginR5')); ?>
				</h4>
			</div>
			<div class="content">
				<div class="row-fluid">
					<div class="span12">
							<div>
								<div class="left" style="margin: 15px 0;">
									<?php echo $this->Paginator->counter('Page {:page} of {:pages}, showing {:current} records out of {:count} total'); ?>
								</div>
								<div class="dataTables_paginate paging_bootstrap pagination">
									<ul class="pagination">
										<?php
										echo $this->Paginator->prev('← Previous', array('tag' => 'li', 'class' => 'prev'), null, array('class' => 'disabledP', 'tag' => 'li'));
										echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li', 'currentClass' => 'active'));
										echo $this->Paginator->next('Next →', array('tag' => 'li', 'class' => 'next'), null, array('class' => 'disabledN', 'tag' => 'li'));
										?>
									</ul>
								</div>
							</div>
						<?php if (!empty($emailTemplatesArr)) { ?>
							<table cellpadding="0" cellspacing="0" border="0" class="responsive dynamicTable display table table-bordered" width="100%">
								<thead>
									<tr>
										<th>#</th>
										<th>Unique Key</th>
										<th>Subject</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$indx = (($this->Paginator->current() - 1) * PAGE_LIMIT) + 1;
									foreach ($emailTemplatesArr as $emailTemplateArr) {
										?>
										<tr>
											<td><?php echo $indx; ?></td>
											<td class="txtLeft"><?php echo $emailTemplateArr['Template']['key']; ?></td>
											<td class="txtLeft"><?php echo $emailTemplateArr['Template']['subject']; ?></td>
											<td>
												<?php echo $this->Html->link($this->Html->tag('span', null, array('class' => 'icon12 icon-eye-open')), array('action' => 'view', $emailTemplateArr['Template']['id']), array('escape' => false, 'title' => 'View details')); ?>
												<?php echo $this->Html->link($this->Html->tag('span', null, array('class' => 'icon12 icon-pencil')), array('action' => 'edit', $emailTemplateArr['Template']['id']), array('escape' => false, 'title' => 'Edit emailtemplates')); ?>
												<?php echo $this->Form->postLink($this->Html->tag('span', null, array('class' => 'con12 icon-trash')), array('action' => 'delete', $emailTemplateArr['Template']['id']), array('escape' => false, 'title' => 'Delete emailtemplates'), __('Are you sure you want to delete.?')); ?>
											</td>
										</tr>
										<?php
										$indx++;
									}
									?>
								</tbody>
							</table>
							<div>
								<div class="dataTables_paginate paging_bootstrap pagination">
									<ul class="pagination">
										<?php
										echo $this->Paginator->prev('← Previous', array('tag' => 'li', 'class' => 'prev'), null, array('class' => 'disabledP', 'tag' => 'li'));
										echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li', 'currentClass' => 'active'));
										echo $this->Paginator->next('Next →', array('tag' => 'li', 'class' => 'next'), null, array('class' => 'disabledN', 'tag' => 'li'));
										?>
									</ul>
								</div>
							</div>						
						<?php } else { ?>
							<div class="alert alert-info">
								There was no email templates found.
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div><!-- End .box -->
	</div><!-- End .span12 -->
</div><!-- End .row-fluid -->
<?php echo $this->Html->css('dataTables/jquery.dataTables.min', null, array('inline' => false)); ?>
<?php $this->Blocks->startIfEmpty('viewJavaScript'); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('.pagination ul li.active').html('<a>' + $('.pagination ul li.active').html() + '</a>');
        $('.pagination ul li.disabledP').html('<a>' + $('.pagination ul li.disabledP').html() + '</a>');
        $('.pagination ul li.disabledN').html('<a>' + $('.pagination ul li.disabledN').html() + '</a>');
        $('.pagination ul li.active').removeClass('disabled');
    });
</script>
<?php
echo $this->Html->script('responsive-tables/responsive-tables');
?>
<?php $this->Blocks->end(); ?>