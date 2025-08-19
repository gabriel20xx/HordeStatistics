import express from 'express';
import { cache } from '../utils/cache.js';
import { fetchJson } from '../utils/fetchJson.js';

const router = express.Router();

router.get('/:name', async (req, res) => {
  const { name } = req.params;
  if (!name) return res.status(400).json({ error: 'Model name required' });
  try {
    const key = `model_${name}`;
    const data = await cache.getOrSet(key, 1500, () =>
      fetchJson('https://stablehorde.net/api/v2/status/models/' + encodeURIComponent(name))
    );
    if (!Array.isArray(data)) return res.status(502).json({ error: 'Unexpected upstream format' });
    res.json(data);
  } catch (e) {
    res.status(502).json({ error: e.message });
  }
});

export default router;
