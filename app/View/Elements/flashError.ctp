<script type="text/javascript">
    $.pnotify({
        type: 'error',
        title: false,
        text: '<?php echo addslashes($message); ?>',
        icon: 'picon icon16 typ-icon-cancel white',
        opacity: 0.95,
        hide: true,
        delay: 3000,
        history: false,
        sticker: false
    });
</script>