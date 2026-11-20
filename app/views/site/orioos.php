<?php
/** @var array $metrics @var array $caps @var array $specs @var array $sensors @var array $platform @var array $reqs */
$email = tx('site.email');
$modes = array_keys($caps);
?>
<!-- ── HERO ── -->
<header class="product-hero" id="top">
  <div class="hero-grid" aria-hidden="true"></div>
  <div class="product-hero-inner">
    <div class="reveal">
      <div class="hero-eyebrow"><?= e(tx('orioos.hero_eyebrow')) ?></div>
      <h1 class="product-title"><?= tx_html('orioos.hero_title') ?></h1>
      <p class="product-subtitle"><?= e(tx('orioos.hero_subtitle')) ?></p>
      <div class="hero-cta">
        <a class="btn btn-primary btn-lg" href="#contact"><?= e(tx('orioos.hero_cta1')) ?></a>
        <a class="btn btn-ghost btn-lg" href="#specifications"><?= e(tx('orioos.hero_cta2')) ?></a>
      </div>
    </div>

    <div class="hero-visual reveal">
      <img src="<?= e(tx_file('orioos.hero_image')) ?>" alt="<?= e(tx('orioos.hero_image_alt')) ?>">
    </div>
  </div>
</header>

<?php if ($metrics): ?>
<!-- ── METRICS ── -->
<section class="metrics-strip">
  <div class="metrics-inner" data-stagger="70">
<?php foreach ($metrics as $m): ?>
    <div class="metric reveal">
      <div class="metric-value"><?= e($m['value']) ?></div>
      <div class="metric-label"><?= e($m['label']) ?></div>
    </div>
<?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php if (tx('orioos.video') !== ''): ?>
<!-- ── PRODUCT VIDEO ── -->
<section id="orioos-video" data-stagger="70">
  <div class="section-wrap">
    <div class="reveal">
      <div class="section-label"><?= e(tx('orioos.video_label')) ?></div>
      <h2 class="section-title"><?= e(tx('orioos.video_title')) ?></h2>
      <p class="section-desc"><?= e(tx('orioos.video_desc')) ?></p>
    </div>

    <div class="product-video reveal">
      <video autoplay muted loop playsinline preload="metadata">
        <source src="<?= e(tx_file('orioos.video')) ?>" type="video/<?= str_ends_with(tx('orioos.video'), '.webm') ? 'webm' : 'mp4' ?>">
      </video>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ── OVERVIEW ── -->
<section>
  <div class="section-wrap">
    <div class="two-col">
      <div class="reveal">
        <div class="section-label"><?= e(tx('orioos.overview_label')) ?></div>
        <h2 class="section-title"><?= e(tx('orioos.overview_title')) ?></h2>
      </div>
      <div class="copy-block reveal">
<?php foreach (lines(tx('orioos.overview_text')) as $para): ?>
        <p><?= e($para) ?></p>
<?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<?php if ($caps): ?>
<!-- ── CAPABILITIES ── -->
<section>
  <div class="section-wrap">
    <div class="capability-header reveal">
      <div>
        <div class="section-label"><?= e(tx('orioos.cap_label')) ?></div>
        <h2 class="section-title"><?= e(tx('orioos.cap_title')) ?></h2>
        <p class="section-desc"><?= e(tx('orioos.cap_desc')) ?></p>
      </div>

<?php if (count($modes) > 1): ?>
      <div class="capability-toggle" role="tablist" aria-label="Capability mode">
<?php foreach ($modes as $i => $mode): ?>
        <button<?= $i === 0 ? ' class="is-active"' : '' ?> type="button" role="tab" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>" aria-controls="<?= e($mode) ?>-panel" data-capability="<?= e($mode) ?>"><?= e(tx("orioos.cap_tab_$mode")) ?></button>
<?php endforeach; ?>
      </div>
<?php endif; ?>
    </div>

<?php foreach ($modes as $i => $mode): ?>
    <div class="capability-panel<?= $i === 0 ? ' is-active' : '' ?>" id="<?= e($mode) ?>-panel" role="tabpanel" data-panel="<?= e($mode) ?>">
      <div class="feature-grid" data-stagger="70">
<?php foreach ($caps[$mode] as $c): ?>
        <div class="feature-card reveal">
          <div class="feature-icon"><?= icon_svg($c['icon']) ?></div>
          <h3><?= e($c['title']) ?></h3>
          <p><?= e($c['body']) ?></p>
        </div>
<?php endforeach; ?>
      </div>

<?php if (tx("orioos.cap_note_$mode") !== ''): ?>
      <div class="use-case-note reveal"><?= e(tx("orioos.cap_note_$mode")) ?></div>
<?php endif; ?>
    </div>
<?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- ── SPECIFICATIONS ── -->
<section id="specifications">
  <div class="section-wrap">
    <div class="two-col">
      <div class="reveal">
        <div class="section-label"><?= e(tx('orioos.spec_label')) ?></div>
        <h2 class="section-title"><?= e(tx('orioos.spec_title')) ?></h2>
        <p class="section-desc"><?= e(tx('orioos.spec_desc')) ?></p>
      </div>

      <div class="spec-panel reveal">
<?php foreach ($specs as $s): ?>
        <div class="spec-row">
          <div class="spec-key"><?= e($s['name']) ?></div>
          <div class="spec-value"><?= e($s['value']) ?></div>
        </div>
<?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<?php if ($sensors): ?>
<!-- ── SENSORS ── -->
<section>
  <div class="section-wrap">
    <div class="reveal">
      <div class="section-label"><?= e(tx('orioos.sensors_label')) ?></div>
      <h2 class="section-title"><?= e(tx('orioos.sensors_title')) ?></h2>
      <p class="section-desc"><?= e(tx('orioos.sensors_desc')) ?></p>
    </div>

    <div class="feature-grid" data-stagger="70">
<?php foreach ($sensors as $s): ?>
      <div class="feature-card reveal">
        <h3><?= e($s['title']) ?></h3>
        <p><?= e($s['body']) ?></p>
      </div>
<?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($platform): ?>
<!-- ── PLATFORM ── -->
<section class="dark-band">
  <div class="section-wrap">
    <div class="reveal">
      <div class="section-label"><?= e(tx('orioos.platform_label')) ?></div>
      <h2 class="section-title"><?= e(tx('orioos.platform_title')) ?></h2>
      <p class="section-desc"><?= e(tx('orioos.platform_desc')) ?></p>
    </div>

    <div class="platform-grid" data-stagger="70">
<?php foreach ($platform as $p): ?>
      <div class="platform-card reveal">
        <h3><?= e($p['title']) ?></h3>
        <p><?= e($p['body']) ?></p>
      </div>
<?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ── PRIVACY ── -->
<section>
  <div class="section-wrap">
    <div class="two-col">
      <div class="reveal">
        <div class="section-label"><?= e(tx('orioos.privacy_label')) ?></div>
        <h2 class="section-title"><?= e(tx('orioos.privacy_title')) ?></h2>
        <p class="section-desc"><?= e(tx('orioos.privacy_desc')) ?></p>
      </div>

      <div class="copy-block reveal">
<?php foreach (lines(tx('orioos.privacy_text')) as $para): ?>
        <p><?= e($para) ?></p>
<?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<?php if ($reqs): ?>
<!-- ── DEPLOYMENT ── -->
<section>
  <div class="section-wrap">
    <div class="reveal">
      <div class="section-label"><?= e(tx('orioos.deploy_label')) ?></div>
      <h2 class="section-title"><?= e(tx('orioos.deploy_title')) ?></h2>
      <p class="section-desc"><?= e(tx('orioos.deploy_desc')) ?></p>
    </div>

    <div class="requirements-list" data-stagger="70">
<?php foreach ($reqs as $r): ?>
      <div class="requirement-card reveal">
        <h3><?= e($r['title']) ?></h3>
        <ul>
<?php foreach (lines($r['items']) as $item): ?>
          <li><?= e($item) ?></li>
<?php endforeach; ?>
        </ul>
      </div>
<?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ── CTA ── -->
<section id="contact">
  <div class="section-wrap">
    <div class="cta-card reveal">
      <div>
        <h2><?= e(tx('orioos.cta_title')) ?></h2>
        <p><?= e(tx('orioos.cta_text')) ?></p>
      </div>
      <a class="btn btn-primary btn-lg" href="mailto:<?= e($email) ?>?subject=<?= rawurlencode('Orioos Demo Request') ?>"><?= e(tx('orioos.cta_btn')) ?></a>
    </div>
  </div>
</section>
