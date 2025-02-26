<fieldset>
    <h3><?= $text_instruction ?></h3>
    <div class="border rounded p-3 mb-2">
        <p><b><?= $text_payable ?></b></p>
        <p><?= $payable ?></p>
        <b><?= $text_address ?></b>
        <br />
        <p><?= $address ?></p>
        <p><?= $text_payment ?></p>
    </div>
    <div class="text-end">
        <button type="button" id="button-confirm" class="btn btn-primary"><?= $button_confirm ?></button>
    </div>
</fieldset>

<script><!--
$('#button-confirm').on('click', function () {
    var element = this;

    $.ajax({
        url: 'index.php?route=extension/ventocart/payment/cheque.confirm&language=<?= $language ?>',
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