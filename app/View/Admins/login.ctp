<div class="login-box">
    <div class="login-logo">
        <a href="/admins/login">Wallpaper</a>
    </div><!-- /.login-logo -->
    
    <div class="login-box-body">
        
        <?php echo $this->Form->create('Login', array('url' => '/admins/login', 'class' => 'form-horizontal','inputDefaults' => array(
        'format' => array('before', 'label', 'between', 'input', 'error', 'after'),
        'class' => array('form-control'),
        'between' => '<div class="col-lg-12">',
        'after' => '</div>',
        'error' => array('attributes' => array('wrap' => 'label', 'class' => 'error'))),
        'id' => 'loginForm'
        )); ?>
	<div class="form-group">
            <?php
            echo $this->Form->input('email', array(
                    'type' => 'text',
                    'class' => 'form-control uniform-input text',
                    'div' => false,
                    'label' => array('text' => __('Email', true), 'class' => 'col-lg-12'),
                    'after' => '<span class="glyphicon glyphicon-envelope form-control-feedback" style="right:10px"></span></div>'
                    )
                );
            ?>
	</div>
	<div class="form-group">
            <?php
            echo $this->Form->input('password', array(
                    'type' => 'password',
                    'class' => 'form-control uniform-input password',
                    'label' => array(
                            'text' => __('Password', true),
                            'class' => 'col-lg-12'
                        ),
                    'after' => '<span class="glyphicon glyphicon-lock form-control-feedback" style="right:10px"></span></div>',
                    'div' => false
                    )
                );
            ?>
	</div>
	<div class="form-group">
            <div class="col-lg-12 clearfix form-actions">
                <?php echo $this->Form->button('<span class="icon16 icomoon-icon-enter white"></span>' . __('Login', true), array('type' => 'submit', 'class' => 'btn btn-info', 'id' => 'loginBtn')); ?>
            </div>
	</div>
	<?php echo $this->Form->end(); ?>
</div>
<?php
$validationRules = array(
    'Login.email' => array(
        'required' => array(
            'rule' => array('required' => 'true'),
            'message' => 'Please enter email.'
        ),
        'email' => array(
            'rule' => array('email' => 'true'),
            'message' => 'Please enter valid email.'
        )
    ),
    'Login.password' => array(
        'required' => array(
            'rule' => array('required' => 'true'),
            'message' => 'Please enter password.'
        )
    )
);

echo $this->Html->css('validate/validate');
//echo $this->Html->script('validate/jquery.validate.min');

?>
<script type="text/javascript">
<?php
if (!empty($validationRules)) {
    //echo $this->FormValidation->generateValidationRules('#loginForm', $validationRules);
}
?>
</script>
