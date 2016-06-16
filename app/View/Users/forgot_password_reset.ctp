<?php
if (!empty($result)) {
    if ($result == 'success') {
        echo '<div class="login-box"><div class="alert alert-success">' . $msg . '</div>';
        echo '</div>';
    } else if ($result == 'wrong') {
        echo '<div class="login-box"><div class="alert alert-danger">' . $msg . '</div>';
        echo '</div>';
    } else {
        echo '<div class="login-box"><div class="alert alert-danger">' . $msg . '</div>';
        echo '<button onclick="window.history.back();" class="btn btn-default">Retry</button>';
        echo '</div>';
    }
} else {
    ?>
    <div class="login-box">
        <div class="login-logo">
            <a href="/admins/login">Wallpaper</a>
        </div><!-- /.login-logo -->
        <div class="login-box-body">
            <?php
            echo $this->Form->create('User', array('url' => '/users/forgot_password_reset/' . $code, 'class' => 'form-horizontal', 'inputDefaults' => array(
                    'format' => array('before', 'label', 'between', 'input', 'error', 'after'),
                    'class' => array('form-control'),
                    'between' => '<div class="col-lg-12">',
                    'after' => '</div>',
                    'error' => array('attributes' => array('wrap' => 'label', 'class' => 'error'))),
                'id' => 'resetPasswordForm'
            ));
            ?>
            <div class="form-group">
                <?php
                echo $this->Form->input('password', array(
                    'type' => 'password',
                    'placeholder' => 'Password',
                    'class' => 'form-control uniform-input',
                    'label' => array('Password', 'class' => 'col-lg-12'),
                    'div' => false,
                    'value' => ""
                ));
                ?>
            </div>
            <div class="form-group">
                <?php
                echo $this->Form->input('password_confirm', array(
                    'type' => 'password',
                    'placeholder' => 'Confirm Password',
                    'class' => 'form-control',
                    'label' => array('Confirm Password', 'class' => 'col-lg-12'),
                    'default' => ""
                ));
                ?>
            </div>
            <div class="form-group">
                <div class="col-lg-12 clearfix form-actions">
                    <?php echo $this->Form->button('<i class="entypo-right-open-mini"></i>' . __('Reset Password', true), array('type' => 'submit', 'class' => 'btn btn-success', 'style' => 'width: 220px;')); ?>
                    <?php
                    echo $this->Form->input('code', array(
                        'type' => 'hidden',
                        'value' => $code,
                        'class' => 'form-control',
                        'label' => false,
                        'div' => array('class' => 'row-fluid')
                    ));
                    ?>
                </div>
            </div>
        <?php echo $this->Form->end(); ?>
        </div>
    </div>
        <?php
        $validationRules = array(
            'User.password' => array(
                'required' => array(
                    'rule' => array('required' => 'true'),
                    'message' => 'Please enter password.'
                )
            ),
            'User.password_confirm' => array(
                'required' => array(
                    'rule' => array('required' => 'true'),
                    'message' => 'Please enter confirm password.'
                )
            )
        );

        echo $this->Html->css('validate/validate');
        echo $this->Html->script('validate/jquery.validate.min');
        ?>
        <script type="text/javascript">
    <?php
    if (!empty($validationRules)) {
        echo $this->FormValidation->generateValidationRules('#resetPasswordForm', $validationRules);
    }
    ?>
        //----------to remove prefilled values from form ------------------
        $('#resetPasswordForm').trigger('reset');
        </script>
<?php } ?>