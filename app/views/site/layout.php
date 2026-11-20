<?php
/** @var string $page @var string $title @var string $description @var string $content */
$h = hp($page);
$contactHref = $page === 'news' ? '/#contact' : '#contact';
$cssFile = PAGES[$page]['css'] ?? 'news'; // 404 usa o estilo do topo das notícias
$canonical = abs_url(PAGES[$page]['path'] ?? '/');
$shareImage = tx('site.share_image') !== '' ? abs_url(tx('site.share_image')) : '';
$noindex = !empty($noindex) || !indexing_allowed();
$linkedin = tx('site.linkedin');
$mailto = tx('site.email');
$v = fn(string $file) => '?v=' . (is_file(ROOT . $file) ? filemtime(ROOT . $file) : 0);
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title) ?></title>
<?php if ($description !== ''): ?><meta name="description" content="<?= e($description) ?>">
<?php endif; ?>
<?php if ($noindex): ?><meta name="robots" content="noindex, nofollow">
<?php else: ?><link rel="canonical" href="<?= e($canonical) ?>">
<?php endif; ?>
<?php if (tx('seo.google_verification') !== ''): ?><meta name="google-site-verification" content="<?= e(tx('seo.google_verification')) ?>">
<?php endif; ?>
<?php if (tx('seo.bing_verification') !== ''): ?><meta name="msvalidate.01" content="<?= e(tx('seo.bing_verification')) ?>">
<?php endif; ?>
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= e(tx('site.name')) ?>">
<meta property="og:title" content="<?= e($title) ?>">
<meta property="og:description" content="<?= e($description) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<?php if ($shareImage !== ''): ?><meta property="og:image" content="<?= e($shareImage) ?>">
<meta name="twitter:card" content="summary_large_image">
<?php endif; ?>
<link rel="icon" href="<?= e(asset_url(tx('site.logo'))) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/base.css<?= $v('/css/base.css') ?>">
<link rel="stylesheet" href="/css/<?= e($cssFile) ?>.css<?= $v("/css/$cssFile.css") ?>">
<?php if ($page === 'home'): ?>
<script type="application/ld+json"><?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => tx('site.name'),
    'url' => abs_url('/'),
    'logo' => abs_url(tx('site.logo')),
    'email' => tx('site.email'),
    'sameAs' => $linkedin !== '' ? [$linkedin] : [],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>
<?php endif; ?>
</head>
<body>

<nav>
  <a class="nav-logo" href="<?= $h ?>#top">
    <img src="<?= e(asset_url(tx('site.logo'))) ?>" alt="<?= e(tx('site.name')) ?>">
    <span class="nav-wordmark"><?= e(tx('site.name')) ?></span>
  </a>
  <div class="nav-links">
    <a href="<?= $h ?>#technology">Technology</a>
    <a href="<?= $h ?>#industries">Industries</a>
    <div class="nav-item">
      <a href="<?= $h ?>#solutions">Solutions</a>
      <div class="nav-dropdown" aria-label="Solutions submenu">
        <a href="/orioos"<?= $page === 'orioos' ? ' aria-current="page"' : '' ?>>Orioos</a>
<?php foreach (subsystems() as [$subName, $subUrl]): ?>
        <a href="<?= e($subUrl) ?>"><?= e($subName) ?></a>
<?php endforeach; ?>
      </div>
    </div>
    <a href="<?= $h ?>#team">Team</a>
    <div class="nav-item">
      <a href="/news"<?= $page === 'news' ? ' aria-current="page"' : '' ?>>News</a>
      <div class="nav-dropdown nav-dropdown-news" aria-label="Latest news">
        <a href="/news"><strong>All news →</strong></a>
<?php foreach (latest_news(4) as $n): ?>
        <a class="nav-news-link" href="<?= e($n['url'] ?: '/news') ?>"<?= preg_match('#^https?://#i', (string) $n['url']) ? ' target="_blank" rel="noopener"' : '' ?>>
          <img src="<?= e(news_image($n)) ?>" alt="" loading="lazy">
          <span><small><?= e(news_date($n['date'])) ?></small><?= e($n['title']) ?></span>
        </a>
<?php endforeach; ?>
      </div>
    </div>
  </div>
  <a class="nav-cta" href="<?= $contactHref ?>">Contact us</a>
</nav>

<?= $content ?>

<footer>
  <div class="footer-inner">
    <div class="footer-top">
      <div>
        <div class="footer-brand">
          <img src="<?= e(asset_url(tx('site.logo_white'))) ?>" alt="<?= e(tx('site.name')) ?>">
          <span class="footer-brand-name"><?= e(tx('site.name')) ?></span>
        </div>
        <p class="footer-tagline"><?= e(tx('site.footer_tagline')) ?></p>
<?php if ($linkedin !== ''): ?>
        <div class="footer-socials">
          <a class="social-btn" href="<?= e($linkedin) ?>" target="_blank" rel="noopener" aria-label="LinkedIn">
            <svg viewBox="0 0 24 24"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6ZM2 9h4v12H2Z"/><circle cx="4" cy="4" r="2"/></svg>
          </a>
        </div>
<?php endif; ?>
      </div>

      <div class="footer-col">
        <h4>Navigation</h4>
        <nav>
          <a href="<?= $h ?>#top">Home</a>
          <a href="<?= $h ?>#technology">Technology</a>
          <a href="<?= $h ?>#industries">Industries</a>
          <a href="<?= $h ?>#solutions">Solutions</a>
          <a href="<?= $h ?>#team">Team</a>
          <a href="/news">News</a>
<?php foreach (subsystems() as [$subName, $subUrl]): ?>
          <a href="<?= e($subUrl) ?>"><?= e($subName) ?> (login)</a>
<?php endforeach; ?>
          <a href="<?= $contactHref ?>">Contact</a>
        </nav>
      </div>

      <div class="footer-col">
        <h4>Contact</h4>
        <nav>
          <a href="mailto:<?= e($mailto) ?>"><?= e($mailto) ?></a>
          <span style="font-size:13px; color: oklch(62% 0.07 158); line-height:1.65;"><?= tx_br('site.address') ?></span>
        </nav>
      </div>

      <div class="footer-col">
        <h4><?= e(tx('site.backed_title')) ?></h4>
        <div style="display:flex; flex-direction:column; gap: var(--sp-4); margin-top: var(--sp-2);">
<?php foreach ([1 => 26, 2 => 24] as $n => $height): if (tx("site.backer{$n}_logo") === '') continue; ?>
          <img src="<?= e(asset_url(tx("site.backer{$n}_logo"))) ?>" alt="<?= e(tx("site.backer{$n}_name")) ?>" style="height:<?= $height ?>px; width:auto; object-fit:contain; opacity:.6; filter:brightness(0) invert(1);">
<?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="footer-copy">© <span id="year"><?= date('Y') ?></span> <?= e(tx('site.copyright')) ?></div>
      <div class="footer-legal">
        <a href="<?= $h ?>#top">Back to top</a>
<?php if ($page === 'home'): ?>
        <a href="#contact">Contact</a>
<?php else: ?>
        <a href="/">Back to home</a>
<?php endif; ?>
        <a href="/admin" rel="nofollow">Backoffice</a>
      </div>
    </div>
  </div>
</footer>

<script src="/js/main.js<?= $v('/js/main.js') ?>"></script>
<?php if ($page === 'news'): ?><script src="/js/news.js<?= $v('/js/news.js') ?>"></script>
<?php endif; ?>
</body>
</html>
