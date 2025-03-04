<?= $header ?>
<div id="product-category" class="container">
    <?= $breadcrumb ?>
    <div class="row">
        <?= $column_left ?>
        <div id="content" class="col">
            <?= $content_top ?>
            <h1><?= $heading_title ?></h1>

            <?php if ($image): ?>
                <img src="<?= $image ?>" alt="<?= $heading_title ?>" title="<?= $heading_title ?>"
                    class="img-thumbnail mb-3" />
            <?php endif; ?>

            <?php if ($description): ?>
                <div class="mb-3"><?= $description ?></div>
            <?php endif; ?>
            <hr />

            <h3><?= $text_refine ?></h3>
            <div class="input-group dropdown mb-3">
                <input type="text" name="search" value="<?= $search ?>" placeholder="<?= $entry_search ?>"
                    id="input-search" class="form-control">
                <?php if ($topics): ?>
                    <select name="topic_id" id="input-topic" class="form-select">
                        <option value=""><?= $text_all ?></option>
                        <?php foreach ($topics as $topic): ?>
                            <option value="<?= $topic['topic_id'] ?>" <?= ($topic['topic_id'] == $topic_id) ? ' selected' : '' ?>>
                                <?= $topic['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
                <button id="button-search" class="btn btn-primary"><?= $button_search ?></button>
            </div>

            <?php if ($articles): ?>
                <?php foreach ($articles as $article): ?>
                    <div class="mb-4">
                        <h2><a href="<?= $article['href'] ?>"><?= $article['name'] ?></a></h2>
                        <?php if ($article['image']): ?>
                            <div><a href="<?= $article['href'] ?>"><img src="<?= $article['image'] ?>"
                                        title="<?= $article['name'] ?>" alt="<?= $article['name'] ?>"
                                        class="img-thumbnail mb-1" /></a></div>
                        <?php endif; ?>
                        <ul class="list-inline">
                            <li class="list-inline-item"><?= $text_by ?> <a
                                    href="<?= $article['filter_author'] ?>"><?= $article['author'] ?></a></li>
                            <li class="list-inline-item"><?= $article['date_added'] ?></li>
                            <li class="list-inline-item"><?= $article['comment_total'] ?>         <?= $text_comment ?></li>
                        </ul>
                        <p class="mb-3"><?= $article['description'] ?></p>
                        <div class="text-end">
                            <a href="<?= $article['href'] ?>" class="btn btn-primary"><?= $button_continue ?></a>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="row">
                    <div class="col-sm-6 text-start"><?= $pagination ?></div>
                    <div class="col-sm-6 text-end"><?= $results ?></div>
                </div>

            <?php else: ?>
                <p><?= $text_no_results ?></p>
            <?php endif; ?>

            <?= $content_bottom ?>
        </div>
        <?= $column_right ?>
    </div>
</div>
<script><!--
  $('#button-search').bind('click', function() {
      url = 'index.php?route=cms/blog&language=<?= $language ?>';

    var search = $('#input-search').val();

    if (search) {
        url += '&search=' + encodeURIComponent(search);
    }

    var topic_id = $('#input-topic').prop('value');

    if (topic_id > 0) {
        url += '&topic_id=' + topic_id;
    }

    location = url;
  });

    $('#input-search').bind('keydown', function (e) {
        if (e.keyCode == 13) {
            $('#button-search').trigger('click');
        }
    });
    //--></script>
<?= $footer ?>