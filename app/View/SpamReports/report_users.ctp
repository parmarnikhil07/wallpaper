<section class="content-header">
    <h1>Reports</h1>
    <?php $this->Breadcrumb->draw(); ?>
</section>
<section class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">
                    <h3>
                        <span class="left">Reported Users</span>
                    </h3>
                </div>
                <div class="box-body">
                    <div role="grid" class="dataTables_wrapper form-inline" id="DataTables_Table_0_wrapper">
                        <?php if (!empty($spamsArr)) { ?>
                            <table cellpadding="0" cellspacing="0" border="0" class="responsive dynamicTable display table table-bordered" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Sender</th>
                                        <th>Username</th>
                                        <th>Report Reason</th>
                                        <th>Registered</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $indx = (($this->Paginator->current() - 1) * PAGE_LIMIT) + 1;
                                    foreach ($spamsArr as $spamArr) {
                                        ?>
                                        <tr>
                                            <td><?php echo $indx; ?></td>
                                            <td><?php echo isset($spamArr['SenderUser']['user_name']) ? $spamArr['SenderUser']['user_name'] : '-'; ?></td>

                                            <td class="txtLeft"><?php echo isset($spamArr['User']['user_name']) ? $this->Html->link($spamArr['User']['user_name'] . "(" . $spamArr['User']['email'] . ")", array('action' => 'show_user_detail/' . $spamArr['User']['user_name'])) : '-'; ?></td>
                                            <td class="txtLeft"><?php echo isset($spamArr['SpamReport']['reason_title']) ? $spamArr['SpamReport']['reason_title'] : '-'; ?></td>
                                            <td class="center"><?php echo isset($spamArr['User']['created']) ? $spamArr['User']['created'] : '-'; ?></td>
                                            <td>
                                                <?php echo $this->Form->postLink($this->Html->tag('span', null, array('class' => 'con12 icon-ban-circle')), array('action' => 'ignore_user_report', $spamArr['SpamReport']['id']), array('escape' => false, 'title' => 'Ignore'), __('Are you sure you want to ignore?')); ?>
                                                <?php echo $this->Form->postLink($this->Html->tag('span', null, array('class' => 'con12 icon-trash')), array('action' => 'delete_user_report', $spamArr['SpamReport']['id']), array('escape' => false, 'title' => 'Delete report'), __('Are you sure you want to delete?')); ?>
                                            </td>
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
                            There was no report found.
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
    $(document).ready(function () {
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
    tr .center{
        text-align: center;
    }
</style>