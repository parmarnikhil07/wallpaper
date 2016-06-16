<header class="main-header">
    <a href="/" class="logo">
        <span class="logo-lg">Walp</span>
    </a>
    <?php
    $email = $loggedinAdmin['email'];
    $adminName = $loggedinAdmin['name'];
    ?>
    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-user"></i>
                        <span><?php echo $adminName; ?> <i class="caret"></i></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="user-header bg-light-blue">
                            <?php 
                                if(!empty($loggedinAdmin['avatar'])){
                                    echo $this->Common->getAvatar($loggedinAdmin['avatar'], array('class' => 'img-circle', 'width' => 150, 'height' => 150)); 
                                } else {
                                    echo '<img alt="User Image" class="img-circle" src="/img/default.jpg">';
                                }
                            ?>
                            <p><?php echo $adminName; ?></p>
                        </li>
                        <li class="user-footer">
<!--                            <div class="pull-left">
                                <?php
//                                echo $this->Html->link('Profile', array('controller' => 'admins', 'action' => 'edit_profile'), array('escape' => false, 'class' => 'btn btn-default btn-flat'));
                                ?>
                            </div>-->
                            <div class="pull-right">
                                <?php
                                echo $this->Html->link('Sign out', array('controller' => 'admins', 'action' => 'logout'), array('escape' => false, 'class' => 'btn btn-default btn-flat'));
                                ?>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

