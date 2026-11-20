<form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="head">
        <h1><?= e($def['label']) ?></h1>
        <div class="actions">
            <a class="btn" href="<?= e($def['url']) ?>" target="_blank">Ver página ↗</a>
            <button class="btn primary">Guardar</button>
        </div>
    </div>

    <?php if ($pageKey === 'seo'): ?>
        <section class="card">
            <h2>Estado do SEO</h2>
            <ul class="checks">
                <?php foreach ($checks as [$kind, $text]): ?><li class="<?= e($kind) ?>"><?= e($text) ?></li><?php endforeach; ?>
            </ul>
        </section>
        <section class="card">
            <h2>Como aparece no Google (guardado)</h2>
            <?php foreach (['home' => ['Página inicial', '/'], 'orioos' => ['Página Orioos', '/orioos'], 'news' => ['Notícias', '/news']] as $p => [$label, $url]): ?>
                <div class="muted small"><?= e($label) ?></div>
                <div class="serp">
                    <div class="u"><?= e(abs_url($url)) ?></div>
                    <div class="t"><?= e(mb_strimwidth(tx("$p.meta_title"), 0, 62, '…')) ?></div>
                    <div class="d"><?= e(mb_strimwidth(tx("$p.meta_description"), 0, 162, '…')) ?></div>
                </div>
            <?php endforeach; ?>
            <p class="hint">Ficheiros automáticos: <a href="/robots.txt" target="_blank">robots.txt</a> · <a href="/sitemap.xml" target="_blank">sitemap.xml</a>.
                A pré-visualização atualiza depois de guardar.</p>
        </section>
    <?php endif; ?>

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
