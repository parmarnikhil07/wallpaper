<header class="main-header">
<div id="header">
    <div class="navbar navbar-default">
        <div class="navbar-header">
            <?php echo $this->Html->link($this->Html->image('med-noshadow.png', array('class' => 'image')), '/', array('class' => 'navbar-brand', 'style' => 'float: none;', 'escape' => false)); ?>
            <!-- for bootstrap menu -->
            <button data-target=".navbar-ex1-collapse" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon16 icomoon-icon-arrow-4"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav">
                <li <?php echo ('dashboard' == $this->request->params['controller'] && 'index' == $this->request->params['action']) ? 'class="active"' : ''; ?>>
                    <?php echo $this->Html->link('<span class="icon16 icomoon-icon-screen-2"></span> <span class="txt">Dashboard</span>', '/', array('escape' => false)); ?>
                </li>
            </ul>
            <ul class="nav navbar-right adminnav">
                <?php if (!empty($loggedinAdmin)) { ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <span class="icon16 icomoon-icon-admin-4"></span>
                            <span class="txt"><?php echo $loggedinAdmin['name']; ?></span>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="menu">
                                <ul>
                                    <li>
                                        <?php echo $this->Html->link('<span class="icon16 icomoon-icon-admin-3"></span> Edit profile', '/admins/edit_profile', array('escape' => false)); ?>
                                    </li>
                                    <li>
                                        <?php echo $this->Html->link('<span class="icon16 icomoon-icon-key"></span> Change Password', '/admins/change_password', array('escape' => false)); ?>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                <?php } ?>
                <li>
                    <?php echo $this->Html->link('<span class="icon16 icomoon-icon-exit"></span> <span class="txt">Logout</span>', '/admins/logout', array('escape' => false)); ?>
                </li>
            </ul>
        </div><!-- /.nav-collapse -->
    </div><!-- /navbar -->
</div><!-- End #header -->
</header>