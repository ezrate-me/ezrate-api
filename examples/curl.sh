#!/usr/bin/env bash
# EZRate API with cURL — set your key first:  export EZRATE_KEY=ezr_live_...
set -euo pipefail
API=https://api.ezrate.me/v1
H="X-API-Key: ${EZRATE_KEY:?set EZRATE_KEY}"

# dollar, 18k gold, Emami coin and USDT in Toman (names in Persian)
curl -s -H "$H" "$API/rates?symbols=usd,g18,emami,usdt&lang=fa"; echo

# every gold price in US dollars
curl -s -H "$H" "$API/rates?category=gold&quote=usd"; echo

# 100 USD in euro
curl -s -H "$H" "$API/convert?from=usd&to=eur&amount=100"; echo

# daily candles of the dollar since the start of the month
curl -s -H "$H" "$API/history/usd?interval=1d&from=$(date +%Y-%m-01)"; echo

# your usage today (does not count)
curl -s -H "$H" "$API/usage"; echo
