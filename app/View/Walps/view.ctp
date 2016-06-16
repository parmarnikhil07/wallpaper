<?php
if(!empty($title) && !empty($poll_image)){
	echo '<h1>'.$title.'</h1>';
	echo '<div><img src="'. CONFIG_POLL_IMAGE_URL_S3 . $poll_image . '" /></div>';
	echo "<br/>";
	echo '<div><a href="https://itunes.apple.com/us/app/urateit/id1032942055?ls=1&mt=8"><img src="'. IOS_LINK_IMAGE_URL . '" /></a></div>';
	echo "<br/>";
}
?>