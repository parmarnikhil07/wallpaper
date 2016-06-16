<?php $paginator = $this->Paginator; ?>
<section class="content-header">
    <h1> News/Features </h1>    
</section>
<section class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">
                    <h4>
                        <span class="left"> News/Features </span>                        
                        <span><?php echo $this->Html->link('Add News', array('controller' => 'news_feeds', 'action' => 'add'), array('class' => 'btn btn-primary pull-right')); ?></span>
                    </h4>
                </div>
                <div class="box-body">
                    <div role="grid" class="dataTables_wrapper form-inline" id="DataTables_Table_0_wrapper">                    
                        <?php if(!empty($newsRecords)){ ?>
                        <table  cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover table-responsive" width="100%">
                            <tr>       
                                <th> <?php echo $this->paginator->sort('id'); ?></th>
                                <th> <?php echo $this->paginator->sort('type'); ?></th>
                                <th> <?php echo $this->paginator->sort('message'); ?></th>                                                                        
                                <th> <?php echo $this->paginator->sort('created'); ?></th>                                                                        
                                <th colspan="4"> Actions</th>
                            </tr>
                            <?php
                            $indx = 1;
                            $paginatorParams = $this->Paginator->params();
                            $indx = $indx + ( ((int) $this->Paginator->counter('{:page}') - 1) * $paginatorParams['limit'] );
                            foreach ($newsRecords as $news):
                                ?>
                                <tr>
                                    <td style="text-align:center"><?php echo $indx; ?></td>
                                    <td><?php echo $news['NewsFeed']['type']; ?></td>
                                    <td><?php echo $news['NewsFeed']['message']; ?></td>                                                                      
                                    <td style="text-align:center"><?php echo $news['NewsFeed']['created']; ?></td>                                                                      
                                    <td style="text-align:center">
                                        <?php echo $this->Html->link($this->Html->tag('span', null, array('class' => 'fa fa-fw fa-edit')), array('action' => 'edit', $news['NewsFeed']['id']), array('escape' => false, 'title' => 'Edit details')); ?>
                                        <?php
                                        echo $this->Form->postLink($this->Html->tag('span', null, array(
                                                    'class' => 'fa fa-fw fa-trash-o')), array('action' => 'delete', $news['NewsFeed']['id']), array('escape' => false,
                                            'title' => 'Delete News',
                                            'class' => 'padding10Px'), __('Are you sure you want to delete ' . $news['NewsFeed']['type'], $news['NewsFeed']['id']));
                                        ?>                                                                                          
                                    </td>                                
                                </tr>
                                <?php
                                $indx++;
                            endforeach;
                            ?>
                        </table>               
                    </div>             
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
                    <?php echo $this->form->end; ?>
                </div>
                <?php } else { ?>
                    <div class="alert alert-info">
                        There was no admin found.
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    $(document).ready(function () {
        $('.pagination ul li.active').html('<a>' + $('.pagination ul li.active').html() + '</a>');
        $('.pagination ul li.disabledP').html('<a>' + $('.pagination ul li.disabledP').html() + '</a>');
        $('.pagination ul li.disabledN').html('<a>' + $('.pagination ul li.disabledN').html() + '</a>');
        $('.pagination ul li.active').removeClass('disabled');
    });
</script>
<style type="text/css">
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