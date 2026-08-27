export function getFirstImage(gallery) {
  if (!gallery) return "";
  if (Array.isArray(gallery)) return gallery[0] || "";
  try {
    const parsed = JSON.parse(gallery);
    return Array.isArray(parsed) ? parsed[0] || "" : "";
  } catch (e) {
    return "";
  }
}
