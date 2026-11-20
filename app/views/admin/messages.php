<div class="head"><h1>Mensagens</h1></div>
<p class="hint">Mensagens enviadas pelo formulário de contacto do site. Ficam sempre guardadas aqui; se o servidor enviar email, também chegam ao endereço definido em Textos → Geral.</p>

<?php if ($items): foreach ($items as $m): ?>
    <article class="card msg<?= $m['is_read'] ? '' : ' unread' ?>" id="m<?= (int) $m['id'] ?>">
        <div class="msg-head">
            <div>
                <b><?= e($m['name']) ?></b> · <a href="mailto:<?= e($m['email']) ?>?subject=<?= rawurlencode('Re: ' . tx('site.name') . ' — Contact Request') ?>"><?= e($m['email']) ?></a>
                <div class="muted small"><?= e($m['created_at']) ?> · <?= $m['mailed'] ? 'email enviado' : 'sem email (só guardada)' ?></div>
            </div>
            <div class="actions">
                <form class="inline" method="post" action="/admin/messages/<?= (int) $m['id'] ?>/<?= $m['is_read'] ? 'unread' : 'read' ?>">
                    <?= csrf_field() ?><button class="btn small"><?= $m['is_read'] ? 'Marcar por ler' : 'Marcar como lida' ?></button>
                </form>
                <form class="inline" method="post" action="/admin/messages/<?= (int) $m['id'] ?>/delete">
                    <?= csrf_field() ?><button class="btn small danger-text" data-confirm="Apagar esta mensagem?">Apagar</button>
                </form>
            </div>
        </div>
        <p class="msg-body"><?= nl2br(e($m['message'])) ?></p>
    </article>
<?php endforeach; else: ?>
    <p class="muted">Ainda não há mensagens.</p>
<?php endif; ?>
