<form id="form-comment">
  <div id="comment"><?= $list ?></div>
  <h2><?= $text_write ?></h2>
  <?php if ($comment_guest): ?>
    <div class="mb-3 required">
      <label for="input-author" class="form-label"><?= $entry_author ?></label>
      <input type="text" name="author" value="<?= $author ?>" id="input-author" class="form-control">
      <div id="error-author" class="invalid-feedback"></div>
    </div>
    <div class="mb-3 required">
      <label for="input-comment" class="form-label"><?= $entry_comment ?></label>
      <textarea name="comment" rows="5" id="input-comment" class="form-control"></textarea>
      <div id="error-comment" class="invalid-feedback"></div>
    </div>
    <?= $captcha ?>
    <div class="row">
      <div class="col">
        <a href="<?= $back ?>" class="btn btn-light"><?= $button_back ?></a>
      </div>
      <div class="col text-end">
        <button type="submit" id="button-comment" class="btn btn-primary"><?= $button_submit ?></button>
      </div>
    </div>
  <?php else: ?>
    <?= $text_login ?>
  <?php endif; ?>
</form>

<script ><!--
$('#comment').on('click', '.pagination a', function(e) {
    e.preventDefault();

    $('#comment').load(this.href);
});

// Forms
$('#form-comment').on('submit', function(e) {
    e.preventDefault();

    var element = this;

    $.ajax({
        url: 'index.php?route=cms/comment.write&language={{ language }}&comment_token={{ comment_token }}&article_id={{ article_id }}',
        type: 'post',
        data: $('#form-comment').serialize(),
        dataType: 'json',
        cache: false,
        contentType: 'application/x-www-form-urlencoded',
        processData: false,
        beforeSend: function() {
            $('#button-comment').button('loading');
        },
        complete: function() {
            $('#button-comment').button('reset');
        },
        success: function(json) {
            $('.alert-dismissible').remove();
            $('#form-comment').find('.is-invalid').removeClass('is-invalid');
            $('#form-comment').find('.invalid-feedback').removeClass('d-block');

            if (json['error']) {
                if (json['error']['warning']) {
                    $('#alert').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-circle-exclamation"></i> ' + json['error']['warning'] + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                }

                for (key in json['error']) {
                    $('#input-' + key.replaceAll('_', '-')).addClass('is-invalid').find('.form-control, .form-select, .form-check-input, .form-check-label').addClass('is-invalid');
                    $('#error-' + key.replaceAll('_', '-')).html(json['error'][key]).addClass('d-block');
                }
            }

            if (json['success']) {
                $('#alert').prepend('<div class="alert alert-success alert-dismissible"><i class="fa-solid fa-circle-exclamation"></i> ' + json['success'] + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');

                $('#input-comment').val('');
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            //x console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
});
//--></script>