<section class="content-header">
          <h1>Dashboard</h1>
    <?php $this->Breadcrumb->draw(); ?>
</section>
<section class="content">
    <div class="row">
        <div class="col-lg-12">
            <h3 class="box-title" style="padding-bottom: 10px; border-bottom: 1px solid rgb(204, 204, 204);">
                Users
            </h3>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <span class="col-lg-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-aqua"><i class="ion ion-ios-people-outline"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Users</span>
                            <span class="info-box-number"><?php echo $totalUsers; ?></span>
                        </div><!-- /.info-box-content -->
                    </div>
                </span>
                <span class="col-lg-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="ion ion-ios-people-outline"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Active Users</span>
                            <span class="info-box-number"><?php echo $activeCount; ?></span>
                        </div><!-- /.info-box-content -->
                    </div>
                </span>
                <span class="col-lg-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-red"><i class="ion ion-ios-people-outline"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Inactive Users</span>
                            <span class="info-box-number"><?php echo $UnActiveCount; ?></span>
                        </div><!-- /.info-box-content -->
                    </div>
                </span>
            </div>
        </div>
    </div>
</div><!-- End .row -->
</section>
<?php
    echo $this->Html->css('datatables/dataTables.bootstrap');
?>
    <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
<style type="text/css">
    .fancybox-custom .fancybox-skin {
        box-shadow: 0 0 50px #222;
    }
    tr th{
        text-align: center;
    }
    tr td{
        vertical-align: middle !important;
    }
</style>