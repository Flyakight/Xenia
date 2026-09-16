#!/usr/bin/env bash
# Pull a public CodePen's three source panels into research/pens/<author>-<id>/.
#
#   ./research/fetch-pen.sh https://codepen.io/designfenix/pen/qENLeEB
#
# CodePen serves each panel as raw source when you append the file extension
# to the pen URL. If that ever stops working the script says so loudly rather
# than committing a saved error page.

set -uo pipefail

url="${1:-}"
if [[ -z "$url" ]]; then
  echo "usage: $0 <codepen-url> [more urls...]" >&2
  exit 1
fi

root="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/pens"
UA='Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 Chrome/124 Safari/537.36'

for url in "$@"; do
  # https://codepen.io/<author>/pen/<id>  ->  author, id
  clean="${url%%\?*}"; clean="${clean%/}"
  id="${clean##*/}"
  rest="${clean%/*}"; rest="${rest%/pen}"
  author="${rest##*/}"

  if [[ -z "$id" || -z "$author" ]]; then
    echo "!! could not parse: $url" >&2
    continue
  fi

  dir="$root/$author-$id"
  mkdir -p "$dir"
  echo "== $author/$id -> research/pens/$author-$id"

  {
    echo "source:    $clean"
    echo "author:    $author"
    echo "retrieved: $(date -u +%Y-%m-%dT%H:%M:%SZ)"
    echo "license:   CodePen default is MIT unless the pen says otherwise — check before reusing verbatim."
  } > "$dir/SOURCE.txt"

  ok=0
  for ext in html css js; do
    out="$dir/pen.$ext"
    code=$(curl -sS -L -A "$UA" -o "$out" -w '%{http_code}' "$clean.$ext" || echo 000)
    size=$(wc -c < "$out" | tr -d ' ')

    if [[ "$code" != "200" ]]; then
      echo "   $ext  HTTP $code — removed"
      rm -f "$out"; continue
    fi
    # A panel that comes back as a full HTML document is CodePen's error or
    # login page, not source. Don't let that get committed as "the code".
    if [[ "$ext" != "html" ]] && head -c 200 "$out" | grep -qi '<!doctype html'; then
      echo "   $ext  got an HTML page, not source — removed"
      rm -f "$out"; continue
    fi
    if [[ "$size" -eq 0 ]]; then
      echo "   $ext  empty — removed"
      rm -f "$out"; continue
    fi
    echo "   $ext  ${size} bytes"
    ok=1
  done

  [[ "$ok" -eq 0 ]] && echo "   !! nothing retrieved — fall back to copying the panels by hand into $dir"
done

echo
echo "Then: git add research && git commit -m 'Add pen references' && git push"
