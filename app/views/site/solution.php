<?php
/** @var array $s Linha da coleção «solutions» (modelo igual ao da página do Orioos). */
$email = tx('site.email');
$metrics = pairs($s['metrics']);
$features = pairs($s['features']);
$specs = pairs($s['specs']);
$details = pairs($s['details']);
$overview = lines($s['overview_text']);
$cta1 = $s['cta1'] !== '' ? $s['cta1'] : 'Request a demo';
$img = $s['image'] !== '' ? asset_url($s['image']) : '';
?>
<!-- ── HERO ── -->
<header class="product-hero" id="top">
  <div class="hero-grid" aria-hidden="true"></div>
  <div class="product-hero-inner">
    <div class="reveal">
<?php if ($s['eyebrow'] !== ''): ?>
      <div class="hero-eyebrow"><?= e($s['eyebrow']) ?></div>
<?php endif; ?>
      <h1 class="product-title"><?= em_markup($s['title']) ?></h1>
<?php if ($s['subtitle'] !== ''): ?>
      <p class="product-subtitle"><?= e($s['subtitle']) ?></p>
<?php endif; ?>
      <div class="hero-cta">
        <a class="btn btn-primary btn-lg" href="#contact"><?= e($cta1) ?></a>
<?php if ($specs): ?>
        <a class="btn btn-ghost btn-lg" href="#specifications">Specifications</a>
<?php endif; ?>
      </div>
    </div>
<?php if ($img !== ''): ?>
    <div class="hero-visual reveal">
      <img src="<?= e($img) ?>" alt="<?= e($s['image_alt'] ?: $s['name']) ?>">
    </div>
<?php endif; ?>
  </div>
</header>

<?php if ($metrics): ?>
<!-- ── METRICS ── -->
<section class="metrics-strip">
  <div class="metrics-inner" data-stagger="70">
<?php foreach ($metrics as [$value, $label]): ?>
    <div class="metric reveal">
      <div class="metric-value"><?= e($value) ?></div>
      <div class="metric-label"><?= e($label) ?></div>
    </div>
<?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php if ($s['video'] !== ''): ?>
<!-- ── VIDEO ── -->
<section data-stagger="70">
  <div class="section-wrap">
    <div class="product-video reveal">
      <video muted loop playsinline preload="none" data-src="<?= e(asset_url($s['video'])) ?>"></video>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($overview || $s['overview_title'] !== ''): ?>
<!-- ── OVERVIEW ── -->
<section>
  <div class="section-wrap">
    <div class="two-col">
      <div class="reveal">
        <div class="section-label">Overview</div>
        <h2 class="section-title"><?= e($s['overview_title']) ?></h2>
      </div>
      <div class="copy-block reveal">
<?php foreach ($overview as $para): ?>
        <p><?= e($para) ?></p>
<?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($features): ?>
<!-- ── FEATURES ── -->
<section>
  <div class="section-wrap">
    <div class="reveal">
      <div class="section-label">Capabilities</div>
<?php if ($s['features_title'] !== ''): ?>
      <h2 class="section-title"><?= e($s['features_title']) ?></h2>
<?php endif; ?>
    </div>
    <div class="feature-grid" data-stagger="70">
<?php foreach ($features as [$t, $b]): ?>
      <div class="feature-card reveal">
        <h3><?= e($t) ?></h3>
        <p><?= e($b) ?></p>
      </div>
<?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($specs): ?>
<!-- ── SPECIFICATIONS ── -->
<section id="specifications">
  <div class="section-wrap">
    <div class="two-col">
      <div class="reveal">
        <div class="section-label">Specifications</div>
        <h2 class="section-title"><?= e($s['specs_title']) ?></h2>
      </div>
      <div class="spec-panel reveal">
<?php foreach ($specs as [$k, $v]): ?>
        <div class="spec-row">
          <div class="spec-key"><?= e($k) ?></div>
          <div class="spec-value"><?= e($v) ?></div>
        </div>
<?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($details): ?>
<!-- ── DETAILS (faixa escura) ── -->
<section class="dark-band">
  <div class="section-wrap">
    <div class="reveal">
<?php if ($s['details_title'] !== ''): ?>
      <h2 class="section-title"><?= e($s['details_title']) ?></h2>
<?php endif; ?>
    </div>
    <div class="platform-grid" data-stagger="70">
<?php foreach ($details as [$t, $b]): ?>
      <div class="platform-card reveal">
        <h3><?= e($t) ?></h3>
        <p><?= e($b) ?></p>
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
        <h2><?= e($s['cta_title'] !== '' ? $s['cta_title'] : 'Interested in ' . $s['name'] . '?') ?></h2>
<?php if ($s['cta_text'] !== ''): ?>
        <p><?= e($s['cta_text']) ?></p>
<?php endif; ?>
      </div>
      <a class="btn btn-primary btn-lg" href="mailto:<?= e($email) ?>?subject=<?= rawurlencode($s['name'] . ' request') ?>"><?= e($s['cta_btn'] !== '' ? $s['cta_btn'] : 'Get in touch') ?></a>
    </div>
  </div>
</section>
