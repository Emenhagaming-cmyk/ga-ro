import dns from "node:dns/promises";
import net from "node:net";
import { CONFIG } from "./config.js";

const PRIVATE_NETS = [
  { re: /^127\\./ },
  { re: /^10\\./ },
  { re: /^169\\.254\\./ },
  { re: /^172\\.(1[6-9]|2[0-9]|3[0-1])\\./ },
  { re: /^192\\.168\\./ },
  { re: /^100\\.(6[4-9]|[7-9][0-9]|1[0-1][0-9]|12[0-7])\\./ },
  { re: /^192\\.0\\.0\\./ },
  { re: /^192\\.0\\.2\\./ },
  { re: /^198\\.51\\.100\\./ },
  { re: /^203\\.0\\.113\\./ },
  { re: /^198\\.(1[8-9])\\./ },
  { re: /^224\\./ },
  { re: /^225\\./ },
  { re: /^226\\./ },
  { re: /^227\\./ },
  { re: /^228\\./ },
  { re: /^229\\./ },
  { re: /^230\\./ },
  { re: /^231\\./ },
  { re: /^232\\./ },
  { re: /^233\\./ },
  { re: /^234\\./ },
  { re: /^235\\./ },
  { re: /^236\\./ },
  { re: /^237\\./ },
  { re: /^238\\./ },
  { re: /^239\\./ },
  { re: /^240\\./ },
];

function isPrivateIp(ip) {
  if (typeof ip !== "string") return true;
  if (ip.includes(":")) {
    const v6 = ip.toLowerCase();
    if (v6 === "::1") return true;
    if (v6.startsWith("fc") || v6.startsWith("fd")) return true;
    if (v6.startsWith("fe80")) return true;
    if (v6.startsWith("::ffff:")) {
      const v4 = v6.replace("::ffff:", "");
      return isPrivateIp(v4);
    }
    return false;
  }
  for (const r of PRIVATE_NETS) {
    if (r.re.test(ip)) return true;
  }
  return false;
}

export function isAllowedUrl(input) {
  let u;
  try {
    u = new URL(input);
  } catch {
    return false;
  }
  if (u.protocol !== "https:") return false;
  if (u.username || u.password) return false;
  const port = u.port;
  if (port && port !== "443") return false;
  const h = u.hostname.toLowerCase();
  const apex = CONFIG.ALLOWED_APEX;
  if (h === apex) return true;
  if (h.endsWith("." + apex)) return true;
  return false;
}

export async function assertSafeHost(hostname) {
  const h = String(hostname || "");
  if (!isAllowedUrl("https://" + h + "/")) {
    throw new Error("DOMAIN_BLOCKED");
  }
  const hn = h.toLowerCase();
  if (net.isIP(hn) !== 0) {
    if (isPrivateIp(hn)) throw new Error("PRIVATE_IP_BLOCKED");
    return;
  }
  try {
    const addrs = await dns.lookup(hn, { all: true });
    for (const a of addrs) {
      if (isPrivateIp(a.address)) throw new Error("PRIVATE_IP_BLOCKED");
    }
  } catch (e) {
    throw new Error("DNS_LOOKUP_FAIL");
  }
}

export function normalizeUrl(input, base) {
  let u;
  try {
    u = new URL(input, base);
  } catch {
    return null;
  }
  if (u.protocol !== "https:") return null;
  if (u.username || u.password) return null;
  const port = u.port;
  if (port && port !== "443") return null;
  u.hostname = u.hostname.toLowerCase();
  u.hash = "";
  const TRACKING = ["utm_source","utm_medium","utm_campaign","utm_term","utm_content","fbclid","gclid","msclkid"];
  for (const k of [...u.searchParams.keys()]) {
    if (TRACKING.includes(k.toLowerCase())) u.searchParams.delete(k);
  }
  u.searchParams.sort();
  return u.href;
}

export function resolveSameSite(href, baseUrl) {
  const abs = normalizeUrl(href, baseUrl);
  if (!abs) return null;
  if (!isAllowedUrl(abs)) return null;
  return abs;
}
