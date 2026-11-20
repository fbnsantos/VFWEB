<div class="head"><h1>Painel</h1><a class="btn" href="/" target="_blank">Ver site ↗</a></div>

<div class="stats">
    <a class="stat" href="/admin/messages"><b><?= (int) $unread ?></b><span>Mensagens por ler</span></a>
    <?php foreach (collections() as $name => $col): ?>
        <a class="stat" href="/admin/c/<?= e($name) ?>"><b><?= (int) $counts[$name] ?></b><span><?= e($col['label']) ?></span></a>
    <?php endforeach; ?>
</div>

<div class="grid2">
    <section class="card">
        <h2>Editar textos</h2>
        <p class="hint">Títulos, parágrafos, botões, contactos e imagens de cada página.</p>
        <ul class="links">
            <?php foreach (text_pages() as $key => $p): ?>
                <li><a href="/admin/texts/<?= e($key) ?>"><?= e($p['label']) ?></a></li>
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
