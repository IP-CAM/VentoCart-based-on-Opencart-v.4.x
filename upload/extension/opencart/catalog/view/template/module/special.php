<div class="container">
<h3><?= $heading_title ?></h3>
<div class="row<?= $axis === 'horizontal' ? ' row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4' : '' ?>">
    <?php foreach ($products as $product): ?>
        <div class="col mb-3"><?=  $product ?></div>
    <?php endforeach; ?>
</div>
</div>