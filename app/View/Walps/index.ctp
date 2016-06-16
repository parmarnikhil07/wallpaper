<section class="content-header">
          <h1>Users</h1>
    <?php $this->Breadcrumb->draw(); ?>
</section>
<section class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">
                    <h3>
                        <span class="left">Users</span>
                    </h3>
                </div>
                <div class="box-body">
                    <div role="grid" class="dataTables_wrapper form-inline" id="DataTables_Table_0_wrapper">
                        <?php if (!empty($wallpapersArr)) { ?>
                        <table id="adminList" cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover table-responsive" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th align="center">Wallpaper</th>
                                        <th><?php echo $this->Paginator->sort('title', 'Title'); ?></th>
                                        <th><?php echo $this->Paginator->sort('full_name', 'Wallpaper Creator'); ?></th>
                                        <th><?php echo $this->Paginator->sort('likes_count', 'Likes Count'); ?></th>
                                        <th><?php echo $this->Paginator->sort('rating_count', 'Rating count'); ?></th>
                                        <th><?php echo $this->Paginator->sort('comments_count', 'Comments Count'); ?></th>
                                        <th align="center"><?php echo $this->Paginator->sort('created', 'Created'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $indx = (($this->Paginator->current() - 1) * PAGE_LIMIT) + 1;
                                    foreach ($wallpapersArr as $wallpaperArr) {
                                        ?>
                                        <tr>
                                            <td align="center"><?php echo $indx; ?></td>
                                            <td align="center">
                                                 <video width="120" controls>
                                                    <source src="<?php echo WALLPAPER_VIDEO_URL . $wallpaperArr['Wallpaper']['video']; ?>">
                                                  </video> 
                                            </td>
                                            <td align="center"><?php echo $wallpaperArr['Wallpaper']['title']; ?></td>
                                            <td><?php echo $this->Html->link($wallpaperArr['User']['full_name'], array('controller' => 'wallpapers', 'action' => 'index', 'user_id' => $wallpaperArr['User']['id']), array('escape' => false, 'title' => 'User')); ?></td>
                                            <td align="center"><?php echo $wallpaperArr['Wallpaper']['likes_count']; ?></td>
                                            <td align="center"><?php echo $wallpaperArr['Wallpaper']['rating_count']; ?></td>
                                            <td align="center"><?php echo $wallpaperArr['Wallpaper']['comments_count']; ?></td>
                                            <td align="center"><?php echo date("m/d/Y H:i:s", strtotime($wallpaperArr['Wallpaper']['created'])); ?></td>
                                        </tr>
                                        <?php
                                        $indx++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <div class="col-lg-12">
                                <div class="dataTables_paginate paging_bootstrap pagination">
                                    <ul class="pagination">
                                        <?php
                                        echo $this->Paginator->prev('← Previous', array('tag' => 'li', 'class' => 'prev'), null, array('class' => 'disabledP', 'tag' => 'li'));
                                        echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li', 'currentClass' => 'active'));
                                        echo $this->Paginator->next('Next →', array('tag' => 'li', 'class' => 'next'), null, array('class' => 'disabledN', 'tag' => 'li'));
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-info">
                            There was no wallpaper found.
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div><!-- End .row -->
</section>
<?php
    echo $this->Html->css('datatables/dataTables.bootstrap');
?>
    <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
<?php
//$this->Blocks->startIfEmpty('viewJavaScript');
echo $this->Html->script('datatables/jquery.dataTables.min');
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('.pagination ul li.active').html('<a>' + $('.pagination ul li.active').html() + '</a>');
        $('.pagination ul li.disabledP').html('<a>' + $('.pagination ul li.disabledP').html() + '</a>');
        $('.pagination ul li.disabledN').html('<a>' + $('.pagination ul li.disabledN').html() + '</a>');
        $('.pagination ul li.active').removeClass('disabled');
    });
</script>
<?php $this->Blocks->end(); ?>
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