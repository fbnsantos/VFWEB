<?php $isNew = empty($row['id']); ?>
<form method="post" enctype="multipart/form-data" action="/admin/c/<?= e($name) ?>/<?= $isNew ? 'new' : (int) $row['id'] ?>">
    <?= csrf_field() ?>
    <div class="head">
        <h1><?= e($title) ?></h1>
        <div class="actions">
            <a class="btn" href="/admin/c/<?= e($name) ?>">← Voltar à lista</a>
            <button class="btn primary">Guardar</button>
        </div>
    </div>

    <section class="card">
        <?php foreach ($col['fields'] as $key => $spec):
            $value = (string) ($row[$key] ?? ($isNew ? ($spec['default'] ?? '') : '')); ?>
            <?= render_field($key, $spec, $value) ?>
        <?php endforeach; ?>
        <label class="check"><input type="checkbox" name="visible" value="1"<?= ($isNew || !empty($row['visible'])) ? ' checked' : '' ?>> Visível no site</label>
    </section>
</form>
