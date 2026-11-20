<div class="head"><h1>Painel</h1><a class="btn" href="/" target="_blank">Ver site ↗</a></div>

<div class="stats">
    <a class="stat" href="/admin/stats"><b><?= (int) $views30 ?></b><span>Visualizações (30 dias)</span></a>
    <a class="stat" href="/admin/messages"><b><?= (int) $unread ?></b><span>Mensagens por ler</span></a>
    <?php foreach (collections() as $name => $col): ?>
        <a class="stat" href="/admin/c/<?= e($name) ?>"><b><?= (int) $counts[$name] ?></b><span><?= e($col['label']) ?></span></a>
    <?php endforeach; ?>
</div>

<div class="grid2">
    <section class="card">
        <h2>Editar o site</h2>
        <p class="hint">Cada página tem os seus textos e listas, em separadores.</p>
        <ul class="links">
            <?php foreach (admin_nav()['Conteúdo'] as [$label, $items]): ?>
                <li><a href="<?= e($items[0][1]) ?>"><?= e($label) ?></a>
                    <?php if (count($items) > 1): ?><span class="muted small">— <?= e(implode(', ', array_column($items, 0))) ?></span><?php endif; ?></li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="card">
        <h2>Últimas mensagens</h2>
        <?php if ($recent): ?>
            <ul class="links">
                <?php foreach ($recent as $m): ?>
                    <li><a href="/admin/messages#m<?= (int) $m['id'] ?>"><?= $m['is_read'] ? '' : '● ' ?><?= e($m['name']) ?></a>
                        <span class="muted small"><?= e(substr($m['created_at'], 0, 16)) ?></span></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="muted">Ainda não chegou nenhuma mensagem pelo formulário de contacto.</p>
        <?php endif; ?>
    </section>
</div>

<section class="card">
    <h2>Como funciona</h2>
    <ul class="links">
        <li><b>Textos das páginas</b>: edite títulos e parágrafos; use <code>*palavra*</code> para destacar a verde nos títulos.</li>
        <li><b>Listas</b>: notícias, equipa, tecnologia, setores e conteúdo do Orioos. Crie, edite, esconda ou reordene com ↑ ↓.</li>
        <li><b>Imagens e vídeos</b>: envie ficheiros novos, ou envie diretamente dentro de cada campo.</li>
        <li><b>Atualizar site (git)</b>: o código vem do GitHub; o conteúdo editado aqui fica guardado no servidor e não é afetado.</li>
    </ul>
</section>
