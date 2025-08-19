// Simple TTL cache with optional stale-while-revalidate behavior.
export class TTLCache {
  constructor() {
    this.store = new Map(); // key -> { value, expiry }
  }

  set(key, value, ttlMs) {
    this.store.set(key, { value, expiry: Date.now() + ttlMs });
  }

  get(key) {
    const entry = this.store.get(key);
    if (!entry) return null;
    if (Date.now() > entry.expiry) {
      this.store.delete(key);
      return null;
    }
    return entry.value;
  }

  getOrSet(key, ttlMs, producer) {
    const existing = this.get(key);
    if (existing !== null) return Promise.resolve(existing);
    return Promise.resolve(producer()).then(v => {
      this.set(key, v, ttlMs);
      return v;
    });
  }
}

export const cache = new TTLCache();
