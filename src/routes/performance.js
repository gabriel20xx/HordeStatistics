import express from 'express';
import { cache } from '../utils/cache.js';
import { fetchJson } from '../utils/fetchJson.js';

const router = express.Router();

router.get('/', async (req, res) => {
  try {
    const data = await cache.getOrSet('performance', 1000, () =>
      fetchJson('https://stablehorde.net/api/v2/status/performance')
    );
    res.json(data);
  } catch (e) {
    res.status(502).json({ error: e.message });
  }
});

export default router;
