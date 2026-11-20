/* ────────────────────────────────────────────────────────────────
   ViField — newsroom content
   ----------------------------------------------------------------
   This is the ONLY file you edit to manage the newsroom.
   Add a clipping by copying one { ... } block below and filling it in.

   Fields:
     category  'press' | 'announcement' | 'event'   (drives the tag + filter)
     date      'YYYY-MM-DD'                          (sorted newest-first automatically)
     title     headline
     excerpt   one or two sentences
     source    outlet or context (shown on the card)
     url       link to the article ('#' if none yet)
     featured  true on ONE item to show it as the big story up top (optional)
     image     featured item only — path to the hero image (optional)
     badge     featured item only — small label on the image (optional)

   NOTE: the three publication dates below are best estimates — please
   confirm each 'date' against the real article and adjust if needed.

   The filter buttons (All / Press / Announcements / Events) only appear
   once you have items in more than one category, so they'll show up
   automatically as the newsroom grows.
──────────────────────────────────────────────────────────────── */

window.VIFIELD_NEWS = [
  {
    category: "press",
    date: "2026-07-23",
    title: "A new INESC TEC spin-off is bringing intelligent robotics to agriculture and industry",
    excerpt: "ViField is a new Portuguese spin-off focused on bringing to market a robotic platform developed at INESC TEC: ORIOOS.",
    source: "INESC TEC",
    url: "https://www.inesctec.pt/en/news/a-new-inesc-tec-spin-off-is-bringing-intelligent-robotics-to-agriculture-and-industry",
    featured: true,
    badge: "INESC TEC",
    image: "images/team_news_feature.jpg"
  },
  {
    category: "press",
    date: "2026-07-24",
    title: "New robot uses AI to ‘read’ entire orchards",
    excerpt: "Developed by Porto-based ViField, Orioos uses cameras and algorithms to predict yields and detect diseases more accurately.",
    source: "Fruitnet",
    url: "https://www.fruitnet.com/eurofruit/new-robot-uses-ai-to-read-entire-orchards/272225.article"
  },
  {
    category: "press",
    date: "2026-07-23",
    title: "INESC TEC spin-off brings intelligent robotics to agriculture and industry",
    excerpt: "As ViField's first commercial product, ORIOOS will provide high-value data to farmers and security professionals, making decision-making faster and more informed.",
    source: "Science|Business",
    url: "https://sciencebusiness.net/network-updates/inesc-tec-spin-brings-intelligent-robotics-agriculture-and-industry"
  },
  {
    category: "press",
    date: "2026-07-24",
    title: "Autonomous AI Robot Helps Farmers Detect Crop Diseases Before Harvest",
    excerpt: "An electric, modular robot equipped with sensors, capable of moving non-invasively through orchards and vineyards and detecting crop diseases, is now available to farmers and agricultural producers.",
    source: "AZO Robotics",
    url: "https://www.azorobotics.com/News.aspx?newsID=16441"
  },
  {
    category: "press",
    date: "2026-07-29",
    title: "Autonomous AI robot helps farmers",
    excerpt: "An autonomous robot equipped with cameras and trained with data collected over four years can move autonomously through fields without compacting the soil and provide crucial information.",
    source: "Smart Machines & Factories",
    url: "https://smartmachinesandfactories.co.uk/autonomous-ai-robot-helps-farmers/"
  },
  {
    category: "press",
    date: "2026-07-29",
    title: "INESC TEC spin-off ViField brings intelligent robotics to agriculture",
    excerpt: "ViField is a new Portuguese spin-off focused on bringing to market a robotic platform developed at INESC TEC: ORIOOS.",
    source: "Agro Pages",
    url: "https://news.agropages.com/News/NewsDetail---58406.htm"
  },
];
