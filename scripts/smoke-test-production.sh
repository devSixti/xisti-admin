#!/usr/bin/env bash
# Smoke tests against production (or staging) XISTI admin/API.
#
# Usage:
#   ./scripts/smoke-test-production.sh
#   API_BASE=https://admin.xistiapp.com ./scripts/smoke-test-production.sh
#
# Optional: pass XISTI_APP_KEY to test Authorization header (reads from .env if local).
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "${ROOT}"

API_BASE="${API_BASE:-https://admin.xistiapp.com}"
API_BASE="${API_BASE%/}"
PASS=0
FAIL=0
SKIP=0

pass() { echo "  PASS: $*"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $*" >&2; FAIL=$((FAIL + 1)); }
skip() { echo "  SKIP: $*"; SKIP=$((SKIP + 1)); }

read_local_app_key() {
  if [[ -n "${XISTI_APP_KEY:-}" ]]; then
    printf '%s' "${XISTI_APP_KEY}"
    return
  fi
  if [[ -f .env ]]; then
    grep -E '^XISTI_APP_KEY=' .env 2>/dev/null | cut -d= -f2- | tr -d '"' | tr -d "'" || true
  fi
}

build_auth_header() {
  local key="$1"
  if [[ -f vendor/autoload.php ]]; then
    php scripts/generate-app-auth-header.php "${key}" --raw
    return
  fi
  php -r '
$key = $argv[1];
$digest = md5(base64_encode($key));
$chars = "AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz1234567890";
$rand = function (int $n) use ($chars): string {
    $s = "";
    for ($i = 0; $i < $n; $i++) {
        $s .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $s;
};
echo $rand(57) . $digest . $rand(43);
' "${key}"
}

http_code() {
  curl -sS -o /dev/null -w '%{http_code}' "$@"
}

echo "XISTI smoke tests — ${API_BASE}"
echo "================================"

# 1. HTTPS admin root
CODE="$(http_code -L --connect-timeout 15 "${API_BASE}/")"
if [[ "${CODE}" == "200" ]]; then pass "HTTPS admin root (${CODE})"; else fail "HTTPS admin root (${CODE})"; fi

# 2. Admin login page
CODE="$(http_code -L --connect-timeout 15 "${API_BASE}/admin/login")"
if [[ "${CODE}" == "200" ]]; then pass "Admin login page (${CODE})"; else fail "Admin login page (${CODE})"; fi

# 3. Privacy policy
CODE="$(http_code -L --connect-timeout 15 "${API_BASE}/privacy-policy")"
if [[ "${CODE}" == "200" ]]; then pass "Privacy policy (${CODE})"; else fail "Privacy policy (${CODE})"; fi

# 4. app-version-check
VERSION_JSON="$(curl -fsS --connect-timeout 15 -X POST "${API_BASE}/api/customer/app-version-check" \
  -H "Content-Type: application/json" \
  -d '{"app_type":"1","app_version":"1.0.0","device_type":"android","login_device":"1"}' 2>/dev/null || echo '{}')"

while IFS= read -r line; do
  if [[ "${line}" == PASS:* ]]; then pass "${line#PASS: }"; else fail "${line#FAIL: }"; fi
done < <(php -r '
$json = $argv[1];
$j = json_decode($json, true);
if (!is_array($j) || ($j["status"] ?? 0) != 1) { echo "FAIL: app-version-check status\n"; exit(0); }
$key = (string)($j["app_key"] ?? "");
if ($key === "" || str_contains($key, "CHANGE_ME") || str_contains($key, "ChangeThis")) {
  echo "FAIL: app-version-check missing valid app_key\n";
} else {
  echo "PASS: app-version-check OK (app_key present for mobile bootstrap)\n";
}
if ((int)($j["enable_encomiendas_mobile"] ?? -1) !== 1) echo "FAIL: enable_encomiendas_mobile != 1\n";
else echo "PASS: enable_encomiendas_mobile=1\n";
if ((int)($j["enable_expreso_mobile"] ?? -1) !== 0) echo "FAIL: enable_expreso_mobile != 0\n";
else echo "PASS: enable_expreso_mobile=0\n";
if ((int)($j["admin_commission_percent"] ?? 0) !== 8) echo "FAIL: admin_commission_percent != 8\n";
else echo "PASS: admin_commission_percent=8\n";
if ((int)($j["fare_negotiation_step"] ?? 0) !== 500) echo "FAIL: fare_negotiation_step != 500\n";
else echo "PASS: fare_negotiation_step=500\n";
' "${VERSION_JSON}")

# 5. Wompi webhook routes exist (401 = auth required, 404 = missing)
for path in /api/wompi/webhook /webhook/wompi; do
  CODE="$(http_code --connect-timeout 15 -X POST "${API_BASE}${path}" -H "Content-Type: application/json" -d '{}')"
  if [[ "${CODE}" == "401" || "${CODE}" == "200" || "${CODE}" == "422" ]]; then
    pass "Wompi webhook ${path} (${CODE})"
  elif [[ "${CODE}" == "404" ]]; then
    fail "Wompi webhook ${path} (404 — route missing)"
  else
    fail "Wompi webhook ${path} (${CODE})"
  fi
done

# 6. Authorization header
APP_KEY="$(read_local_app_key)"
if [[ -z "${APP_KEY}" || "${APP_KEY}" == *"CHANGE_ME"* ]]; then
  APP_KEY="$(ssh -o BatchMode=yes -o ConnectTimeout=10 "${XISTI_SSH_HOST:-xisti-ec2}" \
    "sudo -u ubuntu bash -lc 'cd /var/www/xisti-admin && php artisan tinker --execute=\"echo trim((string)(\\\\App\\\\Models\\\\GeneralSettings::query()->value(\\\"app_key\\\") ?? config(\\\"xisti.app_key\\\")));\"'" 2>/dev/null | tail -1 | tr -d '\r' || true)"
fi

if [[ -z "${APP_KEY}" || "${APP_KEY}" == *"CHANGE_ME"* ]]; then
  skip "Authorization header (no valid app_key available locally or in API)"
else
    AUTH_HEADER="$(build_auth_header "${APP_KEY}")"
    LOGIN_JSON="$(curl -fsS --connect-timeout 15 -X POST "${API_BASE}/api/customer/login" \
      -H "Content-Type: application/json" \
      -H "Authorization: ${AUTH_HEADER}" \
      -d '{"login_type":"email","contact_number":"3001234567","select_country_code":"+57","select_currency":"COP","select_language":"es","device_token":"smoke","login_device":"1"}' 2>/dev/null || echo '{}')"
    AUTH_OK="$(php -r '
$j=json_decode(file_get_contents("php://stdin"), true);
if (!is_array($j)) { echo "no"; exit; }
$msg = (string)($j["message"] ?? "");
if (str_contains(strtolower($msg), "authorization") || str_contains(strtolower($msg), "autoriz")) { echo "no"; exit; }
echo "yes";
' <<<"${LOGIN_JSON}")"
    if [[ "${AUTH_OK}" == "yes" ]]; then pass "Authorization header accepted on /login"; else fail "Authorization header rejected on /login"; fi
fi

# 7. Home API — delivery_vehicle_options (Moto + Carro + Bicicleta)
if [[ -n "${APP_KEY}" && "${APP_KEY}" != *"CHANGE_ME"* ]]; then
    AUTH_HEADER="$(build_auth_header "${APP_KEY}")"
    LOGIN_JSON="$(curl -fsS --connect-timeout 15 -X POST "${API_BASE}/api/customer/login" \
      -H "Content-Type: application/json" \
      -H "Authorization: ${AUTH_HEADER}" \
      -d '{"login_type":"email","contact_number":"3001234567","select_country_code":"+57","select_currency":"COP","select_language":"es","device_token":"smoke","login_device":"1"}' 2>/dev/null || echo '{}')"
    HOME_RESULT="$(php -r '
$api = $argv[1];
$auth = $argv[2];
$login = json_decode($argv[3], true);
if (!is_array($login) || empty($login["user_id"]) || empty($login["access_token"])) { echo "SKIP:no_login_user"; exit(0); }
$verifyBody = json_encode([
  "user_id" => $login["user_id"],
  "access_token" => $login["access_token"],
  "otp" => "123456",
]);
$chV = curl_init($api . "/api/customer/contact-verification");
curl_setopt_array($chV, [
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => ["Content-Type: application/json", "Authorization: " . $auth],
  CURLOPT_POSTFIELDS => $verifyBody,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_TIMEOUT => 15,
]);
curl_exec($chV);
curl_close($chV);
$body = json_encode([
  "user_id" => $login["user_id"],
  "access_token" => $login["access_token"],
  "app_version" => "1.0.0",
  "current_lat" => "4.6243",
  "current_long" => "-74.0636",
  "service_category_id" => 1,
]);
$ch = curl_init($api . "/api/customer/home");
curl_setopt_array($ch, [
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => ["Content-Type: application/json", "Authorization: " . $auth],
  CURLOPT_POSTFIELDS => $body,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_TIMEOUT => 15,
]);
$raw = curl_exec($ch);
curl_close($ch);
$j = json_decode($raw, true);
if (!is_array($j) || ($j["status"] ?? 0) != 1) { echo "FAIL:home status=" . ($j["status"] ?? "?"); exit(0); }
$opts = $j["delivery_vehicle_options"] ?? [];
$n = count($opts);
if ($n !== 3) { echo "FAIL:delivery_vehicle_options count=$n (expected 3)"; exit(0); }
$ids = array_map(fn($o) => (int)($o["vehicle_service_id"] ?? 0), $opts);
sort($ids);
if ($ids !== [1, 3, 4]) { echo "FAIL:delivery ids=" . implode(",", $ids) . " (expected 1,3,4)"; exit(0); }
echo "PASS:home delivery_vehicle_options Moto+Carro+Bicicleta (ids 1,3,4)";
' "${API_BASE}" "${AUTH_HEADER}" "${LOGIN_JSON}")"
    case "${HOME_RESULT}" in
      PASS:*) pass "${HOME_RESULT#PASS:}" ;;
      FAIL:*) fail "${HOME_RESULT#FAIL:}" ;;
      SKIP:*) skip "${HOME_RESULT#SKIP:}" ;;
    esac
fi

# 8. Google map proxy (authenticated mobile API)
if [[ -n "${APP_KEY}" && "${APP_KEY}" != *"CHANGE_ME"* ]]; then
    AUTH_HEADER="$(build_auth_header "${APP_KEY}")"
    LOGIN_JSON="$(curl -fsS --connect-timeout 15 -X POST "${API_BASE}/api/customer/login" \
      -H "Content-Type: application/json" \
      -H "Authorization: ${AUTH_HEADER}" \
      -d '{"login_type":"email","contact_number":"3001234567","select_country_code":"+57","select_currency":"COP","select_language":"es","device_token":"smoke","login_device":"1"}' 2>/dev/null || echo '{}')"
    MAP_RESULT="$(php -r '
$api = $argv[1];
$auth = $argv[2];
$login = json_decode($argv[3], true);
if (!is_array($login) || empty($login["user_id"]) || empty($login["access_token"])) { echo "SKIP:no_login_user"; exit(0); }
$verifyBody = json_encode([
  "user_id" => $login["user_id"],
  "access_token" => $login["access_token"],
  "otp" => "123456",
]);
$chV = curl_init($api . "/api/customer/contact-verification");
curl_setopt_array($chV, [
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => ["Content-Type: application/json", "Authorization: " . $auth],
  CURLOPT_POSTFIELDS => $verifyBody,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_TIMEOUT => 15,
]);
curl_exec($chV);
curl_close($chV);
$body = json_encode([
  "user_id" => $login["user_id"],
  "access_token" => $login["access_token"],
  "url" => "https://maps.googleapis.com/maps/api/geocode/json?latlng=6.2442,-75.5812&key=",
]);
$ch = curl_init($api . "/api/google-map");
curl_setopt_array($ch, [
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => ["Content-Type: application/json", "Authorization: " . $auth],
  CURLOPT_POSTFIELDS => $body,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_TIMEOUT => 15,
]);
$raw = curl_exec($ch);
$code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
$j = json_decode($raw, true);
if (is_array($j) && (($j["status"] ?? "") === "OK" || ($j["status"] ?? 0) === 1)) { echo "PASS:Google map proxy responds"; exit(0); }
if (is_array($j) && isset($j["results"])) { echo "PASS:Google map proxy responds"; exit(0); }
if ($code === 403 || $code === 401) { echo "FAIL:Google map proxy auth HTTP $code"; exit(0); }
if ($code === 503) { echo "SKIP:Google map proxy (server_map_key missing)"; exit(0); }
echo "FAIL:Google map proxy HTTP $code";
' "${API_BASE}" "${AUTH_HEADER}" "${LOGIN_JSON}")"
    case "${MAP_RESULT}" in
      PASS:*) pass "${MAP_RESULT#PASS:}" ;;
      FAIL:*) fail "${MAP_RESULT#FAIL:}" ;;
      SKIP:*) skip "${MAP_RESULT#SKIP:}" ;;
    esac
else
    skip "Google map proxy (no app_key for auth)"
fi

echo ""
echo "================================"
echo "Results: ${PASS} passed, ${FAIL} failed, ${SKIP} skipped"
if [[ "${FAIL}" -gt 0 ]]; then exit 1; fi
