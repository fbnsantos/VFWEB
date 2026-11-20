<?php
/** @var array $tech @var array $sectors @var array $team */
$email = tx('site.email');
$check = '<svg viewBox="0 0 10 10" fill="none" stroke="var(--vf-green-600)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1.5 5l2.5 2.5 4.5-4"/></svg>';
?>
<!-- ── HERO ── -->
<section class="hero" id="top">
  <div class="hero-grid" aria-hidden="true"></div>
  <div class="hero-eyebrow"><?= e(tx('home.hero_eyebrow')) ?></div>
  <h1 class="hero-title"><?= tx_html('home.hero_title') ?></h1>
  <p class="hero-model"><?= e(tx('home.hero_model')) ?></p>
  <p class="hero-desc"><?= e(tx('home.hero_desc')) ?></p>
  <div class="hero-cta">
    <a class="btn btn-primary btn-lg" href="#solutions"><?= e(tx('home.hero_cta1')) ?></a>
    <a class="btn btn-ghost btn-lg" href="#contact"><?= e(tx('home.hero_cta2')) ?></a>
  </div>
  <div class="scroll-indicator" aria-hidden="true">
    <span>Scroll</span>
    <div class="scroll-line"></div>
  </div>
</section>

<!-- ── TECHNOLOGY ── -->
<section id="technology">
  <div class="section-wrap">
    <div class="reveal">
      <div class="section-label"><?= e(tx('home.tech_label')) ?></div>
      <h2 class="section-title"><?= e(tx('home.tech_title')) ?></h2>
      <p class="section-desc"><?= e(tx('home.tech_desc')) ?></p>
    </div>

    <div class="tech-grid" data-stagger="70">
<?php foreach ($tech as $t): ?>
      <div class="tech-card reveal">
        <div class="tech-icon"><?= icon_svg($t['icon']) ?></div>
        <h3><?= e($t['title']) ?></h3>
        <p><?= e($t['body']) ?></p>
      </div>
<?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── INDUSTRIES ── -->
<section id="industries">
  <div class="section-wrap">
    <div class="reveal">
      <div class="section-label"><?= e(tx('home.ind_label')) ?></div>
      <h2 class="section-title"><?= e(tx('home.ind_title')) ?></h2>
      <p class="section-desc"><?= e(tx('home.ind_desc')) ?></p>
    </div>

    <div class="industry-list">
<?php foreach ($sectors as $i => $s): ?>
      <div class="industry-row<?= $i % 2 ? ' reverse' : '' ?> reveal">
        <div class="industry-media">
<?php if ($s['video'] !== ''): ?>
          <video autoplay muted loop playsinline preload="metadata">
            <source src="<?= e(asset_url($s['video'])) ?>" type="video/<?= str_ends_with($s['video'], '.webm') ? 'webm' : 'mp4' ?>">
          </video>
<?php endif; ?>
        </div>
        <div class="industry-text">
          <div class="section-label"><?= e($s['label']) ?></div>
          <h3><?= e($s['title']) ?></h3>
          <p><?= e($s['body']) ?></p>
<?php if ($pills = lines($s['pills'])): ?>
          <div class="pill-row">
<?php foreach ($pills as $pill): ?>
            <span class="pill"><?= e($pill) ?></span>
<?php endforeach; ?>
          </div>
<?php endif; ?>
          <div class="industry-cta">
            <a class="btn btn-primary" href="#contact"><?= e(tx('home.ind_cta1')) ?></a>
            <a class="btn btn-ghost" href="#solutions"><?= e(tx('home.ind_cta2')) ?></a>
          </div>
        </div>
      </div>
<?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── SOLUTIONS ── -->
<section id="solutions">
  <div class="section-wrap">
    <div class="reveal">
      <div class="section-label"><?= e(tx('home.sol_label')) ?></div>
      <h2 class="section-title"><?= e(tx('home.sol_title')) ?></h2>
      <p class="section-desc"><?= e(tx('home.sol_desc')) ?></p>
    </div>

    <div class="product-card reveal">
      <div class="product-media">
        <img src="<?= e(tx_file('home.product_image')) ?>" alt="<?= e(tx('home.product_image_alt')) ?>">
      </div>
      <div class="product-info">
        <div class="section-label"><?= e(tx('home.product_label')) ?></div>
        <div class="product-name"><?= e(tx('home.product_name')) ?></div>
        <p class="product-desc"><?= e(tx('home.product_desc')) ?></p>
        <ul class="product-specs">
<?php foreach (lines(tx('home.product_features')) as $feature): ?>
          <li>
            <div class="spec-check"><?= $check ?></div>
            <?= e($feature) ?>
          </li>
<?php endforeach; ?>
        </ul>
        <div>
          <a class="btn btn-primary" href="/orioos"><?= e(tx('home.product_btn')) ?></a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── TEAM ── -->
<section id="team">
  <div class="section-wrap">
    <div class="reveal">
      <div class="section-label"><?= e(tx('home.team_label')) ?></div>
      <h2 class="section-title"><?= e(tx('home.team_title')) ?></h2>
      <p class="section-desc"><?= e(tx('home.team_desc')) ?></p>
    </div>

<?php
$board = array_values(array_filter($team, fn($m) => $m['section'] === 'advisory'));
$team = array_values(array_filter($team, fn($m) => $m['section'] !== 'advisory'));
$card = function (array $m): void { ?>
      <article class="team-card reveal" tabindex="0">
        <img src="<?= e(asset_url($m['photo'])) ?>" alt="<?= e($m['name']) ?>">
        <div class="team-overlay">
          <div class="team-name"><?= e($m['name']) ?></div>
<?php if ($m['role'] !== ''): ?>
          <div class="team-role"><?= e($m['role']) ?></div>
<?php endif; ?>
          <p class="team-bio"><?= e($m['bio']) ?></p>
        </div>
<?php if ($m['url'] !== ''): ?>
        <a class="team-link" href="<?= e($m['url']) ?>"<?= preg_match('#^https?://#i', $m['url']) ? ' target="_blank" rel="noopener"' : '' ?> aria-label="<?= e($m['name']) ?>"></a>
<?php endif; ?>
      </article>
<?php }; ?>
    <div class="team-grid" data-stagger="70">
<?php foreach ($team as $m) { $card($m); } ?>
    </div>

<?php if ($board): ?>
    <div class="reveal" id="advisory" style="margin-top: var(--sp-16);">
      <div class="section-label"><?= e(tx('home.advisory_label')) ?></div>
      <h2 class="section-title"><?= e(tx('home.advisory_title')) ?></h2>
<?php if (tx('home.advisory_desc') !== ''): ?>
      <p class="section-desc"><?= e(tx('home.advisory_desc')) ?></p>
<?php endif; ?>
    </div>
    <div class="team-grid" data-stagger="70">
<?php foreach ($board as $m) { $card($m); } ?>
    </div>
<?php endif; ?>

    <div class="spinoff-note reveal">
      <div class="spinoff-note-text">
        <div class="section-label" style="margin-bottom: var(--sp-2);"><?= e(tx('home.origin_label')) ?></div>
        <p><?= e(tx('home.origin_text')) ?></p>
      </div>
      <div class="spinoff-logos">
<?php foreach ([1, 2] as $n): if (tx("site.backer{$n}_logo") === '') continue; ?>
        <img src="<?= e(asset_url(tx("site.backer{$n}_logo"))) ?>" alt="<?= e(tx("site.backer{$n}_name")) ?>">
<?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- ── CONTACT ── -->
<section id="contact">
  <div class="section-wrap">
    <div class="contact-wrap">
      <div class="contact-info reveal">
        <div class="section-label"><?= e(tx('home.contact_label')) ?></div>
        <h2 class="section-title"><?= e(tx('home.contact_title')) ?></h2>
        <p class="section-desc"><?= e(tx('home.contact_desc')) ?></p>

        <div class="contact-detail">
          <div class="contact-item">
            <div class="contact-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
              </svg>
            </div>
            <div>
              <div class="contact-item-label">Email</div>
              <div class="contact-item-val"><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></div>
            </div>
          </div>

          <div class="contact-item">
            <div class="contact-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
            </div>
            <div>
              <div class="contact-item-label">Address</div>
              <div class="contact-item-val"><?= tx_br('site.address') ?></div>
            </div>
          </div>
        </div>
      </div>

      <div class="contact-form-card reveal">
        <div class="form-title"><?= e(tx('home.form_title')) ?></div>
        <div class="form-subtitle"><?= e(tx('home.form_subtitle')) ?></div>
        <form id="contactForm" action="/contact" method="post" data-mailto="<?= e($email) ?>" data-subject="<?= e(tx('site.name')) ?> — Contact Request">
          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="name">Name</label>
              <input class="form-input" id="name" name="name" type="text" placeholder="Your name" required maxlength="120">
            </div>
            <div class="form-group">
              <label class="form-label" for="email">Email</label>
              <input class="form-input" id="email" name="email" type="email" placeholder="you@company.com" required maxlength="200">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="message">Message</label>
            <textarea class="form-textarea" id="message" name="message" placeholder="Tell us about your project..." required maxlength="5000"></textarea>
          </div>
          <div style="position:absolute; left:-9999px;" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
          <button class="submit-btn" type="submit">Send Message →</button>
          <p class="form-status" id="formStatus" role="status" aria-live="polite" hidden></p>
          <p class="form-note"><?= e(tx('home.form_note')) ?> <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></p>
        </form>
      </div>
    </div>
  </div>
</section>
