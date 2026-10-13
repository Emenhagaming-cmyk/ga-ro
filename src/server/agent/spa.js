export function isSpaShell(html) {
  if (typeof html !== "string") return false;
  const src = html;
  const low = src.toLowerCase();

  if (low.includes(<div id="app"></div>)) return true;

  if (/<script[^>]+type=["']module["'][^>]+src=["'][^"']*\\/assets\\/index-[^"']+\\.js["']/.test(src)) {
    return true;
  }

  if (src.includes("id=\\"app\\"")) {
    const textOnly = src.replace(/<[^>]+>/g, " "").replace(/\\s+/g, " "").trim();
    if (textOnly.length < 200) return true;
  }

  return false;
}
