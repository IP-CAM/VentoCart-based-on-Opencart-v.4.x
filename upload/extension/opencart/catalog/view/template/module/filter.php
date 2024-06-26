<div class="card mb-3">
  <div class="card-header"><i class="fa-solid fa-filter"></i> <?= $heading_title ?></div>
  <div class="list-group list-group-flush">
    <?php foreach ($filter_groups as $filter_group): ?>
      <a class="list-group-item"><?= $filter_group['name'] ?></a>
      <div class="list-group-item">
        <div id="filter-group-<?= $filter_group['filter_group_id'] ?>">
          <?php foreach ($filter_group['filter'] as $filter): ?>
            <div class="form-check">

        <input
    type="checkbox"
    name="filter[]"
    value="<?= $filter['filter_id']  ?>"
    id="input-filter-<?= $filter['filter_id']  ?>"
    class="form-check-input"
    <?= (in_array($filter['filter_id'], $filter_category)) ? 'checked' : '' ?>
/>
            
              <label for="input-filter-<?= $filter['filter_id']  ?>" class="form-check-label"><?= $filter['name'] ?></label>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="card-footer text-right">
    <button type="button" id="button-filter" class="btn btn-primary"><i class="fa-solid fa-filter"></i> <?= $button_filter  ?></button>
  </div>
</div>
<script ><!--
$('#button-filter').on('click', function () {
    filter = [];

    $('input[name^=\'filter\']:checked').each(function (element) {
        filter.push(this.value);
    });

    location = '<?= $action  ?>&filter=' + filter.join(',');
});
//--></script>
