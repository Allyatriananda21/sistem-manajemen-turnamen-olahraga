/**
 * resolveStorageUrl
 *
 * Laravel's asset() helper returns an absolute URL such as:
 *   http://127.0.0.1:8000/storage/team-logos/logo.jpg
 *
 * The Vite dev server only proxies paths that start with /api, so an
 * absolute http://127.0.0.1:8000/... URL is fetched directly by the
 * browser — which fails when the frontend runs on a different origin
 * (e.g. localhost:5173 in dev, or a different domain in production).
 *
 * This helper strips the origin from the URL and returns just the
 * pathname so every request goes through the Vite proxy:
 *   /storage/team-logos/logo.jpg   → fetched via proxy → backend serves it
 *
 * If the value is already a relative path (starts with /) it is returned
 * as-is. If it is null / undefined / empty the function returns null.
 */
export function resolveStorageUrl(url: string | null | undefined): string | null {
  if (!url) return null;

  // Already a relative path — nothing to fix.
  if (url.startsWith('/')) return url;

  try {
    const parsed = new URL(url);
    // Return only the pathname (e.g. /storage/team-logos/logo.jpg)
    return parsed.pathname;
  } catch {
    // Not a valid URL at all — return as-is and let the browser try.
    return url;
  }
}

export default resolveStorageUrl;
