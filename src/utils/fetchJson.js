import fetch from 'node-fetch';
import { AbortController } from 'node-abort-controller';

export async function fetchJson(url, { timeoutMs = 8000, headers = {} } = {}) {
  const controller = new AbortController();
  const timer = setTimeout(() => controller.abort(), timeoutMs);
  try {
    const resp = await fetch(url, { headers, signal: controller.signal });
    if (!resp.ok) {
      const text = await resp.text().catch(() => '');
      throw new Error(`Upstream ${resp.status}: ${text.slice(0,200)}`);
    }
    return await resp.json();
  } finally {
    clearTimeout(timer);
  }
}
