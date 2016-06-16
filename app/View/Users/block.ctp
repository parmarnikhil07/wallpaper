<?php
if(!empty($code)){
    echo '<div class="alert alert-success">' . $msg . '</div>';
} else {
    echo '<div class="alert alert-danger">' . $msg . '</div>';
}
?>