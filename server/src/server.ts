import dotenv from 'dotenv';
dotenv.config();
import { app } from './app.js';
import { purgeExpiredHolds } from './config/db.js';

const PORT = Number(process.env.PORT || 4000);
// Safety net: sweep expired seat holds every minute (requests also purge lazily)
setInterval(() => purgeExpiredHolds(), 60_000).unref?.();
app.listen(PORT, () => {
  console.log(`Vox Cinemas API listening on http://localhost:${PORT}`);
});
