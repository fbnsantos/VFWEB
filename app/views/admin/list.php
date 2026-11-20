<?php $sortable = !empty($col['sortable']); $first = array_key_first($col['list']); ?>
<div class="head">
    <h1><?= e($col['label']) ?></h1>
    <div class="actions">
        <?php if (!empty($col['url'])): ?><a class="btn" href="<?= e($col['url']) ?>" target="_blank">Ver no site ↗</a><?php endif; ?>
        <a class="btn primary" href="/admin/c/<?= e($name) ?>/new">+ Nova <?= e($col['singular']) ?></a>
    </div>
</div>
<?php if (!empty($col['hint'])): ?><p class="hint"><?= e($col['hint']) ?></p><?php endif; ?>

<?php if ($rows): ?>
    <table class="table card">
        <thead><tr>
            <?php foreach ($col['list'] as $field => $label): ?><th><?= e($label) ?></th><?php endforeach; ?>
            <th></th>
        </tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr<?= $r['visible'] ? '' : ' class="off"' ?>>
                <?php foreach ($col['list'] as $field => $label):
                    $spec = $col['fields'][$field];
                    $val = (string) $r[$field];
                    if ($spec['type'] === 'select') {
                        $val = $spec['options'][$val] ?? $val;
                    }
                    $val = mb_strimwidth($val, 0, 70, '…'); ?>
                    <td>
                    <?php if ($field === $first): ?>
                        <a href="/admin/c/<?= e($name) ?>/<?= (int) $r['id'] ?>"><b><?= e($val) ?></b></a>
                        <?php if (!$r['visible']): ?><span class="badge draft">escondido</span><?php endif; ?>
                        <?php if (!empty($r['featured'])): ?><span class="badge pub">destaque</span><?php endif; ?>
                    <?php else: ?>
                        <?= e($val) ?>
                    <?php endif; ?>
                    </td>
                <?php endforeach; ?>
                <td class="nowrap right">
                    <?php if ($sortable): ?>
                        <form class="inline" method="post" action="/admin/c/<?= e($name) ?>/<?= (int) $r['id'] ?>/move">
                            <?= csrf_field() ?>
                            <button class="btn small" name="dir" value="up" title="Subir">↑</button>
                            <button class="btn small" name="dir" value="down" title="Descer">↓</button>
                        </form>
                    <?php endif; ?>
                    <a class="btn small" href="/admin/c/<?= e($name) ?>/<?= (int) $r['id'] ?>">Editar</a>
                    <form class="inline" method="post" action="/admin/c/<?= e($name) ?>/<?= (int) $r['id'] ?>/delete">
                        <?= csrf_field() ?>
                        <button class="btn small danger-text" data-confirm="Apagar? Não é possível desfazer. (Para só esconder do site, edite e desmarque «Visível».)">Apagar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="muted">Ainda não há itens.</p>
<?php endif; ?>
