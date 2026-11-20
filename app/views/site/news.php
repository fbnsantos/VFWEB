<?php
/** @var ?array $featured @var array $items */
$arrow = '<svg viewBox="0 0 16 16" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8h9M9 4l4 4-4 4"/></svg>';
$order = array_keys(collections()['news']['fields']['category']['options']);
$present = array_values(array_filter($order, fn($c) => in_array($c, array_column($items, 'category'), true)));
$plural = ['press' => 'Press', 'announcement' => 'Announcements', 'event' => 'Events'];
?>
<!-- ── HERO ── -->
<section class="news-hero" id="top">
  <div class="hero-grid" aria-hidden="true"></div>
  <div class="news-hero-inner">
    <div class="news-eyebrow"><?= e(tx('news.hero_eyebrow')) ?></div>
    <h1><?= tx_html('news.hero_title') ?></h1>
    <p class="lead"><?= e(tx('news.hero_lead')) ?></p>
  </div>
</section>

<?php if ($featured): ?>
<!-- ── FEATURED STORY ── -->
<section id="featured">
  <div class="section-wrap" style="padding-bottom: var(--sp-8);">
    <div class="section-label reveal"><?= e(tx('news.featured_label')) ?></div>
    <a class="featured-card reveal" href="<?= e($featured['url'] ?: '#') ?>">
      <div class="featured-media"><span class="featured-badge"><?= e($featured['badge'] ?: 'Featured') ?></span>
        <img src="<?= e(news_image($featured)) ?>" alt="<?= e($featured['image_alt'] ?: $featured['title']) ?>"></div>
      <div class="featured-info"><div class="featured-meta">
        <span class="tag"><?= e(news_label($featured['category'])) ?></span><span class="meta-date"><?= e(news_date($featured['date'])) ?></span></div>
        <h2><?= e($featured['title']) ?></h2><p><?= e($featured['excerpt']) ?></p>
        <span class="read-link">Read the article <?= $arrow ?></span></div>
    </a>
  </div>
</section>
<?php endif; ?>

<!-- ── ALL UPDATES ── -->
<section id="all-news">
  <div class="section-wrap" style="padding-top: var(--sp-8);">
    <div class="reveal">
      <div class="section-label"><?= e(tx('news.all_label')) ?></div>
      <h2 class="section-title"><?= e(tx('news.all_title')) ?></h2>
    </div>

    <div class="news-controls reveal" id="newsControls"<?= count($present) < 2 ? ' style="display:none"' : '' ?>>
      <div class="filter-toggle" id="filterToggle" role="tablist" aria-label="Filter news by type">
<?php if (count($present) >= 2): ?>
        <button class="is-active" data-filter="all" role="tab" aria-selected="true">All</button>
<?php foreach ($present as $c): ?>
        <button data-filter="<?= e($c) ?>" role="tab" aria-selected="false"><?= e($plural[$c] ?? news_label($c)) ?></button>
<?php endforeach; ?>
<?php endif; ?>
      </div>
      <div class="news-count"><span id="newsCount"><?= count($items) ?></span> items</div>
    </div>

    <div class="news-grid" id="newsGrid" data-stagger="60">
<?php foreach ($items as $n): ?>
      <a class="news-card reveal" data-category="<?= e($n['category']) ?>" href="<?= e($n['url'] ?: '#') ?>">
        <div class="news-card-media"><img src="<?= e(news_image($n)) ?>" alt="<?= e($n['image_alt'] ?: $n['title']) ?>" loading="lazy"></div>
        <div class="news-card-top"><span class="tag"><?= e(news_label($n['category'])) ?></span>
          <span class="meta-date"><?= e(news_date($n['date'])) ?></span></div>
        <h3><?= e($n['title']) ?></h3>
        <p><?= e($n['excerpt']) ?></p>
        <div class="news-card-foot"><span class="news-source"><?= e($n['source']) ?></span>
          <span class="read-link">Read <?= $arrow ?></span></div>
      </a>
<?php endforeach; ?>
    </div>
    <div class="news-empty<?= $items ? '' : ' is-shown' ?>" id="newsEmpty"><?= e(tx('news.empty')) ?></div>
  </div>
</section>
