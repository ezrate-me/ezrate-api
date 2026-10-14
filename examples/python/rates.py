"""EZRate API with Python 3 (standard library only).  Run:  EZRATE_KEY=ezr_live_... python3 rates.py"""
import json
import os
import urllib.parse
import urllib.request

KEY = os.environ.get("EZRATE_KEY") or exit("Set EZRATE_KEY first.")


def ezrate(path: str, **query) -> dict:
    url = f"https://api.ezrate.me/v1/{path}"
    if query:
        url += "?" + urllib.parse.urlencode(query)
    req = urllib.request.Request(url, headers={"X-API-Key": KEY, "Accept": "application/json"})
    try:
        with urllib.request.urlopen(req, timeout=10) as res:
            return json.load(res)
    except urllib.error.HTTPError as e:
        raise RuntimeError(json.load(e).get("error", {}).get("message", f"HTTP {e.code}")) from None


# dollar, 18k gold, Emami coin and USDT in Toman
for r in ezrate("rates", symbols="usd,g18,emami,usdt", lang="en")["data"]:
    print(f"{r['name']:<16} {r['sell']:>14,} Toman  {r['change_pct']:+.2f}%")

# daily candles of the dollar (for a chart)
candles = ezrate("history/usd", interval="1d", **{"from": "2026-10-01"})["data"]
print("last close:", candles[-1]["c"] if candles else "-")

# 100 dollars in euro
print("100 USD =", round(ezrate("convert", **{"from": "usd", "to": "eur", "amount": 100})["data"]["result"], 2), "EUR")
