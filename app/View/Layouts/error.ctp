<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php echo $this->Html->charset(); ?>
		<title>
			<?php echo (!empty($title_for_layout)) ? $title_for_layout . CONFIG_SITE_TITLE_SEPARATOR . CONFIG_SITE_TITLE_POSTFIX : CONFIG_SITE_TITLE_POSTFIX; ?>
		</title>
		<?php
		echo $this->Html->meta('icon');
		echo $this->Html->meta(array('name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0'));
		?>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css' /> <!-- Headings -->
		<link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css' /> <!-- Text -->
		<!--[if lt IE 9]>
		<link href="http://fonts.googleapis.com/css?family=Open+Sans:400" rel="stylesheet" type="text/css" />
		<link href="http://fonts.googleapis.com/css?family=Open+Sans:700" rel="stylesheet" type="text/css" />
		<link href="http://fonts.googleapis.com/css?family=Droid+Sans:400" rel="stylesheet" type="text/css" />
		<link href="http://fonts.googleapis.com/css?family=Droid+Sans:700" rel="stylesheet" type="text/css" />
		<![endif]-->
		<?php
//		echo $this->Html->css('bootstrap/bootstrap.min');
//		echo $this->Html->css('bootstrap/bootstrap-theme.min');
//		echo $this->Html->css('icons');
//		echo $this->Html->css('main');
		?>
		<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<?php
		echo $this->Html->script('bootstrap/bootstrap.min');
		echo $this->fetch('viewJavaScript');
		?>
        </head>
	<body class="errorPage">

		<?php echo $this->element('blank_navbar'); ?>

		<div class="container">
			<?php echo $this->fetch('content'); ?>
		</div><!-- End .container -->
		<?php echo $this->Session->flash(); ?>
	</body>
</html>