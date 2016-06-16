<script type="text/javascript">
    $.pnotify({
        type: 'notice',
        title: false,
        text: '<?php echo addslashes($message); ?>',
        icon: 'picon icon16 entypo-icon-warning white',
        opacity: 0.95,
        hide: true,
        delay: 3000,
        sticker: false,
        history: false
    });
</script>