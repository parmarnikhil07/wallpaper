<section class="content-header">
    <h1>Reports</h1>
    <?php $this->Breadcrumb->draw(); ?>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">
                    <h3>
                        <span class="left">User detail</span>
                        <span class="pull-right">
                            <?php echo $this->Html->link("Back", array('controller' => 'spam_reports', 'action' => 'report_users'), array('class' => 'btn btn-block btn-primary'));
                            ?></span>
                    </h3>
                </div>
                <div class="content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row-fluid">
                                <div class="col-lg-2">
                                    <?php
                                    if (!empty($userArr['User']['avatar'])) {
                                        echo '<img src="' . USER_THUMB_IMAGE_URL_S3 . $userArr['User']['avatar'] . '" alt="Avtar" width="140px">';
                                    } else {
                                        echo '<img src="' . USER_THUMB_IMAGE_URL_S3 . "no-picture-icon.png" . '" alt="Avtar" width="140px">';
                                    }
                                    ?>
                                </div>
                                <div class="col-lg-10">
                                    <div class="row-fluid">
                                        <div class="col-lg-6">
                                            <div class="row-fluid">
                                                <div class="col-lg-12">
                                                    User Name : <?php echo $userArr['User']['user_name'] ?>
                                                </div>
                                                <div class="col-lg-12">
                                                    Full Name : <?php echo $userArr['User']['full_name'] ?>
                                                </div>
                                                <div class="col-lg-12">
                                                    Email : <?php echo $userArr['User']['email'] ?>
                                                </div>
                                                <div class="col-lg-12">
                                                    From : <?php echo!empty($userArr['User']['country']) ? $userArr['User']['country'] : ' ---'; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row-fluid">
                                                <div class="col-lg-12">
                                                    Friends: <?php echo $friendsCount ?>
                                                </div>
                                                <div class="col-lg-12">
                                                    Followers: <?php echo $UserFollower ?>
                                                </div>
                                                <div class="col-lg-12">
                                                    Following: <?php echo $UserFollowing ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content">
                    <div class="row">
                        <div class="col-lg-12">
                            <?php if (!empty($pollArr)) { ?>
                                <table cellpadding="0" cellspacing="0" border="0" class="responsive dynamicTable display table table-bordered" width="100%">
                                    <thead>
                                        <tr>
                                            <th width="10%">#</th>
                                            <th width="30%">Poll</th>
                                            <th width="25%">Poll Media</th>
                                            <th width="10%">Invites</th>
                                            <th width="10%">Answers</th>
                                            <th width="15%">Created</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $indx = (($this->Paginator->current() - 1) * PAGE_LIMIT) + 1;
                                        foreach ($pollArr as $poll) {
                                            ?>
                                            <tr>
                                                <td><?php echo $indx; ?></td>
                                                <td class="txtLeft"><a class="animated fadeInUp" rel="prettyPhoto" href="<?php echo CONFIG_POLL_IMAGE_URL_S3 . $poll['Poll']['poll_image']; ?>"><?php echo $poll['Poll']['title']; ?></a></td>
                                                <td class="txtLeft">
                                                    <?php
                                                    $mediaLink = "";
                                                    if ($poll['Poll']['poll_data_type'] == 0) {
                                                        $mediaLink = S3_IMAGE_FILE;
                                                    } else if ($poll['Poll']['poll_data_type'] == 1) {
                                                        $mediaLink = S3_MP3_FILE;
                                                    } else if ($poll['Poll']['poll_data_type'] == 2) {
                                                        $mediaLink = S3_VIDEO_FILE;
                                                    } else if ($poll['Poll']['poll_data_type'] == 3) {
                                                        $mediaLink = "";
                                                    } else if ($poll['Poll']['poll_data_type'] == 9) {
                                                        $mediaLink = "";
                                                    }

                                                    $temp = 1;
                                                    foreach ($poll['PollOption'] as $pollMediaArr) {
                                                        echo!empty($mediaLink) ? "<a href='" . $mediaLink . $pollMediaArr['file_name'] . "' target='_blank'>" . "Media " . $temp . "</a>" : $pollMediaArr['file_name'];
                                                        echo "<br/>";
                                                        $temp++;
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo isset($poll['Poll']['invite_count']) ? $poll['Poll']['invite_count'] : '-'; ?></td>
                                                <td><?php echo isset($poll['Poll']['invite_answer_count']) ? $poll['Poll']['invite_answer_count'] : '-'; ?></td>
                                                <td class="txtLeft"><?php echo isset($poll['Poll']['created']) ? date("m/d/Y H:i:s", strtotime($poll['Poll']['created'])) : '-'; ?></td>
                                            </tr>
                                            <?php
                                            $indx++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            <?php } else { ?>
                                <div class="alert alert-info">
                                    No walp created by the user.
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div><!-- End .box -->
        </div><!-- End .col-lg-12 -->
    </div>
</div><!-- End .row-fluid -->
<?php echo $this->Html->css('dataTables/jquery.dataTables.min', null, array('inline' => false)); ?>
<?php $this->Blocks->startIfEmpty('viewJavaScript'); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('.pagination ul li.active').html('<a>' + $('.pagination ul li.active').html() + '</a>');
        $('.pagination ul li.disabledP').html('<a>' + $('.pagination ul li.disabledP').html() + '</a>');
        $('.pagination ul li.disabledN').html('<a>' + $('.pagination ul li.disabledN').html() + '</a>');
        $('.pagination ul li.active').removeClass('disabled');
    });
</script>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function () {
        $("a[rel^='prettyPhoto']").prettyPhoto({social_tools: false});
    });
</script>
<?php
echo $this->Html->script('responsive-tables/responsive-tables');
?>
<?php $this->Blocks->end(); ?>