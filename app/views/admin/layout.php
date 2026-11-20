<?php
$path = (string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$unread = empty($bare) ? unread_messages() : 0;
$matches = fn(string $href) => $path === $href || str_starts_with($path, $href . '/');

// Entrada do menu ativa (a que contém o ecrã atual) e os seus separadores.
$activeKey = null;
$tabs = [];
foreach (admin_nav() as $entries) {
    foreach ($entries as $key => [$label, $items]) {
        foreach ($items as [, $href]) {
            if ($matches($href)) {
                $activeKey = $key;
                $tabs = $items;
            }
        }
    }
}
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
            <a href="/admin"<?= $path === '/admin' ? ' class="active"' : '' ?>>Painel</a>
            <?php foreach (admin_nav() as $section => $entries): ?>
                <div class="group"><?= e($section) ?></div>
                <?php foreach ($entries as $key => [$label, $items]): ?>
                    <a href="<?= e($items[0][1]) ?>"<?= $key === $activeKey ? ' class="active"' : '' ?>><?= e($label) ?><?php
                        if ($key === 'messages' && $unread): ?> <span class="count"><?= $unread ?></span><?php endif; ?></a>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </nav>
        <div class="side-foot">
            <a href="/" target="_blank">Ver site ↗</a>
            <a href="/admin/password">Palavra-passe</a>
            <form method="post" action="/admin/logout"><?= csrf_field() ?><button class="link">Sair</button></form>
        </div>
    </aside>
<?php endif; ?>
<main class="main">
    <?php if (count($tabs) > 1): ?>
        <nav class="tabs" aria-label="Secções desta página">
            <?php foreach ($tabs as [$tabLabel, $tabHref]): ?>
                <a href="<?= e($tabHref) ?>"<?= $matches($tabHref) ? ' class="active" aria-current="page"' : '' ?>><?= e($tabLabel) ?></a>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>
    <?php if ($flash): ?><div class="flash <?= e($flash['type']) ?>"><?= e($flash['msg']) ?></div><?php endif; ?>
    <?= $content ?>
</main>
<script src="/assets/admin.js?v=<?= filemtime(ROOT . '/assets/admin.js') ?>"></script>
</body>
</html>
