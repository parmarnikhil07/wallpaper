<section class="content-header">
          <h1>Edit News/Feature </h1>   
</section>
<section class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">
                    <h4>
                        <span>Edit News/Feature </span>
                    </h4>
                </div>
                <div class="box-body">
                    <?php
                    echo $this->Form->create('NewsFeed', array('url' => array('controller' => 'news_feeds', 'action' => 'edit'), 'type' => 'file', 'class' => 'form-horizontal', 'inputDefaults' => array(
                            'format' => array('before', 'label', 'between', 'input', 'error', 'after'),
                            'class' => array('form-group'),
                            'between' => '<div class="col-lg-10">',
                            'after' => '</div>',
                            'error' => array('attributes' => array('wrap' => 'label', 'class' => 'error'))),
                        'id' => 'editNewsFeedForm'));
                    ?>
                    <div class="form-group">
                           <?php echo $this->Form->input('NewsFeed.id',array('type'=>'hidden')); ?>
                    </div>
                    <div class="form-group">
                       <?php echo $this->Form->input('NewsFeed.type',array(
                                         'options' => array('news' => 'News', 'feature' => 'Feature'),
                                         'label' => array('text' => 'News/Feature', 'class' => 'col-lg-2 control-label'),                                        
                                         'required'=>true,
                                         'empty' => '--Select--', 
                                         'div' => false,
                                         'class' => 'form-control  text'
                                         ));?>                       
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->input('NewsFeed.message', array('type' => 'textarea', 'maxlength' => '500', 'label' => array('text' => 'Message *', 'class' => 'col-lg-2 control-label'), 'div' => false, 'class' => 'form-control  text')); ?>
                    </div>                    
                </div>
                <div class="box-footer">
                    <div class="col-lg-offset-2">
                        <?php echo $this->Form->button('Update', array('type' => 'submit', 'class' => 'btn btn-info')); ?>
                        <?php echo $this->Html->link('Cancel', array('action' => 'index'), array('class' => 'btn btn-default')); ?>
                    </div>
                </div>
                <?php echo $this->Form->end(); ?>
            </div><!-- End .box -->
        </div><!-- End .col-lg-12 -->
    </div><!-- End .row -->
</section>
<?php
$validationRules = array(
    'NewsFeed.type' => array(
        'required' => array(
            'rule' => array('required' => 'true'),
            'message' => 'Please select type.'
        )
    ),
    'NewsFeed.message' => array(
        'required' => array(
            'rule' => array('required' => 'true'),
            'message' => 'Please enter messages.'
        )
    )
);

echo $this->Html->css('validate/validate');
echo $this->Html->script('validate/jquery.validate.min');

?>
<script type="text/javascript">
<?php
if (!empty($validationRules)) {
    echo $this->FormValidation->generateValidationRules('#editNewsFeedForm', $validationRules);
}
?>
</script>