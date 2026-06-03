<?php
declare(strict_types=1);
$base = APP_ENV === 'production' ? 'https://villaplaisance.fr' : APP_URL;
?>
# robots.txt — Villa Plaisance
# Politique : tous les bots (Google, Bing, LLMs) ont accès au contenu public.
# Seules /admin/ et /seeds/ sont bloquées (zones sensibles).

User-agent: *
Allow: /
Disallow: /admin/
Disallow: /seeds/

# LLMs autorisés explicitement (entraînement + browsing + search).
# Bloquer un bot ici reviendrait à perdre la visibilité dans ChatGPT,
# Perplexity, Claude, Gemini, Apple Intelligence. Villa Plaisance étant
# une vitrine, on autorise tout.

User-agent: GPTBot
Allow: /
Disallow: /admin/
Disallow: /seeds/

User-agent: ChatGPT-User
Allow: /
Disallow: /admin/
Disallow: /seeds/

User-agent: OAI-SearchBot
Allow: /
Disallow: /admin/
Disallow: /seeds/

User-agent: PerplexityBot
Allow: /
Disallow: /admin/
Disallow: /seeds/

User-agent: ClaudeBot
Allow: /
Disallow: /admin/
Disallow: /seeds/

User-agent: Claude-Web
Allow: /
Disallow: /admin/
Disallow: /seeds/

User-agent: Claude-User
Allow: /
Disallow: /admin/
Disallow: /seeds/

User-agent: anthropic-ai
Allow: /
Disallow: /admin/
Disallow: /seeds/

User-agent: Google-Extended
Allow: /
Disallow: /admin/
Disallow: /seeds/

User-agent: Applebot-Extended
Allow: /
Disallow: /admin/
Disallow: /seeds/

Sitemap: <?= $base ?>/sitemap.xml
