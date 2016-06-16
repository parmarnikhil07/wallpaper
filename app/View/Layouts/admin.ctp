<!DOCTYPE html>
<html>
    <head>
        <?php echo $this->Html->charset(); ?>
        <title>
            <?php echo $title_for_layout; ?>
        </title>
        <?php
        echo $this->Html->meta('icon');

        echo $this->Html->css('bootstrap/bootstrap.min');
        echo $this->Html->css('adminlte.min');
	echo $this->Html->css('skins/_all-skins.min');
        echo $this->Html->css('icons');
        echo $this->Html->css('ionicons.min');
        echo $this->Html->css('datatables/dataTables.bootstrap');
	echo $this->Html->css('pnotify/jquery.pnotify.default');
        echo $this->Html->css('pretty-photo/prettyPhoto');
    ?>
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />

    </head>
    <body class="skin-blue sidebar-mini">
    <?php
        echo $this->Html->script('jquery/jquery-2.1.4.min');
        echo $this->Html->script('bootstrap/bootstrap.min');
        echo $this->Html->script('fastclick/fastclick.min');
        echo $this->Html->script('uniform/jquery.uniform.min');
        echo $this->Html->script('pnotify/jquery.pnotify.min');
        echo $this->Html->script('icheck/icheck.min');
        echo $this->Html->script('app.min');
        echo $this->Html->script('demo');
        echo $this->Html->script('pretty-photo/jquery.prettyPhoto');
        ?>
            <?php echo $this->Session->flash(); ?>
        <div class="wrapper">
            <?php
            echo $this->element('navbar');
            ?>
                <aside class="main-sidebar">
                    <!-- sidebar: style can be found in sidebar.less -->
                        <?php echo $this->element('sidebar');?>
                </aside>
                <div class="content-wrapper" style="min-height: 946px;">
                    <?php //echo $this->Session->flash(); ?>
                    <?php echo $this->fetch('content'); ?>
                </div>
        </div>
    </body>
</html>
