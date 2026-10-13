import { listBerita } from './listBerita.js';
import { getBeritaDetail } from './getBeritaDetail.js';
import { fetchHtml } from './fetchHtml.js';
import { extractMainContent } from './extractMainContent.js';
import { extractLinks } from './extractLinks.js';
import { getPageSummary } from './getPageSummary.js';
import { searchInSite } from './searchInSite.js';
import { CONFIG } from '../config.js';

export const TOOLS = {
  listBerita,
  getBeritaDetail,
  fetchHtml,
  extractMainContent,
  extractLinks,
  getPageSummary,
  searchInSite,
};

export async function runTool(name, args, ctx) {
  const fn = TOOLS[name];
  if (!fn) return { found: false, reason: 'unknown_tool' };
  if (ctx && ctx.toolCalls >= CONFIG.MAX_TOOL_CALLS) return { found: false, reason: 'TOOL_BUDGET_EXCEEDED' };
  if (ctx) ctx.toolCalls = (ctx.toolCalls || 0) + 1;
  try {
    return await fn(ctx || {}, args || {});
  } catch (e) {
    return { found: false, reason: 'TOOL_ERROR', message: String(e?.message || e).slice(0, 200) };
  }
}
