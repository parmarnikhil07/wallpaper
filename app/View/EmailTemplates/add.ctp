<div class="heading">
	<h3>Add New Template</h3>
</div>
<div class="row-fluid">
	<div class="span12">
		<div class="box">
			<div class="title">
				<h4 class="clearfix">
					<span>Add Template</span>
				</h4>
			</div>
			<div class="content">
				<?php echo $this->Form->create('addEmailTemplate', array('url' => array('controller' => 'emailTemplates', 'action' => 'add'), 'type' => 'file', 'class' => 'form-horizontal', 'id' => 'addEmailForm')); ?>
				<div class="form-row row-fluid">
					<?php echo $this->Form->input('Template.key', array('label' => array('text' =>'Unique Key', 'class' => 'form-label span4'), 'div' => array('class' => 'span8'), 'class' => "span6 controls")); ?>
				</div>
				<div class="form-row row-fluid">
					<?php echo $this->Form->input('Template.subject', array('label' => array('text' =>'Subject', 'class' => 'form-label span4'), 'div' => array('class' => 'span8'), 'class' => "span6 controls")); ?>
				</div>
				<div class="form-row row-fluid">
					<?php echo $this->Form->input('Template.content', array('label' => array('text' =>'Contents', 'class' => 'form-label span4'),'type' => 'textarea' , 'div' => array('class' => 'span8'), 'class' => "span6 controls")); ?>
				</div>
				<div class="form-row row-fluid">
					<?php echo $this->Form->input('Template.tags', array('label' => array('text' =>'Tags', 'class' => 'form-label span4'), 'div' => array('class' => 'span8'), 'class' => "span6 controls")); ?>
				</div>
				<div class="form-actions">
					<?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-info')); ?>
					<?php echo $this->Html->link('Cancel', array('action' => 'index'), array('class' => 'btn')); ?>
				</div>
				<?php echo $this->Form->end(); ?>
			</div>
		</div><!-- End .box -->
	</div><!-- End .span12 -->
</div><!-- End .row-fluid -->
<?php
$validationRules = array(
	'Template.subject' => array(
		'required' => array(
			'rule' => array('required' => 'true'),
			'message' => 'Please enter subject.'
		)
	),
	'Template.content' => array(
		'required' => array(
			'rule' => array('required' => 'true'),
			'message' => 'Please enter content.'
		)
	),
	'Template.tags' => array(
		'required' => array(
			'rule' => array('required' => 'true'),
			'message' =>'Please enter tags.'
		)
	)
);

echo $this->Html->css('uniform/uniform.default', null, array('inline' => false));
echo $this->Html->css('validate/validate', null, array('inline' => false));
echo $this->Html->css('select/select2', null, array('inline' => false));

$this->Blocks->startIfEmpty('viewJavaScript');

echo $this->Html->script('uniform/jquery.uniform.min');
echo $this->Html->script('validate/jquery.validate.min');
echo $this->Html->script('select/select2.min');
?>
<script type="text/javascript">
<?php
if (!empty($validationRules)) {
	echo $this->FormValidation->generateValidationRules('#addEmailForm', $validationRules);
}
?>

$("input, textarea, select").not('.nostyle').uniform();
</script>
<?php $this->Blocks->end(); ?>