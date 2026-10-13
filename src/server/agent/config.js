export const CONFIG = {
  ALLOWED_APEX: "smkbu-sby.my.id",
  APEX: "https://smkbu-sby.my.id",
  API: "https://api.smkbu-sby.my.id",

  NEWS_JSON: "/data/news.json",
  SITEMAP: "/sitemap.xml",

  FUNC_MAX_MS: 30_000,
  BUDGET_TOTAL_MS: 20_000,
  FETCH_TIMEOUT_MS: 8_000,
  LLM_TIMEOUT_MS: 12_000,

  MAX_PAGES: 6,
  MAX_ITERATIONS: 4,
  MAX_DEPTH: 2,
  MAX_REDIRECTS: 3,
  MAX_CONCURRENCY: 4,
  MAX_TOOL_CALLS: 8,

  MAX_HTML_BYTES: 512 * 1024,
  MAX_TEXT_CHARS: 12000,
  MAX_LINKS: 40,

  CACHE_TTL_MS: 5 * 60 * 1000,

  SLUG_RE: /^[a-z0-9]+(?:-[a-z0-9]+)*$/,

  SEARCH_MAX_PAGES: 4,
  SEARCH_MAX_BYTES: 1024 * 1024,
};

export const APEX_HOST = CONFIG.ALLOWED_APEX;
