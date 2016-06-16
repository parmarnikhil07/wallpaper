<section class="content-header">
    <h1><span class="gray">News/Feature </span> </h1>  
</section>
<section class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="box">             
                <div class="box-body">                    
                    <div class="row form-group">
                        <div class="col-lg-12">
                            <label class="col-lg-2">
                               Type
                            </label>
                            <div class="col-lg-10">
                              <?php echo $viewdata['NewsFeed']['type'];?>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-lg-12">
                            <label class="col-lg-2">
                              Message
                            </label>
                            <div class="col-lg-10">
                              <?php echo $viewdata['NewsFeed']['message'];?>
                            </div>
                        </div>
                    </div>       
                    <div class="box-footer">
                    <div class="col-lg-2">                    
                        <?php echo $this->Html->link('Cancel', array('action' => 'index'), array('class' => 'btn btn-default')); ?>
                    </div>
                </div>
                </div>
            </div><!-- End .box -->
        </div><!-- End .col-lg-6 -->
        
    </div>
</section>
