// EZRate API with JavaScript (Node.js 18+, no packages).  Run:  EZRATE_KEY=ezr_live_... node rates.mjs
// Server-side only: never put your API key in browser code. For websites, use the widgets (no key).

const KEY = process.env.EZRATE_KEY
if (!KEY) throw new Error('Set EZRATE_KEY first.')

async function ezrate(path, query = {}) {
  const url = new URL(`https://api.ezrate.me/v1/${path}`)
  for (const [k, v] of Object.entries(query)) url.searchParams.set(k, v)
  const res = await fetch(url, { headers: { 'X-API-Key': KEY, Accept: 'application/json' } })
  const json = await res.json()
  if (!res.ok) throw new Error(json?.error?.message || `HTTP ${res.status}`)
  return json
}

// dollar, 18k gold, Emami coin and USDT in Toman
const { data } = await ezrate('rates', { symbols: 'usd,g18,emami,usdt', lang: 'en' })
for (const r of data) console.log(`${r.name.padEnd(16)} ${r.sell.toLocaleString('en-US').padStart(14)} Toman  ${r.change_pct >= 0 ? '+' : ''}${r.change_pct}%`)

// every crypto price in USDT
const crypto = await ezrate('rates', { category: 'crypto', quote: 'USDT' })
console.log(crypto.data.slice(0, 5).map(r => `${r.code}: ${r.sell} USDT`).join(' · '))

// 100 dollars in euro
const c = await ezrate('convert', { from: 'usd', to: 'eur', amount: 100 })
console.log(`100 USD = ${c.data.result.toFixed(2)} EUR`)
