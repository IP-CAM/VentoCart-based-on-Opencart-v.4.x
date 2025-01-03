<?= $header ?>
<div id="account-login" class="container">
  <?= $breadcrumb ?>
  <?php if ($success): ?>
    <div class="alert alert-success alert-dismissible"><i class="fa-solid fa-circle-check"></i> <?= $this->e($success) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <?php if (!empty($error_warning)): ?>
    <div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-circle-exclamation"></i>
      <?= $this->e($error_warning) ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>
  <div class="row"><?= $column_left ?>
    <div id="content" class="col  p-3 bg-white border"><?= $content_top ?>
      <div class="row">
        <div class="col mb-3">
          <div class="border rounded p-3 d-flex flex-column h-100">
            <h2><?= $this->e($text_new_customer) ?></h2>
            <p><strong><?= $this->e($text_register) ?></strong></p>
            <p><?= $this->e($text_register_account) ?></p>
            <div class="text-end">
              <a href="<?= $register ?>" class="btn btn-primary"><?= $this->e($button_continue) ?></a>
            </div>
          </div>
        </div>
        <div class="col mb-3">
          <div class="border rounded p-3 d-flex flex-column h-100">
            <form id="<?= $form_name ?>" action="<?= $login ?>" method="post" data-oc-toggle="ajax">
              <h2><?= $this->e($text_returning_customer) ?></h2>
              <p><strong><?= $this->e($text_i_am_returning_customer) ?></strong></p>
              <div class="mb-3">
                <label for="input-email" class="col-form-label"><?= $this->e($entry_email) ?></label>
                <input type="text" name="email" value="<?= isset($email) ? $email : '' ?>"
                  placeholder="<?= $entry_email ?>" id="input-email" class="form-control">
              </div>
              <div class="mb-3">
                <label for="input-password" class="col-form-label"><?= $this->e($entry_password) ?></label>

                <input type="password" name="password" value="<?= isset($password) ? $password : '' ?>"
                  placeholder="<?= $this->e($entry_password) ?>" id="input-password" class="form-control mb-1">

                <a href="<?= $forgotten ?>"><?= $this->e($text_forgotten) ?></a>
              </div>
              <div class="text-end">
                <button type="submit" class="btn btn-primary"><?= $this->e($button_login) ?></button>
              </div>
              <?php if ($redirect): ?>
                <input type="hidden" name="redirect" value="<?= $redirect ?>">
              <?php endif; ?>
            </form>
          </div>
        </div>
      </div>
      <?= $content_bottom ?>
    </div>
    <?= $column_right ?>
  </div>
</div>

<script>

  $(document).ready(function () {
    $('#<?= $form_name ?>').append($('<input>', { type: 'hidden', name: '<?= $login_token_field ?>', value: '<?= $login_token ?>' }));
  });


</script>
<?= $footer ?>