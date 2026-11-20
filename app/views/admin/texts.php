<form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="head">
        <h1><?= e($def['label']) ?></h1>
        <div class="actions">
            <a class="btn" href="<?= e($def['url']) ?>" target="_blank">Ver página ↗</a>
            <button class="btn primary">Guardar</button>
        </div>
    </div>

    <?php foreach ($def['sections'] as $sectionTitle => $fields): ?>
        <section class="card">
            <h2><?= e($sectionTitle) ?></h2>
            <?php foreach ($fields as $key => $spec): ?>
                <?= render_field($key, $spec, tx($key)) ?>
            <?php endforeach; ?>
        </section>
    <?php endforeach; ?>

    <div class="stickybar"><button class="btn primary">Guardar</button></div>
</form>
