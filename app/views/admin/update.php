<h1>Atualizar site (git)</h1>

<?php if ($result): ?>
    <div class="flash <?= $result['ok'] ? 'ok' : 'error' ?>"><?= $result['ok'] ? 'Site atualizado.' : 'A atualização falhou. Veja o registo abaixo.' ?></div>
    <pre class="card deploy-log"><?= e($result['output']) ?></pre>
<?php endif; ?>

<form class="card narrow" method="post" onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').textContent = 'A atualizar…';">
    <?= csrf_field() ?>
    <p>Vai buscar a versão mais recente do ramo <strong><?= e($branch) ?></strong> ao GitHub e substitui o código no servidor
        (o mesmo que o <code>git.php</code>). Textos, listas, imagens enviadas e <code>config.php</code> não são afetados.</p>
    <p class="muted">Versão instalada: <?= $version !== '' ? e($version) : 'desconhecida (git indisponível)' ?></p>
    <button class="btn primary" data-confirm="Atualizar o site com a versão do GitHub?">Atualizar agora</button>
</form>

<div class="card">
    <h2>Deploy automático (git.php)</h2>
    <?php if ($hook): ?>
        <p class="ok-text">Ativo: o <code>deploy_secret</code> está definido em <code>config.php</code>.</p>
        <p>No GitHub → <em>Settings → Webhooks → Add webhook</em>: URL <code><?= e(abs_url('/git.php')) ?></code>,
            Content type <code>application/json</code>, Secret = o <code>deploy_secret</code>, evento <em>push</em>.
            Cada <code>git push</code> para <code><?= e($branch) ?></code> atualiza o site em segundos.</p>
        <p>À mão: <code><?= e(abs_url('/git.php')) ?>?token=&lt;deploy_secret&gt;</code></p>
    <?php else: ?>
        <p class="muted">Desativado. Defina <code>deploy_secret</code> (valor longo e aleatório) em <code>config.php</code> para ativar o <code>git.php</code>.</p>
    <?php endif; ?>
    <?php if ($log !== ''): ?>
        <h3>Últimos deploys</h3>
        <pre class="deploy-log"><?= e($log) ?></pre>
    <?php endif; ?>
</div>
