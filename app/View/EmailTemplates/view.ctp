<div class="heading">
	<h3>Template</h3>
</div>
<div class="row-fluid">
	<div class="span12">
		<div class="right">
			<?php echo $this->Html->link("Back",array("action" => 'index'),array('class' => 'btn btn-info')); ?>
			<?php echo $this->Html->link("Edit",array("action" => 'edit', $templateData['Template']['id']),array('class' => 'btn btn-info')); ?>
			<?php echo $this->Form->postLink('Delete', array('action' => 'delete', $templateData['Template']['id']), array('title' => 'Delete this user', 'class' => 'btn btn-danger'), __('Are you sure you want to delete %s ?', $templateData['Template']['subject'])); ?>
		</div>
	</div>
	<div class="clearfix">&nbsp;</div>
</div>
<div class="row-fluid">
	<div class="span12">
		<div class="box">
			<div class="title">
				<h4 class="clearfix">
					<span>Template Details</span>
				</h4>
			</div>
			<div class="content seperator">
				<div class="form-row row-fluid">
					<div class="span12">
						<div class="row-fluid">
							<label class="span2 gray">Key</label>
							<div class="span8">
								<?php echo!empty($templateData['Template']['key']) ? $templateData['Template']['key'] : '--'; ?>
							</div>
						</div>
					</div>
				</div>
				
				<div class="form-row row-fluid">
					<div class="span12">
						<div class="row-fluid">
							<label class="span2 gray">Title</label>
							<div class="span8">
								<?php echo!empty($templateData['Template']['subject']) ? $templateData['Template']['subject'] : '--'; ?>
							</div>
						</div>
					</div>
				</div>
				
				<div class="form-row row-fluid">
					<div class="span12">
						<div class="row-fluid">
							<label class="span2 gray">Content</label>
							<div class="span8">
								<?php echo !empty($templateData['Template']['content']) ? $templateData['Template']['content'] : '--'; ?>
							</div>
						</div>
					</div>
				</div>
				
				<div class="form-row row-fluid">
					<div class="span12">
						<div class="row-fluid">
							<label class="span2 gray">Tags</label>
							<div class="span8">
								<?php echo !empty($templateData['Template']['tags']) ? $templateData['Template']['tags'] : '--'; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- End .box -->
	</div><!-- End .span6 -->
</div><!-- End .row-fluid -->
<?php $this->Blocks->startIfEmpty('viewJavaScript'); ?>
<script type="text/javascript">
	$(document).ready(function() {
		$('.fancybox').fancybox();
		$(".fancybox-effects-a").fancybox({
			helpers: {
				title : {
					type : 'outside'
				},
				overlay : {
					speedOut : 0
				}
			}
		});
	});
</script>

<style type="text/css">
	.fancybox-custom .fancybox-skin {
		box-shadow: 0 0 50px #222;
	}
	.image-list a {
	padding: 5px;
	}
</style>

<?php $this->Blocks->end(); ?>