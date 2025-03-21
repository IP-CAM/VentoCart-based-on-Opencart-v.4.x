<div class="text-end">
    <?php if (isset($insufficient_balance)): ?>
        <button type="button" id="button-confirm" class="btn btn-danger"><?= $insufficient_balance ?></button>
    <?php else: ?>
        <button type="button" id="button-confirm" class="btn btn-primary"><?= $button_confirm ?></button>
    <?php endif; ?>
</div>
<script><!--
$('#button-confirm').on('click', function () {
    var element = this;

    $.ajax({
        url: 'index.php?route=extension/ventocart/payment/giftcard.confirm&language=<?= $language ?>',
    dataType: 'json',
        beforeSend: function () {
            $(element).button('loading');
        },
    complete: function () {
        $(element).button('reset');
    },
    success: function (json) {
        if (json['error']) {
            $('#alert').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-circle-exclamation"></i> ' + json['error'] + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
        }

        if (json['redirect']) {
            location = json['redirect'];
        }
    },
    error: function (xhr, ajaxOptions, thrownError) {
        //x console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
    }
    });
});
//--></script>