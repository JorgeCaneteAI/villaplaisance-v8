<?php declare(strict_types=1); ?>
<?php /**
 * Vue : Itinéraire personnalisé (rendu par /itineraire/{slug}).
 * Portée en design proto. Layout : front-proto.
 * Carte Leaflet conservée à l'identique. Tout le reste reskinné proto.
 *
 * @var string $lang
 * @var array  $seo
 * @var array  $jsonLd
 * @var array  $itinerary  row de vp_itineraries
 * @var array  $steps      rows de vp_itinerary_steps
 * @var array  $comments   rows de vp_itinerary_comments
 * @var string $csrf
 */
$date = $itinerary['itinerary_date'] ? date('d/m/Y', strtotime($itinerary['itinerary_date'])) : '';
$itLang = $itinerary['lang'] ?? 'fr';
$dayNamesFr = ['Sunday'=>'Dimanche','Monday'=>'Lundi','Tuesday'=>'Mardi','Wednesday'=>'Mercredi','Thursday'=>'Jeudi','Friday'=>'Vendredi','Saturday'=>'Samedi'];
$dayNamesEn = ['Sunday'=>'Sunday','Monday'=>'Monday','Tuesday'=>'Tuesday','Wednesday'=>'Wednesday','Thursday'=>'Thursday','Friday'=>'Friday','Saturday'=>'Saturday'];
$dayNamesMap = $itLang === 'en' ? $dayNamesEn : $dayNamesFr;
$dayName = $itinerary['itinerary_date'] ? ($dayNamesMap[date('l', strtotime($itinerary['itinerary_date']))] ?? '') : '';

// Titre dynamique : premier → dernier lieu
$firstStep = $steps[0]['title'] ?? '';
$lastStep  = end($steps)['title'] ?? '';
$originClean = preg_replace('/^(Departure from|Départ( de)?)\s*/i', '', $firstStep);
$destClean   = preg_replace('/^(Arrival in|Arrivée à)\s*/i', '', $lastStep);

// Coordonnées pour la carte
$mapPoints = [];
$gmapsWaypoints = [];
foreach ($steps as $s) {
    if (!empty($s['lat']) && !empty($s['lng'])) {
        $mapPoints[] = ['lat' => (float)$s['lat'], 'lng' => (float)$s['lng'], 'title' => $s['title'], 'time' => $s['time_label'] ?? ''];
    }
    $gmapsWaypoints[] = urlencode($s['title']);
}
$hasMap = count($mapPoints) >= 2;
$gmapsUrl = 'https://www.google.com/maps/dir/' . implode('/', $gmapsWaypoints);

$firstTime = $steps[0]['time_label'] ?? '';
$lastTime  = end($steps)['time_label'] ?? '';
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">

<style>
  .it-page { padding-top: clamp(72px, 9vw, 120px); }
  .it-page .page-hero { padding-top: 0; }
  .it-crumbs {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.14em;
    color: var(--stone-500); text-transform: uppercase;
    margin: 0 0 24px;
  }
  .it-crumbs a { color: var(--stone-500); }
  .it-crumbs a:hover { color: var(--ink-900); }
  .it-crumbs .sep { margin: 0 8px; opacity: 0.5; }

  .it-meta {
    font-family: var(--font-mono); font-size: 12px; letter-spacing: 0.06em;
    color: var(--stone-600);
    margin: 8px 0 0;
  }
  .it-meta strong { color: var(--ink-900); font-weight: 500; }
  .it-intro {
    font-family: var(--font-sans); font-size: 16px; line-height: 1.65; color: var(--stone-600);
    margin: 24px 0 0; max-width: 56ch;
  }

  /* Stats sous le hero */
  .it-stats {
    display: flex; gap: clamp(36px, 5vw, 80px);
    margin-top: 36px; padding-top: 28px; border-top: var(--hairline);
  }
  .it-stat .v {
    font-family: var(--font-display); font-weight: 400;
    font-size: clamp(32px, 3.6vw, 48px); line-height: 1.0; letter-spacing: -0.015em;
    color: var(--ink-900);
  }
  .it-stat .l {
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.14em;
    text-transform: uppercase; color: var(--stone-500);
    margin-top: 6px;
  }

  /* Carte */
  .it-map-wrap {
    margin: 56px 0;
    border: var(--hairline);
    overflow: hidden;
  }
  #itinerary-map { height: 420px; width: 100%; }
  @media (max-width: 720px) { #itinerary-map { height: 320px; } }
  .it-map-actions {
    display: flex; gap: 8px; flex-wrap: wrap;
    padding: 16px 20px;
    background: var(--linen-100);
    border-top: var(--hairline);
  }

  /* Timeline */
  .it-section-label {
    font-family: var(--font-mono); font-size: 11.5px; letter-spacing: 0.18em;
    text-transform: uppercase; color: var(--stone-500);
    margin: 56px 0 28px;
    padding-bottom: 14px; border-bottom: var(--hairline);
  }

  .it-timeline {
    position: relative;
    padding-left: 56px;
    max-width: 760px;
  }
  .it-timeline::before {
    content: '';
    position: absolute;
    left: 17px; top: 8px; bottom: 8px;
    width: 1px;
    background: linear-gradient(to bottom, var(--olive-900) 0%, var(--sage-700) 40%, color-mix(in oklab, var(--sage-700) 25%, transparent) 100%);
  }
  .it-step {
    position: relative;
    margin-bottom: 36px;
  }
  .it-step:last-child { margin-bottom: 0; }
  .it-step::before {
    content: '';
    position: absolute;
    left: -50px; top: 6px;
    width: 20px; height: 20px; border-radius: 50%;
    background: var(--linen-50);
    border: 2px solid var(--sage-700);
    z-index: 1;
  }
  .it-step:first-child::before {
    background: var(--olive-900); border-color: var(--olive-900);
    box-shadow: 0 0 0 4px color-mix(in oklab, var(--olive-900) 20%, transparent);
  }
  .it-step:last-child::before {
    background: var(--sage-700); border-color: var(--sage-700);
    box-shadow: 0 0 0 4px color-mix(in oklab, var(--sage-700) 20%, transparent);
  }
  .it-step .it-time {
    font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.14em;
    text-transform: uppercase; color: var(--olive-900);
    margin-bottom: 4px;
  }
  .it-step .it-title {
    font-family: var(--font-display); font-weight: 500;
    font-size: clamp(22px, 1.8vw, 26px); line-height: 1.2; letter-spacing: -0.005em;
    color: var(--ink-900); margin: 0 0 4px;
    display: inline-flex; align-items: baseline; gap: 8px;
  }
  .it-step .it-title a { color: var(--ink-900); }
  .it-step .it-title a:hover { color: var(--sage-700); }
  .it-step .it-title .ext { font-size: 0.65em; color: var(--sage-700); }
  .it-step .it-duration {
    display: inline-block;
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.06em;
    color: var(--stone-600);
    background: color-mix(in oklab, var(--sage-700) 10%, var(--linen-50));
    padding: 2px 8px;
    margin-bottom: 10px;
  }
  .it-step .it-desc {
    font-family: var(--font-sans); font-size: 15px; line-height: 1.65; color: var(--stone-600);
    margin: 0;
  }
  .it-step .it-image {
    margin-top: 14px; overflow: hidden;
    border: var(--hairline);
  }
  .it-step .it-image img { width: 100%; height: auto; display: block; }

  /* Marker carte */
  .step-marker {
    background: var(--olive-900);
    color: var(--linen-50);
    border-radius: 50%;
    width: 28px; height: 28px;
    display: flex; align-items: center; justify-content: center;
    font-family: var(--font-mono); font-size: 12px; font-weight: 600;
    border: 2px solid var(--linen-50);
    box-shadow: 0 2px 6px rgba(0,0,0,0.25);
  }

  /* Commentaires */
  .it-comments { max-width: 760px; margin-top: 64px; }
  .it-comment {
    padding: 18px 0;
    border-bottom: var(--hairline);
  }
  .it-comment:last-of-type { border-bottom: 0; }
  .it-comment .who {
    font-family: var(--font-display); font-style: italic;
    font-size: 18px; color: var(--ink-900);
  }
  .it-comment .when {
    font-family: var(--font-mono); font-size: 10.5px; letter-spacing: 0.06em;
    color: var(--stone-500); text-transform: uppercase;
    margin-left: 10px;
  }
  .it-comment .what {
    font-family: var(--font-sans); font-size: 15px; line-height: 1.65; color: var(--stone-600);
    margin: 6px 0 0;
  }

  .it-form {
    margin-top: 28px;
    padding: 24px;
    background: var(--linen-100);
    border: var(--hairline);
    display: flex; flex-direction: column; gap: 12px;
  }
  .it-form input,
  .it-form textarea {
    width: 100%; box-sizing: border-box;
    padding: 12px 14px;
    background: var(--linen-50);
    border: var(--hairline);
    font-family: var(--font-sans); font-size: 14px; color: var(--ink-900);
  }
  .it-form input:focus,
  .it-form textarea:focus { outline: 1px solid var(--olive-900); border-color: var(--olive-900); }
  .it-form textarea { resize: vertical; min-height: 100px; }
  .it-form button { align-self: flex-start; }
</style>

<section class="section it-page">
  <div class="container-wide">

    <nav class="it-crumbs" aria-label="Fil d'Ariane">
      <a href="<?= LangService::url('/') ?>" data-en="Home">Accueil</a>
      <span class="sep" aria-hidden="true">/</span>
      <a href="<?= LangService::url('itineraire') ?>" data-en="Custom itineraries">Itinéraires</a>
      <span class="sep" aria-hidden="true">/</span>
      <span aria-current="page"><?= htmlspecialchars($itinerary['guest_name']) ?></span>
    </nav>

    <div class="page-hero">
      <div class="page-hero-inner">
        <div>
          <div class="page-hero-issue">
            <span><?= $itLang === 'en' ? 'Day Trip' : 'Itinéraire du jour' ?></span>
            <?php if ($date): ?>
            <span><?= htmlspecialchars($dayName) ?> &middot; <?= htmlspecialchars($date) ?></span>
            <?php endif; ?>
          </div>
          <h1><?= htmlspecialchars($originClean) ?> <em>&rarr;</em> <?= htmlspecialchars($destClean) ?></h1>
          <p class="it-meta">
            <?= $itLang === 'en' ? 'Prepared for' : 'Préparé pour' ?> <strong><?= htmlspecialchars($itinerary['guest_name']) ?></strong>
          </p>
        </div>
        <?php if (!empty($itinerary['intro_text'])): ?>
        <p class="lede"><?= nl2br(htmlspecialchars($itinerary['intro_text'])) ?></p>
        <?php endif; ?>
      </div>
    </div>

    <div class="it-stats">
      <div class="it-stat"><div class="v"><?= count($steps) ?></div><div class="l"><?= $itLang === 'en' ? 'Stops' : 'Étapes' ?></div></div>
      <?php if ($firstTime): ?>
      <div class="it-stat"><div class="v"><?= htmlspecialchars(trim($firstTime, '~')) ?></div><div class="l"><?= $itLang === 'en' ? 'Departure' : 'Départ' ?></div></div>
      <?php endif; ?>
      <?php if ($lastTime): ?>
      <div class="it-stat"><div class="v"><?= htmlspecialchars(trim($lastTime, '~')) ?></div><div class="l"><?= $itLang === 'en' ? 'Arrival' : 'Arrivée' ?></div></div>
      <?php endif; ?>
    </div>

    <?php if ($hasMap): ?>
    <div class="it-map-wrap">
      <div id="itinerary-map"></div>
      <div class="it-map-actions">
        <a class="btn" href="<?= htmlspecialchars($gmapsUrl) ?>" target="_blank" rel="noopener">
          <?= $itLang === 'en' ? 'Open in Google Maps' : 'Ouvrir dans Google Maps' ?> &rarr;
        </a>
        <button type="button" class="btn btn-ghost" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($gmapsUrl) ?>').then(()=>{this.textContent='<?= $itLang === 'en' ? 'Copied' : 'Copié' ?>';})">
          <?= $itLang === 'en' ? 'Copy link' : 'Copier le lien' ?>
        </button>
      </div>
    </div>
    <?php endif; ?>

    <div class="it-section-label"><?= $itLang === 'en' ? 'Your route, step by step' : 'Votre parcours, étape par étape' ?></div>
    <div class="it-timeline">
      <?php foreach ($steps as $step): ?>
      <div class="it-step">
        <?php if (!empty($step['time_label'])): ?>
        <div class="it-time"><?= htmlspecialchars($step['time_label']) ?></div>
        <?php endif; ?>
        <?php if (!empty($step['link'])): ?>
          <h3 class="it-title"><a href="<?= htmlspecialchars($step['link']) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($step['title']) ?> <span class="ext">&#x2197;</span></a></h3>
        <?php else: ?>
          <h3 class="it-title"><?= htmlspecialchars($step['title']) ?></h3>
        <?php endif; ?>
        <?php if (!empty($step['duration'])): ?>
        <span class="it-duration"><?= htmlspecialchars($step['duration']) ?></span>
        <?php endif; ?>
        <?php if (!empty($step['description'])): ?>
        <p class="it-desc"><?= nl2br(htmlspecialchars($step['description'])) ?></p>
        <?php endif; ?>
        <?php if (!empty($step['image'])): ?>
        <div class="it-image">
          <?= \ImageService::img($step['image'], htmlspecialchars($step['title']), 800, 400) ?>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="it-comments" id="comments">
      <div class="it-section-label"><?= $itLang === 'en' ? 'Guest comments' : 'Commentaires' ?></div>

      <?php if (!empty($comments)): ?>
        <?php foreach ($comments as $c): ?>
        <div class="it-comment">
          <span class="who"><?= htmlspecialchars($c['guest_name']) ?></span>
          <span class="when"><?= date('d/m/Y', strtotime($c['created_at'])) ?></span>
          <p class="what"><?= nl2br(htmlspecialchars($c['message'])) ?></p>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <form class="it-form" method="post" action="/itineraire/<?= htmlspecialchars($itinerary['slug']) ?>/comment">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <div style="position:absolute;left:-9999px" aria-hidden="true">
          <input type="text" name="website" tabindex="-1" autocomplete="off">
        </div>
        <input type="text" name="guest_name" placeholder="<?= $itLang === 'en' ? 'Your name' : 'Votre nom' ?>" required>
        <textarea name="message" placeholder="<?= $itLang === 'en' ? 'Leave a comment or a thank you note…' : 'Laissez un commentaire ou un mot de remerciement…' ?>" required></textarea>
        <button type="submit" class="btn"><?= $itLang === 'en' ? 'Send' : 'Envoyer' ?> &rarr;</button>
      </form>
    </div>

  </div>
</section>

<?php if ($hasMap): ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
(function() {
    var points = <?= json_encode($mapPoints, JSON_UNESCAPED_UNICODE) ?>;
    var map = L.map('itinerary-map', { scrollWheelZoom: false, attributionControl: true });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    var latlngs = [];
    points.forEach(function(p, i) {
        var icon = L.divIcon({
            className: '',
            html: '<div class="step-marker">' + (i + 1) + '</div>',
            iconSize: [28, 28],
            iconAnchor: [14, 14]
        });
        var label = (p.time ? p.time + ' — ' : '') + p.title;
        L.marker([p.lat, p.lng], { icon: icon })
            .bindPopup('<strong>' + label + '</strong>')
            .addTo(map);
        latlngs.push([p.lat, p.lng]);
    });

    L.polyline(latlngs, {
        color: '#1F2814',
        weight: 3,
        opacity: 0.7,
        dashArray: '8, 6'
    }).addTo(map);

    var bounds = L.latLngBounds(latlngs);
    map.fitBounds(bounds, { padding: [35, 35] });
})();
</script>
<?php endif; ?>
