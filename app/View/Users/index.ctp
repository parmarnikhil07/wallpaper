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
                        <?php if (!empty($usersArr)) { ?>
                        <table id="adminList" cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover table-responsive" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Avatar</th>
                                        <th><?php echo $this->Paginator->sort('full_name', 'Full Name'); ?></th>
                                        <th><?php echo $this->Paginator->sort('email', 'Email'); ?></th>
                                        <th><?php echo $this->Paginator->sort('user_name', 'Username'); ?></th>
                                        <th><?php echo $this->Paginator->sort('user_time_zone', 'Zone'); ?></th>
                                        <th><?php echo $this->Paginator->sort('facebook_id', 'Facebook'); ?></th>
                                        <th><?php echo $this->Paginator->sort('twitter_id', 'Twitter'); ?></th>
                                        <th><?php echo $this->Paginator->sort('google_id', 'Google'); ?></th>
                                        <th><?php echo $this->Paginator->sort('created', 'Registered'); ?></th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $indx = (($this->Paginator->current() - 1) * PAGE_LIMIT) + 1;
                                    foreach ($usersArr as $userArr) {
                                        ?>
                                        <tr>
                                            <td><?php echo $indx; ?></td>
                                            <td>
                                                <?php
                                                if (!empty($userArr['User']['avatar'])) {
                                                ?>
                                                <a class="animated fadeInUp" rel="prettyPhoto" href="<?php echo USER_AVATAR_URL . $userArr['User']['avatar']; ?>"><img width="50px" src="<?php echo USER_AVATAR_THUMB_URL . $userArr['User']['avatar']; ?>" alt="<?php echo $userArr['User']['full_name']; ?>" id="openModalDialog"></a>
                                                <?php
                                                } else {
                                                    echo '<img src="' . USER_AVATAR_THUMB_URL . "no-picture-icon.png" . '" alt="Avtar" width="40px">';
                                                }
                                                ?></td>
                                            <td class="txtLeft"><?php echo $userArr['User']['full_name']; ?></td>
                                            <td class="txtLeft"><?php echo $userArr['User']['email']; ?></td>
                                            <td class="txtLeft"><?php echo $userArr['User']['user_name']; ?></td>
                                            <td class="txtLeft"><?php echo $userArr['User']['user_time_zone']; ?></td>
                                            <td><?php echo!empty($userArr['User']['facebook_id']) ? 'Yes' : 'No'; ?></td>
                                            <td><?php echo!empty($userArr['User']['twitter_id']) ? 'Yes' : 'No'; ?></td>
                                            <td><?php echo!empty($userArr['User']['google_id']) ? 'Yes' : 'No'; ?></td>
                                            <td><?php echo date("m/d/Y H:i:s", strtotime($userArr['User']['created'])); ?></td>
                                            <td style="text-align:center"><?php echo $this->Html->link("View Wallpapers", array('controller' => 'wallpapers', 'action' => 'index', 'user_id' => $userArr['User']['id']), array('escape' => false, 'title' => 'Wallpapers')); ?></td>
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
                            There was no admin found.
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div><!-- End .row -->
</section>
<?php echo $this->Html->css('dataTables/jquery.dataTables.min', null, array('inline' => false)); ?>
<?php // $this->Blocks->startIfEmpty('viewJavaScript'); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('.pagination ul li.active').html('<a>' + $('.pagination ul li.active').html() + '</a>');
        $('.pagination ul li.disabledP').html('<a>' + $('.pagination ul li.disabledP').html() + '</a>');
        $('.pagination ul li.disabledN').html('<a>' + $('.pagination ul li.disabledN').html() + '</a>');
        $('.pagination ul li.active').removeClass('disabled');
    });
</script>
<script type="text/javascript" charset="utf-8">
  $(document).ready(function(){
    $("a[rel^='prettyPhoto']").prettyPhoto({social_tools: false});
  });
</script><?php
echo $this->Html->script('responsive-tables/responsive-tables');
?>
<?php $this->Blocks->end(); ?>