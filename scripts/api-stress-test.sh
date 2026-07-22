#!/usr/bin/env bash
# Lightweight API stress test for pre-campaign validation.
#
# Usage:
#   ./scripts/api-stress-test.sh
#   API_BASE=https://admin.appzimo.com REQUESTS=200 CONCURRENCY=25 ./scripts/api-stress-test.sh
set -euo pipefail

API_BASE="${API_BASE:-https://admin.appzimo.com}"
API_BASE="${API_BASE%/}"
REQUESTS="${REQUESTS:-150}"
CONCURRENCY="${CONCURRENCY:-25}"
MAX_ERROR_RATE="${MAX_ERROR_RATE:-1.0}"

ENDPOINTS=(
  "POST|/api/customer/app-version-check|{\"app_type\":\"1\",\"app_version\":\"1.0.0\",\"device_type\":\"android\",\"login_device\":\"1\"}"
  "POST|/api/customer/home|{\"user_id\":1,\"access_token\":\"test-token-12345678\"}"
  "POST|/api/customer/country-and-currency-list|{}"
  "POST|/api/customer/support-pages|{}"
)

TMP_DIR="$(mktemp -d /tmp/api-stress.XXXXXX)"
trap 'rm -rf "${TMP_DIR}"' EXIT

echo "API stress test — ${API_BASE}"
echo "Requests/endpoint: ${REQUESTS}, concurrency: ${CONCURRENCY}, max error rate: ${MAX_ERROR_RATE}%"
echo "================================================================"

TOTAL=0
ERRORS=0

for spec in "${ENDPOINTS[@]}"; do
  IFS='|' read -r method path body <<< "${spec}"
  idx="${method}-${path//\//-}"
  LOG="${TMP_DIR}/codes-${idx}.log"
  : > "${LOG}"

  seq 1 "${REQUESTS}" | xargs -P "${CONCURRENCY}" -I{} bash -c '
    code="$(curl -sS -o /dev/null -w "%{http_code}" --connect-timeout 15 --max-time 20 \
      -X "$1" "'"${API_BASE}"'$2" \
      -H "Content-Type: application/json" \
      -d "$3" 2>/dev/null || echo 000)"
    echo "$code"
  ' _ "${method}" "${path}" "${body}" >> "${LOG}"

  EP_TOTAL="$(wc -l < "${LOG}" | tr -d ' ')"
  EP_500="$(grep -c '^500$' "${LOG}" || true)"
  EP_502="$(grep -c '^502$' "${LOG}" || true)"
  EP_503="$(grep -c '^503$' "${LOG}" || true)"
  EP_000="$(grep -c '^000$' "${LOG}" || true)"
  EP_ERR=$((EP_500 + EP_502 + EP_503 + EP_000))
  EP_RATE="$(awk -v e="${EP_ERR}" -v t="${EP_TOTAL}" 'BEGIN { if (t==0) print 100; else printf "%.2f", (e/t)*100 }')"

  echo "${path}: total=${EP_TOTAL} errors=${EP_ERR} (500=${EP_500} 502=${EP_502} 503=${EP_503} timeout=${EP_000}) rate=${EP_RATE}%"
  TOTAL=$((TOTAL + EP_TOTAL))
  ERRORS=$((EP_ERR + EP_500)) # wait that's wrong
  ERRORS=$((ERRORS + EP_ERR))
done

# Fix double counting bug - reset ERRORS accumulation
ERRORS=0
for spec in "${ENDPOINTS[@]}"; do
  IFS='|' read -r method path body <<< "${spec}"
  idx="${method}-${path//\//-}"
  LOG="${TMP_DIR}/codes-${idx}.log"
  EP_500="$(grep -c '^500$' "${LOG}" || true)"
  EP_502="$(grep -c '^502$' "${LOG}" || true)"
  EP_503="$(grep -c '^503$' "${LOG}" || true)"
  EP_000="$(grep -c '^000$' "${LOG}" || true)"
  ERRORS=$((ERRORS + EP_500 + EP_502 + EP_503 + EP_000))
done

OVERALL_RATE="$(awk -v e="${ERRORS}" -v t="${TOTAL}" 'BEGIN { if (t==0) print 100; else printf "%.2f", (e/t)*100 }')"
echo "================================================================"
echo "Overall: ${TOTAL} requests, ${ERRORS} server errors, ${OVERALL_RATE}% error rate"

awk -v rate="${OVERALL_RATE}" -v max="${MAX_ERROR_RATE}" 'BEGIN { exit (rate <= max ? 0 : 1) }' || {
  echo "FAIL: error rate ${OVERALL_RATE}% exceeds max ${MAX_ERROR_RATE}%" >&2
  exit 1
}
echo "PASS: error rate within threshold"
