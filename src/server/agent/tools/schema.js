export const TOOL_SCHEMA = [
  {
    type: 'function',
    function: {
      name: 'listBerita',
      description: 'Ambil daftar berita',
      parameters: { type: 'object', properties: { limit: { type: 'integer', minimum: 1, maximum: 20 } }, additionalProperties: false },
    },
  },
  {
    type: 'function',
    function: {
      name: 'getBeritaDetail',
      description: 'Ambil detail berita',
      parameters: { type: 'object', properties: { slug: { type: 'string' } }, required: ['slug'], additionalProperties: false },
    },
  },
  {
    type: 'function',
    function: {
      name: 'fetchHtml',
      description: 'Fetch HTML internal',
      parameters: { type: 'object', properties: { url: { type: 'string' } }, required: ['url'], additionalProperties: false },
    },
  },
  {
    type: 'function',
    function: {
      name: 'extractMainContent',
      description: 'Ekstrak konten utama',
      parameters: { type: 'object', properties: { url: { type: 'string' }, maxChars: { type: 'integer', minimum: 200, maximum: 4000 } }, required: ['url'], additionalProperties: false },
    },
  },
  {
    type: 'function',
    function: {
      name: 'extractLinks',
      description: 'Ekstrak link internal',
      parameters: { type: 'object', properties: { url: { type: 'string' } }, required: ['url'], additionalProperties: false },
    },
  },
  {
    type: 'function',
    function: {
      name: 'getPageSummary',
      description: 'Ringkas halaman',
      parameters: { type: 'object', properties: { url: { type: 'string' } }, required: ['url'], additionalProperties: false },
    },
  },
  {
    type: 'function',
    function: {
      name: 'searchInSite',
      description: 'Cari di situs',
      parameters: { type: 'object', properties: { query: { type: 'string' }, limit: { type: 'integer', minimum: 1, maximum: 10 } }, required: ['query'], additionalProperties: false },
    },
  },
];
