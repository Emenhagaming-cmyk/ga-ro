import fs from "node:fs";
import path from "node:path";

function resolveDataDir() {
  const cwd = process.cwd();
  const candidates = [
    path.join(cwd, "src", "server", "agent", "data"),
    path.join(cwd, "api", "knowledge", "data"),
    path.join(cwd, "public", "data"),
  ];
  for (const p of candidates) {
    try {
      if (fs.existsSync(p)) return p;
    } catch {}
  }
  return candidates[0];
}

const DATA_DIR = resolveDataDir();

export function loadJsonSafe(name) {
  try {
    if (!name || typeof name !== "string") return null;
    const safeName = path.basename(name);
    const p = path.join(DATA_DIR, safeName);
    const st = fs.statSync(p);
    if (!st.isFile() || st.size === 0) return null;
    const raw = fs.readFileSync(p, "utf8");
    if (!raw.trim()) return null;
    const parsed = JSON.parse(raw);
    return parsed;
  } catch {
    return null;
  }
}

export function loadManifest() {
  try {
    const p = path.join(DATA_DIR, "manifest.json");
    const raw = fs.readFileSync(p, "utf8");
    const m = JSON.parse(raw);
    if (Array.isArray(m)) return m.filter((x) => typeof x === "string");
    return [];
  } catch {
    return [];
  }
}
