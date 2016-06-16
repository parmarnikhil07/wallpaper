<?php
if(!empty($code)){
    echo '<div class="login-box"><div class="alert alert-success">' . $msg . '</div></div>';
} else {
    echo '<div class="login-box"><div class="alert alert-danger">' . $msg . '</div></div>';
}
?>
