<section class="sidebar">
    <div class="user-panel">
        <div class="pull-left image">
            <?php
            if(!empty($loggedinAdmin['avatar'])){
                echo $this->Common->getAvatar($loggedinAdmin['avatar'], array('class' => 'img-circle', 'width' => 150, 'height' => 150)); 
            } else {
                echo '<img alt="Admin Image" class="img-circle" src="/img/default.jpg">';
            }
            ?>
        </div>
        <div class="pull-left info">
            <p>Hello, <?php echo $loggedinAdmin['name']; ?></p>
        </div>
    </div>
    <ul class="sidebar-menu">
        <li>
            <?php
            echo $this->Html->link(
                    $this->Html->tag('i', '', array('class' => 'fa fa-dashboard')) . "<span>Dashboard</span>", array('controller' => 'dashboards', 'action' => 'index'), array('escape' => false, 'class' => 'Dashboard')
            )
            ?>
        </li>
        <li>
            <?php
            echo $this->Html->link(
                    $this->Html->tag('i', '', array('class' => 'fa fa-users')) . "<span>Users</span>", array('controller' => 'users', 'action' => 'index'), array('escape' => false, 'class' => 'beacon')
            )
            ?>
        </li>
        <li>
            <?php
            echo $this->Html->link(
                    $this->Html->tag('i', '', array('class' => 'fa fa-video-camera')) . "<span>Wallpapers</span>", array('controller' => 'wallpapers', 'action' => 'index'), array('escape' => false, 'class' => 'Wallpaper')
            )
            ?>
        </li>
        <li>
            <?php
            echo $this->Html->link(
                    $this->Html->tag('i', '', array('class' => 'fa fa-video-camera')) . "<span>News/Features</span>", array('controller' => 'news_feeds', 'action' => 'index'), array('escape' => false, 'class' => 'Wallpaper')
            )
            ?>
        </li>
        <li class="treeview">
            <?php
            echo $this->Html->link(
                    $this->Html->tag('i', '', array('class' => 'fa fa-user-secret')) . "<span>Abuse Reports</span>" . $this->Html->tag('i', '', array('class' => 'fa fa-angle-left pull-right')), '#', array('escape' => false,)
            )
            ?>
            <ul class="treeview-menu"  style="display: none;">
                <li>
                  <?php
                    echo $this->Html->link(
                            $this->Html->tag('i', '', array('class' => 'fa fa-hand-o-right')) . "<span>Users Reports</span>", array('controller' => 'spam_reports', 'action' => 'report_users'), array('escape' => false)
                    )
                    ?>
                </li>
                <li>
                  <?php
                    echo $this->Html->link(
                            $this->Html->tag('i', '', array('class' => 'fa fa-hand-o-right')) . "<span>Wallpapers Reports</span>", array('controller' => 'spam_reports', 'action' => 'report_wallpapers'), array('escape' => false)
                    )
                    ?>
                </li>
                <li>
                  <?php
                    echo $this->Html->link(
                            $this->Html->tag('i', '', array('class' => 'fa fa-hand-o-right')) . "<span>Comments Reports</span>", array('controller' => 'spam_reports', 'action' => 'report_comments'), array('escape' => false)
                    )
                    ?>
                </li>
            </ul>
      </li>
    </ul>
</section>
<script>
    $(document).ready(function() {
        var currentPath = window.location.pathname.split("/");
        var currentHref = currentPath[1];
        $('.sidebar-menu').find("a[href='/"+currentHref+"']").parent("li").addClass('active');
        /**** for sub menu ******/
        var currentActionHref = currentPath[2];
        $('.treeview-menu').find("a[href='/"+currentHref+"/"+currentActionHref+"']").parent("li").addClass('active');
        $('.treeview-menu').find("a[href='/"+currentHref+"/"+currentActionHref+"']").parent("li").parent("ul").addClass('menu-open');
        $('.treeview-menu').find("a[href='/"+currentHref+"/"+currentActionHref+"']").parent("li").parent("ul").css("display", "block");
        $('.treeview-menu').find("a[href='/"+currentHref+"/"+currentActionHref+"']").parent("li").parent("ul").parent("li").addClass('active');
    });
</script>