<?php
$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$unread = unread_messages();
$link = function (string $href, string $label, string $extra = '') use ($path) {
    $active = $href === '/admin' ? $path === '/admin' : str_starts_with((string) $path, $href);
    return '<a href="' . e($href) . '"' . ($active ? ' class="active"' : '') . '>' . e($label) . $extra . '</a>';
};
$flash = flash();
?><!doctype html>
<html lang="pt-PT">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf" content="<?= e(csrf_token()) ?>">
    <title><?= e($title ?? 'Backoffice') ?> · Backoffice</title>
    <link rel="icon" href="<?= e(asset_url(tx('site.logo'))) ?>">
    <link rel="stylesheet" href="/assets/admin.css?v=<?= filemtime(ROOT . '/assets/admin.css') ?>">
</head>
<body class="<?= !empty($bare) ? 'bare' : '' ?>">
<?php if (empty($bare)): ?>
    <aside class="side">
        <a class="logo" href="/admin"><?= e(tx('site.name')) ?> · backoffice</a>
        <nav>
            <?= $link('/admin', 'Painel') ?>
            <div class="group">Textos das páginas</div>
            <?php foreach (text_pages() as $key => $p): ?><?= $link("/admin/texts/$key", $p['label']) ?><?php endforeach; ?>
            <div class="group">Listas</div>
            <?php foreach (collections() as $key => $c): ?><?= $link("/admin/c/$key", $c['label']) ?><?php endforeach; ?>
            <div class="group">Mais</div>
            <?= $link('/admin/messages', 'Mensagens', $unread ? ' <span class="count">' . $unread . '</span>' : '') ?>
            <?= $link('/admin/media', 'Imagens e vídeos') ?>
            <?= $link('/admin/update', 'Atualizar site (git)') ?>
        </nav>
        <div class="side-foot">
            <a href="/" target="_blank">Ver site ↗</a>
            <a href="/admin/password">Palavra-passe</a>
            <form method="post" action="/admin/logout"><?= csrf_field() ?><button class="link">Sair</button></form>
        </div>
    </aside>
<?php endif; ?>
<main class="main">
    <?php if ($flash): ?><div class="flash <?= e($flash['type']) ?>"><?= e($flash['msg']) ?></div><?php endif; ?>
    <?= $content ?>
</main>
<script src="/assets/admin.js?v=<?= filemtime(ROOT . '/assets/admin.js') ?>"></script>
</body>
</html>
