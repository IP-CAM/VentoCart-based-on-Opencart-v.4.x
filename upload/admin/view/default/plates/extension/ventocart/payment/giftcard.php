<?= $header ?>
<?= $column_left ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="float-end">
                <button type="submit" form="form-payment" data-bs-toggle="tooltip" title="<?= $button_save ?>"
                    class="btn btn-primary"><i class="fa-solid fa-save"></i></button>
                <a href="<?= $back ?>" data-bs-toggle="tooltip" title="<?= $button_back ?>" class="btn btn-light"><i
                        class="fa-solid fa-reply"></i></a>
            </div>
            <h1><?= $heading_title ?></h1>
            <ol class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb): ?>
                    <li class="breadcrumb-item"><a href="<?= $breadcrumb['href'] ?>"><?= $breadcrumb['text'] ?></a></li>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-pencil"></i> <?= $text_edit ?></div>
            <div class="card-body">
                <form id="form-payment" action="<?= $save ?>" method="post" data-oc-toggle="ajax">
                    <div class="row mb-3">
                        <label for="input-order-status"
                            class="col-sm-2 col-form-label"><?= $entry_order_status ?></label>
                        <div class="col-sm-10">
                            <select name="payment_giftcard_order_status_id" id="input-order-status" class="form-select">
                                <?php foreach ($order_statuses as $order_status): ?>
                                    <option value="<?= $order_status['order_status_id'] ?>" <?php if (isset($payment_giftcard_order_status_id) && $order_status['order_status_id'] == $payment_giftcard_order_status_id)
                                          echo ' selected'; ?>><?= $order_status['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label"><?= $entry_status ?></label>
                        <div class="col-sm-10">
                            <div class="form-check form-switch form-switch-lg">
                                <input type="hidden" name="payment_giftcard_status" value="0" />
                                <input type="checkbox" name="payment_giftcard_status" value="1" id="input-status"
                                    class="form-check-input" <?php if ($payment_giftcard_status)
                                        echo ' checked'; ?> />
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="input-sort-order" class="col-sm-2 col-form-label"><?= $entry_sort_order ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="payment_giftcard_sort_order"
                                value="<?= $payment_giftcard_sort_order ?>" placeholder="<?= $entry_sort_order ?>"
                                id="input-sort-order" class="form-control" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $footer ?>