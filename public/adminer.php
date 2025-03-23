<?php

/** Adminer - Compact database management
 * @link https://www.adminer.org/
 * @author Jakub Vrana, https://www.vrana.cz/
 * @copyright 2007 Jakub Vrana
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
 * @version 5.0.6
 */

namespace
Adminer;

$ga = "5.0.6";
error_reporting(6135);
set_error_handler(function ($ic, $kc) {
  return !!preg_match('~^(Trying to access array offset on( value of type)? null|Undefined (array key|property))~', $kc);
}, E_WARNING);
$Cc = !preg_match('~^(unsafe_raw)?$~', ini_get("filter.default"));
if ($Cc || ini_get("filter.default_flags")) {
  foreach (array('_GET', '_POST', '_COOKIE', '_SERVER') as $X) {
    $Ch = filter_input_array(constant("INPUT$X"), FILTER_UNSAFE_RAW);
    if ($Ch) $$X = $Ch;
  }
}
if (function_exists("mb_internal_encoding")) mb_internal_encoding("8bit");
function
connection()
{
  global $e;
  return $e;
}
function
adminer()
{
  global $b;
  return $b;
}
function
driver()
{
  global $k;
  return $k;
}
function
version()
{
  global $ga;
  return $ga;
}
function
idf_unescape($jd)
{
  if (!preg_match('~^[`\'"[]~', $jd)) return $jd;
  $Jd = substr($jd, -1);
  return
    str_replace($Jd . $Jd, $Jd, substr($jd, 1, -1));
}
function
q($Ig)
{
  global $e;
  return $e->quote($Ig);
}
function
escape_string($X)
{
  return
    substr(q($X), 1, -1);
}
function
number($X)
{
  return
    preg_replace('~[^0-9]+~', '', $X);
}
function
number_type()
{
  return '((?<!o)int(?!er)|numeric|real|float|double|decimal|money)';
}
function
remove_slashes($Ef, $Cc = false)
{
  if (function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) {
    while (list($y, $X) = each($Ef)) {
      foreach (
        $X
        as $Cd => $W
      ) {
        unset($Ef[$y][$Cd]);
        if (is_array($W)) {
          $Ef[$y][stripslashes($Cd)] = $W;
          $Ef[] = &$Ef[$y][stripslashes($Cd)];
        } else $Ef[$y][stripslashes($Cd)] = ($Cc ? $W : stripslashes($W));
      }
    }
  }
}
function
bracket_escape($jd, $ya = false)
{
  static $qh = array(':' => ':1', ']' => ':2', '[' => ':3', '"' => ':4');
  return
    strtr($jd, ($ya ? array_flip($qh) : $qh));
}
function
min_version($Rh, $Wd = "", $f = null)
{
  global $e;
  if (!$f) $f = $e;
  $ng = $f->server_info;
  if ($Wd && preg_match('~([\d.]+)-MariaDB~', $ng, $B)) {
    $ng = $B[1];
    $Rh = $Wd;
  }
  return $Rh && version_compare($ng, $Rh) >= 0;
}
function
charset($e)
{
  return (min_version("5.5.3", 0, $e) ? "utf8mb4" : "utf8");
}
function
ini_bool($pd)
{
  $X = ini_get($pd);
  return (preg_match('~^(on|true|yes)$~i', $X) || (int)$X);
}
function
sid()
{
  static $J;
  if ($J === null) $J = (SID && !($_COOKIE && ini_bool("session.use_cookies")));
  return $J;
}
function
set_password($Qh, $O, $V, $F)
{
  $_SESSION["pwds"][$Qh][$O][$V] = ($_COOKIE["adminer_key"] && is_string($F) ? array(encrypt_string($F, $_COOKIE["adminer_key"])) : $F);
}
function
get_password()
{
  $J = get_session("pwds");
  if (is_array($J)) $J = ($_COOKIE["adminer_key"] ? decrypt_string($J[0], $_COOKIE["adminer_key"]) : false);
  return $J;
}
function
get_val($H, $m = 0)
{
  global $e;
  return $e->result($H, $m);
}
function
get_vals($H, $c = 0)
{
  global $e;
  $J = array();
  $I = $e->query($H);
  if (is_object($I)) {
    while ($K = $I->fetch_row()) $J[] = $K[$c];
  }
  return $J;
}
function
get_key_vals($H, $f = null, $qg = true)
{
  global $e;
  if (!is_object($f)) $f = $e;
  $J = array();
  $I = $f->query($H);
  if (is_object($I)) {
    while ($K = $I->fetch_row()) {
      if ($qg) $J[$K[0]] = $K[1];
      else $J[] = $K[0];
    }
  }
  return $J;
}
function
get_rows($H, $f = null, $l = "<p class='error'>")
{
  global $e;
  $hb = (is_object($f) ? $f : $e);
  $J = array();
  $I = $hb->query($H);
  if (is_object($I)) {
    while ($K = $I->fetch_assoc()) $J[] = $K;
  } elseif (!$I && !is_object($f) && $l && (defined('Adminer\PAGE_HEADER') || $l == "-- ")) echo $l . error() . "\n";
  return $J;
}
function
unique_array($K, $x)
{
  foreach (
    $x
    as $w
  ) {
    if (preg_match("~PRIMARY|UNIQUE~", $w["type"])) {
      $J = array();
      foreach ($w["columns"] as $y) {
        if (!isset($K[$y])) continue
          2;
        $J[$y] = $K[$y];
      }
      return $J;
    }
  }
}
function
escape_key($y)
{
  if (preg_match('(^([\w(]+)(' . str_replace("_", ".*", preg_quote(idf_escape("_"))) . ')([ \w)]+)$)', $y, $B)) return $B[1] . idf_escape(idf_unescape($B[2])) . $B[3];
  return
    idf_escape($y);
}
function
where($Z, $n = array())
{
  global $e;
  $J = array();
  foreach ((array)$Z["where"] as $y => $X) {
    $y = bracket_escape($y, 1);
    $c = escape_key($y);
    $Ac = $n[$y]["type"];
    $J[] = $c . (JUSH == "sql" && $Ac == "json" ? " = CAST(" . q($X) . " AS JSON)" : (JUSH == "sql" && is_numeric($X) && preg_match('~\.~', $X) ? " LIKE " . q($X) : (JUSH == "mssql" && strpos($Ac, "datetime") === false ? " LIKE " . q(preg_replace('~[_%[]~', '[\0]', $X)) : " = " . unconvert_field($n[$y], q($X)))));
    if (JUSH == "sql" && preg_match('~char|text~', $Ac) && preg_match("~[^ -@]~", $X)) $J[] = "$c = " . q($X) . " COLLATE " . charset($e) . "_bin";
  }
  foreach ((array)$Z["null"] as $y) $J[] = escape_key($y) . " IS NULL";
  return
    implode(" AND ", $J);
}
function
where_check($X, $n = array())
{
  parse_str($X, $Na);
  remove_slashes(array(&$Na));
  return
    where($Na, $n);
}
function
where_link($u, $c, $Y, $Le = "=")
{
  return "&where%5B$u%5D%5Bcol%5D=" . urlencode($c) . "&where%5B$u%5D%5Bop%5D=" . urlencode(($Y !== null ? $Le : "IS NULL")) . "&where%5B$u%5D%5Bval%5D=" . urlencode($Y);
}
function
convert_fields($d, $n, $N = array())
{
  $J = "";
  foreach (
    $d
    as $y => $X
  ) {
    if ($N && !in_array(idf_escape($y), $N)) continue;
    $ra = convert_field($n[$y]);
    if ($ra) $J .= ", $ra AS " . idf_escape($y);
  }
  return $J;
}
function
cookie($D, $Y, $Rd = 2592000)
{
  global $ba;
  return
    header("Set-Cookie: $D=" . urlencode($Y) . ($Rd ? "; expires=" . gmdate("D, d M Y H:i:s", time() + $Rd) . " GMT" : "") . "; path=" . preg_replace('~\?.*~', '', $_SERVER["REQUEST_URI"]) . ($ba ? "; secure" : "") . "; HttpOnly; SameSite=lax", false);
}
function
get_settings($mb)
{
  parse_str($_COOKIE[$mb], $rg);
  return $rg;
}
function
get_setting($y, $mb = "adminer_settings")
{
  $rg = get_settings($mb);
  return $rg[$y];
}
function
save_settings($rg, $mb = "adminer_settings")
{
  return
    cookie($mb, http_build_query($rg + get_settings($mb)));
}
function
restart_session()
{
  if (!ini_bool("session.use_cookies")) session_start();
}
function
stop_session($Gc = false)
{
  $Kh = ini_bool("session.use_cookies");
  if (!$Kh || $Gc) {
    session_write_close();
    if ($Kh && @ini_set("session.use_cookies", false) === false) session_start();
  }
}
function &get_session($y)
{
  return $_SESSION[$y][DRIVER][SERVER][$_GET["username"]];
}
function
set_session($y, $X)
{
  $_SESSION[$y][DRIVER][SERVER][$_GET["username"]] = $X;
}
function
auth_url($Qh, $O, $V, $i = null)
{
  global $Nb;
  preg_match('~([^?]*)\??(.*)~', remove_from_uri(implode("|", array_keys($Nb)) . "|username|" . ($i !== null ? "db|" : "") . session_name()), $B);
  return "$B[1]?" . (sid() ? SID . "&" : "") . ($Qh != "server" || $O != "" ? urlencode($Qh) . "=" . urlencode($O) . "&" : "") . "username=" . urlencode($V) . ($i != "" ? "&db=" . urlencode($i) : "") . ($B[2] ? "&$B[2]" : "");
}
function
is_ajax()
{
  return ($_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest");
}
function
redirect($A, $C = null)
{
  if ($C !== null) {
    restart_session();
    $_SESSION["messages"][preg_replace('~^[^?]*~', '', ($A !== null ? $A : $_SERVER["REQUEST_URI"]))][] = $C;
  }
  if ($A !== null) {
    if ($A == "") $A = ".";
    header("Location: $A");
    exit;
  }
}
function
query_redirect($H, $A, $C, $Mf = true, $pc = true, $xc = false, $eh = "")
{
  global $e, $l, $b;
  if ($pc) {
    $Eg = microtime(true);
    $xc = !$e->query($H);
    $eh = format_time($Eg);
  }
  $_g = "";
  if ($H) $_g = $b->messageQuery($H, $eh, $xc);
  if ($xc) {
    $l = error() . $_g . script("messagesPrint();");
    return
      false;
  }
  if ($Mf) redirect($A, $C . $_g);
  return
    true;
}
function
queries($H)
{
  global $e;
  static $Hf = array();
  static $Eg;
  if (!$Eg) $Eg = microtime(true);
  if ($H === null) return
    array(implode("\n", $Hf), format_time($Eg));
  $Hf[] = (preg_match('~;$~', $H) ? "DELIMITER ;;\n$H;\nDELIMITER " : $H) . ";";
  return $e->query($H);
}
function
apply_queries($H, $S, $lc = 'Adminer\table')
{
  foreach (
    $S
    as $Q
  ) {
    if (!queries("$H " . $lc($Q))) return
      false;
  }
  return
    true;
}
function
queries_redirect($A, $C, $Mf)
{
  list($Hf, $eh) = queries(null);
  return
    query_redirect($Hf, $A, $C, $Mf, false, !$Mf, $eh);
}
function
format_time($Eg)
{
  return
    sprintf('%.3f s', max(0, microtime(true) - $Eg));
}
function
relative_uri()
{
  return
    str_replace(":", "%3a", preg_replace('~^[^?]*/([^?]*)~', '\1', $_SERVER["REQUEST_URI"]));
}
function
remove_from_uri($ff = "")
{
  return
    substr(preg_replace("~(?<=[?&])($ff" . (SID ? "" : "|" . session_name()) . ")=[^&]*&~", '', relative_uri() . "&"), 0, -1);
}
function
get_file($y, $Ab = false, $Eb = "")
{
  $Bc = $_FILES[$y];
  if (!$Bc) return
    null;
  foreach (
    $Bc
    as $y => $X
  ) $Bc[$y] = (array)$X;
  $J = '';
  foreach ($Bc["error"] as $y => $l) {
    if ($l) return $l;
    $D = $Bc["name"][$y];
    $mh = $Bc["tmp_name"][$y];
    $ib = file_get_contents($Ab && preg_match('~\.gz$~', $D) ? "compress.zlib://$mh" : $mh);
    if ($Ab) {
      $Eg = substr($ib, 0, 3);
      if (function_exists("iconv") && preg_match("~^\xFE\xFF|^\xFF\xFE~", $Eg)) $ib = iconv("utf-16", "utf-8", $ib);
      elseif ($Eg == "\xEF\xBB\xBF") $ib = substr($ib, 3);
    }
    $J .= $ib;
    if ($Eb) $J .= (preg_match("($Eb\\s*\$)", $ib) ? "" : $Eb) . "\n\n";
  }
  return $J;
}
function
upload_error($l)
{
  $de = ($l == UPLOAD_ERR_INI_SIZE ? ini_get("upload_max_filesize") : 0);
  return ($l ? 'Unable to upload a file.' . ($de ? " " . sprintf('Maximum allowed file size is %sB.', $de) : "") : 'File does not exist.');
}
function
repeat_pattern($of, $Pd)
{
  return
    str_repeat("$of{0,65535}", $Pd / 65535) . "$of{0," . ($Pd % 65535) . "}";
}
function
is_utf8($X)
{
  return (preg_match('~~u', $X) && !preg_match('~[\0-\x8\xB\xC\xE-\x1F]~', $X));
}
function
shorten_utf8($Ig, $Pd = 80, $Mg = "")
{
  if (!preg_match("(^(" . repeat_pattern("[\t\r\n -\x{10FFFF}]", $Pd) . ")($)?)u", $Ig, $B)) preg_match("(^(" . repeat_pattern("[\t\r\n -~]", $Pd) . ")($)?)", $Ig, $B);
  return
    h($B[1]) . $Mg . (isset($B[2]) ? "" : "<i>…</i>");
}
function
format_number($X)
{
  return
    strtr(number_format($X, 0, ".", ','), preg_split('~~u', '0123456789', -1, PREG_SPLIT_NO_EMPTY));
}
function
friendly_url($X)
{
  return
    preg_replace('~\W~i', '-', $X);
}
function
table_status1($Q, $yc = false)
{
  $J = table_status($Q, $yc);
  return ($J ?: array("Name" => $Q));
}
function
column_foreign_keys($Q)
{
  global $b;
  $J = array();
  foreach ($b->foreignKeys($Q) as $p) {
    foreach ($p["source"] as $X) $J[$X][] = $p;
  }
  return $J;
}
function
fields_from_edit()
{
  global $k;
  $J = array();
  foreach ((array)$_POST["field_keys"] as $y => $X) {
    if ($X != "") {
      $X = bracket_escape($X);
      $_POST["function"][$X] = $_POST["field_funs"][$y];
      $_POST["fields"][$X] = $_POST["field_vals"][$y];
    }
  }
  foreach ((array)$_POST["fields"] as $y => $X) {
    $D = bracket_escape($y, 1);
    $J[$D] = array("field" => $D, "privileges" => array("insert" => 1, "update" => 1, "where" => 1, "order" => 1), "null" => 1, "auto_increment" => ($y == $k->primary),);
  }
  return $J;
}
function
dump_headers($hd, $oe = false)
{
  global $b;
  $J = $b->dumpHeaders($hd, $oe);
  $cf = $_POST["output"];
  if ($cf != "text") header("Content-Disposition: attachment; filename=" . $b->dumpFilename($hd) . ".$J" . ($cf != "file" && preg_match('~^[0-9a-z]+$~', $cf) ? ".$cf" : ""));
  session_write_close();
  ob_flush();
  flush();
  return $J;
}
function
dump_csv($K)
{
  foreach (
    $K
    as $y => $X
  ) {
    if (preg_match('~["\n,;\t]|^0|\.\d*0$~', $X) || $X === "") $K[$y] = '"' . str_replace('"', '""', $X) . '"';
  }
  echo
  implode(($_POST["format"] == "csv" ? "," : ($_POST["format"] == "tsv" ? "\t" : ";")), $K) . "\r\n";
}
function
apply_sql_function($s, $c)
{
  return ($s ? ($s == "unixepoch" ? "DATETIME($c, '$s')" : ($s == "count distinct" ? "COUNT(DISTINCT " : strtoupper("$s(")) . "$c)") : $c);
}
function
get_temp_dir()
{
  $J = ini_get("upload_tmp_dir");
  if (!$J) {
    if (function_exists('sys_get_temp_dir')) $J = sys_get_temp_dir();
    else {
      $o = @tempnam("", "");
      if (!$o) return
        false;
      $J = dirname($o);
      unlink($o);
    }
  }
  return $J;
}
function
file_open_lock($o)
{
  if (is_link($o)) return;
  $r = @fopen($o, "c+");
  if (!$r) return;
  chmod($o, 0660);
  if (!flock($r, LOCK_EX)) {
    fclose($r);
    return;
  }
  return $r;
}
function
file_write_unlock($r, $vb)
{
  rewind($r);
  fwrite($r, $vb);
  ftruncate($r, strlen($vb));
  file_unlock($r);
}
function
file_unlock($r)
{
  flock($r, LOCK_UN);
  fclose($r);
}
function
password_file($g)
{
  $o = get_temp_dir() . "/adminer.key";
  if (!$g && !file_exists($o)) return
    false;
  $r = file_open_lock($o);
  if (!$r) return
    false;
  $J = stream_get_contents($r);
  if (!$J) {
    $J = rand_string();
    file_write_unlock($r, $J);
  } else
    file_unlock($r);
  return $J;
}
function
rand_string()
{
  return
    md5(uniqid(mt_rand(), true));
}
function
select_value($X, $_, $m, $dh)
{
  global $b;
  if (is_array($X)) {
    $J = "";
    foreach (
      $X
      as $Cd => $W
    ) $J .= "<tr>" . ($X != array_values($X) ? "<th>" . h($Cd) : "") . "<td>" . select_value($W, $_, $m, $dh);
    return "<table>$J</table>";
  }
  if (!$_) $_ = $b->selectLink($X, $m);
  if ($_ === null) {
    if (is_mail($X)) $_ = "mailto:$X";
    if (is_url($X)) $_ = $X;
  }
  $J = $b->editVal($X, $m);
  if ($J !== null) {
    if (!is_utf8($J)) $J = "\0";
    elseif ($dh != "" && is_shortable($m)) $J = shorten_utf8($J, max(0, +$dh));
    else $J = h($J);
  }
  return $b->selectVal($J, $_, $m, $X);
}
function
is_mail($Yb)
{
  $sa = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]';
  $Mb = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';
  $of = "$sa+(\\.$sa+)*@($Mb?\\.)+$Mb";
  return
    is_string($Yb) && preg_match("(^$of(,\\s*$of)*\$)i", $Yb);
}
function
is_url($Ig)
{
  $Mb = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';
  return
    preg_match("~^(https?)://($Mb?\\.)+$Mb(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i", $Ig);
}
function
is_shortable($m)
{
  return
    preg_match('~char|text|json|lob|geometry|point|linestring|polygon|string|bytea~', $m["type"]);
}
function
count_rows($Q, $Z, $xd, $t)
{
  $H = " FROM " . table($Q) . ($Z ? " WHERE " . implode(" AND ", $Z) : "");
  return ($xd && (JUSH == "sql" || count($t) == 1) ? "SELECT COUNT(DISTINCT " . implode(", ", $t) . ")$H" : "SELECT COUNT(*)" . ($xd ? " FROM (SELECT 1$H GROUP BY " . implode(", ", $t) . ") x" : $H));
}
function
slow_query($H)
{
  global $b, $T, $k;
  $i = $b->database();
  $fh = $b->queryTimeout();
  $vg = $k->slowQuery($H, $fh);
  $f = null;
  if (!$vg && support("kill") && is_object($f = connect($b->credentials())) && ($i == "" || $f->select_db($i))) {
    $Ed = $f->result(connection_id());
    echo
    script("var timeout = setTimeout(function () { ajax('" . js_escape(ME) . "script=kill', function () {}, 'kill=$Ed&token=$T'); }, 1000 * $fh);");
  }
  ob_flush();
  flush();
  $J = @get_key_vals(($vg ?: $H), $f, false);
  if ($f) {
    echo
    script("clearTimeout(timeout);");
    ob_flush();
    flush();
  }
  return $J;
}
function
get_token()
{
  $Kf = rand(1, 1e6);
  return ($Kf ^ $_SESSION["token"]) . ":$Kf";
}
function
verify_token()
{
  list($T, $Kf) = explode(":", $_POST["token"]);
  return ($Kf ^ $_SESSION["token"]) == $T;
}
function
lzw_decompress($Ca)
{
  $Ib = 256;
  $Da = 8;
  $Va = array();
  $Vf = 0;
  $Wf = 0;
  for ($u = 0; $u < strlen($Ca); $u++) {
    $Vf = ($Vf << 8) + ord($Ca[$u]);
    $Wf += 8;
    if ($Wf >= $Da) {
      $Wf -= $Da;
      $Va[] = $Vf >> $Wf;
      $Vf &= (1 << $Wf) - 1;
      $Ib++;
      if ($Ib >> $Da) $Da++;
    }
  }
  $Hb = range("\0", "\xFF");
  $J = "";
  foreach (
    $Va
    as $u => $Ua
  ) {
    $Xb = $Hb[$Ua];
    if (!isset($Xb)) $Xb = $ai . $ai[0];
    $J .= $Xb;
    if ($u) $Hb[] = $ai . $Xb[0];
    $ai = $Xb;
  }
  return $J;
}
function
script($yg, $ph = "\n")
{
  return "<script" . nonce() . ">$yg</script>$ph";
}
function
script_src($Hh)
{
  return "<script src='" . h($Hh) . "'" . nonce() . "></script>\n";
}
function
nonce()
{
  return ' nonce="' . get_nonce() . '"';
}
function
target_blank()
{
  return ' target="_blank" rel="noreferrer noopener"';
}
function
h($Ig)
{
  return
    str_replace("\0", "&#0;", htmlspecialchars($Ig, ENT_QUOTES, 'utf-8'));
}
function
nl_br($Ig)
{
  return
    str_replace("\n", "<br>", $Ig);
}
function
checkbox($D, $Y, $Pa, $Gd = "", $Ke = "", $Ta = "", $Hd = "")
{
  $J = "<input type='checkbox' name='$D' value='" . h($Y) . "'" . ($Pa ? " checked" : "") . ($Hd ? " aria-labelledby='$Hd'" : "") . ">" . ($Ke ? script("qsl('input').onclick = function () { $Ke };", "") : "");
  return ($Gd != "" || $Ta ? "<label" . ($Ta ? " class='$Ta'" : "") . ">$J" . h($Gd) . "</label>" : $J);
}
function
optionlist($Oe, $ig = null, $Lh = false)
{
  $J = "";
  foreach (
    $Oe
    as $Cd => $W
  ) {
    $Pe = array($Cd => $W);
    if (is_array($W)) {
      $J .= '<optgroup label="' . h($Cd) . '">';
      $Pe = $W;
    }
    foreach (
      $Pe
      as $y => $X
    ) $J .= '<option' . ($Lh || is_string($y) ? ' value="' . h($y) . '"' : '') . ($ig !== null && ($Lh || is_string($y) ? (string)$y : $X) === $ig ? ' selected' : '') . '>' . h($X);
    if (is_array($W)) $J .= '</optgroup>';
  }
  return $J;
}
function
html_select($D, $Oe, $Y = "", $Je = "", $Hd = "")
{
  return "<select name='" . h($D) . "'" . ($Hd ? " aria-labelledby='$Hd'" : "") . ">" . optionlist($Oe, $Y) . "</select>" . ($Je ? script("qsl('select').onchange = function () { $Je };", "") : "");
}
function
html_radios($D, $Oe, $Y = "")
{
  $J = "";
  foreach (
    $Oe
    as $y => $X
  ) $J .= "<label><input type='radio' name='" . h($D) . "' value='" . h($y) . "'" . ($y == $Y ? " checked" : "") . ">" . h($X) . "</label>";
  return $J;
}
function
confirm($C = "", $jg = "qsl('input')")
{
  return
    script("$jg.onclick = function () { return confirm('" . ($C ? js_escape($C) : 'Are you sure?') . "'); };", "");
}
function
print_fieldset($v, $Od, $Uh = false)
{
  echo "<fieldset><legend>", "<a href='#fieldset-$v'>$Od</a>", script("qsl('a').onclick = partial(toggle, 'fieldset-$v');", ""), "</legend>", "<div id='fieldset-$v'" . ($Uh ? "" : " class='hidden'") . ">\n";
}
function
bold($Fa, $Ta = "")
{
  return ($Fa ? " class='active $Ta'" : ($Ta ? " class='$Ta'" : ""));
}
function
js_escape($Ig)
{
  return
    addcslashes($Ig, "\r\n'\\/");
}
function
pagination($E, $sb)
{
  return " " . ($E == $sb ? $E + 1 : '<a href="' . h(remove_from_uri("page") . ($E ? "&page=$E" . ($_GET["next"] ? "&next=" . urlencode($_GET["next"]) : "") : "")) . '">' . ($E + 1) . "</a>");
}
function
hidden_fields($Ef, $kd = array(), $yf = '')
{
  $J = false;
  foreach (
    $Ef
    as $y => $X
  ) {
    if (!in_array($y, $kd)) {
      if (is_array($X)) hidden_fields($X, array(), $y);
      else {
        $J = true;
        echo '<input type="hidden" name="' . h($yf ? $yf . "[$y]" : $y) . '" value="' . h($X) . '">';
      }
    }
  }
  return $J;
}
function
hidden_fields_get()
{
  echo (sid() ? '<input type="hidden" name="' . session_name() . '" value="' . h(session_id()) . '">' : ''), (SERVER !== null ? '<input type="hidden" name="' . DRIVER . '" value="' . h(SERVER) . '">' : ""), '<input type="hidden" name="username" value="' . h($_GET["username"]) . '">';
}
function
enum_input($U, $ta, $m, $Y, $bc = null)
{
  global $b;
  preg_match_all("~'((?:[^']|'')*)'~", $m["length"], $Yd);
  $J = ($bc !== null ? "<label><input type='$U'$ta value='$bc'" . ((is_array($Y) ? in_array($bc, $Y) : $Y === $bc) ? " checked" : "") . "><i>" . 'empty' . "</i></label>" : "");
  foreach ($Yd[1] as $u => $X) {
    $X = stripcslashes(str_replace("''", "'", $X));
    $Pa = (is_array($Y) ? in_array($X, $Y) : $Y === $X);
    $J .= " <label><input type='$U'$ta value='" . h($X) . "'" . ($Pa ? ' checked' : '') . '>' . h($b->editVal($X, $m)) . '</label>';
  }
  return $J;
}
function
input($m, $Y, $s, $xa = false)
{
  global $k, $b;
  $D = h(bracket_escape($m["field"]));
  echo "<td class='function'>";
  if (is_array($Y) && !$s) {
    $Y = json_encode($Y, 128);
    $s = "json";
  }
  $Uf = (JUSH == "mssql" && $m["auto_increment"]);
  if ($Uf && !$_POST["save"]) $s = null;
  $Oc = (isset($_GET["select"]) || $Uf ? array("orig" => 'original') : array()) + $b->editFunctions($m);
  $Jb = stripos($m["default"], "GENERATED ALWAYS AS ") === 0 ? " disabled=''" : "";
  $ta = " name='fields[$D]'$Jb" . ($xa ? " autofocus" : "");
  $hc = $k->enumLength($m);
  if ($hc) {
    $m["type"] = "enum";
    $m["length"] = $hc;
  }
  echo $k->unconvertFunction($m) . " ";
  if ($m["type"] == "enum") echo
  h($Oc[""]) . "<td>" . $b->editInput($_GET["edit"], $m, $ta, $Y);
  else {
    $Zc = (in_array($s, $Oc) || isset($Oc[$s]));
    echo (count($Oc) > 1 ? "<select name='function[$D]'$Jb>" . optionlist($Oc, $s === null || $Zc ? $s : "") . "</select>" . on_help("getTarget(event).value.replace(/^SQL\$/, '')", 1) . script("qsl('select').onchange = functionChange;", "") : h(reset($Oc))) . '<td>';
    $rd = $b->editInput($_GET["edit"], $m, $ta, $Y);
    if ($rd != "") echo $rd;
    elseif (preg_match('~bool~', $m["type"])) echo "<input type='hidden'$ta value='0'>" . "<input type='checkbox'" . (preg_match('~^(1|t|true|y|yes|on)$~i', $Y) ? " checked='checked'" : "") . "$ta value='1'>";
    elseif ($m["type"] == "set") {
      preg_match_all("~'((?:[^']|'')*)'~", $m["length"], $Yd);
      foreach ($Yd[1] as $u => $X) {
        $X = stripcslashes(str_replace("''", "'", $X));
        $Pa = in_array($X, explode(",", $Y), true);
        echo " <label><input type='checkbox' name='fields[$D][$u]' value='" . h($X) . "'" . ($Pa ? ' checked' : '') . ">" . h($b->editVal($X, $m)) . '</label>';
      }
    } elseif (preg_match('~blob|bytea|raw|file~', $m["type"]) && ini_bool("file_uploads")) echo "<input type='file' name='fields-$D'>";
    elseif (($ch = preg_match('~text|lob|memo~i', $m["type"])) || preg_match("~\n~", $Y)) {
      if ($ch && JUSH != "sqlite") $ta .= " cols='50' rows='12'";
      else {
        $L = min(12, substr_count($Y, "\n") + 1);
        $ta .= " cols='30' rows='$L'" . ($L == 1 ? " style='height: 1.2em;'" : "");
      }
      echo "<textarea$ta>" . h($Y) . '</textarea>';
    } elseif ($s == "json" || preg_match('~^jsonb?$~', $m["type"])) echo "<textarea$ta cols='50' rows='12' class='jush-js'>" . h($Y) . '</textarea>';
    else {
      $yh = $k->types();
      $fe = (!preg_match('~int~', $m["type"]) && preg_match('~^(\d+)(,(\d+))?$~', $m["length"], $B) ? ((preg_match("~binary~", $m["type"]) ? 2 : 1) * $B[1] + ($B[3] ? 1 : 0) + ($B[2] && !$m["unsigned"] ? 1 : 0)) : ($yh[$m["type"]] ? $yh[$m["type"]] + ($m["unsigned"] ? 0 : 1) : 0));
      if (JUSH == 'sql' && min_version(5.6) && preg_match('~time~', $m["type"])) $fe += 7;
      echo "<input" . ((!$Zc || $s === "") && preg_match('~(?<!o)int(?!er)~', $m["type"]) && !preg_match('~\[\]~', $m["full_type"]) ? " type='number'" : "") . " value='" . h($Y) . "'" . ($fe ? " data-maxlength='$fe'" : "") . (preg_match('~char|binary~', $m["type"]) && $fe > 20 ? " size='40'" : "") . "$ta>";
    }
    echo $b->editHint($_GET["edit"], $m, $Y);
    $Dc = 0;
    foreach (
      $Oc
      as $y => $X
    ) {
      if ($y === "" || !$X) break;
      $Dc++;
    }
    if ($Dc) echo
    script("mixin(qsl('td'), {onchange: partial(skipOriginal, $Dc), oninput: function () { this.onchange(); }});");
  }
}
function
process_input($m)
{
  global $b, $k;
  if (stripos($m["default"], "GENERATED ALWAYS AS ") === 0) return
    null;
  $jd = bracket_escape($m["field"]);
  $s = $_POST["function"][$jd];
  $Y = $_POST["fields"][$jd];
  if ($m["type"] == "enum" || $k->enumLength($m)) {
    if ($Y == -1) return
      false;
    if ($Y == "") return "NULL";
  }
  if ($m["auto_increment"] && $Y == "") return
    null;
  if ($s == "orig") return (preg_match('~^CURRENT_TIMESTAMP~i', $m["on_update"]) ? idf_escape($m["field"]) : false);
  if ($s == "NULL") return "NULL";
  if ($m["type"] == "set") $Y = implode(",", (array)$Y);
  if ($s == "json") {
    $s = "";
    $Y = json_decode($Y, true);
    if (!is_array($Y)) return
      false;
    return $Y;
  }
  if (preg_match('~blob|bytea|raw|file~', $m["type"]) && ini_bool("file_uploads")) {
    $Bc = get_file("fields-$jd");
    if (!is_string($Bc)) return
      false;
    return $k->quoteBinary($Bc);
  }
  return $b->processInput($m, $Y, $s);
}
function
search_tables()
{
  global $b, $e;
  $_GET["where"][0]["val"] = $_POST["query"];
  $lg = "<ul>\n";
  foreach (table_status('', true) as $Q => $R) {
    $D = $b->tableName($R);
    if (isset($R["Engine"]) && $D != "" && (!$_POST["tables"] || in_array($Q, $_POST["tables"]))) {
      $I = $e->query("SELECT" . limit("1 FROM " . table($Q), " WHERE " . implode(" AND ", $b->selectSearchProcess(fields($Q), array())), 1));
      if (!$I || $I->fetch_row()) {
        $Af = "<a href='" . h(ME . "select=" . urlencode($Q) . "&where[0][op]=" . urlencode($_GET["where"][0]["op"]) . "&where[0][val]=" . urlencode($_GET["where"][0]["val"])) . "'>$D</a>";
        echo "$lg<li>" . ($I ? $Af : "<p class='error'>$Af: " . error()) . "\n";
        $lg = "";
      }
    }
  }
  echo ($lg ? "<p class='message'>" . 'No tables.' : "</ul>") . "\n";
}
function
on_help($bb, $tg = 0)
{
  return
    script("mixin(qsl('select, input'), {onmouseover: function (event) { helpMouseover.call(this, event, $bb, $tg) }, onmouseout: helpMouseout});", "");
}
function
edit_form($Q, $n, $K, $Fh)
{
  global $b, $T, $l;
  $Rg = $b->tableName(table_status1($Q, true));
  page_header(($Fh ? 'Edit' : 'Insert'), $l, array("select" => array($Q, $Rg)), $Rg);
  $b->editRowPrint($Q, $n, $K, $Fh);
  if ($K === false) {
    echo "<p class='error'>" . 'No rows.' . "\n";
    return;
  }
  echo "<form action='' method='post' enctype='multipart/form-data' id='form'>\n";
  if (!$n) echo "<p class='error'>" . 'You have no privileges to update this table.' . "\n";
  else {
    echo "<table class='layout'>" . script("qsl('table').onkeydown = editingKeydown;");
    $xa = !$_POST;
    foreach (
      $n
      as $D => $m
    ) {
      echo "<tr><th>" . $b->fieldName($m);
      $j = $_GET["set"][bracket_escape($D)];
      if ($j === null) {
        $j = $m["default"];
        if ($m["type"] == "bit" && preg_match("~^b'([01]*)'\$~", $j, $Sf)) $j = $Sf[1];
        if (JUSH == "sql" && preg_match('~binary~', $m["type"])) $j = bin2hex($j);
      }
      $Y = ($K !== null ? ($K[$D] != "" && JUSH == "sql" && preg_match("~enum|set~", $m["type"]) && is_array($K[$D]) ? implode(",", $K[$D]) : (is_bool($K[$D]) ? +$K[$D] : $K[$D])) : (!$Fh && $m["auto_increment"] ? "" : (isset($_GET["select"]) ? false : $j)));
      if (!$_POST["save"] && is_string($Y)) $Y = $b->editVal($Y, $m);
      $s = ($_POST["save"] ? (string)$_POST["function"][$D] : ($Fh && preg_match('~^CURRENT_TIMESTAMP~i', $m["on_update"]) ? "now" : ($Y === false ? null : ($Y !== null ? '' : 'NULL'))));
      if (!$_POST && !$Fh && $Y == $m["default"] && preg_match('~^[\w.]+\(~', $Y)) $s = "SQL";
      if (preg_match("~time~", $m["type"]) && preg_match('~^CURRENT_TIMESTAMP~i', $Y)) {
        $Y = "";
        $s = "now";
      }
      if ($m["type"] == "uuid" && $Y == "uuid()") {
        $Y = "";
        $s = "uuid";
      }
      if ($xa !== false) $xa = ($m["auto_increment"] || $s == "now" || $s == "uuid" ? null : true);
      input($m, $Y, $s, $xa);
      if ($xa) $xa = false;
      echo "\n";
    }
    if (!support("table")) echo "<tr>" . "<th><input name='field_keys[]'>" . script("qsl('input').oninput = fieldChange;") . "<td class='function'>" . html_select("field_funs[]", $b->editFunctions(array("null" => isset($_GET["select"])))) . "<td><input name='field_vals[]'>" . "\n";
    echo "</table>\n";
  }
  echo "<p>\n";
  if ($n) {
    echo "<input type='submit' value='" . 'Save' . "'>\n";
    if (!isset($_GET["select"])) echo "<input type='submit' name='insert' value='" . ($Fh ? 'Save and continue edit' : 'Save and insert next') . "' title='Ctrl+Shift+Enter'>\n", ($Fh ? script("qsl('input').onclick = function () { return !ajaxForm(this.form, '" . 'Saving' . "…', this); };") : "");
  }
  echo ($Fh ? "<input type='submit' name='delete' value='" . 'Delete' . "'>" . confirm() . "\n" : "");
  if (isset($_GET["select"])) hidden_fields(array("check" => (array)$_POST["check"], "clone" => $_POST["clone"], "all" => $_POST["all"]));
  echo '<input type="hidden" name="referer" value="', h(isset($_POST["referer"]) ? $_POST["referer"] : $_SERVER["HTTP_REFERER"]), '">
<input type="hidden" name="save" value="1">
<input type="hidden" name="token" value="', $T, '">
</form>
';
}
if (isset($_GET["file"])) {
  if (substr($ga, -4) != '-dev') {
    if ($_SERVER["HTTP_IF_MODIFIED_SINCE"]) {
      header("HTTP/1.1 304 Not Modified");
      exit;
    }
    header("Expires: " . gmdate("D, d M Y H:i:s", time() + 365 * 24 * 60 * 60) . " GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: immutable");
  }
  if ($_GET["file"] == "favicon.ico") {
    header("Content-Type: image/x-icon");
    echo
    lzw_decompress("\0\0\0` \0�\0\n @\0�C��\"\0`E�Q����?�tvM'�Jd�d\\�b0\0�\"��fӈ��s5����A�XPaJ�0���8�#R�T��z`�#.��c�X��Ȁ?�-\0�Im?�.�M��\0ȯ(̉��/(%�\0");
  } elseif ($_GET["file"] == "default.css") {
    header("Content-Type: text/css; charset=utf-8");
    echo
    lzw_decompress("b7�'���o9�c`��a1���#y��d��C�1��tFQx�\\2�\n�S���n0�'#I��,\$M�c)��c����1i�Xi3ͦ���n)T�i��d:FcI�[��c��	��Fé�vt2�+�C,�a�G�F����:;Nu�)����Ǜ!�tl���F�|��,�`pw�S-����������oQk�� n�E��O+,=�4�mM���Ƌ�GS��Zh�6��. uO�M�C@����M'�(�b5�ҩ��H�a2)�qиpe6�?t#Z-���ox�<���s���;��H�4\$�䥍�ۚ��a�4�\"�(�!C,D�N��;����Jj����@�@�!����K�����6��jX�\r����@ 2@�b��(Z�Apl��8��h�.�=*H�4q3�AЂ�.��K���!�f�qr�!�1�Ȏ�c���*+ �(�\n�2�j���(dYA���D�t�ϑ�m*H�9+�0�0�\n0t���J�,E�ER� X��u[&@0�A��7=�;����K��;0�D�7Ajm*`�3:v`��ūk��Ʊ.�x�Xv(ec�������Emz\\�0C�G2��Jt2(à��c�N<��s^�2��6Z̅�?c��X�m���ϥ(�d9?>�/�Y^I�%����5=H==\0T �6���\"��ح�\r���mH��-C��z��\n����Q�j<<Z��6��v>����~LHť��p�,�Yp��P�9{}�g߻5���Y�	>g5�@8o�\n>��-nҩ��ԐR)J��#j<7�(��ĸN%�}��*2��\rA�јF0�Xވ�Cp�:��Z�Ƭ��:G�ԏ�v=�j�[C\r��z/��:@���B��<�(z.�P���Y�h7\"�j�/܉��]�\\���6`�Ҋ4=x�^1��\0C0����q�!�4�%l�SP[K}�v�#��@g�����\r�h=�����RGa���TTRNv��:�\"���u���\\e�4	U\rt�W�����Z �CF,@����1\r�\r�0��p!�OY4:����!�����hV��\n�`	�pn�Ϛ\0Y+�20Z�anYq5�p��\"a|��p��n#�U\0�8H���bIp(��s��Yt���HI&�zN0ؖ@���tAf��)��ƙZFՂlKr�/C`�C�B�1*�&��th���WI�0C�\\*)mJ���[âdi3K\0�)�dϘJ261��\n-o�٫��0ߤ���X�b\r�qp��ԆOY0X��֟�C`�S�a��Z�[�t#�x��@2\r��Z̲�� �b-��]C\\��l� \"5,���R����j�t�K�P`��,���s�\r�1xО@���K\"d�'��F.6,T	�[�M�֦��X������������Bx�2�j�иɃC��(�Mm�d�\"��W�t����8�sDY��Z �\"�VS暙�D2��+%�?�VD�#�����9Gr���S�b�e�,\r�S���k�	����(��]a��\r��1�P䀛iɓu\$���	��sbA��>T�T�n�e7�n�R<xf��'��C��5���^���)�\n������`-\"g���Qy�\r�ת�`BN\nAd�#@��)\$�R}�@����yte&r��@����+���[�T�\nX��셜��vAG��U.�]j�`��r*���x����X��Qp-��B��Cy�r���<�.\0#��:�K��)l�NH���x��WP���������8���<��s��6��Ȯʅwʒ���ypc�E��J��w�Q�A�+�4/�02d��mÐf ��\$��ţ4vvy�`I�\$�d�5U\0�Pj(%�K���47�La��^��zM\0�x�m���х���RTy����!���w�m�j[F��{�ca�����m�V� <��s,�������9T�]gu���H�/���V+y�jM���ݨM�G���;�%���mW\r�ђ8��0�ϴ6�K�����W�{�k�]Z�]ÈO*(`#t������A�6�{��%Oaq�H���Er�UC(b\ret�%I��X�\\�0��X�YQ	��(T-���2H���r�����E�qd�w��AKY�W ܹ��<��1�Gp��z��\$�h6X��*f�\"<:��;V���6����;@���@�w��r���K�ѽ�@o��<�\0}v�����M���=���<�GD�wᷴY���pT�s�?~��=��=��Fn?x�������=�w���_(7����R'��Y�| \\y�z�n���}��=%p�0j?���B!���\nT��o��l����k���GHu`4J�/G�]@�	~=N�&�H%��jK|\0pj�8����Np��xׯ��K�5+��*��L��b��\$kI\rv�휎����P��z�~Q0��o�=�\r�i��Y�կ���������Д�\nm�ް�p��`�p:&�&��۰�����H�p��	-��P�\n�p��U\n`�I6��&d�i<�)x�h�s����4�pJ��q4@��7\0Z\0�HJ�g˥�F�����5���N*��!�r�¥@������a®]�`EH�cG@�&���fF��Zː�H�v	��4/\0�&�ąqH.�H�?b-��a7@Ƙ1�i����S�曑�qQKb�E\0�RŃ�8�Q �4Z���Q.8�����\n\r��!�(?�>s�K#gQ\$r0/�P8��\"rW��#�Ds�1%�U&Ì��'2@�y%�P�kc'Re&2Z6\0�)Rꒋ�\"%&Q:�O��R��\"ҍ%�\r�c,2���'�a)�f��&%Q,R� ��-�c �	.hI.���.2�-�\r*\"�*qlX����I%��!����.�R+1o)���!-S0)��\r�6��`��R��2�Y)��Y,�3�552�)� �SD�IDB�/4�f~��D��0R#1�1�8�\"��4��28�2�?S��R�,Ӟ��5RI:��(ө��;s� �1��b�0��1���2ss9`��:��<21:��:s����;�@3�;S�<bm&3�?3�8�92s�9ҳ(�>3�>e�b\0�3�3A��Q�%�>q�Ct;.c�?�;ӡ<�B\0踓�Bq���>T];�<T/4w3s�8ѡK;GR~����t�\$�y �&4�T8�{#�ns���AI��D��'�Dg��JTF��5r� ��&S�.�&t�62g@sM�0��5��O4�Ob�O�QH�����P��7UQKP��P�\0�ژ5#N��3U/Qu+O+&cPe�RU7SUGT�51t\"-4'+r+��9E�3Uk(��&2�S��W3;.��K�#��/(ѵA@؞Tk=Ѵ��X�@�L�E��8��(�\0 ���&�S�M�u'��u�W�\\�RC&5y]�U*W]�00`��;Z�KK ��9R[S�CN5�SV	Q�R,�s_Ĺ6�C6@6';hIb�1,61aV/^�cV=N\0U����(�6R~���Il\\3�e��00�\ra�BD�d��:\"�4���,");
  } elseif ($_GET["file"] == "dark.css") {
    header("Content-Type: text/css; charset=utf-8");
    echo
    lzw_decompress("b7�'���o9�c����b�F��r7�M�HP�`2\r\"'�\r�\rF#s1�p;��Ɠ���e2I���Y.�GF�I��:4��S���3��㔚Y�u(Ìc(�`h��#%0[��L�����h���C!���E����b5�Ú�������y�fb��w	�z#���1�P��6����Xt4a�l�t�4��g�E��B�#��ja�� �K��q8ڝh�]�����a�2ƼP ��y����i2��3)�U���o�l\0�}��vٛ�r��7ϸ� N2�)�3M�#��)PKj��x����8C(\\5��S\n?��v޷邊8��\\������ �[8�x�#��G���!a�^>�qh]�#����L\\6�#��2��<&7����G��1Hr�`*���7*��#��@-��6D��:�;S:��*�l�<�!ʹ�����1��\r.-��5+?T\0@1?n�\n�\$	�6����0��,ڎ�A�lK��=P�0ʉ1\neQ�%\$�2	eGR��K�0�R�V���{.�Ĩ�>��s��0�,b0��͹3��\noe����r��t�e���j1��8Ģ���+��� /[�Aâ758�4�A@d74�*�0,�8�c����+��i�b��ˌY#.7���GhV��`�7���!����cT�d��y��\n�?���F3�g�ƃ	eu���։�h�:+�<6�G�f��\\4���X�>*�`0LϰMzZ�B#{*x27�eo}��xޛ���:\r��C:&`f���MpձCL�T����1����H���`�G��<_�pd��Ajr[����@2\r#�h�V�D>�u%��3���b:h���kA�1\\ɀG����������C��0�Zs���a����;S��L�0L'��Yk��]��i\r)�v ����m*'7Ʋ�[�>@������Hpzf)��`l����`�׫h�w���f�|��7!�i⥀�Ƞtw���\0p��'��sҿ�b`M���gx\")�=I�\$��C��w��ǩPrl��!5 ��p�hc�@�2 ����^���48(��\$dl���A���Q�,���	E�A�:C���P��E\"�!�F��h3\$h�C�a\r��2ȹ'�PmPI��>���bE��Ƹ�΁ln���@Ʃ��~kD���?�9���Q�q\r�8LC,L\0");
  } elseif ($_GET["file"] == "functions.js") {
    header("Content-Type: text/javascript; charset=utf-8");
    echo
    lzw_decompress("f:��gCI��\n8��3)��7���81��x:\nOg#)��r7\n\"��`�|2�gSi�H)N�S��\r��\"0��@�)�`(\$s6O!��V/=��' T4�=��iS��6IO�G#�X�VC��s��Z1.�hp8,�[�H�~Cz���2�l�c3���s���I�b�4\n�F8T��I���U*fz��r0�E����y���f�Y.:��I��(�c��΋!�_l��^�^(��N{S��)r�q�Y��l٦3�3�\n�+G���y���i���xV3w�uh�^r����a۔���c��\r���(.��Ch�<\r)�ѣ�`�7���43'm5���\n�P�:2�P����q ���C�}ī�����38�B�0�hR��r(�0��b\\0�Hr44��B�!�p�\$�rZZ�2܉.Ƀ(\\�5�|\nC(�\"��P���.��N�RT�Γ��>�HN��8HP�\\�7Jp~���2%��OC�1�.��C8·H��*�j����S(�i!Lr��D�# ȗ�`Bγ�u\\�i�B!x\\�c�m-K��X���38�A���\r�X���cH�7�#R�*/-̋�p�;�B \n�3!���z^�pΎ�m�R���t�m�I-\r���\0H��@k,�4����{�.��J�Ȭ�o�Vӷb?[�Q#>=ۖ�~�#\$%wB�>9d�0zW�wJ�D���2�9y��*��z,�NjIh\\9���N4���9�Ax^;�^m\n��r\"3���z7��N�\$����w���6�2�H�v9g���2��kG\n�-Ůp��1�C{\n����7��6������2ۭ�;�Y��4q�? �!pd��oW*��rR;�À�f��,�0��0M���0�\"�� ��\"�ħ���oF2:SH�� �/;������٩ri9��=�^�����z�͵W*�Z��dx՛��֡�ITqA�1��z�Y!u������~��.��P�(�p4�3���#hg-�	'�F�p�0���C+P�����, ����e���N~�y@fZK��O3�v\$�`�C�	N`�!�z�pdh\$6EJ�cBD��c8L��P� �66�OH�d	.�����Y#�t�H62���e��@��~]C�[��&=G��\\P(�2(յ����̐q�2�x�nÁJ�|2�)(�(eR���G�Q��Ty\n�!pΪ\0Q]��&�ޜS�^N`�_(\0R	�'\r*q�P����x�9,��-�);��]�/��w������C.e��y\0�,��	S787���5Hlj(�� �\0�մ����q�I�/=S�� �àD�<\r!�2+��A�� J�e ��!\r�m��NiD�������^ڈl7��z��gK�6��-ӵ�e��!\rEJ\ni*�\$@�RU0,\$U6 ?�6��:��un�(��k�p!���d`�>�5\n�<�\rp9�|ɹ�^fNg(r���TZU��S�jQ8n���y�d�\r�4:O�w>[͞�4�4G\"��7%������\\��P��hnB�i.0�۬��*j�s��	Ho^�}J2*	�J�W��Gjx�S8�F͊e��6�s���*�\r<�0wi-00o`��^�k����*A�,ɸ�䍺��i���nj��2索A\"����[;��n��B^��0-�����\n:<Ԉe�2���h-���2�n�/A�\r6����[o�-��c��R@U3\n�\n�T��=�R�j���7s\"���Y+�\"u�<fH��`a��z�E�����^7syo:!�V���k�m����if�ۻ�/�ڦ8;<e�N�2ͱS�W?e`�C*B��͔�ZB�]����:K�_7��Ċq��Q�)��/�:d�i����Z^�3��tꃥ��t*��\$��f�z50t�UJg���S\r�cX�����\rw7Z�N^`oxP���I��x?��T�ke�� �Jim)�x;�X�����C�=V=���<U��!�0��n��;���~AZ��7�����+Z�=n���{H��PURY����������4�Hǋ6'g��2K���~��|hT�A��1��V���>/�^��l.��SI�.�9g��~O��%ئ��̾�)A|��\n;-��n��[�t,�����Y�<>j\n��N�eP���O<��� q���(G!~����`_�\r~���`��.�>'H�O�2�yK����d:(�,�<�3�:�����+0nUYZ���^�)ww��!�����1����!����mG��ַgd�=���X�[ޢ�<��ߩW�����7���`�o�ҭ��������G���~`�i`��*@��v������\0)�ꐜ\$R#��������Ud�)KL��M*��@�@��O\0H��\\j�F\r����]�gK��i�\$�D�*g\0�\n��	��s� ��\$K0�&��	`{���6W`�x`�8�DG�*���eHV��8���\nmT�O�#P��@��������.�\r8�Y/&:D�	�Q�&%E�.�]��Я�.\"%&��n�\ny\0�-�RSO�B�0��	�v��D@�݂:��;\nDT��< �Q.\nc2��Ry@�m@���	��W����\n�L\r\0}�V����#����-�jE�Zt\\mFv���F���J�p�B���(����1� ��LX���	%t\nM���D���Z���r��Kg´C�[�ʴ	�� �\0Я�������R*-n�#j�#�����4�IW�\r\",�*�f��x�/���^��5&L��2p�L��7�^`����� V�`bS�v�i(�ev\n��|�RNj/%M�%���+�ƫ����߯�'���R�'''�W(r�(��)2�Қ���%�-%6���ˀ�J@�,��ֿN���Q\n�0ꆐ�g	��\$���*L��.n��Q%m�\"n*h�\0�w�B�O�\0\\FJ�Wg� f\$�C5dK5��5�aC��4H�(��.G���BF��8������ E����.��k3m�*)-*��[g,%��	��7�.��!\n�+ O<ȼ�C�+ϫ%�O=Rf����(���n�Y��ϲ�%��s�1�6�3;��ObE@�NSl#��|�4\0�U�G\"@�_ [7��S��@�\$DG���D�5=���K>r����\r ��Z��ֱ@���H�Ds��n\\�e)����b�'���BPGkx�Z���#TK:�w:�a2+�aeK�KR)\"�(4qGTxi	H�H@�&@%bZ����ܪ)3P�3f `�\r�I6G�%�/4�v�\\~�4�ݤ0�p���,��E�)PH8k\0�i��\$���3I4�P�V'F^� �'D��R���+Q�`�����8\n���D[V5,#�qW@�W�0�O2� �t�\rC6sY_6 �ZkZ@z3ryI�<5���.W���ҷ@5�Ģ#ꎄ5N �~��ȥu�\r����)3S]*g7�����ҕ�_ˉ�_�ĸV\nY�)a��1P���FI\r;u@/!![�e� �(CU�O�aS���KP��t3=5��O[�f:Q,_]o_�<�J*�\rg:_ �\r\"ZC��8XV}V2��3s8e��P�sF�SN~�S5U�5�z�ae	k�n�fOL�JV5��j�����Z��lE&]�1\rĢم5\rG� �uo���8<�U]3�2�%n�ַpr�5��\n\$\"O\rq��r)�f���7/Y���p�I#`��Kk;\"!t���h�usYj�[�R�\n{N5t�#NΜ�o6�X)�c6���e+.!��ߗ�\n	�b��ʒ�t�Ү��\n���j��(\0��2��4erEJ��d����@+x�\"\\@���� %v�����{`�����`��\n �	�oRi-IB�-���Nm\\q@�,`��Kz#��\r�?��՘6��<j��f��!�N�7���:��/�Tł\0�K\\�0�*_8L�m�^r���V�w��\"��кB��Q:5Kn���v\0��xt�;`�[��	�B�9!nv�<ۢSҏ{:P�p�r	~����1i*B.�tY�>\r��S�*nJ涨��7{�=|R]��ռ��4�i�U�2�2���Y3�c>a,X��3���9�\$�<A�Q�&2wӭ3���1��/�i����j��sO�&� �M@�\\���گ���8&I��m�x\0�	j�k�ۛE���^�	���&l��Q��\\\"�c�	��\rBs�ɉ��	���BN`�7�*Co<��	 �\n�ν�hC�9�#˙ �Ue�WX�z0Y�7}�c��8?hm�\$.#��\n`�\n���yD�@R�y���@|�Ǎ���P\0x�K� w�5�E�Le�@O��u���|�R�2��%�aA�cZ��:�<d�kZ��y{9Ȑ@ޕ\"B<R`�	�\n������QW(<��ʎ�革�q�j}`N���\$�[��@�ib����f�V%�(Wj:2�(�z��ś�N`��<� [Bښ:k���ʚ�]��piuC�,�����9���e�j&�Sl�h~��N�s;�;9��u@.<1����|�P�!���zC��	�	���{�`��Q!���5�4e�d�G�hr���P���}�{��FZrV:�����Ŀ�Z����|�P��WZ��:��d��~!�}�X��V)����p4���.\$\0C��V󁺩���{�@�\n`�	��<f��;dc'�\r��,\0t~x�N����y� ˽kEC�FK\"Z�@\\C�e�D.Gf�I�8�ͤ���CĥY��q9T�CU[��z�^*�J�K��VD�؊��&���b̷KK+��Ĳ�,C����,N!��\r3�Y�P�9�\$Z���n�\$S��5�\r��aK��E��n�71Z���3e��J؜x5�Q�.��\n@����ǣp��P��ѡֽn\r�r|*�r��% R��蔊�)��#��=W\0�B���z*�W���MC��_`�����P��T�5ۦWU(\0��\\W��&`��a�j)��V�W�ʧ�b�f�O�rU���Ǽ~#c�Ur�5�`���Gd����P��fW������Yj`��ǌ\n��G�>K�h���ǿ��[Mf�g̗�|�\"@s\r ���Ӷ��iU��m��~��f�K�.x�t���X�P�����׬����-���!û�~��+Rw�*©��ܞ�K��\\�-F/bN�s����Ru���i8r�\$\"�8j�Rn��5gf�@�FSM�S�c��5C��*y�C���cU�@o��esI�H9QoCQ�������=c������{�c8S�v!��;g�L�5<	�#�z#���qL�������V\r�2\$�J/{z���m��i�nG�?~ĕVu�0wʹ�=p�I��HĀX=�����t��� -��MJTP�#U��`��/3\\?�L�����y���*p�8���:��0{�k���2�&�P\0p8����Y�\\'�%�����.\r��,ƁJ�����/_,�4�~��,�!�Rn%x@��0Fdt\0�4����\nK�\n��G\$���Y	 �\0@,)�%:\r�]�L2\0�PV C\\Ѧ,B\r0W�\0��Rr<�UH���Q�Al��'�\0 �T�)�(c�\\I��;��/�ik��jV^�p��-PP��)���Hx������	Np&��\0d��8�':#Q\$�Q=�\0Wn�k��,�aS�����qbj\\�<g�9&�e�1����eb:��N|#������φ� ��n���h�wJ<8p�.���9y����A41auf���4�u\nL��%��/�:\r5�%H�jA^����s�\n���|�x��X�� �&�f���얐��@hESЕ^@:8@\r�n��^��H\"�&\rC�bq!�:&�AC�Jj�&	�&\r����N��<	�p�4��Tiw��-���2)ȫ�5O�B�3���#\$� ��e�VUB���v�d���xS��7e!�DF�O`����f.Q0D�#�\\��%��J|�\"�N\0�l`E�W���\nt��UR�(��L���gCcRy��T챞:j���W���E�.Y�\"*�25��X\\)d�\"lo'zJ��DJYX��\"#�����	G���>�)��Y�&2�,'ڄ�M����6�h���Z�N2ȷ@).��#�B��n;��Ga��R+�O!|�Ê��\r�ܓ:�������v[qD¨W��r���c��G�\\\0'WY�ؤ{�ׄ~2n\\�\$<�XQ2Dw�DDxCfH�,~厏�T\n(+A\"�.B�`���ǘ��L[E����	�\"Y\r�C�֐��`,z�XȔ?o��E�K\"��}&@`����i<���Uڹ\$OS1�Y�j�^8\n��8X����_	� z��'ȥQ�s�+c���X��L-ß���\rjD�5}Q���C�L&.=�P0>H�8H�����G��K2�0��B��P7�Q�%iG��h�^�&5�5�Q\"�9ъ)�P�BS�\n3&�ʐ����\rJ!HJ4\n.{�W�\"#Z�\0R��\r9+2���DE+ie2�Y�i�y���&���%��p�������K(��srp�%��`/65�b2T<���a�#�]��-Ի��.!�K������maJ�[KT�\0l�PY�'4�&�膗~���1t��4��2j�'\0��V(��\n*+W���ci4cʭӞ<���X�/�~�ɢ��L�&��2�b23RҐFT���R�%b<UX��������V \"Z�� p��\"�[�@m��A1c���k˔ �p���-�|��l�f4��ю\0�7��]�OI��@�����3r�\\Dd9*��\r3>s���V}���U,�y0���g�\0��\"&�����\0���P�BHCrh����i_�� ���`-pЅ�6J��/��1j�.����kYÎ9�(} r���P����\\�gu@��\0w�-�0�'�<�Ώ�\r�-\r�˖9��r+���Iޙ�+�&�����-=�|��yeж(	\r�H�z���>��N{�����0V��-�!�t���;ກ|\r��@R�\n\0�Y\"����\0��}�s\r\r�A�V�� }�d�H'8 0����9�1���8�\n؍@	P�&:\n�F�\0d�\0��5����3r�\rD�C�1���3���8��	�k�N='�70�QP%S�\\��:B�pzo�D���6�B�H�R�(�4��͐A1����Iv�q]��joD\r#)�#%c�ɱ%��%��_'B�O )x�c�a�=�/���6H�j��>,r��o)G���u)��#�&�#Is	I��~��_��O��J~��՞Yb%*?\$yP0��(	��%㠋�c�<�0	�kPt�B�����3\"E�X�2q�y�-:�@�ʀ���.!�qDW�0���* �(�Z�+/d��_g=���(`f��P���i���b�1x���b�>�pdd��T��E<[e)����y(}E�v|�]���OCQ�r�H��\0�A��W���J`V3�@B�I\rm�I�uISr�Ґx	 ���r�I��HJ�S��UB�\$�@ت�\"<�pe|�1́DD�*��DZ�e�T���5%G�O�\0��0I�(4D��m���פ	V�,{+���P�����A���U�]U�e��s�I+@���\$CMM]��->a�@Z�М�5����3*��v֪���yU�Cebj�ӈ�\r�HN�6�iZ�>V)7u�@��Z�H־�D|��5�Б�J�M�A)SЕ�,�i���f�l��PS:E�M���삩52p:�{�i�i�_�j���p6�.�>H(n\$�1i�IK��֪�3�V\r�]^ĳ��ꮧ\n�)0I��`B@�b�j6>h�F�g�/ y2��A3YG�� ���z�4�����k=��R�Z����AYΪaW���*�	�(5���!O���2ss'xg�x\0��\"��\n@Ek�\0�Rֳ��%Ց��'�B*f�Bnf�Sfצ5��+ʶ���#Bݯ��%RX�¶g�@R4�`\$i�e��;���	%�ʸ�(�|�ȇ\0��������]:�gԵ>��m~��\"+)��?�]������C0��\$S�<����Ѕ�+\0�֭�3��r��;H��iR�>�h�vg��%�����Y����RhT�%�N��l�c���d+a��E?<}�Tfŉ욢�\n\nJ�UG	�sv����kP���u�!s'\$��;�0�E�@������&Lo\$ mUM&��\"�����f����w匄\0�\"��1��M�;���Y�`W�B��\0T�f)X�tT�x�\0000�V�,��!s0��G\"eQڏT4��\$�[�E/eO����<���-��H-0,�d?R�,r�@gG��<���`��sqe��ۢ�m��H��L.�]��HB��\"��\0X��B��7b�s�\n��L�7�Z�Z�\$�����\rפ5���w�Ҟg�/]��h��;b�7���M]4	�ҫ���dFD�1�z�ca��o�Պ\0r�;��P� 8U�!��5�c��#]����R�@ƅ{�'�i@:���ʴ�ɧn�kX�©ֱ������8\n���s|�A\rQ�I�b��M'��PU���2�8�#�;�KO_}��#��wi=��#fc���������֞�ݞ��z�����/&�gw�m�X�	`�d��[�i�`m�X��b},�|�+�����ts�+�iiԇ�t�\n�>8()��6f2���#d��˴IH(\0.��#v/9j���!��2КE/:H/��A�ybj��\r��zV,p�w�E}^``��?X�-�\nz�*�UD~?���\\Hc�U�WXz\\0�!�rJ�A�o`�����Ba��!�C�!��W(�լX�P�.���È`�@ov���f� in�@U���U�<p�GN�U�𩆙c��8�7�O�-��������ABXr�vų,�H�؍58��ո?�cb�Cʸ�5q����&>!�4 �ͅ(q��	I��-Jm\0>�5I�D�m�B�mD�\0��`2��yN��D��4N�L`�����0x�_~1\07����k����h�RO�`j_�ŭ���� ���m{�p0��!9'�K-�\$�����\nj~�h���Q��N��\0��q{��x�\"��2�/q��iT�F/��*V@*�g�-��� b�c0	H�0\0]�@�H�U�:U,V�Gd�?��u�L}L�9��u�:\"\$�Tl���e��ɐ;��V�\nL�!+�ǆT�1�j,�}R\r-[�2�6U�i\0d��/Ҏ���@r�.��PF	��g���<����S��&i\r\$�R72�>fs#��3��7UNȵ\"ϱH��+�9�[8�	B� 	�A��!3�_�Z�5��3��%�r��W9y���\n3K�|��o5gh;���d��\r��	D��3R��g��L\\��v	IGB�_�8`���<�a�?�s�q��䘬�b�2�N�(��u��`L���Ӧ!U�>��\r�e�~_����!�S�t1'=\r�C���Q�r���\rC��*᠗�f�3`{� G����|U\$n�J���3H��;�R5ؖ}�Qw9�B���=k0��F��Ǻ\$1sb���-L3�C\0w!ʹ�P�&[�#0�طPS�\"����%r�{ZA���]�DE%�)��T��{@s����u�e��R��Ԉ�53����#�>��<�\"�A:�t\"�z��KH7�8}�k������3�'�N^���Vh\r�Pj;֯���u�f.����\$�yW�|U\$�:�������qĪM�SŞ8m2�İ�P��.�'��c��,��R\0K�X�Ў�����]��q|��Z�P������,�\r�\r��C�Ř�}�u̟5���?��z	��N�k͉lI�pw3���KMj9�[{È1i�s��yN�Ýz���qv�eGÖq��\"r�媩������W���\r��μN�7�C�+�@FJ.2El���A8��{�Q;n]&�H�\\�>N�d\0ctʄ�Ў�?t%%�v}@ƴ��ZL| y��X/鳍��n�ք�SR�mxW�/�Hr��l�o���ԩ����[�#�� F�k�*��~滓�t���W��;X\0�~����r�i颃\$V��7;��4;F�\$�B��`;6���\\n��Tw�j��:p����t�\$	7i��ֿ����7+Y!�5.#�ۇ�U���ۻGv�۞(��W��*_Sj�c]��`e��nyS�m�ܹ#������i�52�s~G;s��?�F�V��̈́ۋ\0N�,H�@T'L�i@�/�Y����\r\0���xUx�d>漂����@3\0yH^o\"u�ā�&͇x?Ti��\$/n�T�5�ŉ�	�ΰ<����d1ȋ����)��y�|9�2�T98a�/�S�X�)�Q�H}����.�g��K���5�Z����=��pߎ0��ô�kJ\n��L�f�����R	�EFP�d2 +ȥ�q9d �܎�yZ��!<	��j��\$I�W�\") �\n.4��N3	7|��暇td�\ne{ӡ��z��Th��a�nx�,%�/39��rw=\"�]�t��<1���|�\\n�W�~�XA������h��d( ޚv����Loc�8�l�9��W7�}w�8Ch����w\"PZ��]��u��H Nk�,�����.�����&@��\$��w�/�<��O����n�|<��H��RKt6H��2OD��!Ds�¾�0A�4F�ӣ�u(x��]q3M;�^R����t�� ����)\r��0�9ޟ2���g3r�=�L\"�	¾��pe�0H�-=��ㄊb6��a�,�h,�[k�{[�E3�-�I��,��ҹ����ך�P	c:���u��\r�]�M��ؤD (^�eƿ��,��iG^<6��H�jBWK�<ڸ%⫎�w؂l�.��PT��FK+��f�&�v��0��]@�Qx/b�vc'\n�A9�xb����X�ԧ�]y�M�}'\\�)/Hgm	�fϣ���Uz�6]Sŗ� ߬&�<��n�zt�oN�z+��f\r���>Y�{n�~��\$Ԇ̳0y���g%)� �=&�{@t�w�irK������濺zٴ��)�y42��Y�>�V3�^m������|�I�x���T֣�z�wk�����ʉ,<k��\$+�1<�� _�����d���}g��9F]�k)�|�������e}�iΨ�g���B�������\0ڃ?�۳��)HBH��DE�e���V��mP�yP��(�(�1}����W�05;\\\$+@��<v_\"��2@bM۶:�X��V���]꾼(�g\"�s\$���B�3\\�xDp���@D�'�X�*�N�����\$2��/VeM���y�r\0�����W�VL��e�L�dӸ���~�����[K��p_�@^�c�%��)wߩ�s�Hl�\n���w��?VO�H�j�&l�_��YŮׅ���9�b'Q?2[}���{M\n����2\\xml�PѦ�3�.���fD��(+%��D\r�r)@���h^����If�Mlu�x�\0�	�ڿe>�5�5�S��-��;�_��@���X��%,ɿ�f�|���@+�O�|J4P|�	-��Ś�u�h8���`�~�5�l��Q�;�o@����G�D�A�_�\0ӄF_Z�ٱD+:� M�u}�L�\"�J���(�L��_���d�a���o����	?���|�sD\\1A^\\�\\G��a-\\nˣ)���3e�܄�'Q��zf�v}q���P�7\0'�����e\r:�Up��y��x;�_YT��Yl��@ղ+����Mz6��d�)��`5{0�W\0��B�	 ���*U��Z���@�}l՘-��8�p�XR`8����nG( ��\$�%MT����\\���ڪ�FE�dc��3��	��\n�<&-9Jow��\0)?Ƥ0x:j��|i��vAT�;I��Q��AX&C裡M@Jl��L�(LP��3�� ���0�\0��H�+\0	<�; �N��(;6H���F ��e �p��6�/�sJ`�*��ڐ	�L3�2��<�9\"	���^lF������F(����B @��P܇;���F�v5l\0�ݠ00t�k���>\0O�U�<���Xq07�BF�8���K�#4�4 �%�wPN���A�Q1�D�O`�AHBp�p�`2^��c��	P,=���b�����F�C�\r�2���`8@�Ud�1��I��A��T�3��\\\0p1)5)F�@��D\r%VAd���\r^�� Âh	�#�A�����`��\r��������A���\0>���b	��\\p�AK<�w\0��\r�B%��W�f}�mH�j�a��dt\"0P�|���?�Ԇ��6�#�?�O\n\r�R���\$o��d������}	S��䟩\n��������o��[\0	��?����d�r�\$o�+`X-Y.��V��I�G>\0V�PM\0W��G�z]�\0TV��d܀_��a@-<\r��\0Y�+�H���-����f���tu��'b�4O��P*��f���Ry*9�8�:�G�D��3 34��5.F�V/0����R��Й��HaCF`g�+־�\0�<h\0\$���#��m/���㐞x�CAS��Xҋ���p��bc24���|��g�D�7I��J8@����|�6��-p��������*B���=%��������ԯ���r�	^CJ`\0sEpi�h3���`���H��p���U`�e��&�1�%\0V���V'X(W`��\r��i�F�k�W�n�q�����DQ#t��Kt\$���Q���\n\0���B���X<�Cp�\0K�(\0��C�1�BFIq�)q���� C~�����q\r���C�\rؘK�\"���j�ZZB��v�#�< �C�\r�u3���	�+D�钲�Ԍ�;�-��");
  } elseif ($_GET["file"] == "jush.js") {
    header("Content-Type: text/javascript; charset=utf-8");
    echo
    lzw_decompress("v0��F����==��FS	��_6MƳ���r:�E�CI��o:�C��Xc��\r�؄J(:=�E���a28�x�?�'�i�SANN���xs�NB��Vl0���S	��Ul�(D|҄��P��>�E�㩶yHch��-3Eb�� �b��pE�p�9.����~\n�?Kb�iw|�`��d.�x8EN��!��2��3���\r���Y���y6GFmY�8o7\n\r�0�<d4�E'�\n#�\r���.�C!�^t�(��bqH��.���s���2�N�q٤�9��#{�c�����3nӸ2��r�:<�+�9�CȨ���\n<�\r`��/b�\\���!�H�2SڙF#8Ј�I�78�K��*ں�!���鎑��+��:+���&�2|�:��9���:��N���pA/#�� �0D�\\�'�1����2�a@��+J�.�c,�����1��@^.B��ь�`OK=�`B��P�6����>(�eK%! ^!Ϭ�B��HS�s8^9�3�O1��.Xj+���M	#+�F�:�7�S�\$0�V(�FQ�\r!I��*�X�/̊���67=�۪X3݆؇���^��gf#W��g��8ߋ�h�7��E�k\r�ŹG�)��t�We4�V؝����&7�\0R��N!0�1W���y�CP��!��i|�gn��.\r�0�9�Aݸ���۶�^�8v�l\"�b�|�yHY�2�9�0�߅�.��:y���6�:�ؿ�n�\0Q�7��bk�<\0��湸�-�B�{��;�����W����&�/n�w��2A׵�����A�0yu)���kLƹtk�\0�;�d�=%m.��ŏc5�f���*�@4�� ���c�Ƹ܆|�\"맳�h�\\�f�P�N��q����s�f�~P��pHp\n~���>T_��QOQ�\$�V��S�pn1�ʚ��}=���L��Jeuc�����aA|;��ȓN��-��Z�@R��ͳ� �	��.��2�����`RE���^iP1&��ވ(���\$�C�Y�5�؃��axh@��=Ʋ�+>`��ע���\r!�b���r��2p�(=����!�es�X4G�Hhc �M�S.��|YjH��zB�SV��0�j�\nf\r�����D�o��%��\\1���MI`(�:�!�-�3=0������S���gW�e5��z�(h��d�r�ӫ�Ki�@Y.�����\$@�s�ѱEI&��Df�SR}��rڽ?�x\"�@ng����PI\\U��<�5X\"E0��t8��Y�=�`=��>�Q�4B�k���+p`�(8/N�qSK�r����i�O*[J��RJY�&u���7������#�>���Xû�?AP���CD�D���\$�����Y��<���X[�d�d��:��a\$�����Π��W�/ɂ�!+eYIw=9���i�;q\r\n���1��x�0]Q�<�zI9~W��9RD�KI6��L���C�z�\"0NW�WzH4��x�g�ת�x&�F�aӃ��\\�x��=�^ԓ���KH��x��ٓ0�EÝ҂ɚ�X�k,��R���~	��̛�Ny��Sz���6\0D	���؏�hs|.��=I�x}/�uN���'�[�R��`�N��95\0��C������X�ْ�6w1P���u�L\0V��ʲO�9[��O�>��PK�tÈu\r�|�̮R��pO��U��Drf�9�L�cSvn��Qo���@o��(��ްàp��a*�^�O>Oɹ<���e�������\"�ٓ��P>��H^���	psTO\r�0d�{�Z\$	2�,7�C���!u��}B�^����?�D��ڃF�ݱ����H�Ι`���'�@J��3��|O�ܹ�B�Mb�f1�n��@�1���(ղ����!�oow��f���)I�L\\[�����8[1)��!)���u��~�c�-�6-���y*	���>\"�m�61��ӕ�.��~�*�x��諍q��ǚG |��rl��O*%����݅�A�bRAx�g��D�f�V\\��R5l��ޤ`��5`��w�|���Sg��O���B;�Ϯ^LÖ��W?�5 ��ac}��s�ݏ�I��A��r��ݺO0�;w�x���P(�b�m�L'~�wh\0c�¨pE�߲:C�{g&ܾ/Ƒ>[����ۜ)	a}�n͡��wN�˼�x�]V^ye&�@A	�P\"� �E?P>@�|�!8 �Њ�H	�\\�`��@E	�Â�4�\0D�a!�������nr쯜\\���8�o`��H�f�����&���̒<�r��(jN�eN�)�6EO��4�.��n0�������6\r�� �\$����\$�� �N�<��|αN���j�OY\0�R�n��`�o���mkH����*�-Ϙ�w	Oz�NZ*ʛn�O�\n�#�n�⏓p[P_�b�������jP��P��Г\0�}\n/��Ӑ�������П	o}��S'��`b����\nPd�p ?Po0sq\n�:b�L���Uu\r.L`��SP���1mq���~�]%&ʚ�Q��� �\r�D�pq��pV|��f�8\$�p�&��ׂ�F��&����m�O�w��G	��1/elր��D\0�`~��`K���\\�b&�Q�Q�`ʾ�A����V�E�W�n: ؓBƌ�\r�*��l\0N��D��r뭦���[&G��h�r�H4A'�bP>�VƱ��M~�R�%2��r�m��\$�\0��2�c�����Mhʇvc���}cjg�s%l�DȺ�2�D�+�A�9#\$\0�\$RH�l��@Q!��%���\$R�FV�Ny+F\n��	 �%fz���*�ֿ��Mɾ�R�%@ڝ6\"�TN� kփ~@�F@��LQBv����6OD^hhm|6�n��L7`zr֍�Z@ր@܇3h��\$��@ѫ���t7zI��� P\rkf D�\"�b`�E@�\$\0�RZ1�&�\"~0��`��\nb�G�)	c>�[>ήe\"�6��N4�@d���n��9����ɴD4&2��\"/��|�7�u:ӱ;T3 �ԓi<TO`�Z�����B�؃�9�0�S>Qh�r\0A2�8\0W!�t��twH�OA��\0e�I��F��JT�4x�sA�AG�J2�i%:�=��#�^ ��g�7cr7s���%Ms�D v�sZ5\rb��\$�@����P��\r�\$=�%4��nX\\Xd��,l��pO��x�9b�m\"�&��g4�O�\\�(ൔ�5&rs� M�8���.I�Y5U5�IP3d�b/M��\0��3�y��^u^\"UbI�gT�?U4�N�h`�5�t���\r2}5-2�����W��(�f7@��e�/�\rJ�Kd7�- Sli3qU����z�\0�)�\$�c��oF?@]LJb�Dҿ�0��s?[gʜ�%��\rj�Un���^��R5,֪�t�FE\"��xzm��\n`�-�W#S(�l	p��%CU��辚�F�&T|jb�Z����8	��/4L�*nɦyB�:(�8�^9�8U� K���{`Z���\nF�\0Cl\r�'(`m�eR�6��M���B���C���6��v�����n%#nv�D��jGo,^:`�`s�l\r�_���X5CoV-��8RZ�@y��13q GSBt�v�Ѣt���#��bB������]��#�p���fZC�Ĳ����OZ����N��]�����sl�Ԃ���EL,+Q�@Yw�~9�I\"�8!մV5�&r�\\�7��W�&�ܼ�[\r\ri\r��~L|��d���ܷ�,��|i��@,\0�\"g�\$B�~��!)5v0�V ���b|M\$������D�f\r��8;���}�f��f�����icԄV0,Fx\rR��`�a&nȧ�QB.# Y��>w�g�����E��[�Ɨ�X���~RO��Y]8�]rK}�-��?�8�v�L�@�~�A*��f���J�M��tג���-v�[#�xL'L��>�l�8�Pg\n��\r�Q���ѱ\r�M��\":xw����\$b��-������=�kRXoQ乇9;��ˈ過��sՃ�͋�)���~�geB�Bt���,����,����K���y����-,mӀ���+��07yC��˃�Iz�ƍ�Y��^GGW��u�v0#kX��RJ\$JP+�6x��1�8���Y�g����{��?�\0�X�\r�	XF��W��ה��V/��̓dIg9߆�і�y��1��-�G�X����@O��R�y����!�GuY��5�ZF\r�㕵-�\$�O�e�u-��ZF��Zd��i�9+�쵘`M�z��\r�ҫI��y��A�Vp�:��O�J��:�V:�#:��:c��{��k�l��Zs��W����P0����#�9g@Mc�zw���[9U�\\k�����6��9Ӆ� ���y�,�����f6n-Zu���f�ً�c�,����[o�[g�d� �:w#��!W\\@�n�`�߱�\r��ɡ\$۟������\$��%��ߡ۷�z#��\$�imY��c�ɂ�k�I_������y��L���Ϲ�\$�`V��[����F�2C�8�\$��������ؼ�����G�[����¼���=�U��υ[q����K����Y���݋�Q��?�8���aX���m*G����\\��?�U�\0Ϣ���KĤ��|CR�͓�-����|ɜa��e��RY�ƺ饘�ܒ������������PJE��=��u�����\$�{�8�X��{����ŏ����ٓ�ٗ��ՙ��\r�������Ͱ٬&���Y�ҹ�(ټ�M2)��V u7\0S Z_��o]\\�|٩Ec7��S��΄[���<��<����;��-��i�� �}����l���!�,�}%����-۬��=����Ӭ��=��Y�8���PV|���zE.����\r�����bLfƸ��h*;�	ַ�;�؇�Q{��9\n_b\$5��l�UzXn�z\0xb�k�M	�2�� Z\r��c�|�ג/��}%��`�N�A�\0�*=`�F���^Q3�W�X��<���tR>r�`u�ģ>i��zN���اÝi����\$\0r���s����^C���>U�5���^a�)��	��J+>�uB��@?�J�-H���OJ'�-Tʀ�T��oUh�F��{��ԏJ[��N��V�oJ&S�B\"I^5�I�2���T���龽�]\0��\rk�L%�}�t�۷~I0�H|Pk�L5�_T�<�w��=<�x\"esa�K�\"���JH��+�U�a��'Y�~���7�)W��<6�=_�N�h�?6ܘ��y�,����a���w�\rİ�#�-V@�k��?i�b*%�޺��p?����yЀΆ�p��-p��|�n���Ca�f�8A�8�+#\r�R�@n����p��m�~ۈ{`�H?�v�*%�Ǽ�v%��G�`�`�Z��.���,�6�z��U8��|�y��V�����/�p��^��פ�m��]zcӞ���\$�IB0�|����@���pR�\n�j�9 ��G�7���읤#p߭�?����'���=�6H�lψ.�Y�OY��_V�G����O]I����=��x��\$���=�|Ϫ{��\n��<;�{:f^L'S�A1%�8*�^��p75���W��\n��\0��S⟕\02\nX(�u[��rp��B�0ڭ�x���:n	�ZI3�C����{�[��&�C(@}�r���w2�闌�nt����{C�ɆY!\0�He>��P\"�9t5�o���!�\$@\\7SS\r��C� P㄄@��I���nhG����	I�S�`x�7�0b+v5�^g�r%b�p�U��%)<+�S/Z@ �4!��j��8��\0�vN-6a[>�X�,�e\ned/�PX�`�}kOR�N���+�1O\$�π�F6B-�:wڨ�N��T�D>��x�����Y)��n�1��&�7��}�&xZ�\nޖ������W��:U@��a�⺃@��.�R�hbcT\"�����x\n� E���|߈�\r�-\0��\"�QA�Ih�\0�	 F��P\0MH�F�SB؎@�\0*��9���s\0�0'�	@Et�O�����Cx@\"G�81�`ϾP(G�=1ˏ\0��\"f>Qꎸ@�`'�>;���l������82>�zI� IG�\n�R�H	��c\"�\0�;1ێ�n�)���8�B`���(�V@Q�8c\"2���E�4r\0�9��\r�ԑ��� \0'GzH��5E!#���\rA�JЉJ�(��FC��&�d� I�\"I�V솣���G�SAX��Z~`'UA���@�����+A�\n�p��i%��ѿ�G�Z`\$��������>~?�E�\0�}� �<Q����'����E�w�ئ��#\rɂ7rQ� }�'iMI�O�0dm% ��Hʰ\"-h#��XF��M��t\$�!���R���t�,(�H8�8�!J�5I�x��r\n�Thړ~Pe@&eg\"[hؖ��4����|�2�z�D��lw#9	v{lb��/~\0���&I8%�,�IKA��\0�����/GYK�*�>���O/���2�t�eھف�P93=\$�X�d��-�&��|��#154LU���G.�i�2`����M.B���\00036�ISJ�-�~�쩦�jF\\3	o4�u	(@a3�A\0�c��`�P( ��0\$���\\}/d������\0�-�3�%b0\nc�z`��))%*��6\"����ٖ��E4��F�q���J����d��(�Ӏ����1�iLm�2�A��.)&q@\$�`L���2Lrse�� �.�vss�\r����i�KQ�󤙬 �0()�|�Mb�tU�9!�ED	�(	�`8*pa<�����80��s�\r� N���8O0�Ξ���d0��OVx��@'�<�Ol��J)�	�~}���\0U=��O�'Ňd�~\0�Of��X�H�	�L��Ҡ(]'�@�EP�LW��E'=��\0�'�\n��N�\$iI��Zy�	���>i�OH6f��'�߁x�.\"}@��-�wa2vӅ��A��L>����<0/����P��B�����͢��T���\n���<sSQ~|�ӂ��P�f�i�O�φ�lq���9T\r�����ѕgÄ���Fӧ�%O�(1�h⺶n�m�v�;�|���g���SaF��R��Ȥ�Nr��9z�%&�X��\0007\"�2t�-\rh%fŦֽ���3!�\"(�7I�\$s/ �-�7*J\rΕC�Lxw���֗�铴���(Ҫ�B,+�h\n���f\r�F�7Rf���*�:�\"�Δ4t�P�i�X�����*�\0P.(#��+H�oJAG���q�.57�+N	:-m`���&��HJO�Uvi��\0�\nGN:gR�n��2i�)}#���	F駩�>d�`�q������H���ƕe�5J);HQ�����\nHϓGRW�Ԟ��/�Jj�)K*UR���i�b8za�.�����RG��!4ͣ��@9����c: E.F|��T*��s�<Z]_O�i����\r@�2��qTlVUk�CQ\rOe��\"�\n�.�T�EUZ�Ԡ@i��^�ܪ��L��aMUB��V������'�U�+Q �V���W�m�G��Ժ�u0��*�P�T+�!u�\\�kV�y@Ƥ�j+��H��䁐�\"E��P��,�`<�H��Ք�p�ğ%	l\n�K ���\0�\$T!8@�@�2����h��4L��ŝ+��&����,�|��\"�T��Q霋�b#w)umŵ[�ޒ��)E}��[���Exd�)p����	n��-AK��1}W\\IU�nF^�\n��` \$��m)�oZ��	P�D�P�V��D �r%�R)��bұ�l�^�w�)JB���-K�D.1��8����\0��;� le�,L(\"m�N\n�Z��K�����gH���e��\0��\0t7�]��Kk\$�yN����X\0�6�(Y�������f�\\\r�K1y�,�`0��qo����\0�h\$��\n�_����dR��zE���C�h�<Y���p!�\0ro;������'g'*�!��Y�Xv��%�K4R�V�\r����Z�}Z�\r�o��mpN]N��5��xUay��\r�j��W��k�b�~��+m���edyٯʰZ�ksO�4;T���a�l@4[��]�M�7n 7�>�6���ϓ��=�h�*�0HΫj\$��[`���,����y	>��7p��D\$��u9�H ;�������R��~�0[�D��H��삕6�ܐ>-Lxj�Z�k�NȢ����n���dg�;�C\\\n�Pb[�h)3M�c�D4�0uR�#bP��5�:�a��EqH: ���:�.X��?�c�9�%n�K����a�5��J�`�7X�\n�q=ȿvr�E�<�(~���CȷPQxH�bK�ܪ�-]����\"�Q��C�U�.a��Q��v&�� ��7�]Ĩ媻�>�.9\0�=K=)���T�� ���_OX��5�!�b�U��h���AP�-����\r��%zPޔ߀<�x�����c7�|��4q�����p�C<�N���Y�5ь��)�澈��}AN_�RCTx�F�*�3���g���.�`*��B��`&�T�:**�7ƷE�W�R�\\�c�W��[���Kb��\r�o�Hr�����u 2~/խ�	@����aI� ,%b �\0�¡+{��[�,`_6�7��.�@̆�)?�m�m�b�a\n�v�������]`��W�8��!���W`��:�Fpo-`7	�\re��XXzK�I:���bD�_�5�>���ŗ��f+<Y��vg��,�%�H\\  d\$@��q�\n��A \n��6�8F�'|�I��R���T�{s�m3��8b)��	@���Lc�M���F@�#Y`��N���DX��CxzYc�0y���3hDZ��6\"�t\\7�SE;���U#�R^��ީ�s\0Cfb�ܚ��rrI\"Y�	�tå�8ZB/.�`�E��K�|����b��\n|_�}��KC�.��� p�1:����#Y\nTC	%,,��\r#�@�+��dqŁ�\$���{�D	\\J\0񒫇-`m!�|�g��dz�VI��vv&��A�`���MH\\I�����|E������j�B0ۊ@ѡnU��K��ތ��>����]ݸ�h��i�X9upr����a�\$7�v��Q��CA�>1����xif�R���7*�;8%���\"��Ʉ���w�P��TB���yH��'\n攏bظ���v��T5xcH\$�\\��ۏ����X�l��K���a�`���#t�Ew�gh�1�� �z���p���4:�\n�C��2��H�K<X	(!J��;�㏨���,��u�3�y�s�M�C9p��wz\0��ՠ9���ǈ�x�ǃ��1��B�������ي��`r�)=hLƂ�`���?z9�E�?���J���1�����Q��R�<\r�L\n8(#��r���p>��L�Q������|���\"4�(��*���8�fpiWaQ\n�Q��*���\\0@H�;�V��Y�Ά�����OZx�<F��'�I���A\n<�]�dP��_N�T!�\r˧���@*~І�B��=�%�z������;��:���AB}��&�l��c��h�`T��O�))�\0�y���I��ۦ�8��Ny��ј�G�\r\0�T�\"hn�5W@}�����Ն�B�}ZkV���Ф�y=s�	z�Ӕ����;\r쌚��,�hT��i|jza&�ր\$�i�S°�Hi��>I�B{Z*U�Ә�I�n���O�}��XMs��Q��8��I��Њ�	��v&! �k�@���#��<���T�Z�.����j�Z:�	^�B�}Y�����v�O3BTC���6=��k�eS��~���?]ij�O�Ѧ����m,\0}�!���mF!�[J�.��g��Ul�ZP٦����O[;&����]��Oht	`aILA��k�bki�N�vY���m:��v�v���k�g7���)���>��b&�؞��p�\0�5�I����]dp=�+:;��)� ���Dx@^o��ѸA���L�'�����t w�&U�g��3��B`/�=����'d>�/�dbF�\0�w\0y����9���n�Z[��6Tu���b�Z���~��~��\nzd'�@�Ra\n\n@�G���0�;vS����={��~��\0@_c0�ov�1�~�x����������e�\0p�o��>�83�|�pp�<�Il�o˄��O�;� ����%8Gx.>�o��O=^uLG��\r�N7��ݶq8~&n�5��l��]ڀ��������I..��4ओ_ۼ=��x���P������I�����5�]���[\0_�\0̓��  �<:��e��o������� �B�y�/��Eq瑻���f'�J�w��#7���N�x�F��(y��D�7�\\��'��Y2�˕�?߯)9e�nGr�vQ	�.�/�.Y��ܪ�<�zkMޠ�c��M��B+�\"ہ�\r�g�l�\0^\0��B@-	T���6�1����\n�����P�@ \"\"��@���F���0��t����U�\04��!��_|��(B\0Oc<��'����t�\"m)�TW����F ��P?f9�����C��M�mk����D���ސ�|���	�&��3�`�dΞ���\0O8�y�@��\n\0I?@@�@�/��O��\n��0���<d�\r\n�\0���H��C>�k���nm_:Gb�\$�\0�ђ��|�(v�I6�0�\"KB� �J��rK�`|6����F�T�'�9Y9>r�@y��@�%�ʄ��d�7<��\$p>t�\r\0|�yr�́��k9+����6���#��\"97�� N�ڮ���ͪ��Enp{s^�_;�\"��I�\0�J <w6��e�jc%���8�5�ր�����L&F{�2/w;����&CD���+p��%�#��BYo:d4�#H�!�A�,݃\nsα�8#=g�jl:�U��B�YX\0�eտtmd�(v��@k\\9vQ2��-{&/¶A��<%N����`�EKJ��Pպ,s&���8+-�1�T@W���8�l����D��x76@�\$�v�\"���t�X���vj��@t�H��'Ey@5�ك<ɏ��{��v�OY{LW���r:�(�,̗��\n�+�:(�5䏤�����02�%�D�Q�B��{�x-�(�*�~.����C�J�\n������S���ў#K��|䆮��ɨ2C@��a�B���bCq��y�L�7�K��4���O��fQ=�'���<!ٙ�fP+�`���gND��U���ҡ��!�\$�\$��-�/��3�Az_�@d~Q3��'��>�\n�\0�11�>���J�5���T���k8;���d�Y��^��ƥ���\0�Ӈ���(���F왕���`k���Q�+�I}Z�g0>�0MW{�z_BkП;`�(��-�wJ�e&ؤ;�FA%L\r?!��̋��\"�V�_�5G3���s?-eتQ�,�Y�s?24�~l\$߱eؤ޷�G\r�rH�����A~��O�,�G@l���dϲY���l�bЂ�?���#��:�Sߒ��k�n��ü�,�3Jy�\rg�fπ������v��/�4ݒk��d��A}�OY|t�������K�A���ޗ��?|���ށ-�����&���W`������_�\0S�����������\"��os~���G��r\$�Dr��{#�'���Eͽg���/�?����<������?��:��0�'�����Zn�7���9h@�?����b@(�3�o(�.������,���o>�{���I\"���䑂\"�`9ډ^����-�F7��%��h�Ұ��*֬�@|	\0i����@�@~C��\0�X����X\r,���3��\0����ZT ���6�.<;�C;2b��\0���K=1��#�!��� 5�:T�\nꙪMtᵀ��i�l@����9���S�b�@��(��81���i�A� �@�\r�+�8���K�B�6�~�\r�8-R���L\n�*�`6��1w�B�[�Oٻ�:���t�� A�\n�@�J\"���A8k�l[�������Co��<_�#AF��Xn�l��(��W��,�ꮈZ6���ȭXn\0���J3�Pu������>>��d�!=V�{KGe�c�F龪�Ɍ����m/��0�L��XOi*��˻�\0B/�3z���(��������}�0����+I�BPp\nB����ש���Iui�,�)0���%f	S��h�����Ϝ{���:�P�#�_���'T��k2h� �Ⱦ����i¸B����\r� 0k�ΐOn#>�l�	�\n��B���\n��2����̐�������VOiа��Y��b�s�\0����d�I�ſ	�1�6B�[�,\\���+2��(&��\0���\0�\r��p��^�Z)@<AL�zɐ��U\r�\r���tdH��\rl0D�V1� ��9�d0Lt������@[�5�P	P/��+��<Bz�zn;�f� \"�\n��xg�j���`T�2�4���X� @;���7������\"��ț9h�ۮ��>c<����C������-a\nD\np��9�bZ�����k �����*2�Bʡ���\\1���XC��'��Ɂ����D��D6�; 9;�+Ȯ`���ʃ�J���C������\0002���o���PH�>�\rc�`2A����@F��`ۂ%\$�\"D8����+A�\\`ս��y�&7�4�����x��\0ºt��Ѣp�� i��ZHe�HR�����D#LZ���p)�����.�bɀ,��pB�\$�%xB�&�TɈ`�E(�R��b���\0�;F�1i��o�TⲀ��4/��k<U�*\0�K���\r�Q�Z�e���]\0��ɑLEK����:),X�c(�?N���,W���V�GBʯ�Rqhŀ�ih�<S�oŗ�Y��EM���_�Y�YE��]Q]ų�W�KŻ45qv�����zEB��^�r�4��.���9����\n�al*�+,`�S�U�b/QE���kQ5�Xc��mTP��T�{�`����%�=	P\n\0���x{Hq��B��!R�5�P`��]��	����i�>��¤���h��F�\nN��<<| ��h�Oj��ᝐtڝ��C��)�F��88(�1�8�NR�i����\0߯�����i��蓀-�@'�2!����K@��%X\0����Dk��(Z��\0���\0���룆#���ii������(/-��\$���ػ�`t\$�����[�;^�� ׃���;O/:Θӽ��]\n�Ja��L���9F��RS劣\$�T��d����Ճ~`6��2�	����j��D�2\\OG�Q8����� XE�����4�nl��CfA�\0@�bX	b�Xd��4bk#V\r�t�~�W5�ћFEN`�m���#H��F�OX���\0�8��\$%\n;���(���)���0�\n�:D����@@��)���p	�r����)�0�jM�\n\0�8�\0�(\n��#�!�`���QQ�\r(�8��J5R?��M�(��X�)(�<~Q�G졀Rѹ6�䀑� dmǴ]\"b�����\rȵ��ʁ �&>�A��\$h?��c��(\n�\0�>�	�����}R��~\rhH��{�,G�<�m�(VN��\"�\0_�h�7:،�2A��_�>R\$�1\"\\��27\"z�#�G�l~rDG��m��l��[��I-#Srr@u ;d* I/\"1�����'�]�<���\nH���w�AI �������8#��	[v\0001�^l�#27\\��}��ɒ3#���7E&|�i9����l��&�v���\r��9�'zC./�3'�@�j+�h农�*r@��hY��;'��2~��(96{�A(9��HC�T�D��[�҅�](���,0��u(���}�3Q����)<R�2(RL����\rd�'�\n��F2{J���|�u((SA��ȱ(o%�(� °\0[�.��ʐ3�򙆚��J1(T�2��\"j��ʫ*�7ү�]*���I�:0.!H\n+�C��`����(P?Ҹ���L�aF��+��2�ʀ9�� �+�σ�*A��F�L6��0�\0�+�c�\$@cP?R���# �R��Xy:6p�D�� �,����G�5(�QQԤcP\r��+į�'J�B�8�,�m�8������-��P��pM���x�̥B�V��}�|�G,�< 6\n�\r��ҲJ�S� 9�Z������Ļ2��.��E����1K��8:ՌG*A� �&5-ĸ!jK������Ae-�9�'#/�������U'�s0��'�\n����LUJN.m��Ķ�\nK�04��9Lc��p�\0�<���L0t�2��B\$�<LBL�sLJ�xhs��1l�n'�|���W�d�����Lm,�\"��w*t���Lo-Y�hߤ�\"Z�1�ȥx��焨Ĥ� /�1�U�9̤ʒ�K�2��s.��'(̂�vI���|��������̇.cS\r�\$�����a3�r3\r��J#�i�<\r�� �1�+�΀�J�4\$�N�#���-4j�jM��\n�o/��34t��HʘlȒ��8L�/��4��SN�0�Q���4�ҳRM0]����K����3>%0�')L?*T�s���|�3`̋6���|��R�ͅ3��a�J&�r�M�xs9�2<�s+̅6�(�l͑1�>�9͟5ۉ�T��6<�x\0�\\�slM���/}GJ���\0006M�7j7�;��3��gM�7C����+\"�K�7��s�#~<���ˑ8d�i\"���\$������+��,� ���0�8Y&6��7xb/}#3���\0�8����L��	2��9��Mu9K1*��-/�䲟\n54��q�K��œ��wD栏�o1She�~#��s��l�r��:��ӜN|����\"�4���L79�?O}\0[KӉ�7��eE���(\ra�N)3�ܳJ�.k�2��BF��K���L�)�I2o9�%�|2f����sI�'D̒u��'pSBy���>/|��-\0���s�ʖ�r|�O8�DH-N�<�u�Jm:������=X%)��0�Y3�2��o\nդt	���M�,l�D�ͣ=�K����=�+�ق�6���OU>���I�>\0���MR\n�г�OY'�����A�SOM=D�S�ϫ=��r�;s�sO�=��2��?����N[.D�3�ɣ?���O�=�\0\"LO[?u\0���7@T�4v+p+\$��9L�.��1,H�J̎G����P7��F��5>U���'A5�P?A\\���%?���Y@��M��C4LAh�d���<��P�'�TN�?��4%̢��\r�������oB�E����\nҁ�qA��L��L�a�PDT�	T.��B�\n��Я.��422�؈��)�\r��P�?UT1P�@D���5�4\0��Զ�L9��I�I}'�M��*3\$�`6ɫ'H�rv9��\nP�P�?l���P���<QUC��_QGB����悌P��4���J�2|����q����,}�菦>�0��\$f��`)�PY��(�+\0��0���� �ޕ��bWQ�0�p\0�\ne�\$��rP�s��\n�Q�Q�F��n0(�@#�J@�&ў3\0*��FZ9�\"�����#��>�	�(Q����n�	Fm�h�EF�\n`(�N?r;��\0��\\��R&>��`'\0�x	cꎮ(\n�@��F���&\0���n���\n�Ə��R�/���rD�#�đ(c�Q�G����\n>ďT���FRG�ќ�%	�ѥGxtjѮ�kT��JpAr�GJ�,-�Ү(ԁ#�!e+�H�H�*4�R�K04Ar��>�t�G��R�J}�'Q�G	�rQ�GE0�\0��H���\0�e�F�����6ҍJ�9���Km)�n��P�G��J8t���K�,�R� �.t�SH��T�\0�L�+�n�(�(��1Gu�|��G�\"���H5t����!@>S?M5\"4�R�N�4��H�#`��#Ԑ�I5c�#�I=%4��IIl����?6��RL%0Ԃ�IL�Q����3��S@�(\nT�ұN`0�k���M��\0�I�&�'�qI���T\rI�0N�R��52�r��E7  ��G�, �RoI���{Pe(5Ҋe5�����%�#�>�2`\"�UKe?h��eK\\���\0���	���X*7kTH(�#�ѻKM2�#��	���R\n�%*�-!T�Q�= �UT�?T���1O�\r�.T\\�% ,�UR]K!�Q%+��MQp\ni[\0�J�J�!SQT���^�}4�7���J�T�S5H���MS�O�9�KQ`\\��WS�+\0+%MPa�Q�M`����G�G���?�.���Q㨉@#p*=�'���Rt�Ӭ>���USP�PrR��\$�\0%��U�C��0?�\\�.UuL����(�u7�(�����\0�U�7d�N�If�ME\$5K�?쎃���?�0�j�J\rT@\"�H�x�5oUV�U����W)yS)M�]T���S�\$��p>�Fc������O�Z�U.?�S5mU8%<�(Q�F���uF��V\n�MT���K�_��U@=\\5q�L?\rbus��Y\r4�w�gY!1�#�eX�a@�U�>�d4�\0��\0�#��p	�>\0��=��� � h��?�	��?������L�.՜Ԩ��	@'�nX	5`\$J�4e�K@���V-n�ֱK�u�V�]Wի���D�U�Z���m�6���h�VX[��\rV����M-Dվ��Yui;�uU��)BU�[�\$�ģsTMG4kH�!]uWR}o��H�OoI\$�?Eq��H; �\nT�ԙG�:#�\0���t�TMnc�T�-D�VJ�u�ق�?����T�%vC��ʏeG2;y]hh�\$�W�:)CWs^wuu��V�`�M��^E\\��W�^�*ՙW�R�R��W�V�z�Nן_Jt�א>����׿Wg���V5w�G\0�S�}��F�ZU�V)Zuh���WK�	4��qHU��U7X�hUD��_�y6��F�\\��T�`M�V\n�`}�4�XS݃���e`H\n�G���p���GU&#�%�}r	����e��W\"?=1I�Ze�*֞饄�ܣ�T������,���Xd�t����	�����\0&��kT���bM��P��-T��N`�%�^�BU\0�!����\0�a�<�&��G��H�?�D�%�eM9�=��L��e��}Q6=֤�k@�R\ne(�AWWu�� WB]o��Y']�8��U��@є��VԢ��-L5y��b kH�Wh�\r�VO\0Vj?��UP�Oh�ӫQ�	�#��\rm�W�cb}�\$�Le?4jVk!�Q`'U%^h��R��EN\0Tn휂u\rT��_�*\0�-��\$]�76mٻY��4TmfU&8;p?5RU\"���F�*?�g-��x����4�X쏅IuSRf�i[RSb8	4�ٽg5�6���g�*���Y������b͠V��UE n���6t��}O5��l#�M+�����\"�i5+t�#yV��� �] �QԆ��QM��ZoFե�=Zl魥6'Z�i͇YZgQu����c�U��Q�/5�sZ� �T�0>�&c��U@���Q�!ZM��U��\0�.�\$Y�P8R�?}kiցNM��IT�D��K#�x�'T�RH��7��G卵�Tގ-������p\n�i��Ul�t�U�|�V��V�0�����l����\0���D�[+lݎc�[ ���π�c�M5|\0�l�:�ҤfG6�і\r1�=��m] ���\\�Tm�Qg�1��ہX���᣺>�fu���e����b���k�am �ݣkm�Q�:\0�>���##sn}�'���g�\0�ñ��Z�U���\"�X�uk��T�>�2UR�O �%�\\��b��\$\0�`%7�8[:�����mm�7�mH��\\H=��v�KL�\$�p�KFm\$�SH�Z=���W%c�0�>�c�t���o%���X�}L\0\"��S��%Z�o�7\0#H����w�\n�{�*��i�	n��h?]�����\rq�HT`�V��meU�ꀿK�i#��v�	 \"\0��Ű��#�PM�7�Ih��ԝ��\n?�g���T7PEAT�R�PrM5`S\n5x�����@69�h�E!�6��x�T�Z4����\r;Qr��(��-K�;���` �t��UK�/V���N@��S��� �PV�m@���n��v���bT����t>�E5�;jC�?#rLc�����T�[` �yT���\0�p-�W3��������8�-I��S+T���]\"����:�������:�=�N���)XOo�:�9\0��q6�ݯr��@!��� Waۑ]e#@/��?�2tT]wU�v%�mܒQ�'����o\\շ֑��H<�4�\\Yx�SaYU\$�0XqHŔ�Sb�� W)!� �>Yyb-�\0>UY�K�G\0�k�wדSEy-�n�ck-�	؟P@��\0���WY`�\rgt��UD����1=��M޳!u�<Ħ�C�ר\$t`d�9���́\0��z}�cJD�@b�;��\$.�{���i���TP#����\\ɑ���ȍxT������k��|&e�<<D,��B'|8W�B�zk�-�^�p!�P��f�%:�\r�\r.\\_1z�\r��\$�=�0��G|�B��Ţ��{z|Շ#='����ڭ�*Rź�}��.�_nF��7�C�}k�P�1��0��ZJ���/�_eJ� 7��� <�n?-!X],\n`+UQy]�6�Tr�8�UfӏNM��DR�O�0�&ӑm=��5����i6׍]�;@�=K����Tj]�5Y�����Y]�\rwh�ԑRP0����]u�2Ӏ#��_��iG�*?�	\n_�Q�n�̔}4�0�m �0�\0�t��*:� �,��7.�;��� ���UX��*\0004��9e�.���� J�	%\nM�X��>;�!�Bz@���MtHa>�1[��?\0�N\\�<,�+�ЖAv8�D	D�v\r�(���u�jƔ2(�܃n�Ij�H\$���/^�!s�@�a\nv�&d���/A��{l�N�Ơ`�'���T�n�,!<k�:݄�S@��]�c�`،hT�T`�^ T�?;{�p5x4Dx=XkA����\n�A�� M��������\$�S� �N�ìo&������� ȕ�:��k��N�[��	��n���ҙB����߮�/�H����z����:�,t0+��2;�����a)��vPL�z)	{��#�ڂ��6������3b/�}��;)��� *��Qb,�p�b&5�p��P�ΕY���1��\rX\r!%a����<�O\$h����\0006/o�i{�)����[���*��'�4G��p�a!Vh@-��b�H?� ���Jx����Jc-��>*���f��b�&���A_��\"�%��-��=�W{�J�Yb�~%��;���%X/ ���\$�Qb��G8����f,����\rx�c(\ra��:�v1`>c��&a�����a%b@�qL�HkW����t\n���	����7�ɤ�+V|���?���N��cQ`� cg�h 6����F0�86xߝ��A]�9\0�88��J����Ճc���η�1@ 0���ab��7x�\$?8�2�NS�\$�J'D�\\�5��A%�1�v3��O�3�!7N��rh�#�;7�����{��&%��Aw\$�:���;��������pK8�c��5�ܘL���n,Ȕ�Ȁ��#����	�\0��@:�R�NEB�3˯���.h�S�=�.3�\"��ELs�cR�v)��ǭ�\$�����i�O��FImљn��!���Jb�\r�T��d�|`O����n�;(h�5���w�d�;�kN�ʪ��73�T-��78�\n�UY7D���s�7@�\n�5.���	Tsf~�k�n��)	�mA7B��N��d�ͦ�>@E��&�P@� �ツb�ҝ�:��Ҝ�AE\0�<\"�Q�k�������7X����:\0��at�l��;\r�q\0���)��|\\S;(���Y��s��_^�c��&(�|Yj^��~Z�DƸ�K���+�\0܄��;�=�ї +A�(�6\\i�Bz2mXB_��}�6߉.}���_���ӛe� [�B2e�|�(��fz�Z�����c��f}�ن\0�P@2Ad��by�f��bY�Nm��A�2×��d93f\rvd����e9���dY�f�na���c��e���/��fٓf9��f�e�~4?��_{����f�-�l�~7ں�}�bY��vM���LL������v����eш\n9E����u�U�Y\\���	�#�\$��n�g�B�<� �~����w�\r�uC�����W-d|��Ǭ��y���Tz�	1�,k�9�Q�VpRO��,hCB���~�nY˸Q��p�j��Y#��NX��Wum��Z�(��g3V��L�^oy�gq�!�gz!]�p.:�q�)	��gtJa|��u�܃�a6	�/燃���4d\$�6\n����2#1.g���s�ž���\\�&u����+�,g������wy�Y�K1�� 0�9��:מۭf6�˞�xY�9��Qb�\$��~tX'���6z���.�m�`�1�9s�@4�̓hD��y2�☾vqζ�VD.�\0�6��<���\"\0�綊k���>P9�1�vzϏ�\r����N՟�FY���V}\$:���6��`��::';�O�Od\$yF~��8���\"�턚.�5y�6O�����,Q�!=�t%��e���\0�\0yf6��}���R\n�A�`�P�r,�C\0���k@��S�zB�QCX!�I\0�.v�N����\$��@�Tc�F��Hi�Z�2֑K�\n������)]��i>�77�߀MbŸ��?����ŽC;�C���ޓc��I��4������#�0�hT�M��D=zM��X����CY�i�@`�,����y�Cݑ�i��c;�zV%������,M������%~�:ENY����.��NY�N����/�N��7h�<�A j�\\\n�aW-x`ډ��d���i~KP0�M��*i��\$�Fz|�QAV�I�=�j!�,:tB0�-�z����N���V?@K��AzxDb�V��K\0��8KD�����^��;��Gg�je�Ý�F|��oC9����u��n��(��\0���*4�A1�����j�\n��B�f�=n����Q���zxb܂D47i,!v�JP�!�XΎ��xP�{�Zv��U�Ӏj�B^!dj�\r��������K:4��z��4��bp�l����C�Cܢy����Ao\$��)6�z��Q��?A\r`���\\zEיִ\r�݃s���:Eh�e�>�Ќn�f�nڥ;����B��管��j�n~����w�Tho��M�[(�KKɮ���t!���ˤTx�4���o��y�Ɲ�EKR�6:KG��#�.\$t&��7c��-���@�]�Q�Q:ʊ߾�Ҩi-�,lQné��qO�+G�H�:�f�:�ꓯ�ID��_��Bo��M��Aj9���\n�W�3���F��~�/���f9	�0>����G��d����D��\\�A��]bK�\"\r��F~���[��c�\r�˸BOs�1�d!�y/Ѕ��n���\r�0�7�\r���	�%����h\n�2�l����Jב��ց8\"� h�Bh��j�J7�-b*�K�����!�FCV4��SK�ًF-����~�2�;�F�KÛ4������n�Z��1�vR9��\"L��:.�ν�dQh����k�a�n�k#9N�9��Ʋd��U��\0N��6�O��V��5+�iǢd��]{ج�����c	��g�AM^=����U�{vl�\$�P��5��/�(�\r):`F_:Ɨ��=�	�!y�V��9�ϟE�Q��5�>���:5�<c����Ɠ���z���	�M1�[�n��dn/����F�9�F�#`��v�X�<B�Fj�dN`Q�5�󞾴�K��5o���	�h;�������#���BZ�>����o@ck*��@����֓���D\\�S��)��pۭ���sC���6��pU[��G4�����?�.�e\na	��>W@��{�.��£��훭̵�\\9ژ>���CA�����ץ�`�0���d�]�f��M�1���I7�[����\n�]��,�q�VJ���ۑ?�tz��]����um*�p�+틽���.���\0H��W���;+���Bzo���x;^nE�tK��hq�����ꟓ�E!�+n=��T��瓗��xkj�6�{������#�h��#�[�o}��q���P�DղÝ��������o�1��xc��8D�\0�񲆜�J	������v=�W�Fzz�mk���hOޓ5j\$��X��}�<A>�n�{~h]��\"�\r��GD��x�Q�)=:�5����G:�P��D8�p	�sH2pzt�������\\ڀ����k�|)�Yt	���P�E\\D�0����¾�|p�1�Ɛs=&��`�h���IO��\n�,�M틂>Ae\\}���\\>�գ�G��7�N��l\\��L4!�5c,�T������!p}Ĭ��<�Q�H艞�89����!=�F�1j��ː�A�@��o�6�ۏ�U���9�������Ĺ���q���\nM��<_�}����3q��\0���\$n��o�>\$�z/	��+��q}����1�o\0�F8�?��P�����r�������;<�NG���E�c��\$*��qU����}��s�F�����8��b�C6��\rk��G�m� 4K<~4H!��j��m8Nkr	f.U����z��h�#�S�rU(	Zs���n�z!�/%\0����/&�}����ں6rxW`5�cG���O��b�W\$�b�M]��\$�?��z���\rޭ\"q�����J��Θn�ـ�A���&}���#[%�ɸ-�'gt\$ƕ�j��L�wN�re�\0\$8Z�#��:;�s\0M��\\������s\n�D�M�eA�������f��4I�BԾ��p`��@%Z�\0004�v");
  } else {
    header("Content-Type: image/gif");
    switch ($_GET["file"]) {
      case "plus.gif":
        echo "GIF89a\0\0�\0001���\0\0����\0\0\0!�\0\0\0,\0\0\0\0\0\0!�����M��*)�o��) q��e���#��L�\0;";
        break;
      case "cross.gif":
        echo "GIF89a\0\0�\0001���\0\0����\0\0\0!�\0\0\0,\0\0\0\0\0\0#�����#\na�Fo~y�.�_wa��1�J�G�L�6]\0\0;";
        break;
      case "up.gif":
        echo "GIF89a\0\0�\0001���\0\0����\0\0\0!�\0\0\0,\0\0\0\0\0\0 �����MQN\n�}��a8�y�aŶ�\0��\0;";
        break;
      case "down.gif":
        echo "GIF89a\0\0�\0001���\0\0����\0\0\0!�\0\0\0,\0\0\0\0\0\0 �����M��*)�[W�\\��L&ٜƶ�\0��\0;";
        break;
      case "arrow.gif":
        echo "GIF89a\0\n\0�\0\0������!�\0\0\0,\0\0\0\0\0\n\0\0�i������Ӳ޻\0\0;";
        break;
    }
  }
  exit;
}
if ($_GET["script"] == "version") {
  $o = get_temp_dir() . "/adminer.version";
  unlink($o);
  $r = file_open_lock($o);
  if ($r) file_write_unlock($r, serialize(array("signature" => $_POST["signature"], "version" => $_POST["version"])));
  exit;
}
global $b, $e, $k, $Nb, $l, $ba, $ca, $Id, $qf, $ad, $T, $sh, $ga;
if (!$_SERVER["REQUEST_URI"]) $_SERVER["REQUEST_URI"] = $_SERVER["ORIG_PATH_INFO"];
if (!strpos($_SERVER["REQUEST_URI"], '?') && $_SERVER["QUERY_STRING"] != "") $_SERVER["REQUEST_URI"] .= "?$_SERVER[QUERY_STRING]";
if ($_SERVER["HTTP_X_FORWARDED_PREFIX"]) $_SERVER["REQUEST_URI"] = $_SERVER["HTTP_X_FORWARDED_PREFIX"] . $_SERVER["REQUEST_URI"];
$ba = ($_SERVER["HTTPS"] && strcasecmp($_SERVER["HTTPS"], "off")) || ini_bool("session.cookie_secure");
@ini_set("session.use_trans_sid", false);
if (!defined("SID")) {
  session_cache_limiter("");
  session_name("adminer_sid");
  session_set_cookie_params(0, preg_replace('~\?.*~', '', $_SERVER["REQUEST_URI"]), "", $ba, true);
  session_start();
}
remove_slashes(array(&$_GET, &$_POST, &$_COOKIE), $Cc);
if (function_exists("get_magic_quotes_runtime") && get_magic_quotes_runtime()) set_magic_quotes_runtime(false);
@set_time_limit(0);
@ini_set("precision", 15);
function
get_lang()
{
  return 'en';
}
function
lang($rh, $_e = null)
{
  if (is_array($rh)) {
    $tf = ($_e == 1 ? 0 : 1);
    $rh = $rh[$tf];
  }
  $rh = str_replace("%d", "%s", $rh);
  $_e = format_number($_e);
  return
    sprintf($rh, $_e);
}
if (extension_loaded('pdo')) {
  abstract
  class
  PdoDb
  {
    var $server_info, $affected_rows, $errno, $error;
    protected $pdo;
    private $result;
    function
    dsn($Rb, $V, $F, $Oe = array())
    {
      $Oe[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_SILENT;
      $Oe[\PDO::ATTR_STATEMENT_CLASS] = array('Adminer\PdoDbStatement');
      try {
        $this->pdo = new
          \PDO($Rb, $V, $F, $Oe);
      } catch (Exception $nc) {
        auth_error(h($nc->getMessage()));
      }
      $this->server_info = @$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
    }
    abstract
    function
    select_db($xb);
    function
    quote($Ig)
    {
      return $this->pdo->quote($Ig);
    }
    function
    query($H, $zh = false)
    {
      $I = $this->pdo->query($H);
      $this->error = "";
      if (!$I) {
        list(, $this->errno, $this->error) = $this->pdo->errorInfo();
        if (!$this->error) $this->error = 'Unknown error.';
        return
          false;
      }
      $this->store_result($I);
      return $I;
    }
    function
    multi_query($H)
    {
      return $this->result = $this->query($H);
    }
    function
    store_result($I = null)
    {
      if (!$I) {
        $I = $this->result;
        if (!$I) return
          false;
      }
      if ($I->columnCount()) {
        $I->num_rows = $I->rowCount();
        return $I;
      }
      $this->affected_rows = $I->rowCount();
      return
        true;
    }
    function
    next_result()
    {
      if (!$this->result) return
        false;
      $this->result->_offset = 0;
      return @$this->result->nextRowset();
    }
    function
    result($H, $m = 0)
    {
      $I = $this->query($H);
      if (!$I) return
        false;
      $K = $I->fetch();
      return $K[$m];
    }
  }
  class
  PdoDbStatement
  extends
  \PDOStatement
  {
    var $_offset = 0, $num_rows;
    function
    fetch_assoc()
    {
      return $this->fetch(\PDO::FETCH_ASSOC);
    }
    function
    fetch_row()
    {
      return $this->fetch(\PDO::FETCH_NUM);
    }
    function
    fetch_field()
    {
      $K = (object)$this->getColumnMeta($this->_offset++);
      $K->orgtable = $K->table;
      $K->orgname = $K->name;
      $K->charsetnr = (in_array("blob", (array)$K->flags) ? 63 : 0);
      return $K;
    }
    function
    seek($Be)
    {
      for ($u = 0; $u < $Be; $u++) $this->fetch();
    }
  }
}
$Nb = array();
function
add_driver($v, $D)
{
  global $Nb;
  $Nb[$v] = $D;
}
function
get_driver($v)
{
  global $Nb;
  return $Nb[$v];
}
abstract
class
SqlDriver
{
  static $wf = array();
  static $Bd;
  protected $conn;
  protected $types = array();
  var $editFunctions = array();
  var $unsigned = array();
  var $operators = array();
  var $functions = array();
  var $grouping = array();
  var $onActions = "RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";
  var $inout = "IN|OUT|INOUT";
  var $enumLength = "'(?:''|[^'\\\\]|\\\\.)*'";
  var $generated = array();
  function
  __construct($e)
  {
    $this->conn = $e;
  }
  function
  types()
  {
    return
      call_user_func_array('array_merge', array_values($this->types));
  }
  function
  structuredTypes()
  {
    return
      array_map('array_keys', $this->types);
  }
  function
  enumLength($m) {}
  function
  unconvertFunction($m) {}
  function
  select($Q, $N, $Z, $t, $Qe = array(), $z = 1, $E = 0, $Af = false)
  {
    global $b;
    $xd = (count($t) < count($N));
    $H = $b->selectQueryBuild($N, $Z, $t, $Qe, $z, $E);
    if (!$H) $H = "SELECT" . limit(($_GET["page"] != "last" && $z != "" && $t && $xd && JUSH == "sql" ? "SQL_CALC_FOUND_ROWS " : "") . implode(", ", $N) . "\nFROM " . table($Q), ($Z ? "\nWHERE " . implode(" AND ", $Z) : "") . ($t && $xd ? "\nGROUP BY " . implode(", ", $t) : "") . ($Qe ? "\nORDER BY " . implode(", ", $Qe) : ""), ($z != "" ? +$z : null), ($E ? $z * $E : 0), "\n");
    $Eg = microtime(true);
    $J = $this->conn->query($H);
    if ($Af) echo $b->selectQuery($H, $Eg, !$J);
    return $J;
  }
  function
  delete($Q, $If, $z = 0)
  {
    $H = "FROM " . table($Q);
    return
      queries("DELETE" . ($z ? limit1($Q, $H, $If) : " $H$If"));
  }
  function
  update($Q, $P, $If, $z = 0, $mg = "\n")
  {
    $Oh = array();
    foreach (
      $P
      as $y => $X
    ) $Oh[] = "$y = $X";
    $H = table($Q) . " SET$mg" . implode(",$mg", $Oh);
    return
      queries("UPDATE" . ($z ? limit1($Q, $H, $If, $mg) : " $H$If"));
  }
  function
  insert($Q, $P)
  {
    return
      queries("INSERT INTO " . table($Q) . ($P ? " (" . implode(", ", array_keys($P)) . ")\nVALUES (" . implode(", ", $P) . ")" : " DEFAULT VALUES"));
  }
  function
  insertUpdate($Q, $L, $_f)
  {
    return
      false;
  }
  function
  begin()
  {
    return
      queries("BEGIN");
  }
  function
  commit()
  {
    return
      queries("COMMIT");
  }
  function
  rollback()
  {
    return
      queries("ROLLBACK");
  }
  function
  slowQuery($H, $fh) {}
  function
  convertSearch($jd, $X, $m)
  {
    return $jd;
  }
  function
  convertOperator($Le)
  {
    return $Le;
  }
  function
  value($X, $m)
  {
    return (method_exists($this->conn, 'value') ? $this->conn->value($X, $m) : (is_resource($X) ? stream_get_contents($X) : $X));
  }
  function
  quoteBinary($M)
  {
    return
      q($M);
  }
  function
  warnings()
  {
    return '';
  }
  function
  tableHelp($D, $_d = false) {}
  function
  hasCStyleEscapes()
  {
    return
      false;
  }
  function
  supportsIndex($R)
  {
    return !is_view($R);
  }
  function
  checkConstraints($Q)
  {
    return
      get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME
WHERE c.CONSTRAINT_SCHEMA = " . q($_GET["ns"] != "" ? $_GET["ns"] : DB) . "
AND t.TABLE_NAME = " . q($Q) . "
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'");
  }
}
class
Adminer
{
  var $operators;
  function
  name()
  {
    return "<a href='https://www.adminer.org/'" . target_blank() . " id='h1'>Adminer</a>";
  }
  function
  credentials()
  {
    return
      array(SERVER, $_GET["username"], get_password());
  }
  function
  connectSsl() {}
  function
  permanentLogin($g = false)
  {
    return
      password_file($g);
  }
  function
  bruteForceKey()
  {
    return $_SERVER["REMOTE_ADDR"];
  }
  function
  serverName($O)
  {
    return
      h($O);
  }
  function
  database()
  {
    return
      DB;
  }
  function
  databases($Fc = true)
  {
    return
      get_databases($Fc);
  }
  function
  schemas()
  {
    return
      schemas();
  }
  function
  queryTimeout()
  {
    return
      2;
  }
  function
  headers() {}
  function
  csp()
  {
    return
      csp();
  }
  function
  head($ub = null)
  {
    return
      true;
  }
  function
  css()
  {
    $J = array();
    foreach (array("", "-dark") as $ne) {
      $o = "adminer$ne.css";
      if (file_exists($o)) $J[] = "$o?v=" . crc32(file_get_contents($o));
    }
    return $J;
  }
  function
  loginForm()
  {
    global $Nb;
    echo "<table class='layout'>\n", $this->loginFormField('driver', '<tr><th>' . 'System' . '<td>', "<input type='hidden' name='auth[driver]' value='server'>MySQL / MariaDB"), $this->loginFormField('server', '<tr><th>' . 'Server' . '<td>', '<input name="auth[server]" value="' . h(SERVER) . '" title="hostname[:port]" placeholder="localhost" autocapitalize="off">'), $this->loginFormField('username', '<tr><th>' . 'Username' . '<td>', '<input name="auth[username]" id="username" autofocus value="' . h($_GET["username"]) . '" autocomplete="username" autocapitalize="off">'), $this->loginFormField('password', '<tr><th>' . 'Password' . '<td>', '<input type="password" name="auth[password]" autocomplete="current-password">'), $this->loginFormField('db', '<tr><th>' . 'Database' . '<td>', '<input name="auth[db]" value="' . h($_GET["db"]) . '" autocapitalize="off">'), "</table>\n", "<p><input type='submit' value='" . 'Login' . "'>\n", checkbox("auth[permanent]", 1, $_COOKIE["adminer_permanent"], 'Permanent login') . "\n";
  }
  function
  loginFormField($D, $cd, $Y)
  {
    return $cd . $Y . "\n";
  }
  function
  login($Td, $F)
  {
    if ($F == "") return
      sprintf('Adminer does not support accessing a database without a password, <a href="https://www.adminer.org/en/password/"%s>more information</a>.', target_blank());
    return
      true;
  }
  function
  tableName($Qg)
  {
    return
      h($Qg["Name"]);
  }
  function
  fieldName($m, $Qe = 0)
  {
    return '<span title="' . h($m["full_type"] . ($m["comment"] != "" ? " : $m[comment]" : '')) . '">' . h($m["field"]) . '</span>';
  }
  function
  selectLinks($Qg, $P = "")
  {
    global $k;
    echo '<p class="links">';
    $Sd = array("select" => 'Select data');
    if (support("table") || support("indexes")) $Sd["table"] = 'Show structure';
    $_d = false;
    if (support("table")) {
      $_d = is_view($Qg);
      if ($_d) $Sd["view"] = 'Alter view';
      else $Sd["create"] = 'Alter table';
    }
    if ($P !== null) $Sd["edit"] = 'New item';
    $D = $Qg["Name"];
    foreach (
      $Sd
      as $y => $X
    ) echo " <a href='" . h(ME) . "$y=" . urlencode($D) . ($y == "edit" ? $P : "") . "'" . bold(isset($_GET[$y])) . ">$X</a>";
    echo
    doc_link(array(JUSH => $k->tableHelp($D, $_d)), "?"), "\n";
  }
  function
  foreignKeys($Q)
  {
    return
      foreign_keys($Q);
  }
  function
  backwardKeys($Q, $Pg)
  {
    return
      array();
  }
  function
  backwardKeysPrint($za, $K) {}
  function
  selectQuery($H, $Eg, $xc = false)
  {
    global $k;
    $J = "</p>\n";
    if (!$xc && ($Wh = $k->warnings())) {
      $v = "warnings";
      $J = ", <a href='#$v'>" . 'Warnings' . "</a>" . script("qsl('a').onclick = partial(toggle, '$v');", "") . "$J<div id='$v' class='hidden'>\n$Wh</div>\n";
    }
    return "<p><code class='jush-" . JUSH . "'>" . h(str_replace("\n", " ", $H)) . "</code> <span class='time'>(" . format_time($Eg) . ")</span>" . (support("sql") ? " <a href='" . h(ME) . "sql=" . urlencode($H) . "'>" . 'Edit' . "</a>" : "") . $J;
  }
  function
  sqlCommandQuery($H)
  {
    return
      shorten_utf8(trim($H), 1000);
  }
  function
  rowDescription($Q)
  {
    return "";
  }
  function
  rowDescriptions($L, $Ic)
  {
    return $L;
  }
  function
  selectLink($X, $m) {}
  function
  selectVal($X, $_, $m, $af)
  {
    $J = ($X === null ? "<i>NULL</i>" : (preg_match("~char|binary|boolean~", $m["type"]) && !preg_match("~var~", $m["type"]) ? "<code>$X</code>" : (preg_match('~json~', $m["type"]) ? "<code class='jush-js'>$X</code>" : $X)));
    if (preg_match('~blob|bytea|raw|file~', $m["type"]) && !is_utf8($X)) $J = "<i>" . lang(array('%d byte', '%d bytes'), strlen($af)) . "</i>";
    return ($_ ? "<a href='" . h($_) . "'" . (is_url($_) ? target_blank() : "") . ">$J</a>" : $J);
  }
  function
  editVal($X, $m)
  {
    return $X;
  }
  function
  tableStructurePrint($n)
  {
    global $k;
    echo "<div class='scrollable'>\n", "<table class='nowrap odds'>\n", "<thead><tr><th>" . 'Column' . "<td>" . 'Type' . (support("comment") ? "<td>" . 'Comment' : "") . "</thead>\n";
    $Jg = $k->structuredTypes();
    foreach (
      $n
      as $m
    ) {
      echo "<tr><th>" . h($m["field"]);
      $U = h($m["full_type"]);
      echo "<td><span title='" . h($m["collation"]) . "'>" . (in_array($U, (array)$Jg['User types']) ? "<a href='" . h(ME . 'type=' . urlencode($U)) . "'>$U</a>" : $U) . "</span>", ($m["null"] ? " <i>NULL</i>" : ""), ($m["auto_increment"] ? " <i>" . 'Auto Increment' . "</i>" : "");
      $j = h($m["default"]);
      echo (isset($m["default"]) ? " <span title='" . 'Default value' . "'>[<b>" . ($m["generated"] ? "<code class='jush-" . JUSH . "'>$j</code>" : $j) . "</b>]</span>" : ""), (support("comment") ? "<td>" . h($m["comment"]) : ""), "\n";
    }
    echo "</table>\n", "</div>\n";
  }
  function
  tableIndexesPrint($x)
  {
    echo "<table>\n";
    foreach (
      $x
      as $D => $w
    ) {
      ksort($w["columns"]);
      $Af = array();
      foreach ($w["columns"] as $y => $X) $Af[] = "<i>" . h($X) . "</i>" . ($w["lengths"][$y] ? "(" . $w["lengths"][$y] . ")" : "") . ($w["descs"][$y] ? " DESC" : "");
      echo "<tr title='" . h($D) . "'><th>$w[type]<td>" . implode(", ", $Af) . "\n";
    }
    echo "</table>\n";
  }
  function
  selectColumnsPrint($N, $d)
  {
    global $k;
    print_fieldset("select", 'Select', $N);
    $u = 0;
    $N[""] = array();
    foreach (
      $N
      as $y => $X
    ) {
      $X = $_GET["columns"][$y];
      $c = select_input(" name='columns[$u][col]'", $d, $X["col"], ($y !== "" ? "selectFieldChange" : "selectAddRow"));
      echo "<div>" . ($k->functions || $k->grouping ? html_select("columns[$u][fun]", array(-1 => "") + array_filter(array('Functions' => $k->functions, 'Aggregation' => $k->grouping)), $X["fun"]) . on_help("getTarget(event).value && getTarget(event).value.replace(/ |\$/, '(') + ')'", 1) . script("qsl('select').onchange = function () { helpClose();" . ($y !== "" ? "" : " qsl('select, input', this.parentNode).onchange();") . " };", "") . "($c)" : $c) . "</div>\n";
      $u++;
    }
    echo "</div></fieldset>\n";
  }
  function
  selectSearchPrint($Z, $d, $x)
  {
    print_fieldset("search", 'Search', $Z);
    foreach (
      $x
      as $u => $w
    ) {
      if ($w["type"] == "FULLTEXT") echo "<div>(<i>" . implode("</i>, <i>", array_map('Adminer\h', $w["columns"])) . "</i>) AGAINST", " <input type='search' name='fulltext[$u]' value='" . h($_GET["fulltext"][$u]) . "'>", script("qsl('input').oninput = selectFieldChange;", ""), checkbox("boolean[$u]", 1, isset($_GET["boolean"][$u]), "BOOL"), "</div>\n";
    }
    $La = "this.parentNode.firstChild.onchange();";
    foreach (array_merge((array)$_GET["where"], array(array())) as $u => $X) {
      if (!$X || ("$X[col]$X[val]" != "" && in_array($X["op"], $this->operators))) echo "<div>" . select_input(" name='where[$u][col]'", $d, $X["col"], ($X ? "selectFieldChange" : "selectAddRow"), "(" . 'anywhere' . ")"), html_select("where[$u][op]", $this->operators, $X["op"], $La), "<input type='search' name='where[$u][val]' value='" . h($X["val"]) . "'>", script("mixin(qsl('input'), {oninput: function () { $La }, onkeydown: selectSearchKeydown, onsearch: selectSearchSearch});", ""), "</div>\n";
    }
    echo "</div></fieldset>\n";
  }
  function
  selectOrderPrint($Qe, $d, $x)
  {
    print_fieldset("sort", 'Sort', $Qe);
    $u = 0;
    foreach ((array)$_GET["order"] as $y => $X) {
      if ($X != "") {
        echo "<div>" . select_input(" name='order[$u]'", $d, $X, "selectFieldChange"), checkbox("desc[$u]", 1, isset($_GET["desc"][$y]), 'descending') . "</div>\n";
        $u++;
      }
    }
    echo "<div>" . select_input(" name='order[$u]'", $d, "", "selectAddRow"), checkbox("desc[$u]", 1, false, 'descending') . "</div>\n", "</div></fieldset>\n";
  }
  function
  selectLimitPrint($z)
  {
    echo "<fieldset><legend>" . 'Limit' . "</legend><div>", "<input type='number' name='limit' class='size' value='" . h($z) . "'>", script("qsl('input').oninput = selectFieldChange;", ""), "</div></fieldset>\n";
  }
  function
  selectLengthPrint($dh)
  {
    if ($dh !== null) echo "<fieldset><legend>" . 'Text length' . "</legend><div>", "<input type='number' name='text_length' class='size' value='" . h($dh) . "'>", "</div></fieldset>\n";
  }
  function
  selectActionPrint($x)
  {
    echo "<fieldset><legend>" . 'Action' . "</legend><div>", "<input type='submit' value='" . 'Select' . "'>", " <span id='noindex' title='" . 'Full table scan' . "'></span>", "<script" . nonce() . ">\n", "var indexColumns = ";
    $d = array();
    foreach (
      $x
      as $w
    ) {
      $tb = reset($w["columns"]);
      if ($w["type"] != "FULLTEXT" && $tb) $d[$tb] = 1;
    }
    $d[""] = 1;
    foreach (
      $d
      as $y => $X
    ) json_row($y);
    echo ";\n", "selectFieldChange.call(qs('#form')['select']);\n", "</script>\n", "</div></fieldset>\n";
  }
  function
  selectCommandPrint()
  {
    return !information_schema(DB);
  }
  function
  selectImportPrint()
  {
    return !information_schema(DB);
  }
  function
  selectEmailPrint($Zb, $d) {}
  function
  selectColumnsProcess($d, $x)
  {
    global $k;
    $N = array();
    $t = array();
    foreach ((array)$_GET["columns"] as $y => $X) {
      if ($X["fun"] == "count" || ($X["col"] != "" && (!$X["fun"] || in_array($X["fun"], $k->functions) || in_array($X["fun"], $k->grouping)))) {
        $N[$y] = apply_sql_function($X["fun"], ($X["col"] != "" ? idf_escape($X["col"]) : "*"));
        if (!in_array($X["fun"], $k->grouping)) $t[] = $N[$y];
      }
    }
    return
      array($N, $t);
  }
  function
  selectSearchProcess($n, $x)
  {
    global $e, $k;
    $J = array();
    foreach (
      $x
      as $u => $w
    ) {
      if ($w["type"] == "FULLTEXT" && $_GET["fulltext"][$u] != "") $J[] = "MATCH (" . implode(", ", array_map('Adminer\idf_escape', $w["columns"])) . ") AGAINST (" . q($_GET["fulltext"][$u]) . (isset($_GET["boolean"][$u]) ? " IN BOOLEAN MODE" : "") . ")";
    }
    foreach ((array)$_GET["where"] as $y => $X) {
      if ("$X[col]$X[val]" != "" && in_array($X["op"], $this->operators)) {
        $yf = "";
        $gb = " $X[op]";
        if (preg_match('~IN$~', $X["op"])) {
          $md = process_length($X["val"]);
          $gb .= " " . ($md != "" ? $md : "(NULL)");
        } elseif ($X["op"] == "SQL") $gb = " $X[val]";
        elseif ($X["op"] == "LIKE %%") $gb = " LIKE " . $this->processInput($n[$X["col"]], "%$X[val]%");
        elseif ($X["op"] == "ILIKE %%") $gb = " ILIKE " . $this->processInput($n[$X["col"]], "%$X[val]%");
        elseif ($X["op"] == "FIND_IN_SET") {
          $yf = "$X[op](" . q($X["val"]) . ", ";
          $gb = ")";
        } elseif (!preg_match('~NULL$~', $X["op"])) $gb .= " " . $this->processInput($n[$X["col"]], $X["val"]);
        if ($X["col"] != "") $J[] = $yf . $k->convertSearch(idf_escape($X["col"]), $X, $n[$X["col"]]) . $gb;
        else {
          $ab = array();
          foreach (
            $n
            as $D => $m
          ) {
            if (isset($m["privileges"]["where"]) && (preg_match('~^[-\d.' . (preg_match('~IN$~', $X["op"]) ? ',' : '') . ']+$~', $X["val"]) || !preg_match('~' . number_type() . '|bit~', $m["type"])) && (!preg_match("~[\x80-\xFF]~", $X["val"]) || preg_match('~char|text|enum|set~', $m["type"])) && (!preg_match('~date|timestamp~', $m["type"]) || preg_match('~^\d+-\d+-\d+~', $X["val"]))) $ab[] = $yf . $k->convertSearch(idf_escape($D), $X, $m) . $gb;
          }
          $J[] = ($ab ? "(" . implode(" OR ", $ab) . ")" : "1 = 0");
        }
      }
    }
    return $J;
  }
  function
  selectOrderProcess($n, $x)
  {
    $J = array();
    foreach ((array)$_GET["order"] as $y => $X) {
      if ($X != "") $J[] = (preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~', $X) ? $X : idf_escape($X)) . (isset($_GET["desc"][$y]) ? " DESC" : "");
    }
    return $J;
  }
  function
  selectLimitProcess()
  {
    return (isset($_GET["limit"]) ? $_GET["limit"] : "50");
  }
  function
  selectLengthProcess()
  {
    return (isset($_GET["text_length"]) ? $_GET["text_length"] : "100");
  }
  function
  selectEmailProcess($Z, $Ic)
  {
    return
      false;
  }
  function
  selectQueryBuild($N, $Z, $t, $Qe, $z, $E)
  {
    return "";
  }
  function
  messageQuery($H, $eh, $xc = false)
  {
    global $k;
    restart_session();
    $dd = &get_session("queries");
    if (!$dd[$_GET["db"]]) $dd[$_GET["db"]] = array();
    if (strlen($H) > 1e6) $H = preg_replace('~[\x80-\xFF]+$~', '', substr($H, 0, 1e6)) . "\n…";
    $dd[$_GET["db"]][] = array($H, time(), $eh);
    $Bg = "sql-" . count($dd[$_GET["db"]]);
    $J = "<a href='#$Bg' class='toggle'>" . 'SQL command' . "</a>\n";
    if (!$xc && ($Wh = $k->warnings())) {
      $v = "warnings-" . count($dd[$_GET["db"]]);
      $J = "<a href='#$v' class='toggle'>" . 'Warnings' . "</a>, $J<div id='$v' class='hidden'>\n$Wh</div>\n";
    }
    return " <span class='time'>" . @date("H:i:s") . "</span>" . " $J<div id='$Bg' class='hidden'><pre><code class='jush-" . JUSH . "'>" . shorten_utf8($H, 1000) . "</code></pre>" . ($eh ? " <span class='time'>($eh)</span>" : '') . (support("sql") ? '<p><a href="' . h(str_replace("db=" . urlencode(DB), "db=" . urlencode($_GET["db"]), ME) . 'sql=&history=' . (count($dd[$_GET["db"]]) - 1)) . '">' . 'Edit' . '</a>' : '') . '</div>';
  }
  function
  editRowPrint($Q, $n, $K, $Fh) {}
  function
  editFunctions($m)
  {
    global $k;
    $J = ($m["null"] ? "NULL/" : "");
    $Fh = isset($_GET["select"]) || where($_GET);
    foreach (
      $k->editFunctions
      as $y => $Oc
    ) {
      if (!$y || (!isset($_GET["call"]) && $Fh)) {
        foreach (
          $Oc
          as $of => $X
        ) {
          if (!$of || preg_match("~$of~", $m["type"])) $J .= "/$X";
        }
      }
      if ($y && !preg_match('~set|blob|bytea|raw|file|bool~', $m["type"])) $J .= "/SQL";
    }
    if ($m["auto_increment"] && !$Fh) $J = 'Auto Increment';
    return
      explode("/", $J);
  }
  function
  editInput($Q, $m, $ta, $Y)
  {
    if ($m["type"] == "enum") return (isset($_GET["select"]) ? "<label><input type='radio'$ta value='-1' checked><i>" . 'original' . "</i></label> " : "") . ($m["null"] ? "<label><input type='radio'$ta value=''" . ($Y !== null || isset($_GET["select"]) ? "" : " checked") . "><i>NULL</i></label> " : "") . enum_input("radio", $ta, $m, $Y, $Y === 0 ? 0 : null);
    return "";
  }
  function
  editHint($Q, $m, $Y)
  {
    return "";
  }
  function
  processInput($m, $Y, $s = "")
  {
    if ($s == "SQL") return $Y;
    $D = $m["field"];
    $J = q($Y);
    if (preg_match('~^(now|getdate|uuid)$~', $s)) $J = "$s()";
    elseif (preg_match('~^current_(date|timestamp)$~', $s)) $J = $s;
    elseif (preg_match('~^([+-]|\|\|)$~', $s)) $J = idf_escape($D) . " $s $J";
    elseif (preg_match('~^[+-] interval$~', $s)) $J = idf_escape($D) . " $s " . (preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i", $Y) ? $Y : $J);
    elseif (preg_match('~^(addtime|subtime|concat)$~', $s)) $J = "$s(" . idf_escape($D) . ", $J)";
    elseif (preg_match('~^(md5|sha1|password|encrypt)$~', $s)) $J = "$s($J)";
    return
      unconvert_field($m, $J);
  }
  function
  dumpOutput()
  {
    $J = array('text' => 'open', 'file' => 'save');
    if (function_exists('gzencode')) $J['gz'] = 'gzip';
    return $J;
  }
  function
  dumpFormat()
  {
    return (support("dump") ? array('sql' => 'SQL') : array()) + array('csv' => 'CSV,', 'csv;' => 'CSV;', 'tsv' => 'TSV');
  }
  function
  dumpDatabase($i) {}
  function
  dumpTable($Q, $Kg, $_d = 0)
  {
    if ($_POST["format"] != "sql") {
      echo "\xef\xbb\xbf";
      if ($Kg) dump_csv(array_keys(fields($Q)));
    } else {
      if ($_d == 2) {
        $n = array();
        foreach (fields($Q) as $D => $m) $n[] = idf_escape($D) . " $m[full_type]";
        $g = "CREATE TABLE " . table($Q) . " (" . implode(", ", $n) . ")";
      } else $g = create_sql($Q, $_POST["auto_increment"], $Kg);
      set_utf8mb4($g);
      if ($Kg && $g) {
        if ($Kg == "DROP+CREATE" || $_d == 1) echo "DROP " . ($_d == 2 ? "VIEW" : "TABLE") . " IF EXISTS " . table($Q) . ";\n";
        if ($_d == 1) $g = remove_definer($g);
        echo "$g;\n\n";
      }
    }
  }
  function
  dumpData($Q, $Kg, $H)
  {
    global $e;
    if ($Kg) {
      $ae = (JUSH == "sqlite" ? 0 : 1048576);
      $n = array();
      $id = false;
      if ($_POST["format"] == "sql") {
        if ($Kg == "TRUNCATE+INSERT") echo
        truncate_sql($Q) . ";\n";
        $n = fields($Q);
        if (JUSH == "mssql") {
          foreach (
            $n
            as $m
          ) {
            if ($m["auto_increment"]) {
              echo "SET IDENTITY_INSERT " . table($Q) . " ON;\n";
              $id = true;
              break;
            }
          }
        }
      }
      $I = $e->query($H, 1);
      if ($I) {
        $sd = "";
        $Ha = "";
        $Dd = array();
        $Pc = array();
        $Mg = "";
        $_c = ($Q != '' ? 'fetch_assoc' : 'fetch_row');
        while ($K = $I->$_c()) {
          if (!$Dd) {
            $Oh = array();
            foreach (
              $K
              as $X
            ) {
              $m = $I->fetch_field();
              if ($n[$m->name]['generated']) {
                $Pc[$m->name] = true;
                continue;
              }
              $Dd[] = $m->name;
              $y = idf_escape($m->name);
              $Oh[] = "$y = VALUES($y)";
            }
            $Mg = ($Kg == "INSERT+UPDATE" ? "\nON DUPLICATE KEY UPDATE " . implode(", ", $Oh) : "") . ";\n";
          }
          if ($_POST["format"] != "sql") {
            if ($Kg == "table") {
              dump_csv($Dd);
              $Kg = "INSERT";
            }
            dump_csv($K);
          } else {
            if (!$sd) $sd = "INSERT INTO " . table($Q) . " (" . implode(", ", array_map('Adminer\idf_escape', $Dd)) . ") VALUES";
            foreach (
              $K
              as $y => $X
            ) {
              if ($Pc[$y]) {
                unset($K[$y]);
                continue;
              }
              $m = $n[$y];
              $K[$y] = ($X !== null ? unconvert_field($m, preg_match(number_type(), $m["type"]) && !preg_match('~\[~', $m["full_type"]) && is_numeric($X) ? $X : q(($X === false ? 0 : $X))) : "NULL");
            }
            $M = ($ae ? "\n" : " ") . "(" . implode(",\t", $K) . ")";
            if (!$Ha) $Ha = $sd . $M;
            elseif (strlen($Ha) + 4 + strlen($M) + strlen($Mg) < $ae) $Ha .= ",$M";
            else {
              echo $Ha . $Mg;
              $Ha = $sd . $M;
            }
          }
        }
        if ($Ha) echo $Ha . $Mg;
      } elseif ($_POST["format"] == "sql") echo "-- " . str_replace("\n", " ", $e->error) . "\n";
      if ($id) echo "SET IDENTITY_INSERT " . table($Q) . " OFF;\n";
    }
  }
  function
  dumpFilename($hd)
  {
    return
      friendly_url($hd != "" ? $hd : (SERVER != "" ? SERVER : "localhost"));
  }
  function
  dumpHeaders($hd, $oe = false)
  {
    $cf = $_POST["output"];
    $tc = (preg_match('~sql~', $_POST["format"]) ? "sql" : ($oe ? "tar" : "csv"));
    header("Content-Type: " . ($cf == "gz" ? "application/x-gzip" : ($tc == "tar" ? "application/x-tar" : ($tc == "sql" || $cf != "file" ? "text/plain" : "text/csv") . "; charset=utf-8")));
    if ($cf == "gz") {
      ob_start(function ($Ig) {
        return
          gzencode($Ig);
      }, 1e6);
    }
    return $tc;
  }
  function
  dumpFooter()
  {
    if ($_POST["format"] == "sql") echo "-- " . gmdate("Y-m-d H:i:s e") . "\n";
  }
  function
  importServerPath()
  {
    return "adminer.sql";
  }
  function
  homepage()
  {
    echo '<p class="links">' . ($_GET["ns"] == "" && support("database") ? '<a href="' . h(ME) . 'database=">' . 'Alter database' . "</a>\n" : ""), (support("scheme") ? "<a href='" . h(ME) . "scheme='>" . ($_GET["ns"] != "" ? 'Alter schema' : 'Create schema') . "</a>\n" : ""), ($_GET["ns"] !== "" ? '<a href="' . h(ME) . 'schema=">' . 'Database schema' . "</a>\n" : ""), (support("privileges") ? "<a href='" . h(ME) . "privileges='>" . 'Privileges' . "</a>\n" : "");
    return
      true;
  }
  function
  navigation($me)
  {
    global $ga, $Nb, $e;
    echo '<h1>
', $this->name(), '<span class="version">
', $ga, ' <a href="https://www.adminer.org/#download"', target_blank(), ' id="version">', (version_compare($ga, $_COOKIE["adminer_version"]) < 0 ? h($_COOKIE["adminer_version"]) : ""), '</a>
</span>
</h1>
';
    if ($me == "auth") {
      $cf = "";
      foreach ((array)$_SESSION["pwds"] as $Qh => $og) {
        foreach (
          $og
          as $O => $Mh
        ) {
          $D = h(get_setting("vendor-$O") ?: $Nb[$Qh]);
          foreach (
            $Mh
            as $V => $F
          ) {
            if ($F !== null) {
              $_b = $_SESSION["db"][$Qh][$O][$V];
              foreach (($_b ? array_keys($_b) : array("")) as $i) $cf .= "<li><a href='" . h(auth_url($Qh, $O, $V, $i)) . "'>($D) " . h($V . ($O != "" ? "@" . $this->serverName($O) : "") . ($i != "" ? " - $i" : "")) . "</a>\n";
            }
          }
        }
      }
      if ($cf) echo "<ul id='logins'>\n$cf</ul>\n" . script("mixin(qs('#logins'), {onmouseover: menuOver, onmouseout: menuOut});");
    } else {
      $S = array();
      if ($_GET["ns"] !== "" && !$me && DB != "") {
        $e->select_db(DB);
        $S = table_status('', true);
      }
      $this->syntaxHighlighting($S);
      $this->databasesPrint($me);
      $ia = array();
      if (DB == "" || !$me) {
        if (support("sql")) {
          $ia[] = "<a href='" . h(ME) . "sql='" . bold(isset($_GET["sql"]) && !isset($_GET["import"])) . ">" . 'SQL command' . "</a>";
          $ia[] = "<a href='" . h(ME) . "import='" . bold(isset($_GET["import"])) . ">" . 'Import' . "</a>";
        }
        $ia[] = "<a href='" . h(ME) . "dump=" . urlencode(isset($_GET["table"]) ? $_GET["table"] : $_GET["select"]) . "' id='dump'" . bold(isset($_GET["dump"])) . ">" . 'Export' . "</a>";
      }
      $nd = $_GET["ns"] !== "" && !$me && DB != "";
      if ($nd) $ia[] = '<a href="' . h(ME) . 'create="' . bold($_GET["create"] === "") . ">" . 'Create table' . "</a>";
      echo ($ia ? "<p class='links'>\n" . implode("\n", $ia) . "\n" : "");
      if ($nd) {
        if ($S) $this->tablesPrint($S);
        else
          echo "<p class='message'>" . 'No tables.' . "</p>\n";
      }
    }
  }
  function
  syntaxHighlighting($S)
  {
    global $e;
    echo
    script_src(preg_replace("~\\?.*~", "", ME) . "?file=jush.js&version=5.0.6");
    if (support("sql")) {
      echo "<script" . nonce() . ">\n";
      if ($S) {
        $Sd = array();
        foreach (
          $S
          as $Q => $U
        ) $Sd[] = preg_quote($Q, '/');
        echo "var jushLinks = { " . JUSH . ": [ '" . js_escape(ME) . (support("table") ? "table=" : "select=") . "\$&', /\\b(" . implode("|", $Sd) . ")\\b/g ] };\n";
        foreach (array("bac", "bra", "sqlite_quo", "mssql_bra") as $X) echo "jushLinks.$X = jushLinks." . JUSH . ";\n";
      }
      echo "</script>\n";
    }
    echo
    script("bodyLoad('" . (is_object($e) ? preg_replace('~^(\d\.?\d).*~s', '\1', $e->server_info) : "") . "'" . ($e->maria ? ", true" : "") . ");");
  }
  function
  databasesPrint($me)
  {
    global $b, $e;
    $h = $this->databases();
    if (DB && $h && !in_array(DB, $h)) array_unshift($h, DB);
    echo '<form action="">
<p id="dbs">
';
    hidden_fields_get();
    $yb = script("mixin(qsl('select'), {onmousedown: dbMouseDown, onchange: dbChange});");
    echo "<span title='" . 'Database' . "'>" . 'DB' . ":</span> " . ($h ? html_select("db", array("" => "") + $h, DB) . $yb : "<input name='db' value='" . h(DB) . "' autocapitalize='off' size='19'>\n"), "<input type='submit' value='" . 'Use' . "'" . ($h ? " class='hidden'" : "") . ">\n";
    foreach (array("import", "sql", "schema", "dump", "privileges") as $X) {
      if (isset($_GET[$X])) {
        echo "<input type='hidden' name='$X' value=''>";
        break;
      }
    }
    echo "</p></form>\n";
  }
  function
  tablesPrint($S)
  {
    echo "<ul id='tables'>" . script("mixin(qs('#tables'), {onmouseover: menuOver, onmouseout: menuOut});");
    foreach (
      $S
      as $Q => $Fg
    ) {
      $D = $this->tableName($Fg);
      if ($D != "") echo '<li><a href="' . h(ME) . 'select=' . urlencode($Q) . '"' . bold($_GET["select"] == $Q || $_GET["edit"] == $Q, "select") . " title='" . 'Select data' . "'>" . 'select' . "</a> ", (support("table") || support("indexes") ? '<a href="' . h(ME) . 'table=' . urlencode($Q) . '"' . bold(in_array($Q, array($_GET["table"], $_GET["create"], $_GET["indexes"], $_GET["foreign"], $_GET["trigger"])), (is_view($Fg) ? "view" : "structure")) . " title='" . 'Show structure' . "'>$D</a>" : "<span>$D</span>") . "\n";
    }
    echo "</ul>\n";
  }
}
$b = (function_exists('adminer_object') ? adminer_object() : new
  Adminer);
$Nb = array("server" => "MySQL / MariaDB") + $Nb;
if (!defined('Adminer\DRIVER')) {
  define('Adminer\DRIVER', "server");
  if (extension_loaded("mysqli")) {
    class
    Db
    extends
    \MySQLi
    {
      var $extension = "MySQLi";
      function
      __construct()
      {
        parent::init();
      }
      function
      connect($O = "", $V = "", $F = "", $xb = null, $sf = null, $wg = null)
      {
        global $b;
        mysqli_report(MYSQLI_REPORT_OFF);
        list($fd, $sf) = explode(":", $O, 2);
        $Dg = $b->connectSsl();
        if ($Dg) $this->ssl_set($Dg['key'], $Dg['cert'], $Dg['ca'], '', '');
        $J = @$this->real_connect(($O != "" ? $fd : ini_get("mysqli.default_host")), ($O . $V != "" ? $V : ini_get("mysqli.default_user")), ($O . $V . $F != "" ? $F : ini_get("mysqli.default_pw")), $xb, (is_numeric($sf) ? $sf : ini_get("mysqli.default_port")), (!is_numeric($sf) ? $sf : $wg), ($Dg ? ($Dg['verify'] !== false ? 2048 : 64) : 0));
        $this->options(MYSQLI_OPT_LOCAL_INFILE, false);
        return $J;
      }
      function
      set_charset($Ma)
      {
        if (parent::set_charset($Ma)) return
          true;
        parent::set_charset('utf8');
        return $this->query("SET NAMES $Ma");
      }
      function
      result($H, $m = 0)
      {
        $I = $this->query($H);
        if (!$I) return
          false;
        $K = $I->fetch_array();
        return $K[$m];
      }
      function
      quote($Ig)
      {
        return "'" . $this->escape_string($Ig) . "'";
      }
    }
  } elseif (extension_loaded("mysql") && !((ini_bool("sql.safe_mode") || ini_bool("mysql.allow_local_infile")) && extension_loaded("pdo_mysql"))) {
    class
    Db
    {
      var $extension = "MySQL", $server_info, $affected_rows, $errno, $error;
      private $link, $result;
      function
      connect($O, $V, $F)
      {
        if (ini_bool("mysql.allow_local_infile")) {
          $this->error = sprintf('Disable %s or enable %s or %s extensions.', "'mysql.allow_local_infile'", "MySQLi", "PDO_MySQL");
          return
            false;
        }
        $this->link = @mysql_connect(($O != "" ? $O : ini_get("mysql.default_host")), ("$O$V" != "" ? $V : ini_get("mysql.default_user")), ("$O$V$F" != "" ? $F : ini_get("mysql.default_password")), true, 131072);
        if ($this->link) $this->server_info = mysql_get_server_info($this->link);
        else $this->error = mysql_error();
        return (bool)$this->link;
      }
      function
      set_charset($Ma)
      {
        if (function_exists('mysql_set_charset')) {
          if (mysql_set_charset($Ma, $this->link)) return
            true;
          mysql_set_charset('utf8', $this->link);
        }
        return $this->query("SET NAMES $Ma");
      }
      function
      quote($Ig)
      {
        return "'" . mysql_real_escape_string($Ig, $this->link) . "'";
      }
      function
      select_db($xb)
      {
        return
          mysql_select_db($xb, $this->link);
      }
      function
      query($H, $zh = false)
      {
        $I = @($zh ? mysql_unbuffered_query($H, $this->link) : mysql_query($H, $this->link));
        $this->error = "";
        if (!$I) {
          $this->errno = mysql_errno($this->link);
          $this->error = mysql_error($this->link);
          return
            false;
        }
        if ($I === true) {
          $this->affected_rows = mysql_affected_rows($this->link);
          $this->info = mysql_info($this->link);
          return
            true;
        }
        return
          new
          Result($I);
      }
      function
      multi_query($H)
      {
        return $this->result = $this->query($H);
      }
      function
      store_result()
      {
        return $this->result;
      }
      function
      next_result()
      {
        return
          false;
      }
      function
      result($H, $m = 0)
      {
        $I = $this->query($H);
        return ($I ? $I->fetch_column($m) : false);
      }
    }
    class
    Result
    {
      var $num_rows;
      private $result, $offset = 0;
      function
      __construct($I)
      {
        $this->result = $I;
        $this->num_rows = mysql_num_rows($I);
      }
      function
      fetch_assoc()
      {
        return
          mysql_fetch_assoc($this->result);
      }
      function
      fetch_row()
      {
        return
          mysql_fetch_row($this->result);
      }
      function
      fetch_column($m)
      {
        return ($this->num_rows ? mysql_result($this->result, 0, $m) : false);
      }
      function
      fetch_field()
      {
        $J = mysql_fetch_field($this->result, $this->offset++);
        $J->orgtable = $J->table;
        $J->orgname = $J->name;
        $J->charsetnr = ($J->blob ? 63 : 0);
        return $J;
      }
      function
      __destruct()
      {
        mysql_free_result($this->result);
      }
    }
  } elseif (extension_loaded("pdo_mysql")) {
    class
    Db
    extends
    PdoDb
    {
      var $extension = "PDO_MySQL";
      function
      connect($O, $V, $F)
      {
        global $b;
        $Oe = array(\PDO::MYSQL_ATTR_LOCAL_INFILE => false);
        $Dg = $b->connectSsl();
        if ($Dg) {
          if ($Dg['key']) $Oe[\PDO::MYSQL_ATTR_SSL_KEY] = $Dg['key'];
          if ($Dg['cert']) $Oe[\PDO::MYSQL_ATTR_SSL_CERT] = $Dg['cert'];
          if ($Dg['ca']) $Oe[\PDO::MYSQL_ATTR_SSL_CA] = $Dg['ca'];
          if (isset($Dg['verify'])) $Oe[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = $Dg['verify'];
        }
        $this->dsn("mysql:charset=utf8;host=" . str_replace(":", ";unix_socket=", preg_replace('~:(\d)~', ';port=\1', $O)), $V, $F, $Oe);
        return
          true;
      }
      function
      set_charset($Ma)
      {
        $this->query("SET NAMES $Ma");
      }
      function
      select_db($xb)
      {
        return $this->query("USE " . idf_escape($xb));
      }
      function
      query($H, $zh = false)
      {
        $this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, !$zh);
        return
          parent::query($H, $zh);
      }
    }
  }
  class
  Driver
  extends
  SqlDriver
  {
    static $wf = array("MySQLi", "MySQL", "PDO_MySQL");
    static $Bd = "sql";
    var $unsigned = array("unsigned", "zerofill", "unsigned zerofill");
    var $operators = array("=", "<", ">", "<=", ">=", "!=", "LIKE", "LIKE %%", "REGEXP", "IN", "FIND_IN_SET", "IS NULL", "NOT LIKE", "NOT REGEXP", "NOT IN", "IS NOT NULL", "SQL");
    var $functions = array("char_length", "date", "from_unixtime", "lower", "round", "floor", "ceil", "sec_to_time", "time_to_sec", "upper");
    var $grouping = array("avg", "count", "count distinct", "group_concat", "max", "min", "sum");
    function
    __construct($e)
    {
      parent::__construct($e);
      $this->types = array('Numbers' => array("tinyint" => 3, "smallint" => 5, "mediumint" => 8, "int" => 10, "bigint" => 20, "decimal" => 66, "float" => 12, "double" => 21), 'Date and time' => array("date" => 10, "datetime" => 19, "timestamp" => 19, "time" => 10, "year" => 4), 'Strings' => array("char" => 255, "varchar" => 65535, "tinytext" => 255, "text" => 65535, "mediumtext" => 16777215, "longtext" => 4294967295), 'Lists' => array("enum" => 65535, "set" => 64), 'Binary' => array("bit" => 20, "binary" => 255, "varbinary" => 65535, "tinyblob" => 255, "blob" => 65535, "mediumblob" => 16777215, "longblob" => 4294967295), 'Geometry' => array("geometry" => 0, "point" => 0, "linestring" => 0, "polygon" => 0, "multipoint" => 0, "multilinestring" => 0, "multipolygon" => 0, "geometrycollection" => 0),);
      $this->editFunctions = array(array("char" => "md5/sha1/password/encrypt/uuid", "binary" => "md5/sha1", "date|time" => "now",), array(number_type() => "+/-", "date" => "+ interval/- interval", "time" => "addtime/subtime", "char|text" => "concat",));
      if (min_version('5.7.8', 10.2, $e)) $this->types['Strings']["json"] = 4294967295;
      if (min_version('', 10.7, $e)) {
        $this->types['Strings']["uuid"] = 128;
        $this->editFunctions[0]['uuid'] = 'uuid';
      }
      if (min_version(9, '', $e)) {
        $this->types['Numbers']["vector"] = 16383;
        $this->editFunctions[0]['vector'] = 'string_to_vector';
      }
      if (min_version(5.7, 10.2, $e)) $this->generated = array("STORED", "VIRTUAL");
    }
    function
    unconvertFunction($m)
    {
      return (preg_match("~binary~", $m["type"]) ? "<code class='jush-sql'>UNHEX</code>" : ($m["type"] == "bit" ? doc_link(array('sql' => 'bit-value-literals.html'), "<code>b''</code>") : (preg_match("~geometry|point|linestring|polygon~", $m["type"]) ? "<code class='jush-sql'>GeomFromText</code>" : "")));
    }
    function
    insert($Q, $P)
    {
      return ($P ? parent::insert($Q, $P) : queries("INSERT INTO " . table($Q) . " ()\nVALUES ()"));
    }
    function
    insertUpdate($Q, $L, $_f)
    {
      $d = array_keys(reset($L));
      $yf = "INSERT INTO " . table($Q) . " (" . implode(", ", $d) . ") VALUES\n";
      $Oh = array();
      foreach (
        $d
        as $y
      ) $Oh[$y] = "$y = VALUES($y)";
      $Mg = "\nON DUPLICATE KEY UPDATE " . implode(", ", $Oh);
      $Oh = array();
      $Pd = 0;
      foreach (
        $L
        as $P
      ) {
        $Y = "(" . implode(", ", $P) . ")";
        if ($Oh && (strlen($yf) + $Pd + strlen($Y) + strlen($Mg) > 1e6)) {
          if (!queries($yf . implode(",\n", $Oh) . $Mg)) return
            false;
          $Oh = array();
          $Pd = 0;
        }
        $Oh[] = $Y;
        $Pd += strlen($Y) + 2;
      }
      return
        queries($yf . implode(",\n", $Oh) . $Mg);
    }
    function
    slowQuery($H, $fh)
    {
      if (min_version('5.7.8', '10.1.2')) {
        if ($this->conn->maria) return "SET STATEMENT max_statement_time=$fh FOR $H";
        elseif (preg_match('~^(SELECT\b)(.+)~is', $H, $B)) return "$B[1] /*+ MAX_EXECUTION_TIME(" . ($fh * 1000) . ") */ $B[2]";
      }
    }
    function
    convertSearch($jd, $X, $m)
    {
      return (preg_match('~char|text|enum|set~', $m["type"]) && !preg_match("~^utf8~", $m["collation"]) && preg_match('~[\x80-\xFF]~', $X['val']) ? "CONVERT($jd USING " . charset($this->conn) . ")" : $jd);
    }
    function
    warnings()
    {
      $I = $this->conn->query("SHOW WARNINGS");
      if ($I && $I->num_rows) {
        ob_start();
        select($I);
        return
          ob_get_clean();
      }
    }
    function
    tableHelp($D, $_d = false)
    {
      $Vd = $this->conn->maria;
      if (information_schema(DB)) return
        strtolower("information-schema-" . ($Vd ? "$D-table/" : str_replace("_", "-", $D) . "-table.html"));
      if (DB == "mysql") return ($Vd ? "mysql$D-table/" : "system-schema.html");
    }
    function
    hasCStyleEscapes()
    {
      static $Ia;
      if ($Ia === null) {
        $Cg = $this->conn->result("SHOW VARIABLES LIKE 'sql_mode'", 1);
        $Ia = (strpos($Cg, 'NO_BACKSLASH_ESCAPES') === false);
      }
      return $Ia;
    }
  }
  function
  idf_escape($jd)
  {
    return "`" . str_replace("`", "``", $jd) . "`";
  }
  function
  table($jd)
  {
    return
      idf_escape($jd);
  }
  function
  connect($pb)
  {
    global $Nb;
    $e = new
      Db;
    if ($e->connect($pb[0], $pb[1], $pb[2])) {
      $e->set_charset(charset($e));
      $e->query("SET sql_quote_show_create = 1, autocommit = 1");
      $e->maria = preg_match('~MariaDB~', $e->server_info);
      $Nb[DRIVER] = ($e->maria ? "MariaDB" : "MySQL");
      return $e;
    }
    $J = $e->error;
    if (function_exists('iconv') && !is_utf8($J) && strlen($M = iconv("windows-1250", "utf-8", $J)) > strlen($J)) $J = $M;
    return $J;
  }
  function
  get_databases($Fc)
  {
    $J = get_session("dbs");
    if ($J === null) {
      $H = "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";
      $J = ($Fc ? slow_query($H) : get_vals($H));
      restart_session();
      set_session("dbs", $J);
      stop_session();
    }
    return $J;
  }
  function
  limit($H, $Z, $z, $Be = 0, $mg = " ")
  {
    return " $H$Z" . ($z !== null ? $mg . "LIMIT $z" . ($Be ? " OFFSET $Be" : "") : "");
  }
  function
  limit1($Q, $H, $Z, $mg = "\n")
  {
    return
      limit($H, $Z, 1, 0, $mg);
  }
  function
  db_collation($i, $Za)
  {
    $J = null;
    $g = get_val("SHOW CREATE DATABASE " . idf_escape($i), 1);
    if (preg_match('~ COLLATE ([^ ]+)~', $g, $B)) $J = $B[1];
    elseif (preg_match('~ CHARACTER SET ([^ ]+)~', $g, $B)) $J = $Za[$B[1]][-1];
    return $J;
  }
  function
  engines()
  {
    $J = array();
    foreach (get_rows("SHOW ENGINES") as $K) {
      if (preg_match("~YES|DEFAULT~", $K["Support"])) $J[] = $K["Engine"];
    }
    return $J;
  }
  function
  logged_user()
  {
    return
      get_val("SELECT USER()");
  }
  function
  tables_list()
  {
    return
      get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");
  }
  function
  count_tables($h)
  {
    $J = array();
    foreach (
      $h
      as $i
    ) $J[$i] = count(get_vals("SHOW TABLES IN " . idf_escape($i)));
    return $J;
  }
  function
  table_status($D = "", $yc = false)
  {
    $J = array();
    foreach (get_rows($yc ? "SELECT TABLE_NAME AS Name, ENGINE AS Engine, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() " . ($D != "" ? "AND TABLE_NAME = " . q($D) : "ORDER BY Name") : "SHOW TABLE STATUS" . ($D != "" ? " LIKE " . q(addcslashes($D, "%_\\")) : "")) as $K) {
      if ($K["Engine"] == "InnoDB") $K["Comment"] = preg_replace('~(?:(.+); )?InnoDB free: .*~', '\1', $K["Comment"]);
      if (!isset($K["Engine"])) $K["Comment"] = "";
      if ($D != "") {
        $K["Name"] = $D;
        return $K;
      }
      $J[$K["Name"]] = $K;
    }
    return $J;
  }
  function
  is_view($R)
  {
    return $R["Engine"] === null;
  }
  function
  fk_support($R)
  {
    return
      preg_match('~InnoDB|IBMDB2I~i', $R["Engine"]) || (preg_match('~NDB~i', $R["Engine"]) && min_version(5.6));
  }
  function
  fields($Q)
  {
    global $e;
    $Vd = $e->maria;
    $J = array();
    foreach (get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = " . q($Q) . " ORDER BY ORDINAL_POSITION") as $K) {
      $m = $K["COLUMN_NAME"];
      $U = $K["COLUMN_TYPE"];
      $Qc = $K["GENERATION_EXPRESSION"];
      $vc = $K["EXTRA"];
      preg_match('~^(VIRTUAL|PERSISTENT|STORED)~', $vc, $Pc);
      preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~', $U, $Xd);
      $j = $K["COLUMN_DEFAULT"];
      if ($j != "") {
        $zd = preg_match('~text|json~', $Xd[1]);
        if (!$Vd && $zd) $j = preg_replace("~^(_\w+)?('.*')$~", '\2', stripslashes($j));
        if ($Vd || $zd) {
          $j = ($j == "NULL" ? null : preg_replace_callback("~^'(.*)'$~", function ($B) {
            return
              stripslashes(str_replace("''", "'", $B[1]));
          }, $j));
        }
        if (!$Vd && preg_match('~binary~', $Xd[1]) && preg_match('~^0x(\w*)$~', $j, $B)) $j = pack("H*", $B[1]);
      }
      $J[$m] = array("field" => $m, "full_type" => $U, "type" => $Xd[1], "length" => $Xd[2], "unsigned" => ltrim($Xd[3] . $Xd[4]), "default" => ($Pc ? ($Vd ? $Qc : stripslashes($Qc)) : $j), "null" => ($K["IS_NULLABLE"] == "YES"), "auto_increment" => ($vc == "auto_increment"), "on_update" => (preg_match('~\bon update (\w+)~i', $vc, $B) ? $B[1] : ""), "collation" => $K["COLLATION_NAME"], "privileges" => array_flip(explode(",", "$K[PRIVILEGES],where,order")), "comment" => $K["COLUMN_COMMENT"], "primary" => ($K["COLUMN_KEY"] == "PRI"), "generated" => ($Pc[1] == "PERSISTENT" ? "STORED" : $Pc[1]),);
    }
    return $J;
  }
  function
  indexes($Q, $f = null)
  {
    $J = array();
    foreach (get_rows("SHOW INDEX FROM " . table($Q), $f) as $K) {
      $D = $K["Key_name"];
      $J[$D]["type"] = ($D == "PRIMARY" ? "PRIMARY" : ($K["Index_type"] == "FULLTEXT" ? "FULLTEXT" : ($K["Non_unique"] ? ($K["Index_type"] == "SPATIAL" ? "SPATIAL" : "INDEX") : "UNIQUE")));
      $J[$D]["columns"][] = $K["Column_name"];
      $J[$D]["lengths"][] = ($K["Index_type"] == "SPATIAL" ? null : $K["Sub_part"]);
      $J[$D]["descs"][] = null;
    }
    return $J;
  }
  function
  foreign_keys($Q)
  {
    global $k;
    static $of = '(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';
    $J = array();
    $nb = get_val("SHOW CREATE TABLE " . table($Q), 1);
    if ($nb) {
      preg_match_all("~CONSTRAINT ($of) FOREIGN KEY ?\\(((?:$of,? ?)+)\\) REFERENCES ($of)(?:\\.($of))? \\(((?:$of,? ?)+)\\)(?: ON DELETE ($k->onActions))?(?: ON UPDATE ($k->onActions))?~", $nb, $Yd, PREG_SET_ORDER);
      foreach (
        $Yd
        as $B
      ) {
        preg_match_all("~$of~", $B[2], $yg);
        preg_match_all("~$of~", $B[5], $Yg);
        $J[idf_unescape($B[1])] = array("db" => idf_unescape($B[4] != "" ? $B[3] : $B[4]), "table" => idf_unescape($B[4] != "" ? $B[4] : $B[3]), "source" => array_map('Adminer\idf_unescape', $yg[0]), "target" => array_map('Adminer\idf_unescape', $Yg[0]), "on_delete" => ($B[6] ?: "RESTRICT"), "on_update" => ($B[7] ?: "RESTRICT"),);
      }
    }
    return $J;
  }
  function
  view($D)
  {
    return
      array("select" => preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU', '', get_val("SHOW CREATE VIEW " . table($D), 1)));
  }
  function
  collations()
  {
    $J = array();
    foreach (get_rows("SHOW COLLATION") as $K) {
      if ($K["Default"]) $J[$K["Charset"]][-1] = $K["Collation"];
      else $J[$K["Charset"]][] = $K["Collation"];
    }
    ksort($J);
    foreach (
      $J
      as $y => $X
    ) asort($J[$y]);
    return $J;
  }
  function
  information_schema($i)
  {
    return ($i == "information_schema") || (min_version(5.5) && $i == "performance_schema");
  }
  function
  error()
  {
    global $e;
    return
      h(preg_replace('~^You have an error.*syntax to use~U', "Syntax error", $e->error));
  }
  function
  create_database($i, $Ya)
  {
    return
      queries("CREATE DATABASE " . idf_escape($i) . ($Ya ? " COLLATE " . q($Ya) : ""));
  }
  function
  drop_databases($h)
  {
    $J = apply_queries("DROP DATABASE", $h, 'Adminer\idf_escape');
    restart_session();
    set_session("dbs", null);
    return $J;
  }
  function
  rename_database($D, $Ya)
  {
    $J = false;
    if (create_database($D, $Ya)) {
      $S = array();
      $Th = array();
      foreach (tables_list() as $Q => $U) {
        if ($U == 'VIEW') $Th[] = $Q;
        else $S[] = $Q;
      }
      $J = (!$S && !$Th) || move_tables($S, $Th, $D);
      drop_databases($J ? array(DB) : array());
    }
    return $J;
  }
  function
  auto_increment()
  {
    $wa = " PRIMARY KEY";
    if ($_GET["create"] != "" && $_POST["auto_increment_col"]) {
      foreach (indexes($_GET["create"]) as $w) {
        if (in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"], $w["columns"], true)) {
          $wa = "";
          break;
        }
        if ($w["type"] == "PRIMARY") $wa = " UNIQUE";
      }
    }
    return " AUTO_INCREMENT$wa";
  }
  function
  alter_table($Q, $D, $n, $Hc, $db, $cc, $Ya, $va, $kf)
  {
    global $e;
    $qa = array();
    foreach (
      $n
      as $m
    ) {
      if ($m[1]) {
        $j = $m[1][3];
        if (preg_match('~ GENERATED~', $j)) {
          $m[1][3] = ($e->maria ? "" : $m[1][2]);
          $m[1][2] = $j;
        }
        $qa[] = ($Q != "" ? ($m[0] != "" ? "CHANGE " . idf_escape($m[0]) : "ADD") : " ") . " " . implode($m[1]) . ($Q != "" ? $m[2] : "");
      } else $qa[] = "DROP " . idf_escape($m[0]);
    }
    $qa = array_merge($qa, $Hc);
    $Fg = ($db !== null ? " COMMENT=" . q($db) : "") . ($cc ? " ENGINE=" . q($cc) : "") . ($Ya ? " COLLATE " . q($Ya) : "") . ($va != "" ? " AUTO_INCREMENT=$va" : "");
    if ($Q == "") return
      queries("CREATE TABLE " . table($D) . " (\n" . implode(",\n", $qa) . "\n)$Fg$kf");
    if ($Q != $D) $qa[] = "RENAME TO " . table($D);
    if ($Fg) $qa[] = ltrim($Fg);
    return ($qa || $kf ? queries("ALTER TABLE " . table($Q) . "\n" . implode(",\n", $qa) . $kf) : true);
  }
  function
  alter_indexes($Q, $qa)
  {
    foreach (
      $qa
      as $y => $X
    ) $qa[$y] = ($X[2] == "DROP" ? "\nDROP INDEX " . idf_escape($X[1]) : "\nADD $X[0] " . ($X[0] == "PRIMARY" ? "KEY " : "") . ($X[1] != "" ? idf_escape($X[1]) . " " : "") . "(" . implode(", ", $X[2]) . ")");
    return
      queries("ALTER TABLE " . table($Q) . implode(",", $qa));
  }
  function
  truncate_tables($S)
  {
    return
      apply_queries("TRUNCATE TABLE", $S);
  }
  function
  drop_views($Th)
  {
    return
      queries("DROP VIEW " . implode(", ", array_map('Adminer\table', $Th)));
  }
  function
  drop_tables($S)
  {
    return
      queries("DROP TABLE " . implode(", ", array_map('Adminer\table', $S)));
  }
  function
  move_tables($S, $Th, $Yg)
  {
    global $e;
    $Tf = array();
    foreach (
      $S
      as $Q
    ) $Tf[] = table($Q) . " TO " . idf_escape($Yg) . "." . table($Q);
    if (!$Tf || queries("RENAME TABLE " . implode(", ", $Tf))) {
      $Db = array();
      foreach (
        $Th
        as $Q
      ) $Db[table($Q)] = view($Q);
      $e->select_db($Yg);
      $i = idf_escape(DB);
      foreach (
        $Db
        as $D => $Sh
      ) {
        if (!queries("CREATE VIEW $D AS " . str_replace(" $i.", " ", $Sh["select"])) || !queries("DROP VIEW $i.$D")) return
          false;
      }
      return
        true;
    }
    return
      false;
  }
  function
  copy_tables($S, $Th, $Yg)
  {
    queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");
    foreach (
      $S
      as $Q
    ) {
      $D = ($Yg == DB ? table("copy_$Q") : idf_escape($Yg) . "." . table($Q));
      if (($_POST["overwrite"] && !queries("\nDROP TABLE IF EXISTS $D")) || !queries("CREATE TABLE $D LIKE " . table($Q)) || !queries("INSERT INTO $D SELECT * FROM " . table($Q))) return
        false;
      foreach (get_rows("SHOW TRIGGERS LIKE " . q(addcslashes($Q, "%_\\"))) as $K) {
        $th = $K["Trigger"];
        if (!queries("CREATE TRIGGER " . ($Yg == DB ? idf_escape("copy_$th") : idf_escape($Yg) . "." . idf_escape($th)) . " $K[Timing] $K[Event] ON $D FOR EACH ROW\n$K[Statement];")) return
          false;
      }
    }
    foreach (
      $Th
      as $Q
    ) {
      $D = ($Yg == DB ? table("copy_$Q") : idf_escape($Yg) . "." . table($Q));
      $Sh = view($Q);
      if (($_POST["overwrite"] && !queries("DROP VIEW IF EXISTS $D")) || !queries("CREATE VIEW $D AS $Sh[select]")) return
        false;
    }
    return
      true;
  }
  function
  trigger($D)
  {
    if ($D == "") return
      array();
    $L = get_rows("SHOW TRIGGERS WHERE `Trigger` = " . q($D));
    return
      reset($L);
  }
  function
  triggers($Q)
  {
    $J = array();
    foreach (get_rows("SHOW TRIGGERS LIKE " . q(addcslashes($Q, "%_\\"))) as $K) $J[$K["Trigger"]] = array($K["Timing"], $K["Event"]);
    return $J;
  }
  function
  trigger_options()
  {
    return
      array("Timing" => array("BEFORE", "AFTER"), "Event" => array("INSERT", "UPDATE", "DELETE"), "Type" => array("FOR EACH ROW"),);
  }
  function
  routine($D, $U)
  {
    global $k;
    $oa = array("bool", "boolean", "integer", "double precision", "real", "dec", "numeric", "fixed", "national char", "national varchar");
    $zg = "(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";
    $ec = $k->enumLength;
    $xh = "((" . implode("|", array_merge(array_keys($k->types()), $oa)) . ")\\b(?:\\s*\\(((?:[^'\")]|$ec)++)\\))?" . "\\s*(zerofill\\s*)?(unsigned(?:\\s+zerofill)?)?)(?:\\s*(?:CHARSET|CHARACTER\\s+SET)\\s*['\"]?([^'\"\\s,]+)['\"]?)?";
    $of = "$zg*(" . ($U == "FUNCTION" ? "" : $k->inout) . ")?\\s*(?:`((?:[^`]|``)*)`\\s*|\\b(\\S+)\\s+)$xh";
    $g = get_val("SHOW CREATE $U " . idf_escape($D), 2);
    preg_match("~\\(((?:$of\\s*,?)*)\\)\\s*" . ($U == "FUNCTION" ? "RETURNS\\s+$xh\\s+" : "") . "(.*)~is", $g, $B);
    $n = array();
    preg_match_all("~$of\\s*,?~is", $B[1], $Yd, PREG_SET_ORDER);
    foreach (
      $Yd
      as $ff
    ) $n[] = array("field" => str_replace("``", "`", $ff[2]) . $ff[3], "type" => strtolower($ff[5]), "length" => preg_replace_callback("~$ec~s", 'Adminer\normalize_enum', $ff[6]), "unsigned" => strtolower(preg_replace('~\s+~', ' ', trim("$ff[8] $ff[7]"))), "null" => 1, "full_type" => $ff[4], "inout" => strtoupper($ff[1]), "collation" => strtolower($ff[9]),);
    return
      array("fields" => $n, "comment" => get_val("SELECT ROUTINE_COMMENT FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_NAME = " . q($D)),) + ($U != "FUNCTION" ? array("definition" => $B[11]) : array("returns" => array("type" => $B[12], "length" => $B[13], "unsigned" => $B[15], "collation" => $B[16]), "definition" => $B[17], "language" => "SQL",));
  }
  function
  routines()
  {
    return
      get_rows("SELECT ROUTINE_NAME AS SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");
  }
  function
  routine_languages()
  {
    return
      array();
  }
  function
  routine_id($D, $K)
  {
    return
      idf_escape($D);
  }
  function
  last_id()
  {
    return
      get_val("SELECT LAST_INSERT_ID()");
  }
  function
  explain($e, $H)
  {
    return $e->query("EXPLAIN " . (min_version(5.1) && !min_version(5.7) ? "PARTITIONS " : "") . $H);
  }
  function
  found_rows($R, $Z)
  {
    return ($Z || $R["Engine"] != "InnoDB" ? null : $R["Rows"]);
  }
  function
  create_sql($Q, $va, $Kg)
  {
    $J = get_val("SHOW CREATE TABLE " . table($Q), 1);
    if (!$va) $J = preg_replace('~ AUTO_INCREMENT=\d+~', '', $J);
    return $J;
  }
  function
  truncate_sql($Q)
  {
    return "TRUNCATE " . table($Q);
  }
  function
  use_sql($xb)
  {
    return "USE " . idf_escape($xb);
  }
  function
  trigger_sql($Q)
  {
    $J = "";
    foreach (get_rows("SHOW TRIGGERS LIKE " . q(addcslashes($Q, "%_\\")), null, "-- ") as $K) $J .= "\nCREATE TRIGGER " . idf_escape($K["Trigger"]) . " $K[Timing] $K[Event] ON " . table($K["Table"]) . " FOR EACH ROW\n$K[Statement];;\n";
    return $J;
  }
  function
  show_variables()
  {
    return
      get_key_vals("SHOW VARIABLES");
  }
  function
  process_list()
  {
    return
      get_rows("SHOW FULL PROCESSLIST");
  }
  function
  show_status()
  {
    return
      get_key_vals("SHOW STATUS");
  }
  function
  convert_field($m)
  {
    if (preg_match("~binary~", $m["type"])) return "HEX(" . idf_escape($m["field"]) . ")";
    if ($m["type"] == "bit") return "BIN(" . idf_escape($m["field"]) . " + 0)";
    if (preg_match("~geometry|point|linestring|polygon~", $m["type"])) return (min_version(8) ? "ST_" : "") . "AsWKT(" . idf_escape($m["field"]) . ")";
  }
  function
  unconvert_field($m, $J)
  {
    if (preg_match("~binary~", $m["type"])) $J = "UNHEX($J)";
    if ($m["type"] == "bit") $J = "CONVERT(b$J, UNSIGNED)";
    if (preg_match("~geometry|point|linestring|polygon~", $m["type"])) {
      $yf = (min_version(8) ? "ST_" : "");
      $J = $yf . "GeomFromText($J, $yf" . "SRID($m[field]))";
    }
    return $J;
  }
  function
  support($zc)
  {
    return !preg_match("~scheme|sequence|type|view_trigger|materializedview" . (min_version(8) ? "" : "|descidx" . (min_version(5.1) ? "" : "|event|partitioning")) . (min_version('8.0.16', '10.2.1') ? "" : "|check") . "~", $zc);
  }
  function
  kill_process($X)
  {
    return
      queries("KILL " . number($X));
  }
  function
  connection_id()
  {
    return "SELECT CONNECTION_ID()";
  }
  function
  max_connections()
  {
    return
      get_val("SELECT @@max_connections");
  }
}
define('Adminer\JUSH', Driver::$Bd);
define('Adminer\SERVER', $_GET[DRIVER]);
define('Adminer\DB', $_GET["db"]);
define('Adminer\ME', preg_replace('~\?.*~', '', relative_uri()) . '?' . (sid() ? SID . '&' : '') . (SERVER !== null ? DRIVER . "=" . urlencode(SERVER) . '&' : '') . (isset($_GET["username"]) ? "username=" . urlencode($_GET["username"]) . '&' : '') . (DB != "" ? 'db=' . urlencode(DB) . '&' . (isset($_GET["ns"]) ? "ns=" . urlencode($_GET["ns"]) . "&" : "") : ''));
if (!ob_get_level()) ob_start(null, 4096);
function
page_header($hh, $l = "", $Ga = array(), $ih = "")
{
  global $ca, $ga, $b, $Nb;
  page_headers();
  if (is_ajax() && $l) {
    page_messages($l);
    exit;
  }
  $jh = $hh . ($ih != "" ? ": $ih" : "");
  $kh = strip_tags($jh . (SERVER != "" && SERVER != "localhost" ? h(" - " . SERVER) : "") . " - " . $b->name());
  echo '<!DOCTYPE html>
<html lang="en" dir="ltr">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width">
<title>', $kh, '</title>
<link rel="stylesheet" href="', h(preg_replace("~\\?.*~", "", ME) . "?file=default.css&version=5.0.6"), '">
';
  $rb = $b->css();
  $ub = (count($rb) == 1 ? !!preg_match('~-dark~', $rb[0]) : null);
  if ($ub !== false) echo "<link rel='stylesheet'" . ($ub ? "" : " media='(prefers-color-scheme: dark)'") . " href='" . h(preg_replace("~\\?.*~", "", ME) . "?file=dark.css&version=5.0.6") . "'>\n";
  echo "<meta name='color-scheme' content='" . ($ub === null ? "light dark" : ($ub ? "dark" : "light")) . "'>\n", script_src(preg_replace("~\\?.*~", "", ME) . "?file=functions.js&version=5.0.6");
  if ($b->head($ub)) echo "<link rel='shortcut icon' type='image/x-icon' href='" . h(preg_replace("~\\?.*~", "", ME) . "?file=favicon.ico&version=5.0.6") . "'>\n", "<link rel='apple-touch-icon' href='" . h(preg_replace("~\\?.*~", "", ME) . "?file=favicon.ico&version=5.0.6") . "'>\n";
  foreach (
    $rb
    as $X
  ) echo "<link rel='stylesheet'" . (preg_match('~-dark~', $X) && !$ub ? " media='(prefers-color-scheme: dark)'" : "") . " href='" . h($X) . "'>\n";
  echo "\n<body class='" . 'ltr' . " nojs'>\n";
  $o = get_temp_dir() . "/adminer.version";
  if (!$_COOKIE["adminer_version"] && function_exists('openssl_verify') && file_exists($o) && filemtime($o) + 86400 > time()) {
    $Rh = unserialize(file_get_contents($o));
    $Gf = "-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwqWOVuF5uw7/+Z70djoK
RlHIZFZPO0uYRezq90+7Amk+FDNd7KkL5eDve+vHRJBLAszF/7XKXe11xwliIsFs
DFWQlsABVZB3oisKCBEuI71J4kPH8dKGEWR9jDHFw3cWmoH3PmqImX6FISWbG3B8
h7FIx3jEaw5ckVPVTeo5JRm/1DZzJxjyDenXvBQ/6o9DgZKeNDgxwKzH+sw9/YCO
jHnq1cFpOIISzARlrHMa/43YfeNRAm/tsBXjSxembBPo7aQZLAWHmaj5+K19H10B
nCpz9Y++cipkVEiKRGih4ZEvjoFysEOdRLj6WiD/uUNky4xGeA6LaJqh5XpkFkcQ
fQIDAQAB
-----END PUBLIC KEY-----
";
    if (openssl_verify($Rh["version"], base64_decode($Rh["signature"]), $Gf) == 1) $_COOKIE["adminer_version"] = $Rh["version"];
  }
  echo
  script("mixin(document.body, {onkeydown: bodyKeydown, onclick: bodyClick" . (isset($_COOKIE["adminer_version"]) ? "" : ", onload: partial(verifyVersion, '$ga', '" . js_escape(ME) . "', '" . get_token() . "')") . "});
document.body.className = document.body.className.replace(/ nojs/, ' js');
var offlineMessage = '" . js_escape('You are offline.') . "';
var thousandsSeparator = '" . js_escape(',') . "';"), "<div id='help' class='jush-" . JUSH . " jsonly hidden'></div>\n", script("mixin(qs('#help'), {onmouseover: function () { helpOpen = 1; }, onmouseout: helpMouseout});"), "<div id='content'>\n";
  if ($Ga !== null) {
    $_ = substr(preg_replace('~\b(username|db|ns)=[^&]*&~', '', ME), 0, -1);
    echo '<p id="breadcrumb"><a href="' . h($_ ?: ".") . '">' . $Nb[DRIVER] . '</a> » ';
    $_ = substr(preg_replace('~\b(db|ns)=[^&]*&~', '', ME), 0, -1);
    $O = $b->serverName(SERVER);
    $O = ($O != "" ? $O : 'Server');
    if ($Ga === false) echo "$O\n";
    else {
      echo "<a href='" . h($_) . "' accesskey='1' title='Alt+Shift+1'>$O</a> » ";
      if ($_GET["ns"] != "" || (DB != "" && is_array($Ga))) echo '<a href="' . h($_ . "&db=" . urlencode(DB) . (support("scheme") ? "&ns=" : "")) . '">' . h(DB) . '</a> » ';
      if (is_array($Ga)) {
        if ($_GET["ns"] != "") echo '<a href="' . h(substr(ME, 0, -1)) . '">' . h($_GET["ns"]) . '</a> » ';
        foreach (
          $Ga
          as $y => $X
        ) {
          $Fb = (is_array($X) ? $X[1] : h($X));
          if ($Fb != "") echo "<a href='" . h(ME . "$y=") . urlencode(is_array($X) ? $X[0] : $X) . "'>$Fb</a> » ";
        }
      }
      echo "$hh\n";
    }
  }
  echo "<h2>$jh</h2>\n", "<div id='ajaxstatus' class='jsonly hidden'></div>\n";
  restart_session();
  page_messages($l);
  $h = &get_session("dbs");
  if (DB != "" && $h && !in_array(DB, $h, true)) $h = null;
  stop_session();
  define('Adminer\PAGE_HEADER', 1);
}
function
page_headers()
{
  global $b;
  header("Content-Type: text/html; charset=utf-8");
  header("Cache-Control: no-cache");
  header("X-Frame-Options: deny");
  header("X-XSS-Protection: 0");
  header("X-Content-Type-Options: nosniff");
  header("Referrer-Policy: origin-when-cross-origin");
  foreach ($b->csp() as $qb) {
    $bd = array();
    foreach (
      $qb
      as $y => $X
    ) $bd[] = "$y $X";
    header("Content-Security-Policy: " . implode("; ", $bd));
  }
  $b->headers();
}
function
csp()
{
  return
    array(array("script-src" => "'self' 'unsafe-inline' 'nonce-" . get_nonce() . "' 'strict-dynamic'", "connect-src" => "'self'", "frame-src" => "https://www.adminer.org", "object-src" => "'none'", "base-uri" => "'none'", "form-action" => "'self'",),);
}
function
get_nonce()
{
  static $xe;
  if (!$xe) $xe = base64_encode(rand_string());
  return $xe;
}
function
page_messages($l)
{
  $Gh = preg_replace('~^[^?]*~', '', $_SERVER["REQUEST_URI"]);
  $ke = $_SESSION["messages"][$Gh];
  if ($ke) {
    echo "<div class='message'>" . implode("</div>\n<div class='message'>", $ke) . "</div>" . script("messagesPrint();");
    unset($_SESSION["messages"][$Gh]);
  }
  if ($l) echo "<div class='error'>$l</div>\n";
}
function
page_footer($me = "")
{
  global $b, $T;
  echo '</div>

<div id="menu">
';
  $b->navigation($me);
  echo '</div>

';
  if ($me != "auth") echo '<form action="" method="post">
<p class="logout">
<span>', h($_GET["username"]) . "\n", '</span>
<input type="submit" name="logout" value="Logout" id="logout">
<input type="hidden" name="token" value="', $T, '">
</p>
</form>
';
  echo
  script("setupSubmitHighlight(document);");
}
function
int32($qe)
{
  while ($qe >= 2147483648) $qe -= 4294967296;
  while ($qe <= -2147483649) $qe += 4294967296;
  return (int)$qe;
}
function
long2str($W, $Vh)
{
  $M = '';
  foreach (
    $W
    as $X
  ) $M .= pack('V', $X);
  if ($Vh) return
    substr($M, 0, end($W));
  return $M;
}
function
str2long($M, $Vh)
{
  $W = array_values(unpack('V*', str_pad($M, 4 * ceil(strlen($M) / 4), "\0")));
  if ($Vh) $W[] = strlen($M);
  return $W;
}
function
xxtea_mx($ci, $bi, $Ng, $Cd)
{
  return
    int32((($ci >> 5 & 0x7FFFFFF) ^ $bi << 2) + (($bi >> 3 & 0x1FFFFFFF) ^ $ci << 4)) ^ int32(($Ng ^ $bi) + ($Cd ^ $ci));
}
function
encrypt_string($Hg, $y)
{
  if ($Hg == "") return "";
  $y = array_values(unpack("V*", pack("H*", md5($y))));
  $W = str2long($Hg, true);
  $qe = count($W) - 1;
  $ci = $W[$qe];
  $bi = $W[0];
  $G = floor(6 + 52 / ($qe + 1));
  $Ng = 0;
  while ($G-- > 0) {
    $Ng = int32($Ng + 0x9E3779B9);
    $Tb = $Ng >> 2 & 3;
    for ($df = 0; $df < $qe; $df++) {
      $bi = $W[$df + 1];
      $pe = xxtea_mx($ci, $bi, $Ng, $y[$df & 3 ^ $Tb]);
      $ci = int32($W[$df] + $pe);
      $W[$df] = $ci;
    }
    $bi = $W[0];
    $pe = xxtea_mx($ci, $bi, $Ng, $y[$df & 3 ^ $Tb]);
    $ci = int32($W[$qe] + $pe);
    $W[$qe] = $ci;
  }
  return
    long2str($W, false);
}
function
decrypt_string($Hg, $y)
{
  if ($Hg == "") return "";
  if (!$y) return
    false;
  $y = array_values(unpack("V*", pack("H*", md5($y))));
  $W = str2long($Hg, false);
  $qe = count($W) - 1;
  $ci = $W[$qe];
  $bi = $W[0];
  $G = floor(6 + 52 / ($qe + 1));
  $Ng = int32($G * 0x9E3779B9);
  while ($Ng) {
    $Tb = $Ng >> 2 & 3;
    for ($df = $qe; $df > 0; $df--) {
      $ci = $W[$df - 1];
      $pe = xxtea_mx($ci, $bi, $Ng, $y[$df & 3 ^ $Tb]);
      $bi = int32($W[$df] - $pe);
      $W[$df] = $bi;
    }
    $ci = $W[$qe];
    $pe = xxtea_mx($ci, $bi, $Ng, $y[$df & 3 ^ $Tb]);
    $bi = int32($W[0] - $pe);
    $W[0] = $bi;
    $Ng = int32($Ng - 0x9E3779B9);
  }
  return
    long2str($W, true);
}
$e = '';
$ad = $_SESSION["token"];
if (!$ad) $_SESSION["token"] = rand(1, 1e6);
$T = get_token();
$qf = array();
if ($_COOKIE["adminer_permanent"]) {
  foreach (explode(" ", $_COOKIE["adminer_permanent"]) as $X) {
    list($y) = explode(":", $X);
    $qf[$y] = $X;
  }
}
function
add_invalid_login()
{
  global $b;
  $Aa = get_temp_dir() . "/adminer.invalid";
  foreach (glob("$Aa*") ?: array($Aa) as $o) {
    $r = file_open_lock($o);
    if ($r) break;
  }
  if (!$r) $r = file_open_lock("$Aa-" . rand_string());
  if (!$r) return;
  $vd = unserialize(stream_get_contents($r));
  $eh = time();
  if ($vd) {
    foreach (
      $vd
      as $wd => $X
    ) {
      if ($X[0] < $eh) unset($vd[$wd]);
    }
  }
  $ud = &$vd[$b->bruteForceKey()];
  if (!$ud) $ud = array($eh + 30 * 60, 0);
  $ud[1]++;
  file_write_unlock($r, serialize($vd));
}
function
check_invalid_login()
{
  global $b;
  $vd = array();
  foreach (glob(get_temp_dir() . "/adminer.invalid*") as $o) {
    $r = file_open_lock($o);
    if ($r) {
      $vd = unserialize(stream_get_contents($r));
      file_unlock($r);
      break;
    }
  }
  $ud = ($vd ? $vd[$b->bruteForceKey()] : array());
  $we = ($ud[1] > 29 ? $ud[0] - time() : 0);
  if ($we > 0) auth_error(lang(array('Too many unsuccessful logins, try again in %d minute.', 'Too many unsuccessful logins, try again in %d minutes.'), ceil($we / 60)));
}
$ua = $_POST["auth"];
if ($ua) {
  session_regenerate_id();
  $Qh = $ua["driver"];
  $O = $ua["server"];
  $V = $ua["username"];
  $F = (string)$ua["password"];
  $i = $ua["db"];
  set_password($Qh, $O, $V, $F);
  $_SESSION["db"][$Qh][$O][$V][$i] = true;
  if ($ua["permanent"]) {
    $y = implode("-", array_map('base64_encode', array($Qh, $O, $V, $i)));
    $Bf = $b->permanentLogin(true);
    $qf[$y] = "$y:" . base64_encode($Bf ? encrypt_string($F, $Bf) : "");
    cookie("adminer_permanent", implode(" ", $qf));
  }
  if (count($_POST) == 1 || DRIVER != $Qh || SERVER != $O || $_GET["username"] !== $V || DB != $i) redirect(auth_url($Qh, $O, $V, $i));
} elseif ($_POST["logout"] && (!$ad || verify_token())) {
  foreach (array("pwds", "db", "dbs", "queries") as $y) set_session($y, null);
  unset_permanent();
  redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~', '', ME), 0, -1), 'Logout successful.' . ' ' . 'Thanks for using Adminer, consider <a href="https://www.adminer.org/en/donation/">donating</a>.');
} elseif ($qf && !$_SESSION["pwds"]) {
  session_regenerate_id();
  $Bf = $b->permanentLogin();
  foreach (
    $qf
    as $y => $X
  ) {
    list(, $Sa) = explode(":", $X);
    list($Qh, $O, $V, $i) = array_map('base64_decode', explode("-", $y));
    set_password($Qh, $O, $V, decrypt_string(base64_decode($Sa), $Bf));
    $_SESSION["db"][$Qh][$O][$V][$i] = true;
  }
}
function
unset_permanent()
{
  global $qf;
  foreach (
    $qf
    as $y => $X
  ) {
    list($Qh, $O, $V, $i) = array_map('base64_decode', explode("-", $y));
    if ($Qh == DRIVER && $O == SERVER && $V == $_GET["username"] && $i == DB) unset($qf[$y]);
  }
  cookie("adminer_permanent", implode(" ", $qf));
}
function
auth_error($l)
{
  global $b, $ad;
  $pg = session_name();
  if (isset($_GET["username"])) {
    header("HTTP/1.1 403 Forbidden");
    if (($_COOKIE[$pg] || $_GET[$pg]) && !$ad) $l = 'Session expired, please login again.';
    else {
      restart_session();
      add_invalid_login();
      $F = get_password();
      if ($F !== null) {
        if ($F === false) $l .= ($l ? '<br>' : '') . sprintf('Master password expired. <a href="https://www.adminer.org/en/extension/"%s>Implement</a> %s method to make it permanent.', target_blank(), '<code>permanentLogin()</code>');
        set_password(DRIVER, SERVER, $_GET["username"], null);
      }
      unset_permanent();
    }
  }
  if (!$_COOKIE[$pg] && $_GET[$pg] && ini_bool("session.use_only_cookies")) $l = 'Session support must be enabled.';
  $gf = session_get_cookie_params();
  cookie("adminer_key", ($_COOKIE["adminer_key"] ?: rand_string()), $gf["lifetime"]);
  page_header('Login', $l, null);
  echo "<form action='' method='post'>\n", "<div>";
  if (hidden_fields($_POST, array("auth"))) echo "<p class='message'>" . 'The action will be performed after successful login with the same credentials.' . "\n";
  echo "</div>\n";
  $b->loginForm();
  echo "</form>\n";
  page_footer("auth");
  exit;
}
if (isset($_GET["username"]) && !class_exists('Adminer\Db')) {
  unset($_SESSION["pwds"][DRIVER]);
  unset_permanent();
  page_header('No extension', sprintf('None of the supported PHP extensions (%s) are available.', implode(", ", Driver::$wf)), false);
  page_footer("auth");
  exit;
}
stop_session(true);
if (isset($_GET["username"]) && is_string(get_password())) {
  list($fd, $sf) = explode(":", SERVER, 2);
  if (preg_match('~^\s*([-+]?\d+)~', $sf, $B) && ($B[1] < 1024 || $B[1] > 65535)) auth_error('Connecting to privileged ports is not allowed.');
  check_invalid_login();
  $e = connect($b->credentials());
  if (is_object($e)) {
    $k = new
      Driver($e);
    if ($b->operators === null) $b->operators = $k->operators;
    if (isset($e->maria) || $e->cockroach) save_settings(array("vendor-" . SERVER => $Nb[DRIVER]));
  }
}
$Td = null;
if (!is_object($e) || ($Td = $b->login($_GET["username"], get_password())) !== true) {
  $l = (is_string($e) ? nl_br(h($e)) : (is_string($Td) ? $Td : 'Invalid credentials.'));
  auth_error($l . (preg_match('~^ | $~', get_password()) ? '<br>' . 'There is a space in the input password which might be the cause.' : ''));
}
if ($_POST["logout"] && $ad && !verify_token()) {
  page_header('Logout', 'Invalid CSRF token. Send the form again.');
  page_footer("db");
  exit;
}
if ($ua && $_POST["token"]) $_POST["token"] = $T;
$l = '';
if ($_POST) {
  if (!verify_token()) {
    $pd = "max_input_vars";
    $ee = ini_get($pd);
    if (extension_loaded("suhosin")) {
      foreach (array("suhosin.request.max_vars", "suhosin.post.max_vars") as $y) {
        $X = ini_get($y);
        if ($X && (!$ee || $X < $ee)) {
          $pd = $y;
          $ee = $X;
        }
      }
    }
    $l = (!$_POST["token"] && $ee ? sprintf('Maximum number of allowed fields exceeded. Please increase %s.', "'$pd'") : 'Invalid CSRF token. Send the form again.' . ' ' . 'If you did not send this request from Adminer then close this page.');
  }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
  $l = sprintf('Too big POST data. Reduce the data or increase the %s configuration directive.', "'post_max_size'");
  if (isset($_GET["sql"])) $l .= ' ' . 'You can upload a big SQL file via FTP and import it from server.';
}
function
select($I, $f = null, $Ue = array(), $z = 0)
{
  $Sd = array();
  $x = array();
  $d = array();
  $Ea = array();
  $yh = array();
  $J = array();
  for ($u = 0; (!$z || $u < $z) && ($K = $I->fetch_row()); $u++) {
    if (!$u) {
      echo "<div class='scrollable'>\n", "<table class='nowrap odds'>\n", "<thead><tr>";
      for ($Ad = 0; $Ad < count($K); $Ad++) {
        $m = $I->fetch_field();
        $D = $m->name;
        $Te = $m->orgtable;
        $Se = $m->orgname;
        $J[$m->table] = $Te;
        if ($Ue && JUSH == "sql") $Sd[$Ad] = ($D == "table" ? "table=" : ($D == "possible_keys" ? "indexes=" : null));
        elseif ($Te != "") {
          if (!isset($x[$Te])) {
            $x[$Te] = array();
            foreach (indexes($Te, $f) as $w) {
              if ($w["type"] == "PRIMARY") {
                $x[$Te] = array_flip($w["columns"]);
                break;
              }
            }
            $d[$Te] = $x[$Te];
          }
          if (isset($d[$Te][$Se])) {
            unset($d[$Te][$Se]);
            $x[$Te][$Se] = $Ad;
            $Sd[$Ad] = $Te;
          }
        }
        if ($m->charsetnr == 63) $Ea[$Ad] = true;
        $yh[$Ad] = $m->type;
        echo "<th" . ($Te != "" || $m->name != $Se ? " title='" . h(($Te != "" ? "$Te." : "") . $Se) . "'" : "") . ">" . h($D) . ($Ue ? doc_link(array('sql' => "explain-output.html#explain_" . strtolower($D), 'mariadb' => "explain/#the-columns-in-explain-select",)) : "");
      }
      echo "</thead>\n";
    }
    echo "<tr>";
    foreach (
      $K
      as $y => $X
    ) {
      $_ = "";
      if (isset($Sd[$y]) && !$d[$Sd[$y]]) {
        if ($Ue && JUSH == "sql") {
          $Q = $K[array_search("table=", $Sd)];
          $_ = ME . $Sd[$y] . urlencode($Ue[$Q] != "" ? $Ue[$Q] : $Q);
        } else {
          $_ = ME . "edit=" . urlencode($Sd[$y]);
          foreach ($x[$Sd[$y]] as $Wa => $Ad) $_ .= "&where" . urlencode("[" . bracket_escape($Wa) . "]") . "=" . urlencode($K[$Ad]);
        }
      } elseif (is_url($X)) $_ = $X;
      if ($X === null) $X = "<i>NULL</i>";
      elseif ($Ea[$y] && !is_utf8($X)) $X = "<i>" . lang(array('%d byte', '%d bytes'), strlen($X)) . "</i>";
      else {
        $X = h($X);
        if ($yh[$y] == 254) $X = "<code>$X</code>";
      }
      if ($_) $X = "<a href='" . h($_) . "'" . (is_url($_) ? target_blank() : '') . ">$X</a>";
      echo "<td" . ($yh[$y] <= 9 || $yh[$y] == 246 ? " class='number'" : "") . ">$X";
    }
  }
  echo ($u ? "</table>\n</div>" : "<p class='message'>" . 'No rows.') . "\n";
  return $J;
}
function
referencable_primary($kg)
{
  $J = array();
  foreach (table_status('', true) as $Rg => $Q) {
    if ($Rg != $kg && fk_support($Q)) {
      foreach (fields($Rg) as $m) {
        if ($m["primary"]) {
          if ($J[$Rg]) {
            unset($J[$Rg]);
            break;
          }
          $J[$Rg] = $m;
        }
      }
    }
  }
  return $J;
}
function
textarea($D, $Y, $L = 10, $ab = 80)
{
  echo "<textarea name='" . h($D) . "' rows='$L' cols='$ab' class='sqlarea jush-" . JUSH . "' spellcheck='false' wrap='off'>";
  if (is_array($Y)) {
    foreach (
      $Y
      as $X
    ) echo
    h($X[0]) . "\n\n\n";
  } else
    echo
    h($Y);
  echo "</textarea>";
}
function
select_input($ta, $Oe, $Y = "", $Je = "", $rf = "")
{
  $Xg = ($Oe ? "select" : "input");
  return "<$Xg$ta" . ($Oe ? "><option value=''>$rf" . optionlist($Oe, $Y, true) . "</select>" : " size='10' value='" . h($Y) . "' placeholder='$rf'>") . ($Je ? script("qsl('$Xg').onchange = $Je;", "") : "");
}
function
json_row($y, $X = null)
{
  static $Dc = true;
  if ($Dc) echo "{";
  if ($y != "") {
    echo ($Dc ? "" : ",") . "\n\t\"" . addcslashes($y, "\r\n\t\"\\/") . '": ' . ($X !== null ? '"' . addcslashes($X, "\r\n\"\\/") . '"' : 'null');
    $Dc = false;
  } else {
    echo "\n}\n";
    $Dc = true;
  }
}
function
edit_type($y, $m, $Za, $q = array(), $wc = array())
{
  global $k;
  $U = $m["type"];
  echo "<td><select name='" . h($y) . "[type]' class='type' aria-labelledby='label-type'>";
  if ($U && !array_key_exists($U, $k->types()) && !isset($q[$U]) && !in_array($U, $wc)) $wc[] = $U;
  $Jg = $k->structuredTypes();
  if ($q) $Jg['Foreign keys'] = $q;
  echo
  optionlist(array_merge($wc, $Jg), $U), "</select><td>", "<input name='" . h($y) . "[length]' value='" . h($m["length"]) . "' size='3'" . (!$m["length"] && preg_match('~var(char|binary)$~', $U) ? " class='required'" : "") . " aria-labelledby='label-length'>", "<td class='options'>", ($Za ? "<input list='collations' name='" . h($y) . "[collation]'" . (preg_match('~(char|text|enum|set)$~', $U) ? "" : " class='hidden'") . " value='" . h($m["collation"]) . "' placeholder='(" . 'collation' . ")'>" : ''), ($k->unsigned ? "<select name='" . h($y) . "[unsigned]'" . (!$U || preg_match(number_type(), $U) ? "" : " class='hidden'") . '><option>' . optionlist($k->unsigned, $m["unsigned"]) . '</select>' : ''), (isset($m['on_update']) ? "<select name='" . h($y) . "[on_update]'" . (preg_match('~timestamp|datetime~', $U) ? "" : " class='hidden'") . '>' . optionlist(array("" => "(" . 'ON UPDATE' . ")", "CURRENT_TIMESTAMP"), (preg_match('~^CURRENT_TIMESTAMP~i', $m["on_update"]) ? "CURRENT_TIMESTAMP" : $m["on_update"])) . '</select>' : ''), ($q ? "<select name='" . h($y) . "[on_delete]'" . (preg_match("~`~", $U) ? "" : " class='hidden'") . "><option value=''>(" . 'ON DELETE' . ")" . optionlist(explode("|", $k->onActions), $m["on_delete"]) . "</select> " : " ");
}
function
get_partitions_info($Q)
{
  global $e;
  $Mc = "FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = " . q(DB) . " AND TABLE_NAME = " . q($Q);
  $I = $e->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $Mc ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");
  $J = array();
  list($J["partition_by"], $J["partition"], $J["partitions"]) = $I->fetch_row();
  $lf = get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $Mc AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");
  $J["partition_names"] = array_keys($lf);
  $J["partition_values"] = array_values($lf);
  return $J;
}
function
process_length($Pd)
{
  global $k;
  $gc = $k->enumLength;
  return (preg_match("~^\\s*\\(?\\s*$gc(?:\\s*,\\s*$gc)*+\\s*\\)?\\s*\$~", $Pd) && preg_match_all("~$gc~", $Pd, $Yd) ? "(" . implode(",", $Yd[0]) . ")" : preg_replace('~^[0-9].*~', '(\0)', preg_replace('~[^-0-9,+()[\]]~', '', $Pd)));
}
function
process_type($m, $Xa = "COLLATE")
{
  global $k;
  return " $m[type]" . process_length($m["length"]) . (preg_match(number_type(), $m["type"]) && in_array($m["unsigned"], $k->unsigned) ? " $m[unsigned]" : "") . (preg_match('~char|text|enum|set~', $m["type"]) && $m["collation"] ? " $Xa " . (JUSH == "mssql" ? $m["collation"] : q($m["collation"])) : "");
}
function
process_field($m, $wh)
{
  if ($m["on_update"]) $m["on_update"] = str_ireplace("current_timestamp()", "CURRENT_TIMESTAMP", $m["on_update"]);
  return
    array(idf_escape(trim($m["field"])), process_type($wh), ($m["null"] ? " NULL" : " NOT NULL"), default_value($m), (preg_match('~timestamp|datetime~', $m["type"]) && $m["on_update"] ? " ON UPDATE $m[on_update]" : ""), (support("comment") && $m["comment"] != "" ? " COMMENT " . q($m["comment"]) : ""), ($m["auto_increment"] ? auto_increment() : null),);
}
function
default_value($m)
{
  global $k;
  $j = $m["default"];
  $Pc = $m["generated"];
  return ($j === null ? "" : (in_array($Pc, $k->generated) ? (JUSH == "mssql" ? " AS ($j)" . ($Pc == "VIRTUAL" ? "" : " $Pc") . "" : " GENERATED ALWAYS AS ($j) $Pc") : " DEFAULT " . (!preg_match('~^GENERATED ~i', $j) && (preg_match('~char|binary|text|json|enum|set~', $m["type"]) || preg_match('~^(?![a-z])~i', $j)) ? (JUSH == "sql" && preg_match('~text|json~', $m["type"]) ? "(" . q($j) . ")" : q($j)) : str_ireplace("current_timestamp()", "CURRENT_TIMESTAMP", (JUSH == "sqlite" ? "($j)" : $j)))));
}
function
type_class($U)
{
  foreach (array('char' => 'text', 'date' => 'time|year', 'binary' => 'blob', 'enum' => 'set',) as $y => $X) {
    if (preg_match("~$y|$X~", $U)) return " class='$y'";
  }
}
function
edit_fields($n, $Za, $U = "TABLE", $q = array())
{
  global $k;
  $n = array_values($n);
  $Bb = (($_POST ? $_POST["defaults"] : get_setting("defaults")) ? "" : " class='hidden'");
  $eb = (($_POST ? $_POST["comments"] : get_setting("comments")) ? "" : " class='hidden'");
  echo '<thead><tr>
', ($U == "PROCEDURE" ? "<td>" : ""), '<th id="label-name">', ($U == "TABLE" ? 'Column name' : 'Parameter name'), '<td id="label-type">Type<textarea id="enum-edit" rows="4" cols="12" wrap="off" style="display: none;"></textarea>', script("qs('#enum-edit').onblur = editingLengthBlur;"), '<td id="label-length">Length
<td>', 'Options';
  if ($U == "TABLE") echo "<td id='label-null'>NULL\n", "<td><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='" . 'Auto Increment' . "'>AI</abbr>", doc_link(array('sql' => "example-auto-increment.html", 'mariadb' => "auto_increment/",)), "<td id='label-default'$Bb>" . 'Default value', (support("comment") ? "<td id='label-comment'$eb>" . 'Comment' : "");
  echo "<td><input type='image' class='icon' name='add[" . (support("move_col") ? 0 : count($n)) . "]' src='" . h(preg_replace("~\\?.*~", "", ME) . "?file=plus.gif&version=5.0.6") . "' alt='+' title='" . 'Add next' . "'>" . script("row_count = " . count($n) . ";"), "</thead>\n<tbody>\n", script("mixin(qsl('tbody'), {onclick: editingClick, onkeydown: editingKeydown, oninput: editingInput});");
  foreach (
    $n
    as $u => $m
  ) {
    $u++;
    $Ve = $m[($_POST ? "orig" : "field")];
    $Kb = (isset($_POST["add"][$u - 1]) || (isset($m["field"]) && !$_POST["drop_col"][$u])) && (support("drop_col") || $Ve == "");
    echo "<tr" . ($Kb ? "" : " style='display: none;'") . ">\n", ($U == "PROCEDURE" ? "<td>" . html_select("fields[$u][inout]", explode("|", $k->inout), $m["inout"]) : "") . "<th>";
    if ($Kb) echo "<input name='fields[$u][field]' value='" . h($m["field"]) . "' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'>\n";
    echo "<input type='hidden' name='fields[$u][orig]' value='" . h($Ve) . "'>";
    edit_type("fields[$u]", $m, $Za, $q);
    if ($U == "TABLE") echo "<td>" . checkbox("fields[$u][null]", 1, $m["null"], "", "", "block", "label-null"), "<td><label class='block'><input type='radio' name='auto_increment_col' value='$u'" . ($m["auto_increment"] ? " checked" : "") . " aria-labelledby='label-ai'></label>", "<td$Bb>" . ($k->generated ? html_select("fields[$u][generated]", array_merge(array("", "DEFAULT"), $k->generated), $m["generated"]) . " " : checkbox("fields[$u][generated]", 1, $m["generated"], "", "", "", "label-default")), "<input name='fields[$u][default]' value='" . h($m["default"]) . "' aria-labelledby='label-default'>", (support("comment") ? "<td$eb><input name='fields[$u][comment]' value='" . h($m["comment"]) . "' data-maxlength='" . (min_version(5.5) ? 1024 : 255) . "' aria-labelledby='label-comment'>" : "");
    echo "<td>", (support("move_col") ? "<input type='image' class='icon' name='add[$u]' src='" . h(preg_replace("~\\?.*~", "", ME) . "?file=plus.gif&version=5.0.6") . "' alt='+' title='" . 'Add next' . "'> " . "<input type='image' class='icon' name='up[$u]' src='" . h(preg_replace("~\\?.*~", "", ME) . "?file=up.gif&version=5.0.6") . "' alt='↑' title='" . 'Move up' . "'> " . "<input type='image' class='icon' name='down[$u]' src='" . h(preg_replace("~\\?.*~", "", ME) . "?file=down.gif&version=5.0.6") . "' alt='↓' title='" . 'Move down' . "'> " : ""), ($Ve == "" || support("drop_col") ? "<input type='image' class='icon' name='drop_col[$u]' src='" . h(preg_replace("~\\?.*~", "", ME) . "?file=cross.gif&version=5.0.6") . "' alt='x' title='" . 'Remove' . "'>" : "");
  }
}
function
process_fields(&$n)
{
  $Be = 0;
  if ($_POST["up"]) {
    $Jd = 0;
    foreach (
      $n
      as $y => $m
    ) {
      if (key($_POST["up"]) == $y) {
        unset($n[$y]);
        array_splice($n, $Jd, 0, array($m));
        break;
      }
      if (isset($m["field"])) $Jd = $Be;
      $Be++;
    }
  } elseif ($_POST["down"]) {
    $Kc = false;
    foreach (
      $n
      as $y => $m
    ) {
      if (isset($m["field"]) && $Kc) {
        unset($n[key($_POST["down"])]);
        array_splice($n, $Be, 0, array($Kc));
        break;
      }
      if (key($_POST["down"]) == $y) $Kc = $m;
      $Be++;
    }
  } elseif ($_POST["add"]) {
    $n = array_values($n);
    array_splice($n, key($_POST["add"]), 0, array(array()));
  } elseif (!$_POST["drop_col"]) return
    false;
  return
    true;
}
function
normalize_enum($B)
{
  return "'" . str_replace("'", "''", addcslashes(stripcslashes(str_replace($B[0][0] . $B[0][0], $B[0][0], substr($B[0], 1, -1))), '\\')) . "'";
}
function
grant($Rc, $Df, $d, $He)
{
  if (!$Df) return
    true;
  if ($Df == array("ALL PRIVILEGES", "GRANT OPTION")) return ($Rc == "GRANT" ? queries("$Rc ALL PRIVILEGES$He WITH GRANT OPTION") : queries("$Rc ALL PRIVILEGES$He") && queries("$Rc GRANT OPTION$He"));
  return
    queries("$Rc " . preg_replace('~(GRANT OPTION)\([^)]*\)~', '\1', implode("$d, ", $Df) . $d) . $He);
}
function
drop_create($Ob, $g, $Pb, $bh, $Qb, $A, $je, $he, $ie, $Ee, $ue)
{
  if ($_POST["drop"]) query_redirect($Ob, $A, $je);
  elseif ($Ee == "") query_redirect($g, $A, $ie);
  elseif ($Ee != $ue) {
    $ob = queries($g);
    queries_redirect($A, $he, $ob && queries($Ob));
    if ($ob) queries($Pb);
  } else
    queries_redirect($A, $he, queries($bh) && queries($Qb) && queries($Ob) && queries($g));
}
function
create_trigger($He, $K)
{
  $gh = " $K[Timing] $K[Event]" . (preg_match('~ OF~', $K["Event"]) ? " $K[Of]" : "");
  return "CREATE TRIGGER " . idf_escape($K["Trigger"]) . (JUSH == "mssql" ? $He . $gh : $gh . $He) . rtrim(" $K[Type]\n$K[Statement]", ";") . ";";
}
function
create_routine($ag, $K)
{
  global $k;
  $P = array();
  $n = (array)$K["fields"];
  ksort($n);
  foreach (
    $n
    as $m
  ) {
    if ($m["field"] != "") $P[] = (preg_match("~^($k->inout)\$~", $m["inout"]) ? "$m[inout] " : "") . idf_escape($m["field"]) . process_type($m, "CHARACTER SET");
  }
  $Cb = rtrim($K["definition"], ";");
  return "CREATE $ag " . idf_escape(trim($K["name"])) . " (" . implode(", ", $P) . ")" . ($ag == "FUNCTION" ? " RETURNS" . process_type($K["returns"], "CHARACTER SET") : "") . ($K["language"] ? " LANGUAGE $K[language]" : "") . (JUSH == "pgsql" ? " AS " . q($Cb) : "\n$Cb;");
}
function
remove_definer($H)
{
  return
    preg_replace('~^([A-Z =]+) DEFINER=`' . preg_replace('~@(.*)~', '`@`(%|\1)', logged_user()) . '`~', '\1', $H);
}
function
format_foreign_key($p)
{
  global $k;
  $i = $p["db"];
  $ye = $p["ns"];
  return " FOREIGN KEY (" . implode(", ", array_map('Adminer\idf_escape', $p["source"])) . ") REFERENCES " . ($i != "" && $i != $_GET["db"] ? idf_escape($i) . "." : "") . ($ye != "" && $ye != $_GET["ns"] ? idf_escape($ye) . "." : "") . idf_escape($p["table"]) . " (" . implode(", ", array_map('Adminer\idf_escape', $p["target"])) . ")" . (preg_match("~^($k->onActions)\$~", $p["on_delete"]) ? " ON DELETE $p[on_delete]" : "") . (preg_match("~^($k->onActions)\$~", $p["on_update"]) ? " ON UPDATE $p[on_update]" : "");
}
function
tar_file($o, $lh)
{
  $J = pack("a100a8a8a8a12a12", $o, 644, 0, 0, decoct($lh->size), decoct(time()));
  $Ra = 8 * 32;
  for ($u = 0; $u < strlen($J); $u++) $Ra += ord($J[$u]);
  $J .= sprintf("%06o", $Ra) . "\0 ";
  echo $J, str_repeat("\0", 512 - strlen($J));
  $lh->send();
  echo
  str_repeat("\0", 511 - ($lh->size + 511) % 512);
}
function
ini_bytes($pd)
{
  $X = ini_get($pd);
  switch (strtolower(substr($X, -1))) {
    case 'g':
      $X = (int)$X * 1024;
    case 'm':
      $X = (int)$X * 1024;
    case 'k':
      $X = (int)$X * 1024;
  }
  return $X;
}
function
doc_link($nf, $ch = "<sup>?</sup>")
{
  global $e;
  $ng = $e->server_info;
  $Rh = preg_replace('~^(\d\.?\d).*~s', '\1', $ng);
  $Ih = array('sql' => "https://dev.mysql.com/doc/refman/$Rh/en/", 'sqlite' => "https://www.sqlite.org/", 'pgsql' => "https://www.postgresql.org/docs/$Rh/", 'mssql' => "https://learn.microsoft.com/en-us/sql/", 'oracle' => "https://www.oracle.com/pls/topic/lookup?ctx=db" . preg_replace('~^.* (\d+)\.(\d+)\.\d+\.\d+\.\d+.*~s', '\1\2', $ng) . "&id=",);
  if ($e->maria) {
    $Ih['sql'] = "https://mariadb.com/kb/en/";
    $nf['sql'] = (isset($nf['mariadb']) ? $nf['mariadb'] : str_replace(".html", "/", $nf['sql']));
  }
  return ($nf[JUSH] ? "<a href='" . h($Ih[JUSH] . $nf[JUSH] . (JUSH == 'mssql' ? "?view=sql-server-ver$Rh" : "")) . "'" . target_blank() . ">$ch</a>" : "");
}
function
db_size($i)
{
  global $e;
  if (!$e->select_db($i)) return "?";
  $J = 0;
  foreach (table_status() as $R) $J += $R["Data_length"] + $R["Index_length"];
  return
    format_number($J);
}
function
set_utf8mb4($g)
{
  global $e;
  static $P = false;
  if (!$P && preg_match('~\butf8mb4~i', $g)) {
    $P = true;
    echo "SET NAMES " . charset($e) . ";\n\n";
  }
}
if (isset($_GET["status"])) $_GET["variables"] = $_GET["status"];
if (isset($_GET["import"])) $_GET["sql"] = $_GET["import"];
if (!(DB != "" ? $e->select_db(DB) : isset($_GET["sql"]) || isset($_GET["dump"]) || isset($_GET["database"]) || isset($_GET["processlist"]) || isset($_GET["privileges"]) || isset($_GET["user"]) || isset($_GET["variables"]) || $_GET["script"] == "connect" || $_GET["script"] == "kill")) {
  if (DB != "" || $_GET["refresh"]) {
    restart_session();
    set_session("dbs", null);
  }
  if (DB != "") {
    header("HTTP/1.1 404 Not Found");
    page_header('Database' . ": " . h(DB), 'Invalid database.', true);
  } else {
    if ($_POST["db"] && !$l) queries_redirect(substr(ME, 0, -1), 'Databases have been dropped.', drop_databases($_POST["db"]));
    page_header('Select database', $l, false);
    echo "<p class='links'>\n";
    foreach (array('database' => 'Create database', 'privileges' => 'Privileges', 'processlist' => 'Process list', 'variables' => 'Variables', 'status' => 'Status',) as $y => $X) {
      if (support($y)) echo "<a href='" . h(ME) . "$y='>$X</a>\n";
    }
    echo "<p>" . sprintf('%s version: %s through PHP extension %s', $Nb[DRIVER], "<b>" . h($e->server_info) . "</b>", "<b>$e->extension</b>") . "\n", "<p>" . sprintf('Logged as: %s', "<b>" . h(logged_user()) . "</b>") . "\n";
    $h = $b->databases();
    if ($h) {
      $fg = support("scheme");
      $Za = collations();
      echo "<form action='' method='post'>\n", "<table class='checkable odds'>\n", script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"), "<thead><tr>" . (support("database") ? "<td>" : "") . "<th>" . 'Database' . (get_session("dbs") !== null ? " - <a href='" . h(ME) . "refresh=1'>" . 'Refresh' . "</a>" : "") . "<td>" . 'Collation' . "<td>" . 'Tables' . "<td>" . 'Size' . " - <a href='" . h(ME) . "dbsize=1'>" . 'Compute' . "</a>" . script("qsl('a').onclick = partial(ajaxSetHtml, '" . js_escape(ME) . "script=connect');", "") . "</thead>\n";
      $h = ($_GET["dbsize"] ? count_tables($h) : array_flip($h));
      foreach (
        $h
        as $i => $S
      ) {
        $Zf = h(ME) . "db=" . urlencode($i);
        $v = h("Db-" . $i);
        echo "<tr>" . (support("database") ? "<td>" . checkbox("db[]", $i, in_array($i, (array)$_POST["db"]), "", "", "", $v) : ""), "<th><a href='$Zf' id='$v'>" . h($i) . "</a>";
        $Ya = h(db_collation($i, $Za));
        echo "<td>" . (support("database") ? "<a href='$Zf" . ($fg ? "&amp;ns=" : "") . "&amp;database=' title='" . 'Alter database' . "'>$Ya</a>" : $Ya), "<td align='right'><a href='$Zf&amp;schema=' id='tables-" . h($i) . "' title='" . 'Database schema' . "'>" . ($_GET["dbsize"] ? $S : "?") . "</a>", "<td align='right' id='size-" . h($i) . "'>" . ($_GET["dbsize"] ? db_size($i) : "?"), "\n";
      }
      echo "</table>\n", (support("database") ? "<div class='footer'><div>\n" . "<fieldset><legend>" . 'Selected' . " <span id='selected'></span></legend><div>\n" . "<input type='hidden' name='all' value=''>" . script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^db/)); };") . "<input type='submit' name='drop' value='" . 'Drop' . "'>" . confirm() . "\n" . "</div></fieldset>\n" . "</div></div>\n" : ""), "<input type='hidden' name='token' value='$T'>\n", "</form>\n", script("tableCheck();");
    }
  }
  page_footer("db");
  exit;
}
class
TmpFile
{
  private $handler, $size;
  function
  __construct()
  {
    $this->handler = tmpfile();
  }
  function
  write($jb)
  {
    $this->size += strlen($jb);
    fwrite($this->handler, $jb);
  }
  function
  send()
  {
    fseek($this->handler, 0);
    fpassthru($this->handler);
    fclose($this->handler);
  }
}
if (isset($_GET["select"]) && ($_POST["edit"] || $_POST["clone"]) && !$_POST["save"]) $_GET["edit"] = $_GET["select"];
if (isset($_GET["callf"])) $_GET["call"] = $_GET["callf"];
if (isset($_GET["function"])) $_GET["procedure"] = $_GET["function"];
if (isset($_GET["download"])) {
  $a = $_GET["download"];
  $n = fields($a);
  header("Content-Type: application/octet-stream");
  header("Content-Disposition: attachment; filename=" . friendly_url("$a-" . implode("_", $_GET["where"])) . "." . friendly_url($_GET["field"]));
  $N = array(idf_escape($_GET["field"]));
  $I = $k->select($a, $N, array(where($_GET, $n)), $N);
  $K = ($I ? $I->fetch_row() : array());
  echo $k->value($K[0], $n[$_GET["field"]]);
  exit;
} elseif (isset($_GET["table"])) {
  $a = $_GET["table"];
  $n = fields($a);
  if (!$n) $l = error();
  $R = table_status1($a, true);
  $D = $b->tableName($R);
  page_header(($n && is_view($R) ? $R['Engine'] == 'materialized view' ? 'Materialized view' : 'View' : 'Table') . ": " . ($D != "" ? $D : h($a)), $l);
  $Yf = array();
  foreach (
    $n
    as $y => $m
  ) $Yf += $m["privileges"];
  $b->selectLinks($R, (isset($Yf["insert"]) || !support("table") ? "" : null));
  $db = $R["Comment"];
  if ($db != "") echo "<p class='nowrap'>" . 'Comment' . ": " . h($db) . "\n";
  if ($n) $b->tableStructurePrint($n);
  if (support("indexes") && $k->supportsIndex($R)) {
    echo "<h3 id='indexes'>" . 'Indexes' . "</h3>\n";
    $x = indexes($a);
    if ($x) $b->tableIndexesPrint($x);
    echo '<p class="links"><a href="' . h(ME) . 'indexes=' . urlencode($a) . '">' . 'Alter indexes' . "</a>\n";
  }
  if (!is_view($R)) {
    if (fk_support($R)) {
      echo "<h3 id='foreign-keys'>" . 'Foreign keys' . "</h3>\n";
      $q = foreign_keys($a);
      if ($q) {
        echo "<table>\n", "<thead><tr><th>" . 'Source' . "<td>" . 'Target' . "<td>" . 'ON DELETE' . "<td>" . 'ON UPDATE' . "<td></thead>\n";
        foreach (
          $q
          as $D => $p
        ) {
          echo "<tr title='" . h($D) . "'>", "<th><i>" . implode("</i>, <i>", array_map('Adminer\h', $p["source"])) . "</i>";
          $_ = ($p["db"] != "" ? preg_replace('~db=[^&]*~', "db=" . urlencode($p["db"]), ME) : ($p["ns"] != "" ? preg_replace('~ns=[^&]*~', "ns=" . urlencode($p["ns"]), ME) : ME));
          echo "<td><a href='" . h($_ . "table=" . urlencode($p["table"])) . "'>" . ($p["db"] != "" && $p["db"] != DB ? "<b>" . h($p["db"]) . "</b>." : "") . ($p["ns"] != "" && $p["ns"] != $_GET["ns"] ? "<b>" . h($p["ns"]) . "</b>." : "") . h($p["table"]) . "</a>", "(<i>" . implode("</i>, <i>", array_map('Adminer\h', $p["target"])) . "</i>)", "<td>" . h($p["on_delete"]), "<td>" . h($p["on_update"]), '<td><a href="' . h(ME . 'foreign=' . urlencode($a) . '&name=' . urlencode($D)) . '">' . 'Alter' . '</a>', "\n";
        }
        echo "</table>\n";
      }
      echo '<p class="links"><a href="' . h(ME) . 'foreign=' . urlencode($a) . '">' . 'Add foreign key' . "</a>\n";
    }
    if (support("check")) {
      echo "<h3 id='checks'>" . 'Checks' . "</h3>\n";
      $Oa = $k->checkConstraints($a);
      if ($Oa) {
        echo "<table>\n";
        foreach (
          $Oa
          as $y => $X
        ) echo "<tr title='" . h($y) . "'>", "<td><code class='jush-" . JUSH . "'>" . h($X), "<td><a href='" . h(ME . 'check=' . urlencode($a) . '&name=' . urlencode($y)) . "'>" . 'Alter' . "</a>", "\n";
        echo "</table>\n";
      }
      echo '<p class="links"><a href="' . h(ME) . 'check=' . urlencode($a) . '">' . 'Create check' . "</a>\n";
    }
  }
  if (support(is_view($R) ? "view_trigger" : "trigger")) {
    echo "<h3 id='triggers'>" . 'Triggers' . "</h3>\n";
    $vh = triggers($a);
    if ($vh) {
      echo "<table>\n";
      foreach (
        $vh
        as $y => $X
      ) echo "<tr valign='top'><td>" . h($X[0]) . "<td>" . h($X[1]) . "<th>" . h($y) . "<td><a href='" . h(ME . 'trigger=' . urlencode($a) . '&name=' . urlencode($y)) . "'>" . 'Alter' . "</a>\n";
      echo "</table>\n";
    }
    echo '<p class="links"><a href="' . h(ME) . 'trigger=' . urlencode($a) . '">' . 'Add trigger' . "</a>\n";
  }
} elseif (isset($_GET["schema"])) {
  page_header('Database schema', "", array(), h(DB . ($_GET["ns"] ? ".$_GET[ns]" : "")));
  $Sg = array();
  $Tg = array();
  $ea = ($_GET["schema"] ?: $_COOKIE["adminer_schema-" . str_replace(".", "_", DB)]);
  preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~', $ea, $Yd, PREG_SET_ORDER);
  foreach (
    $Yd
    as $u => $B
  ) {
    $Sg[$B[1]] = array($B[2], $B[3]);
    $Tg[] = "\n\t'" . js_escape($B[1]) . "': [ $B[2], $B[3] ]";
  }
  $nh = 0;
  $Ba = -1;
  $eg = array();
  $Qf = array();
  $Nd = array();
  foreach (table_status('', true) as $Q => $R) {
    if (is_view($R)) continue;
    $tf = 0;
    $eg[$Q]["fields"] = array();
    foreach (fields($Q) as $D => $m) {
      $tf += 1.25;
      $m["pos"] = $tf;
      $eg[$Q]["fields"][$D] = $m;
    }
    $eg[$Q]["pos"] = ($Sg[$Q] ?: array($nh, 0));
    foreach ($b->foreignKeys($Q) as $X) {
      if (!$X["db"]) {
        $Ld = $Ba;
        if ($Sg[$Q][1] || $Sg[$X["table"]][1]) $Ld = min(floatval($Sg[$Q][1]), floatval($Sg[$X["table"]][1])) - 1;
        else $Ba -= .1;
        while ($Nd[(string)$Ld]) $Ld -= .0001;
        $eg[$Q]["references"][$X["table"]][(string)$Ld] = array($X["source"], $X["target"]);
        $Qf[$X["table"]][$Q][(string)$Ld] = $X["target"];
        $Nd[(string)$Ld] = true;
      }
    }
    $nh = max($nh, $eg[$Q]["pos"][0] + 2.5 + $tf);
  }
  echo '<div id="schema" style="height: ', $nh, 'em;">
<script', nonce(), '>
qs(\'#schema\').onselectstart = function () { return false; };
var tablePos = {', implode(",", $Tg) . "\n", '};
var em = qs(\'#schema\').offsetHeight / ', $nh, ';
document.onmousemove = schemaMousemove;
document.onmouseup = partialArg(schemaMouseup, \'', js_escape(DB), '\');
</script>
';
  foreach (
    $eg
    as $D => $Q
  ) {
    echo "<div class='table' style='top: " . $Q["pos"][0] . "em; left: " . $Q["pos"][1] . "em;'>", '<a href="' . h(ME) . 'table=' . urlencode($D) . '"><b>' . h($D) . "</b></a>", script("qsl('div').onmousedown = schemaMousedown;");
    foreach ($Q["fields"] as $m) {
      $X = '<span' . type_class($m["type"]) . ' title="' . h($m["full_type"] . ($m["null"] ? " NULL" : '')) . '">' . h($m["field"]) . '</span>';
      echo "<br>" . ($m["primary"] ? "<i>$X</i>" : $X);
    }
    foreach ((array)$Q["references"] as $Zg => $Rf) {
      foreach (
        $Rf
        as $Ld => $Nf
      ) {
        $Md = $Ld - $Sg[$D][1];
        $u = 0;
        foreach ($Nf[0] as $yg) echo "\n<div class='references' title='" . h($Zg) . "' id='refs$Ld-" . ($u++) . "' style='left: $Md" . "em; top: " . $Q["fields"][$yg]["pos"] . "em; padding-top: .5em;'>" . "<div style='border-top: 1px solid gray; width: " . (-$Md) . "em;'></div></div>";
      }
    }
    foreach ((array)$Qf[$D] as $Zg => $Rf) {
      foreach (
        $Rf
        as $Ld => $d
      ) {
        $Md = $Ld - $Sg[$D][1];
        $u = 0;
        foreach (
          $d
          as $Yg
        ) echo "\n<div class='references' title='" . h($Zg) . "' id='refd$Ld-" . ($u++) . "'" . " style='left: $Md" . "em; top: " . $Q["fields"][$Yg]["pos"] . "em; height: 1.25em; background: url(" . h(preg_replace("~\\?.*~", "", ME) . "?file=arrow.gif) no-repeat right center;&version=5.0.6") . "'>" . "<div style='height: .5em; border-bottom: 1px solid gray; width: " . (-$Md) . "em;'></div>" . "</div>";
      }
    }
    echo "\n</div>\n";
  }
  foreach (
    $eg
    as $D => $Q
  ) {
    foreach ((array)$Q["references"] as $Zg => $Rf) {
      foreach (
        $Rf
        as $Ld => $Nf
      ) {
        $le = $nh;
        $ce = -10;
        foreach ($Nf[0] as $y => $yg) {
          $uf = $Q["pos"][0] + $Q["fields"][$yg]["pos"];
          $vf = $eg[$Zg]["pos"][0] + $eg[$Zg]["fields"][$Nf[1][$y]]["pos"];
          $le = min($le, $uf, $vf);
          $ce = max($ce, $uf, $vf);
        }
        echo "<div class='references' id='refl$Ld' style='left: $Ld" . "em; top: $le" . "em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: " . ($ce - $le) . "em;'></div></div>\n";
      }
    }
  }
  echo '</div>
<p class="links"><a href="', h(ME . "schema=" . urlencode($ea)), '" id="schema-link">Permanent link</a>
';
} elseif (isset($_GET["dump"])) {
  $a = $_GET["dump"];
  if ($_POST && !$l) {
    save_settings(array_intersect_key($_POST, array_flip(array("output", "format", "db_style", "types", "routines", "events", "table_style", "auto_increment", "triggers", "data_style"))), "adminer_export");
    $S = array_flip((array)$_POST["tables"]) + array_flip((array)$_POST["data"]);
    $tc = dump_headers((count($S) == 1 ? key($S) : DB), (DB == "" || count($S) > 1));
    $yd = preg_match('~sql~', $_POST["format"]);
    if ($yd) {
      echo "-- Adminer $ga " . $Nb[DRIVER] . " " . str_replace("\n", " ", $e->server_info) . " dump\n\n";
      if (JUSH == "sql") {
        echo "SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
" . ($_POST["data_style"] ? "SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
" : "") . "
";
        $e->query("SET time_zone = '+00:00'");
        $e->query("SET sql_mode = ''");
      }
    }
    $Kg = $_POST["db_style"];
    $h = array(DB);
    if (DB == "") {
      $h = $_POST["databases"];
      if (is_string($h)) $h = explode("\n", rtrim(str_replace("\r", "", $h), "\n"));
    }
    foreach (
      (array)$h
      as $i
    ) {
      $b->dumpDatabase($i);
      if ($e->select_db($i)) {
        if ($yd && preg_match('~CREATE~', $Kg) && ($g = get_val("SHOW CREATE DATABASE " . idf_escape($i), 1))) {
          set_utf8mb4($g);
          if ($Kg == "DROP+CREATE") echo "DROP DATABASE IF EXISTS " . idf_escape($i) . ";\n";
          echo "$g;\n";
        }
        if ($yd) {
          if ($Kg) echo
          use_sql($i) . ";\n\n";
          $bf = "";
          if ($_POST["types"]) {
            foreach (types() as $v => $U) {
              $hc = type_values($v);
              if ($hc) $bf .= ($Kg != 'DROP+CREATE' ? "DROP TYPE IF EXISTS " . idf_escape($U) . ";;\n" : "") . "CREATE TYPE " . idf_escape($U) . " AS ENUM ($hc);\n\n";
              else $bf .= "-- Could not export type $U\n\n";
            }
          }
          if ($_POST["routines"]) {
            foreach (routines() as $K) {
              $D = $K["ROUTINE_NAME"];
              $ag = $K["ROUTINE_TYPE"];
              $g = create_routine($ag, array("name" => $D) + routine($K["SPECIFIC_NAME"], $ag));
              set_utf8mb4($g);
              $bf .= ($Kg != 'DROP+CREATE' ? "DROP $ag IF EXISTS " . idf_escape($D) . ";;\n" : "") . "$g;\n\n";
            }
          }
          if ($_POST["events"]) {
            foreach (get_rows("SHOW EVENTS", null, "-- ") as $K) {
              $g = remove_definer(get_val("SHOW CREATE EVENT " . idf_escape($K["Name"]), 3));
              set_utf8mb4($g);
              $bf .= ($Kg != 'DROP+CREATE' ? "DROP EVENT IF EXISTS " . idf_escape($K["Name"]) . ";;\n" : "") . "$g;;\n\n";
            }
          }
          echo ($bf && JUSH == 'sql' ? "DELIMITER ;;\n\n$bf" . "DELIMITER ;\n\n" : $bf);
        }
        if ($_POST["table_style"] || $_POST["data_style"]) {
          $Th = array();
          foreach (table_status('', true) as $D => $R) {
            $Q = (DB == "" || in_array($D, (array)$_POST["tables"]));
            $vb = (DB == "" || in_array($D, (array)$_POST["data"]));
            if ($Q || $vb) {
              if ($tc == "tar") {
                $lh = new
                  TmpFile;
                ob_start(array($lh, 'write'), 1e5);
              }
              $b->dumpTable($D, ($Q ? $_POST["table_style"] : ""), (is_view($R) ? 2 : 0));
              if (is_view($R)) $Th[] = $D;
              elseif ($vb) {
                $n = fields($D);
                $b->dumpData($D, $_POST["data_style"], "SELECT *" . convert_fields($n, $n) . " FROM " . table($D));
              }
              if ($yd && $_POST["triggers"] && $Q && ($vh = trigger_sql($D))) echo "\nDELIMITER ;;\n$vh\nDELIMITER ;\n";
              if ($tc == "tar") {
                ob_end_flush();
                tar_file((DB != "" ? "" : "$i/") . "$D.csv", $lh);
              } elseif ($yd) echo "\n";
            }
          }
          if (function_exists('Adminer\foreign_keys_sql')) {
            foreach (table_status('', true) as $D => $R) {
              $Q = (DB == "" || in_array($D, (array)$_POST["tables"]));
              if ($Q && !is_view($R)) echo
              foreign_keys_sql($D);
            }
          }
          foreach (
            $Th
            as $Sh
          ) $b->dumpTable($Sh, $_POST["table_style"], 1);
          if ($tc == "tar") echo
          pack("x512");
        }
      }
    }
    $b->dumpFooter();
    exit;
  }
  page_header('Export', $l, ($_GET["export"] != "" ? array("table" => $_GET["export"]) : array()), h(DB));
  echo '
<form action="" method="post">
<table class="layout">
';
  $zb = array('', 'USE', 'DROP+CREATE', 'CREATE');
  $Ug = array('', 'DROP+CREATE', 'CREATE');
  $wb = array('', 'TRUNCATE+INSERT', 'INSERT');
  if (JUSH == "sql") $wb[] = 'INSERT+UPDATE';
  $K = get_settings("adminer_export");
  if (!$K) $K = array("output" => "text", "format" => "sql", "db_style" => (DB != "" ? "" : "CREATE"), "table_style" => "DROP+CREATE", "data_style" => "INSERT");
  if (!isset($K["events"])) {
    $K["routines"] = $K["events"] = ($_GET["dump"] == "");
    $K["triggers"] = $K["table_style"];
  }
  echo "<tr><th>" . 'Output' . "<td>" . html_radios("output", $b->dumpOutput(), $K["output"]) . "\n", "<tr><th>" . 'Format' . "<td>" . html_radios("format", $b->dumpFormat(), $K["format"]) . "\n", (JUSH == "sqlite" ? "" : "<tr><th>" . 'Database' . "<td>" . html_select('db_style', $zb, $K["db_style"]) . (support("type") ? checkbox("types", 1, $K["types"], 'User types') : "") . (support("routine") ? checkbox("routines", 1, $K["routines"], 'Routines') : "") . (support("event") ? checkbox("events", 1, $K["events"], 'Events') : "")), "<tr><th>" . 'Tables' . "<td>" . html_select('table_style', $Ug, $K["table_style"]) . checkbox("auto_increment", 1, $K["auto_increment"], 'Auto Increment') . (support("trigger") ? checkbox("triggers", 1, $K["triggers"], 'Triggers') : ""), "<tr><th>" . 'Data' . "<td>" . html_select('data_style', $wb, $K["data_style"]), '</table>
<p><input type="submit" value="Export">
<input type="hidden" name="token" value="', $T, '">

<table>
', script("qsl('table').onclick = dumpClick;");
  $zf = array();
  if (DB != "") {
    $Pa = ($a != "" ? "" : " checked");
    echo "<thead><tr>", "<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$Pa>" . 'Tables' . "</label>" . script("qs('#check-tables').onclick = partial(formCheck, /^tables\\[/);", ""), "<th style='text-align: right;'><label class='block'>" . 'Data' . "<input type='checkbox' id='check-data'$Pa></label>" . script("qs('#check-data').onclick = partial(formCheck, /^data\\[/);", ""), "</thead>\n";
    $Th = "";
    $Vg = tables_list();
    foreach (
      $Vg
      as $D => $U
    ) {
      $yf = preg_replace('~_.*~', '', $D);
      $Pa = ($a == "" || $a == (substr($a, -1) == "%" ? "$yf%" : $D));
      $Af = "<tr><td>" . checkbox("tables[]", $D, $Pa, $D, "", "block");
      if ($U !== null && !preg_match('~table~i', $U)) $Th .= "$Af\n";
      else
        echo "$Af<td align='right'><label class='block'><span id='Rows-" . h($D) . "'></span>" . checkbox("data[]", $D, $Pa) . "</label>\n";
      $zf[$yf]++;
    }
    echo $Th;
    if ($Vg) echo
    script("ajaxSetHtml('" . js_escape(ME) . "script=db');");
  } else {
    echo "<thead><tr><th style='text-align: left;'>", "<label class='block'><input type='checkbox' id='check-databases'" . ($a == "" ? " checked" : "") . ">" . 'Database' . "</label>", script("qs('#check-databases').onclick = partial(formCheck, /^databases\\[/);", ""), "</thead>\n";
    $h = $b->databases();
    if ($h) {
      foreach (
        $h
        as $i
      ) {
        if (!information_schema($i)) {
          $yf = preg_replace('~_.*~', '', $i);
          echo "<tr><td>" . checkbox("databases[]", $i, $a == "" || $a == "$yf%", $i, "", "block") . "\n";
          $zf[$yf]++;
        }
      }
    } else
      echo "<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";
  }
  echo '</table>
</form>
';
  $Dc = true;
  foreach (
    $zf
    as $y => $X
  ) {
    if ($y != "" && $X > 1) {
      echo ($Dc ? "<p>" : " ") . "<a href='" . h(ME) . "dump=" . urlencode("$y%") . "'>" . h($y) . "</a>";
      $Dc = false;
    }
  }
} elseif (isset($_GET["privileges"])) {
  page_header('Privileges');
  echo '<p class="links"><a href="' . h(ME) . 'user=">' . 'Create user' . "</a>";
  $I = $e->query("SELECT User, Host FROM mysql." . (DB == "" ? "user" : "db WHERE " . q(DB) . " LIKE Db") . " ORDER BY Host, User");
  $Rc = $I;
  if (!$I) $I = $e->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");
  echo "<form action=''><p>\n";
  hidden_fields_get();
  echo "<input type='hidden' name='db' value='" . h(DB) . "'>\n", ($Rc ? "" : "<input type='hidden' name='grant' value=''>\n"), "<table class='odds'>\n", "<thead><tr><th>" . 'Username' . "<th>" . 'Server' . "<th></thead>\n";
  while ($K = $I->fetch_assoc()) echo '<tr><td>' . h($K["User"]) . "<td>" . h($K["Host"]) . '<td><a href="' . h(ME . 'user=' . urlencode($K["User"]) . '&host=' . urlencode($K["Host"])) . '">' . 'Edit' . "</a>\n";
  if (!$Rc || DB != "") echo "<tr><td><input name='user' autocapitalize='off'><td><input name='host' value='localhost' autocapitalize='off'><td><input type='submit' value='" . 'Edit' . "'>\n";
  echo "</table>\n", "</form>\n";
} elseif (isset($_GET["sql"])) {
  if (!$l && $_POST["export"]) {
    save_settings(array("output" => $_POST["output"], "format" => $_POST["format"]), "adminer_import");
    dump_headers("sql");
    $b->dumpTable("", "");
    $b->dumpData("", "table", $_POST["query"]);
    $b->dumpFooter();
    exit;
  }
  restart_session();
  $ed = &get_session("queries");
  $dd = &$ed[DB];
  if (!$l && $_POST["clear"]) {
    $dd = array();
    redirect(remove_from_uri("history"));
  }
  page_header((isset($_GET["import"]) ? 'Import' : 'SQL command'), $l);
  if (!$l && $_POST) {
    $r = false;
    if (!isset($_GET["import"])) $H = $_POST["query"];
    elseif ($_POST["webfile"]) {
      $Ag = $b->importServerPath();
      $r = @fopen((file_exists($Ag) ? $Ag : "compress.zlib://$Ag.gz"), "rb");
      $H = ($r ? fread($r, 1e6) : false);
    } else $H = get_file("sql_file", true, ";");
    if (is_string($H)) {
      if (function_exists('memory_get_usage') && ($ge = ini_bytes("memory_limit")) != "-1") @ini_set("memory_limit", max($ge, 2 * strlen($H) + memory_get_usage() + 8e6));
      if ($H != "" && strlen($H) < 1e6) {
        $G = $H . (preg_match("~;[ \t\r\n]*\$~", $H) ? "" : ";");
        if (!$dd || reset(end($dd)) != $G) {
          restart_session();
          $dd[] = array($G, time());
          set_session("queries", $ed);
          stop_session();
        }
      }
      $zg = "(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";
      $Eb = ";";
      $Be = 0;
      $bc = true;
      $f = connect($b->credentials());
      if (is_object($f) && DB != "") {
        $f->select_db(DB);
        if ($_GET["ns"] != "") set_schema($_GET["ns"], $f);
      }
      $cb = 0;
      $jc = array();
      $hf = '[\'"' . (JUSH == "sql" ? '`#' : (JUSH == "sqlite" ? '`[' : (JUSH == "mssql" ? '[' : ''))) . ']|/\*|-- |$' . (JUSH == "pgsql" ? '|\$[^$]*\$' : '');
      $oh = microtime(true);
      $ja = get_settings("adminer_import");
      $Sb = $b->dumpFormat();
      unset($Sb["sql"]);
      while ($H != "") {
        if (!$Be && preg_match("~^$zg*+DELIMITER\\s+(\\S+)~i", $H, $B)) {
          $Eb = $B[1];
          $H = substr($H, strlen($B[0]));
        } else {
          preg_match('(' . preg_quote($Eb) . "\\s*|$hf)", $H, $B, PREG_OFFSET_CAPTURE, $Be);
          list($Kc, $tf) = $B[0];
          if (!$Kc && $r && !feof($r)) $H .= fread($r, 1e5);
          else {
            if (!$Kc && rtrim($H) == "") break;
            $Be = $tf + strlen($Kc);
            if ($Kc && rtrim($Kc) != $Eb) {
              $Ja = $k->hasCStyleEscapes() || (JUSH == "pgsql" && ($tf > 0 && strtolower($H[$tf - 1]) == "e"));
              $of = ($Kc == '/*' ? '\*/' : ($Kc == '[' ? ']' : (preg_match('~^-- |^#~', $Kc) ? "\n" : preg_quote($Kc) . ($Ja ? "|\\\\." : ""))));
              while (preg_match("($of|\$)s", $H, $B, PREG_OFFSET_CAPTURE, $Be)) {
                $M = $B[0][0];
                if (!$M && $r && !feof($r)) $H .= fread($r, 1e5);
                else {
                  $Be = $B[0][1] + strlen($M);
                  if (!$M || $M[0] != "\\") break;
                }
              }
            } else {
              $bc = false;
              $G = substr($H, 0, $tf);
              $cb++;
              $Af = "<pre id='sql-$cb'><code class='jush-" . JUSH . "'>" . $b->sqlCommandQuery($G) . "</code></pre>\n";
              if (JUSH == "sqlite" && preg_match("~^$zg*+ATTACH\\b~i", $G, $B)) {
                echo $Af, "<p class='error'>" . 'ATTACH queries are not supported.' . "\n";
                $jc[] = " <a href='#sql-$cb'>$cb</a>";
                if ($_POST["error_stops"]) break;
              } else {
                if (!$_POST["only_errors"]) {
                  echo $Af;
                  ob_flush();
                  flush();
                }
                $Eg = microtime(true);
                if ($e->multi_query($G) && is_object($f) && preg_match("~^$zg*+USE\\b~i", $G)) $f->query($G);
                do {
                  $I = $e->store_result();
                  if ($e->error) {
                    echo ($_POST["only_errors"] ? $Af : ""), "<p class='error'>" . 'Error in query' . ($e->errno ? " ($e->errno)" : "") . ": " . error() . "\n";
                    $jc[] = " <a href='#sql-$cb'>$cb</a>";
                    if ($_POST["error_stops"]) break
                      2;
                  } else {
                    $eh = " <span class='time'>(" . format_time($Eg) . ")</span>" . (strlen($G) < 1000 ? " <a href='" . h(ME) . "sql=" . urlencode(trim($G)) . "'>" . 'Edit' . "</a>" : "");
                    $la = $e->affected_rows;
                    $Wh = ($_POST["only_errors"] ? "" : $k->warnings());
                    $Xh = "warnings-$cb";
                    if ($Wh) $eh .= ", <a href='#$Xh'>" . 'Warnings' . "</a>" . script("qsl('a').onclick = partial(toggle, '$Xh');", "");
                    $rc = null;
                    $sc = "explain-$cb";
                    if (is_object($I)) {
                      $z = $_POST["limit"];
                      $Ue = select($I, $f, array(), $z);
                      if (!$_POST["only_errors"]) {
                        echo "<form action='' method='post'>\n";
                        $ze = $I->num_rows;
                        echo "<p>" . ($ze ? ($z && $ze > $z ? sprintf('%d / ', $z) : "") . lang(array('%d row', '%d rows'), $ze) : ""), $eh;
                        if ($f && preg_match("~^($zg|\\()*+SELECT\\b~i", $G) && ($rc = explain($f, $G))) echo ", <a href='#$sc'>Explain</a>" . script("qsl('a').onclick = partial(toggle, '$sc');", "");
                        $v = "export-$cb";
                        echo ", <a href='#$v'>" . 'Export' . "</a>" . script("qsl('a').onclick = partial(toggle, '$v');", "") . "<span id='$v' class='hidden'>: " . html_select("output", $b->dumpOutput(), $ja["output"]) . " " . html_select("format", $Sb, $ja["format"]) . "<input type='hidden' name='query' value='" . h($G) . "'>" . " <input type='submit' name='export' value='" . 'Export' . "'><input type='hidden' name='token' value='$T'></span>\n" . "</form>\n";
                      }
                    } else {
                      if (preg_match("~^$zg*+(CREATE|DROP|ALTER)$zg++(DATABASE|SCHEMA)\\b~i", $G)) {
                        restart_session();
                        set_session("dbs", null);
                        stop_session();
                      }
                      if (!$_POST["only_errors"]) echo "<p class='message' title='" . h($e->info) . "'>" . lang(array('Query executed OK, %d row affected.', 'Query executed OK, %d rows affected.'), $la) . "$eh\n";
                    }
                    echo ($Wh ? "<div id='$Xh' class='hidden'>\n$Wh</div>\n" : "");
                    if ($rc) {
                      echo "<div id='$sc' class='hidden explain'>\n";
                      select($rc, $f, $Ue);
                      echo "</div>\n";
                    }
                  }
                  $Eg = microtime(true);
                } while ($e->next_result());
              }
              $H = substr($H, $Be);
              $Be = 0;
            }
          }
        }
      }
      if ($bc) echo "<p class='message'>" . 'No commands to execute.' . "\n";
      elseif ($_POST["only_errors"]) echo "<p class='message'>" . lang(array('%d query executed OK.', '%d queries executed OK.'), $cb - count($jc)), " <span class='time'>(" . format_time($oh) . ")</span>\n";
      elseif ($jc && $cb > 1) echo "<p class='error'>" . 'Error in query' . ": " . implode("", $jc) . "\n";
    } else
      echo "<p class='error'>" . upload_error($H) . "\n";
  }
  echo '
<form action="" method="post" enctype="multipart/form-data" id="form">
';
  $pc = "<input type='submit' value='" . 'Execute' . "' title='Ctrl+Enter'>";
  if (!isset($_GET["import"])) {
    $G = $_GET["sql"];
    if ($_POST) $G = $_POST["query"];
    elseif ($_GET["history"] == "all") $G = $dd;
    elseif ($_GET["history"] != "") $G = $dd[$_GET["history"]][0];
    echo "<p>";
    textarea("query", $G, 20);
    echo
    script(($_POST ? "" : "qs('textarea').focus();\n") . "qs('#form').onsubmit = partial(sqlSubmit, qs('#form'), '" . js_escape(remove_from_uri("sql|limit|error_stops|only_errors|history")) . "');"), "<p>$pc\n", 'Limit rows' . ": <input type='number' name='limit' class='size' value='" . h($_POST ? $_POST["limit"] : $_GET["limit"]) . "'>\n";
  } else {
    echo "<fieldset><legend>" . 'File upload' . "</legend><div>";
    $Wc = (extension_loaded("zlib") ? "[.gz]" : "");
    echo (ini_bool("file_uploads") ? "SQL$Wc (&lt; " . ini_get("upload_max_filesize") . "B): <input type='file' name='sql_file[]' multiple>\n$pc" : 'File uploads are disabled.'), "</div></fieldset>\n";
    $ld = $b->importServerPath();
    if ($ld) echo "<fieldset><legend>" . 'From server' . "</legend><div>", sprintf('Webserver file %s', "<code>" . h($ld) . "$Wc</code>"), ' <input type="submit" name="webfile" value="' . 'Run file' . '">', "</div></fieldset>\n";
    echo "<p>";
  }
  echo
  checkbox("error_stops", 1, ($_POST ? $_POST["error_stops"] : isset($_GET["import"]) || $_GET["error_stops"]), 'Stop on error') . "\n", checkbox("only_errors", 1, ($_POST ? $_POST["only_errors"] : isset($_GET["import"]) || $_GET["only_errors"]), 'Show only errors') . "\n", "<input type='hidden' name='token' value='$T'>\n";
  if (!isset($_GET["import"]) && $dd) {
    print_fieldset("history", 'History', $_GET["history"] != "");
    for ($X = end($dd); $X; $X = prev($dd)) {
      $y = key($dd);
      list($G, $eh, $Wb) = $X;
      echo '<a href="' . h(ME . "sql=&history=$y") . '">' . 'Edit' . "</a>" . " <span class='time' title='" . @date('Y-m-d', $eh) . "'>" . @date("H:i:s", $eh) . "</span>" . " <code class='jush-" . JUSH . "'>" . shorten_utf8(ltrim(str_replace("\n", " ", str_replace("\r", "", preg_replace('~^(#|-- ).*~m', '', $G)))), 80, "</code>") . ($Wb ? " <span class='time'>($Wb)</span>" : "") . "<br>\n";
    }
    echo "<input type='submit' name='clear' value='" . 'Clear' . "'>\n", "<a href='" . h(ME . "sql=&history=all") . "'>" . 'Edit all' . "</a>\n", "</div></fieldset>\n";
  }
  echo '</form>
';
} elseif (isset($_GET["edit"])) {
  $a = $_GET["edit"];
  $n = fields($a);
  $Z = (isset($_GET["select"]) ? ($_POST["check"] && count($_POST["check"]) == 1 ? where_check($_POST["check"][0], $n) : "") : where($_GET, $n));
  $Fh = (isset($_GET["select"]) ? $_POST["edit"] : $Z);
  foreach (
    $n
    as $D => $m
  ) {
    if (!isset($m["privileges"][$Fh ? "update" : "insert"]) || $b->fieldName($m) == "" || $m["generated"]) unset($n[$D]);
  }
  if ($_POST && !$l && !isset($_GET["select"])) {
    $A = $_POST["referer"];
    if ($_POST["insert"]) $A = ($Fh ? null : $_SERVER["REQUEST_URI"]);
    elseif (!preg_match('~^.+&select=.+$~', $A)) $A = ME . "select=" . urlencode($a);
    $x = indexes($a);
    $Ah = unique_array($_GET["where"], $x);
    $Jf = "\nWHERE $Z";
    if (isset($_POST["delete"])) queries_redirect($A, 'Item has been deleted.', $k->delete($a, $Jf, !$Ah));
    else {
      $P = array();
      foreach (
        $n
        as $D => $m
      ) {
        $X = process_input($m);
        if ($X !== false && $X !== null) $P[idf_escape($D)] = $X;
      }
      if ($Fh) {
        if (!$P) redirect($A);
        queries_redirect($A, 'Item has been updated.', $k->update($a, $P, $Jf, !$Ah));
        if (is_ajax()) {
          page_headers();
          page_messages($l);
          exit;
        }
      } else {
        $I = $k->insert($a, $P);
        $Kd = ($I ? last_id() : 0);
        queries_redirect($A, sprintf('Item%s has been inserted.', ($Kd ? " $Kd" : "")), $I);
      }
    }
  }
  $K = null;
  if ($_POST["save"]) $K = (array)$_POST["fields"];
  elseif ($Z) {
    $N = array();
    foreach (
      $n
      as $D => $m
    ) {
      if (isset($m["privileges"]["select"])) {
        $ra = ($_POST["clone"] && $m["auto_increment"] ? "''" : convert_field($m));
        $N[] = ($ra ? "$ra AS " : "") . idf_escape($D);
      }
    }
    $K = array();
    if (!support("table")) $N = array("*");
    if ($N) {
      $I = $k->select($a, $N, array($Z), $N, array(), (isset($_GET["select"]) ? 2 : 1));
      if (!$I) $l = error();
      else {
        $K = $I->fetch_assoc();
        if (!$K) $K = false;
      }
      if (isset($_GET["select"]) && (!$K || $I->fetch_assoc())) $K = null;
    }
  }
  if (!support("table") && !$n) {
    if (!$Z) {
      $I = $k->select($a, array("*"), $Z, array("*"));
      $K = ($I ? $I->fetch_assoc() : false);
      if (!$K) $K = array($k->primary => "");
    }
    if ($K) {
      foreach (
        $K
        as $y => $X
      ) {
        if (!$Z) $K[$y] = null;
        $n[$y] = array("field" => $y, "null" => ($y != $k->primary), "auto_increment" => ($y == $k->primary));
      }
    }
  }
  edit_form($a, $n, $K, $Fh);
} elseif (isset($_GET["create"])) {
  $a = $_GET["create"];
  $if = array();
  foreach (array('HASH', 'LINEAR HASH', 'KEY', 'LINEAR KEY', 'RANGE', 'LIST') as $y) $if[$y] = $y;
  $Pf = referencable_primary($a);
  $q = array();
  foreach (
    $Pf
    as $Rg => $m
  ) $q[str_replace("`", "``", $Rg) . "`" . str_replace("`", "``", $m["field"])] = $Rg;
  $Xe = array();
  $R = array();
  if ($a != "") {
    $Xe = fields($a);
    $R = table_status($a);
    if (!$R) $l = 'No tables.';
  }
  $K = $_POST;
  $K["fields"] = (array)$K["fields"];
  if ($K["auto_increment_col"]) $K["fields"][$K["auto_increment_col"]]["auto_increment"] = true;
  if ($_POST) save_settings(array("comments" => $_POST["comments"], "defaults" => $_POST["defaults"]));
  if ($_POST && !process_fields($K["fields"]) && !$l) {
    if ($_POST["drop"]) queries_redirect(substr(ME, 0, -1), 'Table has been dropped.', drop_tables(array($a)));
    else {
      $n = array();
      $pa = array();
      $Jh = false;
      $Hc = array();
      $We = reset($Xe);
      $na = " FIRST";
      foreach ($K["fields"] as $y => $m) {
        $p = $q[$m["type"]];
        $wh = ($p !== null ? $Pf[$p] : $m);
        if ($m["field"] != "") {
          if (!$m["generated"]) $m["default"] = null;
          $Ff = process_field($m, $wh);
          $pa[] = array($m["orig"], $Ff, $na);
          if (!$We || $Ff !== process_field($We, $We)) {
            $n[] = array($m["orig"], $Ff, $na);
            if ($m["orig"] != "" || $na) $Jh = true;
          }
          if ($p !== null) $Hc[idf_escape($m["field"])] = ($a != "" && JUSH != "sqlite" ? "ADD" : " ") . format_foreign_key(array('table' => $q[$m["type"]], 'source' => array($m["field"]), 'target' => array($wh["field"]), 'on_delete' => $m["on_delete"],));
          $na = " AFTER " . idf_escape($m["field"]);
        } elseif ($m["orig"] != "") {
          $Jh = true;
          $n[] = array($m["orig"]);
        }
        if ($m["orig"] != "") {
          $We = next($Xe);
          if (!$We) $na = "";
        }
      }
      $kf = "";
      if (support("partitioning")) {
        if (isset($if[$K["partition_by"]])) {
          $gf = array();
          foreach (
            $K
            as $y => $X
          ) {
            if (preg_match('~^partition~', $y)) $gf[$y] = $X;
          }
          foreach ($gf["partition_names"] as $y => $D) {
            if ($D == "") {
              unset($gf["partition_names"][$y]);
              unset($gf["partition_values"][$y]);
            }
          }
          if ($gf != get_partitions_info($a)) {
            $lf = array();
            if ($gf["partition_by"] == 'RANGE' || $gf["partition_by"] == 'LIST') {
              foreach ($gf["partition_names"] as $y => $D) {
                $Y = $gf["partition_values"][$y];
                $lf[] = "\n  PARTITION " . idf_escape($D) . " VALUES " . ($gf["partition_by"] == 'RANGE' ? "LESS THAN" : "IN") . ($Y != "" ? " ($Y)" : " MAXVALUE");
              }
            }
            $kf .= "\nPARTITION BY $gf[partition_by]($gf[partition])";
            if ($lf) $kf .= " (" . implode(",", $lf) . "\n)";
            elseif ($gf["partitions"]) $kf .= " PARTITIONS " . (+$gf["partitions"]);
          }
        } elseif (preg_match("~partitioned~", $R["Create_options"])) $kf .= "\nREMOVE PARTITIONING";
      }
      $C = 'Table has been altered.';
      if ($a == "") {
        cookie("adminer_engine", $K["Engine"]);
        $C = 'Table has been created.';
      }
      $D = trim($K["name"]);
      queries_redirect(ME . (support("table") ? "table=" : "select=") . urlencode($D), $C, alter_table($a, $D, (JUSH == "sqlite" && ($Jh || $Hc) ? $pa : $n), $Hc, ($K["Comment"] != $R["Comment"] ? $K["Comment"] : null), ($K["Engine"] && $K["Engine"] != $R["Engine"] ? $K["Engine"] : ""), ($K["Collation"] && $K["Collation"] != $R["Collation"] ? $K["Collation"] : ""), ($K["Auto_increment"] != "" ? number($K["Auto_increment"]) : ""), $kf));
    }
  }
  page_header(($a != "" ? 'Alter table' : 'Create table'), $l, array("table" => $a), h($a));
  if (!$_POST) {
    $yh = $k->types();
    $K = array("Engine" => $_COOKIE["adminer_engine"], "fields" => array(array("field" => "", "type" => (isset($yh["int"]) ? "int" : (isset($yh["integer"]) ? "integer" : "")), "on_update" => "")), "partition_names" => array(""),);
    if ($a != "") {
      $K = $R;
      $K["name"] = $a;
      $K["fields"] = array();
      if (!$_GET["auto_increment"]) $K["Auto_increment"] = "";
      foreach (
        $Xe
        as $m
      ) {
        $m["generated"] = $m["generated"] ?: (isset($m["default"]) ? "DEFAULT" : "");
        $K["fields"][] = $m;
      }
      if (support("partitioning")) {
        $K += get_partitions_info($a);
        $K["partition_names"][] = "";
        $K["partition_values"][] = "";
      }
    }
  }
  $Za = collations();
  $dc = engines();
  foreach (
    $dc
    as $cc
  ) {
    if (!strcasecmp($cc, $K["Engine"])) {
      $K["Engine"] = $cc;
      break;
    }
  }
  echo '
<form action="" method="post" id="form">
<p>
';
  if (support("columns") || $a == "") {
    echo 'Table name' . "<input name='name'" . ($a == "" && !$_POST ? " autofocus" : "") . " data-maxlength='64' value='" . h($K["name"]) . "' autocapitalize='off'>\n", ($dc ? html_select("Engine", array("" => "(" . 'engine' . ")") + $dc, $K["Engine"]) . on_help("getTarget(event).value", 1) . script("qsl('select').onchange = helpClose;") . "\n" : "");
    if ($Za) echo "<datalist id='collations'>" . optionlist($Za) . "</datalist>", (preg_match("~sqlite|mssql~", JUSH) ? "" : "<input list='collations' name='Collation' value='" . h($K["Collation"]) . "' placeholder='(" . 'collation' . ")'>");
    echo "<input type='submit' value='" . 'Save' . "'>\n";
  }
  if (support("columns")) {
    echo "<div class='scrollable'>\n", "<table id='edit-fields' class='nowrap'>\n";
    edit_fields($K["fields"], $Za, "TABLE", $q);
    echo "</table>\n", script("editFields();"), "</div>\n<p>\n", 'Auto Increment' . ": <input type='number' name='Auto_increment' class='size' value='" . h($K["Auto_increment"]) . "'>\n", checkbox("defaults", 1, ($_POST ? $_POST["defaults"] : get_setting("defaults")), 'Default values', "columnShow(this.checked, 5)", "jsonly");
    $fb = ($_POST ? $_POST["comments"] : get_setting("comments"));
    echo (support("comment") ? checkbox("comments", 1, $fb, 'Comment', "editingCommentsClick(this, true);", "jsonly") . ' ' . (preg_match('~\n~', $K["Comment"]) ? "<textarea name='Comment' rows='2' cols='20'" . ($fb ? "" : " class='hidden'") . ">" . h($K["Comment"]) . "</textarea>" : '<input name="Comment" value="' . h($K["Comment"]) . '" data-maxlength="' . (min_version(5.5) ? 2048 : 60) . '"' . ($fb ? "" : " class='hidden'") . '>') : ''), '<p>
<input type="submit" value="Save">
';
  }
  echo '
';
  if ($a != "") echo '<input type="submit" name="drop" value="Drop">', confirm(sprintf('Drop %s?', $a));
  if (support("partitioning")) {
    $jf = preg_match('~RANGE|LIST~', $K["partition_by"]);
    print_fieldset("partition", 'Partition by', $K["partition_by"]);
    echo "<p>" . html_select("partition_by", array("" => "") + $if, $K["partition_by"]) . on_help("getTarget(event).value.replace(/./, 'PARTITION BY \$&')", 1) . script("qsl('select').onchange = partitionByChange;"), "(<input name='partition' value='" . h($K["partition"]) . "'>)\n", 'Partitions' . ": <input type='number' name='partitions' class='size" . ($jf || !$K["partition_by"] ? " hidden" : "") . "' value='" . h($K["partitions"]) . "'>\n", "<table id='partition-table'" . ($jf ? "" : " class='hidden'") . ">\n", "<thead><tr><th>" . 'Partition name' . "<th>" . 'Values' . "</thead>\n";
    foreach ($K["partition_names"] as $y => $X) echo '<tr>', '<td><input name="partition_names[]" value="' . h($X) . '" autocapitalize="off">', ($y == count($K["partition_names"]) - 1 ? script("qsl('input').oninput = partitionNameChange;") : ''), '<td><input name="partition_values[]" value="' . h($K["partition_values"][$y]) . '">';
    echo "</table>\n</div></fieldset>\n";
  }
  echo '<input type="hidden" name="token" value="', $T, '">
</form>
';
} elseif (isset($_GET["indexes"])) {
  $a = $_GET["indexes"];
  $od = array("PRIMARY", "UNIQUE", "INDEX");
  $R = table_status($a, true);
  if (preg_match('~MyISAM|M?aria' . (min_version(5.6, '10.0.5') ? '|InnoDB' : '') . '~i', $R["Engine"])) $od[] = "FULLTEXT";
  if (preg_match('~MyISAM|M?aria' . (min_version(5.7, '10.2.2') ? '|InnoDB' : '') . '~i', $R["Engine"])) $od[] = "SPATIAL";
  $x = indexes($a);
  $_f = array();
  if (JUSH == "mongo") {
    $_f = $x["_id_"];
    unset($od[0]);
    unset($x["_id_"]);
  }
  $K = $_POST;
  if ($K) save_settings(array("index_options" => $K["options"]));
  if ($_POST && !$l && !$_POST["add"] && !$_POST["drop_col"]) {
    $qa = array();
    foreach ($K["indexes"] as $w) {
      $D = $w["name"];
      if (in_array($w["type"], $od)) {
        $d = array();
        $Qd = array();
        $Gb = array();
        $P = array();
        ksort($w["columns"]);
        foreach ($w["columns"] as $y => $c) {
          if ($c != "") {
            $Pd = $w["lengths"][$y];
            $Fb = $w["descs"][$y];
            $P[] = idf_escape($c) . ($Pd ? "(" . (+$Pd) . ")" : "") . ($Fb ? " DESC" : "");
            $d[] = $c;
            $Qd[] = ($Pd ?: null);
            $Gb[] = $Fb;
          }
        }
        $qc = $x[$D];
        if ($qc) {
          ksort($qc["columns"]);
          ksort($qc["lengths"]);
          ksort($qc["descs"]);
          if ($w["type"] == $qc["type"] && array_values($qc["columns"]) === $d && (!$qc["lengths"] || array_values($qc["lengths"]) === $Qd) && array_values($qc["descs"]) === $Gb) {
            unset($x[$D]);
            continue;
          }
        }
        if ($d) $qa[] = array($w["type"], $D, $P);
      }
    }
    foreach (
      $x
      as $D => $qc
    ) $qa[] = array($qc["type"], $D, "DROP");
    if (!$qa) redirect(ME . "table=" . urlencode($a));
    queries_redirect(ME . "table=" . urlencode($a), 'Indexes have been altered.', alter_indexes($a, $qa));
  }
  page_header('Indexes', $l, array("table" => $a), h($a));
  $n = array_keys(fields($a));
  if ($_POST["add"]) {
    foreach ($K["indexes"] as $y => $w) {
      if ($w["columns"][count($w["columns"])] != "") $K["indexes"][$y]["columns"][] = "";
    }
    $w = end($K["indexes"]);
    if ($w["type"] || array_filter($w["columns"], 'strlen')) $K["indexes"][] = array("columns" => array(1 => ""));
  }
  if (!$K) {
    foreach (
      $x
      as $y => $w
    ) {
      $x[$y]["name"] = $y;
      $x[$y]["columns"][] = "";
    }
    $x[] = array("columns" => array(1 => ""));
    $K["indexes"] = $x;
  }
  $Qd = (JUSH == "sql" || JUSH == "mssql");
  $sg = ($_POST ? $_POST["options"] : get_setting("index_options"));
  echo '
<form action="" method="post">
<div class="scrollable">
<table class="nowrap">
<thead><tr>
<th id="label-type">Index Type
<th><input type="submit" class="wayoff">', 'Column' . ($Qd ? "<span class='idxopts" . ($sg ? "" : " hidden") . "'> (" . 'length' . ")</span>" : "");
  if ($Qd || support("descidx")) echo
  checkbox("options", 1, $sg, 'Options', "indexOptionsShow(this.checked)", "jsonly") . "\n";
  echo '<th id="label-name">Name
<th><noscript>', "<input type='image' class='icon' name='add[0]' src='" . h(preg_replace("~\\?.*~", "", ME) . "?file=plus.gif&version=5.0.6") . "' alt='+' title='" . 'Add next' . "'>", '</noscript>
</thead>
';
  if ($_f) {
    echo "<tr><td>PRIMARY<td>";
    foreach ($_f["columns"] as $y => $c) echo
    select_input(" disabled", $n, $c), "<label><input disabled type='checkbox'>" . 'descending' . "</label> ";
    echo "<td><td>\n";
  }
  $Ad = 1;
  foreach ($K["indexes"] as $w) {
    if (!$_POST["drop_col"] || $Ad != key($_POST["drop_col"])) {
      echo "<tr><td>" . html_select("indexes[$Ad][type]", array(-1 => "") + $od, $w["type"], ($Ad == count($K["indexes"]) ? "indexesAddRow.call(this);" : ""), "label-type"), "<td>";
      ksort($w["columns"]);
      $u = 1;
      foreach ($w["columns"] as $y => $c) {
        echo "<span>" . select_input(" name='indexes[$Ad][columns][$u]' title='" . 'Column' . "'", ($n ? array_combine($n, $n) : $n), $c, "partial(" . ($u == count($w["columns"]) ? "indexesAddColumn" : "indexesChangeColumn") . ", '" . js_escape(JUSH == "sql" ? "" : $_GET["indexes"] . "_") . "')"), "<span class='idxopts" . ($sg ? "" : " hidden") . "'>", ($Qd ? "<input type='number' name='indexes[$Ad][lengths][$u]' class='size' value='" . h($w["lengths"][$y]) . "' title='" . 'Length' . "'>" : ""), (support("descidx") ? checkbox("indexes[$Ad][descs][$u]", 1, $w["descs"][$y], 'descending') : ""), "</span> </span>";
        $u++;
      }
      echo "<td><input name='indexes[$Ad][name]' value='" . h($w["name"]) . "' autocapitalize='off' aria-labelledby='label-name'>\n", "<td><input type='image' class='icon' name='drop_col[$Ad]' src='" . h(preg_replace("~\\?.*~", "", ME) . "?file=cross.gif&version=5.0.6") . "' alt='x' title='" . 'Remove' . "'>" . script("qsl('input').onclick = partial(editingRemoveRow, 'indexes\$1[type]');");
    }
    $Ad++;
  }
  echo '</table>
</div>
<p>
<input type="submit" value="Save">
<input type="hidden" name="token" value="', $T, '">
</form>
';
} elseif (isset($_GET["database"])) {
  $K = $_POST;
  if ($_POST && !$l && !isset($_POST["add_x"])) {
    $D = trim($K["name"]);
    if ($_POST["drop"]) {
      $_GET["db"] = "";
      queries_redirect(remove_from_uri("db|database"), 'Database has been dropped.', drop_databases(array(DB)));
    } elseif (DB !== $D) {
      if (DB != "") {
        $_GET["db"] = $D;
        queries_redirect(preg_replace('~\bdb=[^&]*&~', '', ME) . "db=" . urlencode($D), 'Database has been renamed.', rename_database($D, $K["collation"]));
      } else {
        $h = explode("\n", str_replace("\r", "", $D));
        $Lg = true;
        $Jd = "";
        foreach (
          $h
          as $i
        ) {
          if (count($h) == 1 || $i != "") {
            if (!create_database($i, $K["collation"])) $Lg = false;
            $Jd = $i;
          }
        }
        restart_session();
        set_session("dbs", null);
        queries_redirect(ME . "db=" . urlencode($Jd), 'Database has been created.', $Lg);
      }
    } else {
      if (!$K["collation"]) redirect(substr(ME, 0, -1));
      query_redirect("ALTER DATABASE " . idf_escape($D) . (preg_match('~^[a-z0-9_]+$~i', $K["collation"]) ? " COLLATE $K[collation]" : ""), substr(ME, 0, -1), 'Database has been altered.');
    }
  }
  page_header(DB != "" ? 'Alter database' : 'Create database', $l, array(), h(DB));
  $Za = collations();
  $D = DB;
  if ($_POST) $D = $K["name"];
  elseif (DB != "") $K["collation"] = db_collation(DB, $Za);
  elseif (JUSH == "sql") {
    foreach (get_vals("SHOW GRANTS") as $Rc) {
      if (preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~', $Rc, $B) && $B[1]) {
        $D = stripcslashes(idf_unescape("`$B[2]`"));
        break;
      }
    }
  }
  echo '
<form action="" method="post">
<p>
', ($_POST["add_x"] || strpos($D, "\n") ? '<textarea autofocus name="name" rows="10" cols="40">' . h($D) . '</textarea><br>' : '<input name="name" autofocus value="' . h($D) . '" data-maxlength="64" autocapitalize="off">') . "\n" . ($Za ? html_select("collation", array("" => "(" . 'collation' . ")") + $Za, $K["collation"]) . doc_link(array('sql' => "charset-charsets.html", 'mariadb' => "supported-character-sets-and-collations/",)) : ""), '<input type="submit" value="Save">
';
  if (DB != "") echo "<input type='submit' name='drop' value='" . 'Drop' . "'>" . confirm(sprintf('Drop %s?', DB)) . "\n";
  elseif (!$_POST["add_x"] && $_GET["db"] == "") echo "<input type='image' class='icon' name='add' src='" . h(preg_replace("~\\?.*~", "", ME) . "?file=plus.gif&version=5.0.6") . "' alt='+' title='" . 'Add next' . "'>\n";
  echo '<input type="hidden" name="token" value="', $T, '">
</form>
';
} elseif (isset($_GET["call"])) {
  $da = ($_GET["name"] ?: $_GET["call"]);
  page_header('Call' . ": " . h($da), $l);
  $ag = routine($_GET["call"], (isset($_GET["callf"]) ? "FUNCTION" : "PROCEDURE"));
  $md = array();
  $bf = array();
  foreach ($ag["fields"] as $u => $m) {
    if (substr($m["inout"], -3) == "OUT") $bf[$u] = "@" . idf_escape($m["field"]) . " AS " . idf_escape($m["field"]);
    if (!$m["inout"] || substr($m["inout"], 0, 2) == "IN") $md[] = $u;
  }
  if (!$l && $_POST) {
    $Ka = array();
    foreach ($ag["fields"] as $y => $m) {
      if (in_array($y, $md)) {
        $X = process_input($m);
        if ($X === false) $X = "''";
        if (isset($bf[$y])) $e->query("SET @" . idf_escape($m["field"]) . " = $X");
      }
      $Ka[] = (isset($bf[$y]) ? "@" . idf_escape($m["field"]) : $X);
    }
    $H = (isset($_GET["callf"]) ? "SELECT" : "CALL") . " " . table($da) . "(" . implode(", ", $Ka) . ")";
    $Eg = microtime(true);
    $I = $e->multi_query($H);
    $la = $e->affected_rows;
    echo $b->selectQuery($H, $Eg, !$I);
    if (!$I) echo "<p class='error'>" . error() . "\n";
    else {
      $f = connect($b->credentials());
      if (is_object($f)) $f->select_db(DB);
      do {
        $I = $e->store_result();
        if (is_object($I)) select($I, $f);
        else
          echo "<p class='message'>" . lang(array('Routine has been called, %d row affected.', 'Routine has been called, %d rows affected.'), $la) . " <span class='time'>" . @date("H:i:s") . "</span>\n";
      } while ($e->next_result());
      if ($bf) select($e->query("SELECT " . implode(", ", $bf)));
    }
  }
  echo '
<form action="" method="post">
';
  if ($md) {
    echo "<table class='layout'>\n";
    foreach (
      $md
      as $y
    ) {
      $m = $ag["fields"][$y];
      $D = $m["field"];
      echo "<tr><th>" . $b->fieldName($m);
      $Y = $_POST["fields"][$D];
      if ($Y != "") {
        if ($m["type"] == "set") $Y = implode(",", $Y);
      }
      input($m, $Y, (string)$_POST["function"][$D]);
      echo "\n";
    }
    echo "</table>\n";
  }
  echo '<p>
<input type="submit" value="Call">
<input type="hidden" name="token" value="', $T, '">
</form>

<pre>
';
  function
  pre_tr($M)
  {
    return
      preg_replace('~^~m', '<tr>', preg_replace('~\|~', '<td>', preg_replace('~\|$~m', "", rtrim($M))));
  }
  $Q = '(\+--[-+]+\+\n)';
  $K = '(\| .* \|\n)';
  echo
  preg_replace_callback("~^$Q?$K$Q?($K*)$Q?~m", function ($B) {
    $Ec = pre_tr($B[2]);
    return "<table>\n" . ($B[1] ? "<thead>$Ec</thead>\n" : $Ec) . pre_tr($B[4]) . "\n</table>";
  }, preg_replace('~(\n(    -|mysql)&gt; )(.+)~', "\\1<code class='jush-sql'>\\3</code>", preg_replace('~(.+)\n---+\n~', "<b>\\1</b>\n", h($ag['comment']))));
  echo '</pre>
';
} elseif (isset($_GET["foreign"])) {
  $a = $_GET["foreign"];
  $D = $_GET["name"];
  $K = $_POST;
  if ($_POST && !$l && !$_POST["add"] && !$_POST["change"] && !$_POST["change-js"]) {
    if (!$_POST["drop"]) {
      $K["source"] = array_filter($K["source"], 'strlen');
      ksort($K["source"]);
      $Yg = array();
      foreach ($K["source"] as $y => $X) $Yg[$y] = $K["target"][$y];
      $K["target"] = $Yg;
    }
    if (JUSH == "sqlite") $I = recreate_table($a, $a, array(), array(), array(" $D" => ($K["drop"] ? "" : " " . format_foreign_key($K))));
    else {
      $qa = "ALTER TABLE " . table($a);
      $I = ($D == "" || queries("$qa DROP " . (JUSH == "sql" ? "FOREIGN KEY " : "CONSTRAINT ") . idf_escape($D)));
      if (!$K["drop"]) $I = queries("$qa ADD" . format_foreign_key($K));
    }
    queries_redirect(ME . "table=" . urlencode($a), ($K["drop"] ? 'Foreign key has been dropped.' : ($D != "" ? 'Foreign key has been altered.' : 'Foreign key has been created.')), $I);
    if (!$K["drop"]) $l = "$l<br>" . 'Source and target columns must have the same data type, there must be an index on the target columns and referenced data must exist.';
  }
  page_header('Foreign key', $l, array("table" => $a), h($a));
  if ($_POST) {
    ksort($K["source"]);
    if ($_POST["add"]) $K["source"][] = "";
    elseif ($_POST["change"] || $_POST["change-js"]) $K["target"] = array();
  } elseif ($D != "") {
    $q = foreign_keys($a);
    $K = $q[$D];
    $K["source"][] = "";
  } else {
    $K["table"] = $a;
    $K["source"] = array("");
  }
  echo '
<form action="" method="post">
';
  $yg = array_keys(fields($a));
  if ($K["db"] != "") $e->select_db($K["db"]);
  if ($K["ns"] != "") {
    $Ye = get_schema();
    set_schema($K["ns"]);
  }
  $Of = array_keys(array_filter(table_status('', true), 'Adminer\fk_support'));
  $Yg = array_keys(fields(in_array($K["table"], $Of) ? $K["table"] : reset($Of)));
  $Je = "this.form['change-js'].value = '1'; this.form.submit();";
  echo "<p>" . 'Target table' . ": " . html_select("table", $Of, $K["table"], $Je) . "\n";
  if (JUSH != "sqlite") {
    $_b = array();
    foreach ($b->databases() as $i) {
      if (!information_schema($i)) $_b[] = $i;
    }
    echo 'DB' . ": " . html_select("db", $_b, $K["db"] != "" ? $K["db"] : $_GET["db"], $Je);
  }
  echo '<input type="hidden" name="change-js" value="">
<noscript><p><input type="submit" name="change" value="Change"></noscript>
<table>
<thead><tr><th id="label-source">Source<th id="label-target">Target</thead>
';
  $Ad = 0;
  foreach ($K["source"] as $y => $X) {
    echo "<tr>", "<td>" . html_select("source[" . (+$y) . "]", array(-1 => "") + $yg, $X, ($Ad == count($K["source"]) - 1 ? "foreignAddRow.call(this);" : ""), "label-source"), "<td>" . html_select("target[" . (+$y) . "]", $Yg, $K["target"][$y], "", "label-target");
    $Ad++;
  }
  echo '</table>
<p>
ON DELETE: ', html_select("on_delete", array(-1 => "") + explode("|", $k->onActions), $K["on_delete"]), ' ON UPDATE: ', html_select("on_update", array(-1 => "") + explode("|", $k->onActions), $K["on_update"]), doc_link(array('sql' => "innodb-foreign-key-constraints.html", 'mariadb' => "foreign-keys/",)), '<p>
<input type="submit" value="Save">
<noscript><p><input type="submit" name="add" value="Add column"></noscript>
';
  if ($D != "") echo '<input type="submit" name="drop" value="Drop">', confirm(sprintf('Drop %s?', $D));
  echo '<input type="hidden" name="token" value="', $T, '">
</form>
';
} elseif (isset($_GET["view"])) {
  $a = $_GET["view"];
  $K = $_POST;
  $Ze = "VIEW";
  if (JUSH == "pgsql" && $a != "") {
    $Fg = table_status($a);
    $Ze = strtoupper($Fg["Engine"]);
  }
  if ($_POST && !$l) {
    $D = trim($K["name"]);
    $ra = " AS\n$K[select]";
    $A = ME . "table=" . urlencode($D);
    $C = 'View has been altered.';
    $U = ($_POST["materialized"] ? "MATERIALIZED VIEW" : "VIEW");
    if (!$_POST["drop"] && $a == $D && JUSH != "sqlite" && $U == "VIEW" && $Ze == "VIEW") query_redirect((JUSH == "mssql" ? "ALTER" : "CREATE OR REPLACE") . " VIEW " . table($D) . $ra, $A, $C);
    else {
      $ah = $D . "_adminer_" . uniqid();
      drop_create("DROP $Ze " . table($a), "CREATE $U " . table($D) . $ra, "DROP $U " . table($D), "CREATE $U " . table($ah) . $ra, "DROP $U " . table($ah), ($_POST["drop"] ? substr(ME, 0, -1) : $A), 'View has been dropped.', $C, 'View has been created.', $a, $D);
    }
  }
  if (!$_POST && $a != "") {
    $K = view($a);
    $K["name"] = $a;
    $K["materialized"] = ($Ze != "VIEW");
    if (!$l) $l = error();
  }
  page_header(($a != "" ? 'Alter view' : 'Create view'), $l, array("table" => $a), h($a));
  echo '
<form action="" method="post">
<p>Name: <input name="name" value="', h($K["name"]), '" data-maxlength="64" autocapitalize="off">
', (support("materializedview") ? " " . checkbox("materialized", 1, $K["materialized"], 'Materialized view') : ""), '<p>';
  textarea("select", $K["select"]);
  echo '<p>
<input type="submit" value="Save">
';
  if ($a != "") echo '<input type="submit" name="drop" value="Drop">', confirm(sprintf('Drop %s?', $a));
  echo '<input type="hidden" name="token" value="', $T, '">
</form>
';
} elseif (isset($_GET["event"])) {
  $aa = $_GET["event"];
  $td = array("YEAR", "QUARTER", "MONTH", "DAY", "HOUR", "MINUTE", "WEEK", "SECOND", "YEAR_MONTH", "DAY_HOUR", "DAY_MINUTE", "DAY_SECOND", "HOUR_MINUTE", "HOUR_SECOND", "MINUTE_SECOND");
  $Gg = array("ENABLED" => "ENABLE", "DISABLED" => "DISABLE", "SLAVESIDE_DISABLED" => "DISABLE ON SLAVE");
  $K = $_POST;
  if ($_POST && !$l) {
    if ($_POST["drop"]) query_redirect("DROP EVENT " . idf_escape($aa), substr(ME, 0, -1), 'Event has been dropped.');
    elseif (in_array($K["INTERVAL_FIELD"], $td) && isset($Gg[$K["STATUS"]])) {
      $dg = "\nON SCHEDULE " . ($K["INTERVAL_VALUE"] ? "EVERY " . q($K["INTERVAL_VALUE"]) . " $K[INTERVAL_FIELD]" . ($K["STARTS"] ? " STARTS " . q($K["STARTS"]) : "") . ($K["ENDS"] ? " ENDS " . q($K["ENDS"]) : "") : "AT " . q($K["STARTS"])) . " ON COMPLETION" . ($K["ON_COMPLETION"] ? "" : " NOT") . " PRESERVE";
      queries_redirect(substr(ME, 0, -1), ($aa != "" ? 'Event has been altered.' : 'Event has been created.'), queries(($aa != "" ? "ALTER EVENT " . idf_escape($aa) . $dg . ($aa != $K["EVENT_NAME"] ? "\nRENAME TO " . idf_escape($K["EVENT_NAME"]) : "") : "CREATE EVENT " . idf_escape($K["EVENT_NAME"]) . $dg) . "\n" . $Gg[$K["STATUS"]] . " COMMENT " . q($K["EVENT_COMMENT"]) . rtrim(" DO\n$K[EVENT_DEFINITION]", ";") . ";"));
    }
  }
  page_header(($aa != "" ? 'Alter event' . ": " . h($aa) : 'Create event'), $l);
  if (!$K && $aa != "") {
    $L = get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = " . q(DB) . " AND EVENT_NAME = " . q($aa));
    $K = reset($L);
  }
  echo '
<form action="" method="post">
<table class="layout">
<tr><th>Name<td><input name="EVENT_NAME" value="', h($K["EVENT_NAME"]), '" data-maxlength="64" autocapitalize="off">
<tr><th title="datetime">Start<td><input name="STARTS" value="', h("$K[EXECUTE_AT]$K[STARTS]"), '">
<tr><th title="datetime">End<td><input name="ENDS" value="', h($K["ENDS"]), '">
<tr><th>Every<td><input type="number" name="INTERVAL_VALUE" value="', h($K["INTERVAL_VALUE"]), '" class="size"> ', html_select("INTERVAL_FIELD", $td, $K["INTERVAL_FIELD"]), '<tr><th>Status<td>', html_select("STATUS", $Gg, $K["STATUS"]), '<tr><th>Comment<td><input name="EVENT_COMMENT" value="', h($K["EVENT_COMMENT"]), '" data-maxlength="64">
<tr><th><td>', checkbox("ON_COMPLETION", "PRESERVE", $K["ON_COMPLETION"] == "PRESERVE", 'On completion preserve'), '</table>
<p>';
  textarea("EVENT_DEFINITION", $K["EVENT_DEFINITION"]);
  echo '<p>
<input type="submit" value="Save">
';
  if ($aa != "") echo '<input type="submit" name="drop" value="Drop">', confirm(sprintf('Drop %s?', $aa));
  echo '<input type="hidden" name="token" value="', $T, '">
</form>
';
} elseif (isset($_GET["procedure"])) {
  $da = ($_GET["name"] ?: $_GET["procedure"]);
  $ag = (isset($_GET["function"]) ? "FUNCTION" : "PROCEDURE");
  $K = $_POST;
  $K["fields"] = (array)$K["fields"];
  if ($_POST && !process_fields($K["fields"]) && !$l) {
    $Ve = routine($_GET["procedure"], $ag);
    $ah = "$K[name]_adminer_" . uniqid();
    drop_create("DROP $ag " . routine_id($da, $Ve), create_routine($ag, $K), "DROP $ag " . routine_id($K["name"], $K), create_routine($ag, array("name" => $ah) + $K), "DROP $ag " . routine_id($ah, $K), substr(ME, 0, -1), 'Routine has been dropped.', 'Routine has been altered.', 'Routine has been created.', $da, $K["name"]);
  }
  page_header(($da != "" ? (isset($_GET["function"]) ? 'Alter function' : 'Alter procedure') . ": " . h($da) : (isset($_GET["function"]) ? 'Create function' : 'Create procedure')), $l);
  if (!$_POST && $da != "") {
    $K = routine($_GET["procedure"], $ag);
    $K["name"] = $da;
  }
  $Za = get_vals("SHOW CHARACTER SET");
  sort($Za);
  $bg = routine_languages();
  echo ($Za ? "<datalist id='collations'>" . optionlist($Za) . "</datalist>" : ""), '
<form action="" method="post" id="form">
<p>Name: <input name="name" value="', h($K["name"]), '" data-maxlength="64" autocapitalize="off">
', ($bg ? 'Language' . ": " . html_select("language", $bg, $K["language"]) . "\n" : ""), '<input type="submit" value="Save">
<div class="scrollable">
<table class="nowrap">
';
  edit_fields($K["fields"], $Za, $ag);
  if (isset($_GET["function"])) {
    echo "<tr><td>" . 'Return type';
    edit_type("returns", $K["returns"], $Za, array(), (JUSH == "pgsql" ? array("void", "trigger") : array()));
  }
  echo '</table>
', script("editFields();"), '</div>
<p>';
  textarea("definition", $K["definition"]);
  echo '<p>
<input type="submit" value="Save">
';
  if ($da != "") echo '<input type="submit" name="drop" value="Drop">', confirm(sprintf('Drop %s?', $da));
  echo '<input type="hidden" name="token" value="', $T, '">
</form>
';
} elseif (isset($_GET["check"])) {
  $a = $_GET["check"];
  $D = $_GET["name"];
  $K = $_POST;
  if ($K && !$l) {
    if (JUSH == "sqlite") $I = recreate_table($a, $a, array(), array(), array(), 0, array(), $D, ($K["drop"] ? "" : $K["clause"]));
    else {
      $I = ($D == "" || queries("ALTER TABLE " . table($a) . " DROP CONSTRAINT " . idf_escape($D)));
      if (!$K["drop"]) $I = queries("ALTER TABLE " . table($a) . " ADD" . ($K["name"] != "" ? " CONSTRAINT " . idf_escape($K["name"]) : "") . " CHECK ($K[clause])");
    }
    queries_redirect(ME . "table=" . urlencode($a), ($K["drop"] ? 'Check has been dropped.' : ($D != "" ? 'Check has been altered.' : 'Check has been created.')), $I);
  }
  page_header(($D != "" ? 'Alter check' . ": " . h($D) : 'Create check'), $l, array("table" => $a));
  if (!$K) {
    $Qa = $k->checkConstraints($a);
    $K = array("name" => $D, "clause" => $Qa[$D]);
  }
  echo '
<form action="" method="post">
<p>';
  if (JUSH != "sqlite") echo 'Name' . ': <input name="name" value="' . h($K["name"]) . '" data-maxlength="64" autocapitalize="off"> ';
  echo
  doc_link(array('sql' => "create-table-check-constraints.html", 'mariadb' => "constraint/",), "?"), '<p>';
  textarea("clause", $K["clause"]);
  echo '<p><input type="submit" value="Save">
';
  if ($D != "") echo '<input type="submit" name="drop" value="Drop">', confirm(sprintf('Drop %s?', $D));
  echo '<input type="hidden" name="token" value="', $T, '">
</form>
';
} elseif (isset($_GET["trigger"])) {
  $a = $_GET["trigger"];
  $D = $_GET["name"];
  $uh = trigger_options();
  $K = (array)trigger($D, $a) + array("Trigger" => $a . "_bi");
  if ($_POST) {
    if (!$l && in_array($_POST["Timing"], $uh["Timing"]) && in_array($_POST["Event"], $uh["Event"]) && in_array($_POST["Type"], $uh["Type"])) {
      $He = " ON " . table($a);
      $Ob = "DROP TRIGGER " . idf_escape($D) . (JUSH == "pgsql" ? $He : "");
      $A = ME . "table=" . urlencode($a);
      if ($_POST["drop"]) query_redirect($Ob, $A, 'Trigger has been dropped.');
      else {
        if ($D != "") queries($Ob);
        queries_redirect($A, ($D != "" ? 'Trigger has been altered.' : 'Trigger has been created.'), queries(create_trigger($He, $_POST)));
        if ($D != "") queries(create_trigger($He, $K + array("Type" => reset($uh["Type"]))));
      }
    }
    $K = $_POST;
  }
  page_header(($D != "" ? 'Alter trigger' . ": " . h($D) : 'Create trigger'), $l, array("table" => $a));
  echo '
<form action="" method="post" id="form">
<table class="layout">
<tr><th>Time<td>', html_select("Timing", $uh["Timing"], $K["Timing"], "triggerChange(/^" . preg_quote($a, "/") . "_[ba][iud]$/, '" . js_escape($a) . "', this.form);"), '<tr><th>Event<td>', html_select("Event", $uh["Event"], $K["Event"], "this.form['Timing'].onchange();"), (in_array("UPDATE OF", $uh["Event"]) ? " <input name='Of' value='" . h($K["Of"]) . "' class='hidden'>" : ""), '<tr><th>Type<td>', html_select("Type", $uh["Type"], $K["Type"]), '</table>
<p>Name: <input name="Trigger" value="', h($K["Trigger"]), '" data-maxlength="64" autocapitalize="off">
', script("qs('#form')['Timing'].onchange();"), '<p>';
  textarea("Statement", $K["Statement"]);
  echo '<p>
<input type="submit" value="Save">
';
  if ($D != "") echo '<input type="submit" name="drop" value="Drop">', confirm(sprintf('Drop %s?', $D));
  echo '<input type="hidden" name="token" value="', $T, '">
</form>
';
} elseif (isset($_GET["user"])) {
  $fa = $_GET["user"];
  $Df = array("" => array("All privileges" => ""));
  foreach (get_rows("SHOW PRIVILEGES") as $K) {
    foreach (explode(",", ($K["Privilege"] == "Grant option" ? "" : $K["Context"])) as $kb) $Df[$kb][$K["Privilege"]] = $K["Comment"];
  }
  $Df["Server Admin"] += $Df["File access on server"];
  $Df["Databases"]["Create routine"] = $Df["Procedures"]["Create routine"];
  unset($Df["Procedures"]["Create routine"]);
  $Df["Columns"] = array();
  foreach (array("Select", "Insert", "Update", "References") as $X) $Df["Columns"][$X] = $Df["Tables"][$X];
  unset($Df["Server Admin"]["Usage"]);
  foreach ($Df["Tables"] as $y => $X) unset($Df["Databases"][$y]);
  $te = array();
  if ($_POST) {
    foreach ($_POST["objects"] as $y => $X) $te[$X] = (array)$te[$X] + (array)$_POST["grants"][$y];
  }
  $Sc = array();
  $Fe = "";
  if (isset($_GET["host"]) && ($I = $e->query("SHOW GRANTS FOR " . q($fa) . "@" . q($_GET["host"])))) {
    while ($K = $I->fetch_row()) {
      if (preg_match('~GRANT (.*) ON (.*) TO ~', $K[0], $B) && preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~', $B[1], $Yd, PREG_SET_ORDER)) {
        foreach (
          $Yd
          as $X
        ) {
          if ($X[1] != "USAGE") $Sc["$B[2]$X[2]"][$X[1]] = true;
          if (preg_match('~ WITH GRANT OPTION~', $K[0])) $Sc["$B[2]$X[2]"]["GRANT OPTION"] = true;
        }
      }
      if (preg_match("~ IDENTIFIED BY PASSWORD '([^']+)~", $K[0], $B)) $Fe = $B[1];
    }
  }
  if ($_POST && !$l) {
    $Ge = (isset($_GET["host"]) ? q($fa) . "@" . q($_GET["host"]) : "''");
    if ($_POST["drop"]) query_redirect("DROP USER $Ge", ME . "privileges=", 'User has been dropped.');
    else {
      $ve = q($_POST["user"]) . "@" . q($_POST["host"]);
      $mf = $_POST["pass"];
      if ($mf != '' && !$_POST["hashed"] && !min_version(8)) {
        $mf = get_val("SELECT PASSWORD(" . q($mf) . ")");
        $l = !$mf;
      }
      $ob = false;
      if (!$l) {
        if ($Ge != $ve) {
          $ob = queries((min_version(5) ? "CREATE USER" : "GRANT USAGE ON *.* TO") . " $ve IDENTIFIED BY " . (min_version(8) ? "" : "PASSWORD ") . q($mf));
          $l = !$ob;
        } elseif ($mf != $Fe) queries("SET PASSWORD FOR $ve = " . q($mf));
      }
      if (!$l) {
        $Xf = array();
        foreach (
          $te
          as $Ae => $Rc
        ) {
          if (isset($_GET["grant"])) $Rc = array_filter($Rc);
          $Rc = array_keys($Rc);
          if (isset($_GET["grant"])) $Xf = array_diff(array_keys(array_filter($te[$Ae], 'strlen')), $Rc);
          elseif ($Ge == $ve) {
            $De = array_keys((array)$Sc[$Ae]);
            $Xf = array_diff($De, $Rc);
            $Rc = array_diff($Rc, $De);
            unset($Sc[$Ae]);
          }
          if (preg_match('~^(.+)\s*(\(.*\))?$~U', $Ae, $B) && (!grant("REVOKE", $Xf, $B[2], " ON $B[1] FROM $ve") || !grant("GRANT", $Rc, $B[2], " ON $B[1] TO $ve"))) {
            $l = true;
            break;
          }
        }
      }
      if (!$l && isset($_GET["host"])) {
        if ($Ge != $ve) queries("DROP USER $Ge");
        elseif (!isset($_GET["grant"])) {
          foreach (
            $Sc
            as $Ae => $Xf
          ) {
            if (preg_match('~^(.+)(\(.*\))?$~U', $Ae, $B)) grant("REVOKE", array_keys($Xf), $B[2], " ON $B[1] FROM $ve");
          }
        }
      }
      queries_redirect(ME . "privileges=", (isset($_GET["host"]) ? 'User has been altered.' : 'User has been created.'), !$l);
      if ($ob) $e->query("DROP USER $ve");
    }
  }
  page_header((isset($_GET["host"]) ? 'Username' . ": " . h("$fa@$_GET[host]") : 'Create user'), $l, array("privileges" => array('', 'Privileges')));
  $K = $_POST;
  if ($K) $Sc = $te;
  else {
    $K = $_GET + array("host" => get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)"));
    $K["pass"] = $Fe;
    if ($Fe != "") $K["hashed"] = true;
    $Sc[(DB == "" || $Sc ? "" : idf_escape(addcslashes(DB, "%_\\"))) . ".*"] = array();
  }
  echo '<form action="" method="post">
<table class="layout">
<tr><th>Server<td><input name="host" data-maxlength="60" value="', h($K["host"]), '" autocapitalize="off">
<tr><th>Username<td><input name="user" data-maxlength="80" value="', h($K["user"]), '" autocapitalize="off">
<tr><th>Password<td><input name="pass" id="pass" value="', h($K["pass"]), '" autocomplete="new-password">
', ($K["hashed"] ? "" : script("typePassword(qs('#pass'));")), (min_version(8) ? "" : checkbox("hashed", 1, $K["hashed"], 'Hashed', "typePassword(this.form['pass'], this.checked);")), '</table>

', "<table class='odds'>\n", "<thead><tr><th colspan='2'>" . 'Privileges' . doc_link(array('sql' => "grant.html#priv_level"));
  $u = 0;
  foreach (
    $Sc
    as $Ae => $Rc
  ) {
    echo '<th>' . ($Ae != "*.*" ? "<input name='objects[$u]' value='" . h($Ae) . "' size='10' autocapitalize='off'>" : "<input type='hidden' name='objects[$u]' value='*.*' size='10'>*.*");
    $u++;
  }
  echo "</thead>\n";
  foreach (array("" => "", "Server Admin" => 'Server', "Databases" => 'Database', "Tables" => 'Table', "Columns" => 'Column', "Procedures" => 'Routine',) as $kb => $Fb) {
    foreach ((array)$Df[$kb] as $Cf => $db) {
      echo "<tr><td" . ($Fb ? ">$Fb<td" : " colspan='2'") . ' lang="en" title="' . h($db) . '">' . h($Cf);
      $u = 0;
      foreach (
        $Sc
        as $Ae => $Rc
      ) {
        $D = "'grants[$u][" . h(strtoupper($Cf)) . "]'";
        $Y = $Rc[strtoupper($Cf)];
        if ($kb == "Server Admin" && $Ae != (isset($Sc["*.*"]) ? "*.*" : ".*")) echo "<td>";
        elseif (isset($_GET["grant"])) echo "<td><select name=$D><option><option value='1'" . ($Y ? " selected" : "") . ">" . 'Grant' . "<option value='0'" . ($Y == "0" ? " selected" : "") . ">" . 'Revoke' . "</select>";
        else
          echo "<td align='center'><label class='block'>", "<input type='checkbox' name=$D value='1'" . ($Y ? " checked" : "") . ($Cf == "All privileges" ? " id='grants-$u-all'>" : ">" . ($Cf == "Grant option" ? "" : script("qsl('input').onclick = function () { if (this.checked) formUncheck('grants-$u-all'); };"))), "</label>";
        $u++;
      }
    }
  }
  echo "</table>\n", '<p>
<input type="submit" value="Save">
';
  if (isset($_GET["host"])) echo '<input type="submit" name="drop" value="Drop">', confirm(sprintf('Drop %s?', "$fa@$_GET[host]"));
  echo '<input type="hidden" name="token" value="', $T, '">
</form>
';
} elseif (isset($_GET["processlist"])) {
  if (support("kill")) {
    if ($_POST && !$l) {
      $Fd = 0;
      foreach ((array)$_POST["kill"] as $X) {
        if (kill_process($X)) $Fd++;
      }
      queries_redirect(ME . "processlist=", lang(array('%d process has been killed.', '%d processes have been killed.'), $Fd), $Fd || !$_POST["kill"]);
    }
  }
  page_header('Process list', $l);
  echo '
<form action="" method="post">
<div class="scrollable">
<table class="nowrap checkable odds">
', script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});");
  $u = -1;
  foreach (process_list() as $u => $K) {
    if (!$u) {
      echo "<thead><tr lang='en'>" . (support("kill") ? "<th>" : "");
      foreach (
        $K
        as $y => $X
      ) echo "<th>$y" . doc_link(array('sql' => "show-processlist.html#processlist_" . strtolower($y),));
      echo "</thead>\n";
    }
    echo "<tr>" . (support("kill") ? "<td>" . checkbox("kill[]", $K[JUSH == "sql" ? "Id" : "pid"], 0) : "");
    foreach (
      $K
      as $y => $X
    ) echo "<td>" . ((JUSH == "sql" && $y == "Info" && preg_match("~Query|Killed~", $K["Command"]) && $X != "") || (JUSH == "pgsql" && $y == "current_query" && $X != "<IDLE>") || (JUSH == "oracle" && $y == "sql_text" && $X != "") ? "<code class='jush-" . JUSH . "'>" . shorten_utf8($X, 100, "</code>") . ' <a href="' . h(ME . ($K["db"] != "" ? "db=" . urlencode($K["db"]) . "&" : "") . "sql=" . urlencode($X)) . '">' . 'Clone' . '</a>' : h($X));
    echo "\n";
  }
  echo '</table>
</div>
<p>
';
  if (support("kill")) echo ($u + 1) . "/" . sprintf('%d in total', max_connections()), "<p><input type='submit' value='" . 'Kill' . "'>\n";
  echo '<input type="hidden" name="token" value="', $T, '">
</form>
', script("tableCheck();");
} elseif (isset($_GET["select"])) {
  $a = $_GET["select"];
  $R = table_status1($a);
  $x = indexes($a);
  $n = fields($a);
  $q = column_foreign_keys($a);
  $Ce = $R["Oid"];
  $ka = get_settings("adminer_import");
  $Yf = array();
  $d = array();
  $gg = array();
  $Re = array();
  $dh = null;
  foreach (
    $n
    as $y => $m
  ) {
    $D = $b->fieldName($m);
    $re = html_entity_decode(strip_tags($D), ENT_QUOTES);
    if (isset($m["privileges"]["select"]) && $D != "") {
      $d[$y] = $re;
      if (is_shortable($m)) $dh = $b->selectLengthProcess();
    }
    if (isset($m["privileges"]["where"]) && $D != "") $gg[$y] = $re;
    if (isset($m["privileges"]["order"]) && $D != "") $Re[$y] = $re;
    $Yf += $m["privileges"];
  }
  list($N, $t) = $b->selectColumnsProcess($d, $x);
  $N = array_unique($N);
  $t = array_unique($t);
  $xd = count($t) < count($N);
  $Z = $b->selectSearchProcess($n, $x);
  $Qe = $b->selectOrderProcess($n, $x);
  $z = $b->selectLimitProcess();
  if ($_GET["val"] && is_ajax()) {
    header("Content-Type: text/plain; charset=utf-8");
    foreach ($_GET["val"] as $Bh => $K) {
      $ra = convert_field($n[key($K)]);
      $N = array($ra ?: idf_escape(key($K)));
      $Z[] = where_check($Bh, $n);
      $J = $k->select($a, $N, $Z, $N);
      if ($J) echo
      reset($J->fetch_row());
    }
    exit;
  }
  $_f = $Dh = null;
  foreach (
    $x
    as $w
  ) {
    if ($w["type"] == "PRIMARY") {
      $_f = array_flip($w["columns"]);
      $Dh = ($N ? $_f : array());
      foreach (
        $Dh
        as $y => $X
      ) {
        if (in_array(idf_escape($y), $N)) unset($Dh[$y]);
      }
      break;
    }
  }
  if ($Ce && !$_f) {
    $_f = $Dh = array($Ce => 0);
    $x[] = array("type" => "PRIMARY", "columns" => array($Ce));
  }
  if ($_POST && !$l) {
    $Zh = $Z;
    if (!$_POST["all"] && is_array($_POST["check"])) {
      $Qa = array();
      foreach ($_POST["check"] as $Na) $Qa[] = where_check($Na, $n);
      $Zh[] = "((" . implode(") OR (", $Qa) . "))";
    }
    $Zh = ($Zh ? "\nWHERE " . implode(" AND ", $Zh) : "");
    if ($_POST["export"]) {
      save_settings(array("output" => $_POST["output"], "format" => $_POST["format"]), "adminer_import");
      dump_headers($a);
      $b->dumpTable($a, "");
      $Mc = ($N ? implode(", ", $N) : "*") . convert_fields($d, $n, $N) . "\nFROM " . table($a);
      $Uc = ($t && $xd ? "\nGROUP BY " . implode(", ", $t) : "") . ($Qe ? "\nORDER BY " . implode(", ", $Qe) : "");
      $H = "SELECT $Mc$Zh$Uc";
      if (is_array($_POST["check"]) && !$_f) {
        $_h = array();
        foreach ($_POST["check"] as $X) $_h[] = "(SELECT" . limit($Mc, "\nWHERE " . ($Z ? implode(" AND ", $Z) . " AND " : "") . where_check($X, $n) . $Uc, 1) . ")";
        $H = implode(" UNION ALL ", $_h);
      }
      $b->dumpData($a, "table", $H);
      $b->dumpFooter();
      exit;
    }
    if (!$b->selectEmailProcess($Z, $q)) {
      if ($_POST["save"] || $_POST["delete"]) {
        $I = true;
        $la = 0;
        $P = array();
        if (!$_POST["delete"]) {
          foreach ($_POST["fields"] as $D => $X) {
            $X = process_input($n[$D]);
            if ($X !== null && ($_POST["clone"] || $X !== false)) $P[idf_escape($D)] = ($X !== false ? $X : idf_escape($D));
          }
        }
        if ($_POST["delete"] || $P) {
          if ($_POST["clone"]) $H = "INTO " . table($a) . " (" . implode(", ", array_keys($P)) . ")\nSELECT " . implode(", ", $P) . "\nFROM " . table($a);
          if ($_POST["all"] || ($_f && is_array($_POST["check"])) || $xd) {
            $I = ($_POST["delete"] ? $k->delete($a, $Zh) : ($_POST["clone"] ? queries("INSERT $H$Zh") : $k->update($a, $P, $Zh)));
            $la = $e->affected_rows;
          } else {
            foreach ((array)$_POST["check"] as $X) {
              $Yh = "\nWHERE " . ($Z ? implode(" AND ", $Z) . " AND " : "") . where_check($X, $n);
              $I = ($_POST["delete"] ? $k->delete($a, $Yh, 1) : ($_POST["clone"] ? queries("INSERT" . limit1($a, $H, $Yh)) : $k->update($a, $P, $Yh, 1)));
              if (!$I) break;
              $la += $e->affected_rows;
            }
          }
        }
        $C = lang(array('%d item has been affected.', '%d items have been affected.'), $la);
        if ($_POST["clone"] && $I && $la == 1) {
          $Kd = last_id();
          if ($Kd) $C = sprintf('Item%s has been inserted.', " $Kd");
        }
        queries_redirect(remove_from_uri($_POST["all"] && $_POST["delete"] ? "page" : ""), $C, $I);
        if (!$_POST["delete"]) {
          $xf = (array)$_POST["fields"];
          edit_form($a, array_intersect_key($n, $xf), $xf, !$_POST["clone"]);
          page_footer();
          exit;
        }
      } elseif (!$_POST["import"]) {
        if (!$_POST["val"]) $l = 'Ctrl+click on a value to modify it.';
        else {
          $I = true;
          $la = 0;
          foreach ($_POST["val"] as $Bh => $K) {
            $P = array();
            foreach (
              $K
              as $y => $X
            ) {
              $y = bracket_escape($y, 1);
              $P[idf_escape($y)] = (preg_match('~char|text~', $n[$y]["type"]) || $X != "" ? $b->processInput($n[$y], $X) : "NULL");
            }
            $I = $k->update($a, $P, " WHERE " . ($Z ? implode(" AND ", $Z) . " AND " : "") . where_check($Bh, $n), !$xd && !$_f, " ");
            if (!$I) break;
            $la += $e->affected_rows;
          }
          queries_redirect(remove_from_uri(), lang(array('%d item has been affected.', '%d items have been affected.'), $la), $I);
        }
      } elseif (!is_string($Bc = get_file("csv_file", true))) $l = upload_error($Bc);
      elseif (!preg_match('~~u', $Bc)) $l = 'File must be in UTF-8 encoding.';
      else {
        save_settings(array("output" => $ka["output"], "format" => $_POST["separator"]), "adminer_import");
        $I = true;
        $ab = array_keys($n);
        preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~', $Bc, $Yd);
        $la = count($Yd[0]);
        $k->begin();
        $mg = ($_POST["separator"] == "csv" ? "," : ($_POST["separator"] == "tsv" ? "\t" : ";"));
        $L = array();
        foreach ($Yd[0] as $y => $X) {
          preg_match_all("~((?>\"[^\"]*\")+|[^$mg]*)$mg~", $X . $mg, $Zd);
          if (!$y && !array_diff($Zd[1], $ab)) {
            $ab = $Zd[1];
            $la--;
          } else {
            $P = array();
            foreach ($Zd[1] as $u => $Wa) $P[idf_escape($ab[$u])] = ($Wa == "" && $n[$ab[$u]]["null"] ? "NULL" : q(preg_match('~^".*"$~s', $Wa) ? str_replace('""', '"', substr($Wa, 1, -1)) : $Wa));
            $L[] = $P;
          }
        }
        $I = (!$L || $k->insertUpdate($a, $L, $_f));
        if ($I) $k->commit();
        queries_redirect(remove_from_uri("page"), lang(array('%d row has been imported.', '%d rows have been imported.'), $la), $I);
        $k->rollback();
      }
    }
  }
  $Rg = $b->tableName($R);
  if (is_ajax()) {
    page_headers();
    ob_start();
  } else
    page_header('Select' . ": $Rg", $l);
  $P = null;
  if (isset($Yf["insert"]) || !support("table")) {
    $gf = array();
    foreach ((array)$_GET["where"] as $X) {
      if (isset($q[$X["col"]]) && count($q[$X["col"]]) == 1 && ($X["op"] == "=" || (!$X["op"] && (is_array($X["val"]) || !preg_match('~[_%]~', $X["val"]))))) $gf["set" . "[" . bracket_escape($X["col"]) . "]"] = $X["val"];
    }
    $P = $gf ? "&" . http_build_query($gf) : "";
  }
  $b->selectLinks($R, $P);
  if (!$d && support("table")) echo "<p class='error'>" . 'Unable to select the table' . ($n ? "." : ": " . error()) . "\n";
  else {
    echo "<form action='' id='form'>\n", "<div style='display: none;'>";
    hidden_fields_get();
    echo (DB != "" ? '<input type="hidden" name="db" value="' . h(DB) . '">' . (isset($_GET["ns"]) ? '<input type="hidden" name="ns" value="' . h($_GET["ns"]) . '">' : "") : ""), '<input type="hidden" name="select" value="' . h($a) . '">', "</div>\n";
    $b->selectColumnsPrint($N, $d);
    $b->selectSearchPrint($Z, $gg, $x);
    $b->selectOrderPrint($Qe, $Re, $x);
    $b->selectLimitPrint($z);
    $b->selectLengthPrint($dh);
    $b->selectActionPrint($x);
    echo "</form>\n";
    $E = $_GET["page"];
    if ($E == "last") {
      $Lc = get_val(count_rows($a, $Z, $xd, $t));
      $E = floor(max(0, $Lc - 1) / $z);
    }
    $hg = $N;
    $Tc = $t;
    if (!$hg) {
      $hg[] = "*";
      $lb = convert_fields($d, $n, $N);
      if ($lb) $hg[] = substr($lb, 2);
    }
    foreach (
      $N
      as $y => $X
    ) {
      $m = $n[idf_unescape($X)];
      if ($m && ($ra = convert_field($m))) $hg[$y] = "$ra AS $X";
    }
    if (!$xd && $Dh) {
      foreach (
        $Dh
        as $y => $X
      ) {
        $hg[] = idf_escape($y);
        if ($Tc) $Tc[] = idf_escape($y);
      }
    }
    $I = $k->select($a, $hg, $Z, $Tc, $Qe, $z, $E, true);
    if (!$I) echo "<p class='error'>" . error() . "\n";
    else {
      if (JUSH == "mssql" && $E) $I->seek($z * $E);
      $ac = array();
      echo "<form action='' method='post' enctype='multipart/form-data'>\n";
      $L = array();
      while ($K = $I->fetch_assoc()) {
        if ($E && JUSH == "oracle") unset($K["RNUM"]);
        $L[] = $K;
      }
      if ($_GET["page"] != "last" && $z != "" && $t && $xd && JUSH == "sql") $Lc = get_val(" SELECT FOUND_ROWS()");
      if (!$L) echo "<p class='message'>" . 'No rows.' . "\n";
      else {
        $_a = $b->backwardKeys($a, $Rg);
        echo "<div class='scrollable'>", "<table id='table' class='nowrap checkable odds'>", script("mixin(qs('#table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true), onkeydown: editingKeydown});"), "<thead><tr>" . (!$t && $N ? "" : "<td><input type='checkbox' id='all-page' class='jsonly'>" . script("qs('#all-page').onclick = partial(formCheck, /check/);", "") . " <a href='" . h($_GET["modify"] ? remove_from_uri("modify") : $_SERVER["REQUEST_URI"] . "&modify=1") . "'>" . 'Modify' . "</a>");
        $se = array();
        $Oc = array();
        reset($N);
        $Lf = 1;
        foreach ($L[0] as $y => $X) {
          if (!isset($Dh[$y])) {
            $X = $_GET["columns"][key($N)];
            $m = $n[$N ? ($X ? $X["col"] : current($N)) : $y];
            $D = ($m ? $b->fieldName($m, $Lf) : ($X["fun"] ? "*" : h($y)));
            if ($D != "") {
              $Lf++;
              $se[$y] = $D;
              $c = idf_escape($y);
              $gd = remove_from_uri('(order|desc)[^=]*|page') . '&order%5B0%5D=' . urlencode($y);
              $Fb = "&desc%5B0%5D=1";
              $xg = isset($m["privileges"]["order"]);
              echo "<th id='th[" . h(bracket_escape($y)) . "]'>" . script("mixin(qsl('th'), {onmouseover: partial(columnMouse), onmouseout: partial(columnMouse, ' hidden')});", "");
              $Nc = apply_sql_function($X["fun"], $D);
              echo ($xg ? '<a href="' . h($gd . ($Qe[0] == $c || $Qe[0] == $y || (!$Qe && $xd && $t[0] == $c) ? $Fb : '')) . '">' . "$Nc</a>" : $Nc), "<span class='column hidden'>";
              if ($xg) echo "<a href='" . h($gd . $Fb) . "' title='" . 'descending' . "' class='text'> ↓</a>";
              if (!$X["fun"] && isset($m["privileges"]["where"])) echo '<a href="#fieldset-search" title="' . 'Search' . '" class="text jsonly"> =</a>', script("qsl('a').onclick = partial(selectSearch, '" . js_escape($y) . "');");
              echo "</span>";
            }
            $Oc[$y] = $X["fun"];
            next($N);
          }
        }
        $Qd = array();
        if ($_GET["modify"]) {
          foreach (
            $L
            as $K
          ) {
            foreach (
              $K
              as $y => $X
            ) $Qd[$y] = max($Qd[$y], min(40, strlen(utf8_decode($X))));
          }
        }
        echo ($_a ? "<th>" . 'Relations' : "") . "</thead>\n";
        if (is_ajax()) ob_end_clean();
        foreach ($b->rowDescriptions($L, $q) as $qe => $K) {
          $Ah = unique_array($L[$qe], $x);
          if (!$Ah) {
            $Ah = array();
            foreach ($L[$qe] as $y => $X) {
              if (!preg_match('~^(COUNT\((\*|(DISTINCT )?`(?:[^`]|``)+`)\)|(AVG|GROUP_CONCAT|MAX|MIN|SUM)\(`(?:[^`]|``)+`\))$~', $y)) $Ah[$y] = $X;
            }
          }
          $Bh = "";
          foreach (
            $Ah
            as $y => $X
          ) {
            if ((JUSH == "sql" || JUSH == "pgsql") && preg_match('~char|text|enum|set~', $n[$y]["type"]) && strlen($X) > 64) {
              $y = (strpos($y, '(') ? $y : idf_escape($y));
              $y = "MD5(" . (JUSH != 'sql' || preg_match("~^utf8~", $n[$y]["collation"]) ? $y : "CONVERT($y USING " . charset($e) . ")") . ")";
              $X = md5($X);
            }
            $Bh .= "&" . ($X !== null ? urlencode("where[" . bracket_escape($y) . "]") . "=" . urlencode($X === false ? "f" : $X) : "null%5B%5D=" . urlencode($y));
          }
          echo "<tr>" . (!$t && $N ? "" : "<td>" . checkbox("check[]", substr($Bh, 1), in_array(substr($Bh, 1), (array)$_POST["check"])) . ($xd || information_schema(DB) ? "" : " <a href='" . h(ME . "edit=" . urlencode($a) . $Bh) . "' class='edit'>" . 'edit' . "</a>"));
          foreach (
            $K
            as $y => $X
          ) {
            if (isset($se[$y])) {
              $m = $n[$y];
              $X = $k->value($X, $m);
              if ($X != "" && (!isset($ac[$y]) || $ac[$y] != "")) $ac[$y] = (is_mail($X) ? $se[$y] : "");
              $_ = "";
              if (preg_match('~blob|bytea|raw|file~', $m["type"]) && $X != "") $_ = ME . 'download=' . urlencode($a) . '&field=' . urlencode($y) . $Bh;
              if (!$_ && $X !== null) {
                foreach ((array)$q[$y] as $p) {
                  if (count($q[$y]) == 1 || end($p["source"]) == $y) {
                    $_ = "";
                    foreach ($p["source"] as $u => $yg) $_ .= where_link($u, $p["target"][$u], $L[$qe][$yg]);
                    $_ = ($p["db"] != "" ? preg_replace('~([?&]db=)[^&]+~', '\1' . urlencode($p["db"]), ME) : ME) . 'select=' . urlencode($p["table"]) . $_;
                    if ($p["ns"]) $_ = preg_replace('~([?&]ns=)[^&]+~', '\1' . urlencode($p["ns"]), $_);
                    if (count($p["source"]) == 1) break;
                  }
                }
              }
              if ($y == "COUNT(*)") {
                $_ = ME . "select=" . urlencode($a);
                $u = 0;
                foreach ((array)$_GET["where"] as $W) {
                  if (!array_key_exists($W["col"], $Ah)) $_ .= where_link($u++, $W["col"], $W["val"], $W["op"]);
                }
                foreach (
                  $Ah
                  as $Cd => $W
                ) $_ .= where_link($u++, $Cd, $W);
              }
              $X = select_value($X, $_, $m, $dh);
              $v = h("val[$Bh][" . bracket_escape($y) . "]");
              $Y = $_POST["val"][$Bh][bracket_escape($y)];
              $Vb = !is_array($K[$y]) && is_utf8($X) && $L[$qe][$y] == $K[$y] && !$Oc[$y] && !$m["generated"];
              $ch = preg_match('~text|json|lob~', $m["type"]);
              echo "<td id='$v'" . (preg_match(number_type(), $m["type"]) && is_numeric(strip_tags($X)) ? " class='number'" : "");
              if (($_GET["modify"] && $Vb) || $Y !== null) {
                $Xc = h($Y !== null ? $Y : $K[$y]);
                echo ">" . ($ch ? "<textarea name='$v' cols='30' rows='" . (substr_count($K[$y], "\n") + 1) . "'>$Xc</textarea>" : "<input name='$v' value='$Xc' size='$Qd[$y]'>");
              } else {
                $Ud = strpos($X, "<i>…</i>");
                echo " data-text='" . ($Ud ? 2 : ($ch ? 1 : 0)) . "'" . ($Vb ? "" : " data-warning='" . h('Use edit link to modify this value.') . "'") . ">$X";
              }
            }
          }
          if ($_a) echo "<td>";
          $b->backwardKeysPrint($_a, $L[$qe]);
          echo "</tr>\n";
        }
        if (is_ajax()) exit;
        echo "</table>\n", "</div>\n";
      }
      if (!is_ajax()) {
        if ($L || $E) {
          $oc = true;
          if ($_GET["page"] != "last") {
            if ($z == "" || (count($L) < $z && ($L || !$E))) $Lc = ($E ? $E * $z : 0) + count($L);
            elseif (JUSH != "sql" || !$xd) {
              $Lc = ($xd ? false : found_rows($R, $Z));
              if ($Lc < max(1e4, 2 * ($E + 1) * $z)) $Lc = reset(slow_query(count_rows($a, $Z, $xd, $t)));
              else $oc = false;
            }
          }
          $ef = ($z != "" && ($Lc === false || $Lc > $z || $E));
          if ($ef) echo (($Lc === false ? count($L) + 1 : $Lc - $E * $z) > $z ? '<p><a href="' . h(remove_from_uri("page") . "&page=" . ($E + 1)) . '" class="loadmore">' . 'Load more data' . '</a>' . script("qsl('a').onclick = partial(selectLoadMore, " . (+$z) . ", '" . 'Loading' . "…');", "") : ''), "\n";
        }
        echo "<div class='footer'><div>\n";
        if ($L || $E) {
          if ($ef) {
            $be = ($Lc === false ? $E + (count($L) >= $z ? 2 : 1) : floor(($Lc - 1) / $z));
            echo "<fieldset>";
            if (JUSH != "simpledb") {
              echo "<legend><a href='" . h(remove_from_uri("page")) . "'>" . 'Page' . "</a></legend>", script("qsl('a').onclick = function () { pageClick(this.href, +prompt('" . 'Page' . "', '" . ($E + 1) . "')); return false; };"), pagination(0, $E) . ($E > 5 ? " …" : "");
              for ($u = max(1, $E - 4); $u < min($be, $E + 5); $u++) echo
              pagination($u, $E);
              if ($be > 0) echo ($E + 5 < $be ? " …" : ""), ($oc && $Lc !== false ? pagination($be, $E) : " <a href='" . h(remove_from_uri("page") . "&page=last") . "' title='~$be'>" . 'last' . "</a>");
            } else
              echo "<legend>" . 'Page' . "</legend>", pagination(0, $E) . ($E > 1 ? " …" : ""), ($E ? pagination($E, $E) : ""), ($be > $E ? pagination($E + 1, $E) . ($be > $E + 1 ? " …" : "") : "");
            echo "</fieldset>\n";
          }
          echo "<fieldset>", "<legend>" . 'Whole result' . "</legend>";
          $Lb = ($oc ? "" : "~ ") . $Lc;
          $Ke = "var checked = formChecked(this, /check/); selectCount('selected', this.checked ? '$Lb' : checked); selectCount('selected2', this.checked || !checked ? '$Lb' : checked);";
          echo
          checkbox("all", 1, 0, ($Lc !== false ? ($oc ? "" : "~ ") . lang(array('%d row', '%d rows'), $Lc) : ""), $Ke) . "\n", "</fieldset>\n";
          if ($b->selectCommandPrint()) echo '<fieldset', ($_GET["modify"] ? '' : ' class="jsonly"'), '><legend>Modify</legend><div>
<input type="submit" value="Save"', ($_GET["modify"] ? '' : ' title="' . 'Ctrl+click on a value to modify it.' . '"'), '>
</div></fieldset>
<fieldset><legend>Selected <span id="selected"></span></legend><div>
<input type="submit" name="edit" value="Edit">
<input type="submit" name="clone" value="Clone">
<input type="submit" name="delete" value="Delete">', confirm(), '</div></fieldset>
';
          $Jc = $b->dumpFormat();
          foreach ((array)$_GET["columns"] as $c) {
            if ($c["fun"]) {
              unset($Jc['sql']);
              break;
            }
          }
          if ($Jc) {
            print_fieldset("export", 'Export' . " <span id='selected2'></span>");
            $cf = $b->dumpOutput();
            echo ($cf ? html_select("output", $cf, $ka["output"]) . " " : ""), html_select("format", $Jc, $ka["format"]), " <input type='submit' name='export' value='" . 'Export' . "'>\n", "</div></fieldset>\n";
          }
          $b->selectEmailPrint(array_filter($ac, 'strlen'), $d);
        }
        echo "</div></div>\n";
        if ($b->selectImportPrint()) echo "<div>", "<a href='#import'>" . 'Import' . "</a>", script("qsl('a').onclick = partial(toggle, 'import');", ""), "<span id='import'" . ($_POST["import"] ? "" : " class='hidden'") . ">: ", "<input type='file' name='csv_file'> ", html_select("separator", array("csv" => "CSV,", "csv;" => "CSV;", "tsv" => "TSV"), $ka["format"]), " <input type='submit' name='import' value='" . 'Import' . "'>", "</span>", "</div>";
        echo "<input type='hidden' name='token' value='$T'>\n", "</form>\n", (!$t && $N ? "" : script("tableCheck();"));
      }
    }
  }
  if (is_ajax()) {
    ob_end_clean();
    exit;
  }
} elseif (isset($_GET["variables"])) {
  $Fg = isset($_GET["status"]);
  page_header($Fg ? 'Status' : 'Variables');
  $Ph = ($Fg ? show_status() : show_variables());
  if (!$Ph) echo "<p class='message'>" . 'No rows.' . "\n";
  else {
    echo "<table>\n";
    foreach (
      $Ph
      as $y => $X
    ) echo "<tr>", "<th><code class='jush-" . JUSH . ($Fg ? "status" : "set") . "'>" . h($y) . "</code>", "<td>" . nl_br(h($X));
    echo "</table>\n";
  }
} elseif (isset($_GET["script"])) {
  header("Content-Type: text/javascript; charset=utf-8");
  if ($_GET["script"] == "db") {
    $Og = array("Data_length" => 0, "Index_length" => 0, "Data_free" => 0);
    foreach (table_status() as $D => $R) {
      json_row("Comment-$D", h($R["Comment"]));
      if (!is_view($R)) {
        foreach (array("Engine", "Collation") as $y) json_row("$y-$D", h($R[$y]));
        foreach ($Og + array("Auto_increment" => 0, "Rows" => 0) as $y => $X) {
          if ($R[$y] != "") {
            $X = format_number($R[$y]);
            if ($X >= 0) json_row("$y-$D", ($y == "Rows" && $X && $R["Engine"] == (JUSH == "pgsql" ? "table" : "InnoDB") ? "~ $X" : $X));
            if (isset($Og[$y])) $Og[$y] += ($R["Engine"] != "InnoDB" || $y != "Data_free" ? $R[$y] : 0);
          } elseif (array_key_exists($y, $R)) json_row("$y-$D", "?");
        }
      }
    }
    foreach (
      $Og
      as $y => $X
    ) json_row("sum-$y", format_number($X));
    json_row("");
  } elseif ($_GET["script"] == "kill") $e->query("KILL " . number($_POST["kill"]));
  else {
    foreach (count_tables($b->databases()) as $i => $X) {
      json_row("tables-$i", $X);
      json_row("size-$i", db_size($i));
    }
    json_row("");
  }
  exit;
} else {
  $Wg = array_merge((array)$_POST["tables"], (array)$_POST["views"]);
  if ($Wg && !$l && !$_POST["search"]) {
    $I = true;
    $C = "";
    if (JUSH == "sql" && $_POST["tables"] && count($_POST["tables"]) > 1 && ($_POST["drop"] || $_POST["truncate"] || $_POST["copy"])) queries("SET foreign_key_checks = 0");
    if ($_POST["truncate"]) {
      if ($_POST["tables"]) $I = truncate_tables($_POST["tables"]);
      $C = 'Tables have been truncated.';
    } elseif ($_POST["move"]) {
      $I = move_tables((array)$_POST["tables"], (array)$_POST["views"], $_POST["target"]);
      $C = 'Tables have been moved.';
    } elseif ($_POST["copy"]) {
      $I = copy_tables((array)$_POST["tables"], (array)$_POST["views"], $_POST["target"]);
      $C = 'Tables have been copied.';
    } elseif ($_POST["drop"]) {
      if ($_POST["views"]) $I = drop_views($_POST["views"]);
      if ($I && $_POST["tables"]) $I = drop_tables($_POST["tables"]);
      $C = 'Tables have been dropped.';
    } elseif (JUSH == "sqlite" && $_POST["check"]) {
      foreach ((array)$_POST["tables"] as $Q) {
        foreach (get_rows("PRAGMA integrity_check(" . q($Q) . ")") as $K) $C .= "<b>" . h($Q) . "</b>: " . h($K["integrity_check"]) . "<br>";
      }
    } elseif (JUSH != "sql") {
      $I = (JUSH == "sqlite" ? queries("VACUUM") : apply_queries("VACUUM" . ($_POST["optimize"] ? "" : " ANALYZE"), $_POST["tables"]));
      $C = 'Tables have been optimized.';
    } elseif (!$_POST["tables"]) $C = 'No tables.';
    elseif ($I = queries(($_POST["optimize"] ? "OPTIMIZE" : ($_POST["check"] ? "CHECK" : ($_POST["repair"] ? "REPAIR" : "ANALYZE"))) . " TABLE " . implode(", ", array_map('Adminer\idf_escape', $_POST["tables"])))) {
      while ($K = $I->fetch_assoc()) $C .= "<b>" . h($K["Table"]) . "</b>: " . h($K["Msg_text"]) . "<br>";
    }
    queries_redirect(substr(ME, 0, -1), $C, $I);
  }
  page_header(($_GET["ns"] == "" ? 'Database' . ": " . h(DB) : 'Schema' . ": " . h($_GET["ns"])), $l, true);
  if ($b->homepage()) {
    if ($_GET["ns"] !== "") {
      echo "<h3 id='tables-views'>" . 'Tables and views' . "</h3>\n";
      $Vg = tables_list();
      if (!$Vg) echo "<p class='message'>" . 'No tables.' . "\n";
      else {
        echo "<form action='' method='post'>\n";
        if (support("table")) {
          echo "<fieldset><legend>" . 'Search data in tables' . " <span id='selected2'></span></legend><div>", "<input type='search' name='query' value='" . h($_POST["query"]) . "'>", script("qsl('input').onkeydown = partialArg(bodyKeydown, 'search');", ""), " <input type='submit' name='search' value='" . 'Search' . "'>\n", "</div></fieldset>\n";
          if ($_POST["search"] && $_POST["query"] != "") {
            $_GET["where"][0]["op"] = $k->convertOperator("LIKE %%");
            search_tables();
          }
        }
        echo "<div class='scrollable'>\n", "<table class='nowrap checkable odds'>\n", script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"), '<thead><tr class="wrap">', '<td><input id="check-all" type="checkbox" class="jsonly">' . script("qs('#check-all').onclick = partial(formCheck, /^(tables|views)\[/);", ""), '<th>' . 'Table', '<td>' . 'Engine' . doc_link(array('sql' => 'storage-engines.html')), '<td>' . 'Collation' . doc_link(array('sql' => 'charset-charsets.html', 'mariadb' => 'supported-character-sets-and-collations/')), '<td>' . 'Data Length' . doc_link(array('sql' => 'show-table-status.html',)), '<td>' . 'Index Length' . doc_link(array('sql' => 'show-table-status.html',)), '<td>' . 'Data Free' . doc_link(array('sql' => 'show-table-status.html')), '<td>' . 'Auto Increment' . doc_link(array('sql' => 'example-auto-increment.html', 'mariadb' => 'auto_increment/')), '<td>' . 'Rows' . doc_link(array('sql' => 'show-table-status.html',)), (support("comment") ? '<td>' . 'Comment' . doc_link(array('sql' => 'show-table-status.html',)) : ''), "</thead>\n";
        $S = 0;
        foreach (
          $Vg
          as $D => $U
        ) {
          $Sh = ($U !== null && !preg_match('~table|sequence~i', $U));
          $v = h("Table-" . $D);
          echo '<tr><td>' . checkbox(($Sh ? "views[]" : "tables[]"), $D, in_array($D, $Wg, true), "", "", "", $v), '<th>' . (support("table") || support("indexes") ? "<a href='" . h(ME) . "table=" . urlencode($D) . "' title='" . 'Show structure' . "' id='$v'>" . h($D) . '</a>' : h($D));
          if ($Sh) echo '<td colspan="6"><a href="' . h(ME) . "view=" . urlencode($D) . '" title="' . 'Alter view' . '">' . (preg_match('~materialized~i', $U) ? 'Materialized view' : 'View') . '</a>', '<td align="right"><a href="' . h(ME) . "select=" . urlencode($D) . '" title="' . 'Select data' . '">?</a>';
          else {
            foreach (array("Engine" => array(), "Collation" => array(), "Data_length" => array("create", 'Alter table'), "Index_length" => array("indexes", 'Alter indexes'), "Data_free" => array("edit", 'New item'), "Auto_increment" => array("auto_increment=1&create", 'Alter table'), "Rows" => array("select", 'Select data'),) as $y => $_) {
              $v = " id='$y-" . h($D) . "'";
              echo ($_ ? "<td align='right'>" . (support("table") || $y == "Rows" || (support("indexes") && $y != "Data_length") ? "<a href='" . h(ME . "$_[0]=") . urlencode($D) . "'$v title='$_[1]'>?</a>" : "<span$v>?</span>") : "<td id='$y-" . h($D) . "'>");
            }
            $S++;
          }
          echo (support("comment") ? "<td id='Comment-" . h($D) . "'>" : ""), "\n";
        }
        echo "<tr><td><th>" . sprintf('%d in total', count($Vg)), "<td>" . h(JUSH == "sql" ? get_val("SELECT @@default_storage_engine") : ""), "<td>" . h(db_collation(DB, collations()));
        foreach (array("Data_length", "Index_length", "Data_free") as $y) echo "<td align='right' id='sum-$y'>";
        echo "\n", "</table>\n", "</div>\n";
        if (!information_schema(DB)) {
          echo "<div class='footer'><div>\n";
          $Nh = "<input type='submit' value='" . 'Vacuum' . "'> " . on_help("'VACUUM'");
          $Ne = "<input type='submit' name='optimize' value='" . 'Optimize' . "'> " . on_help(JUSH == "sql" ? "'OPTIMIZE TABLE'" : "'VACUUM OPTIMIZE'");
          echo "<fieldset><legend>" . 'Selected' . " <span id='selected'></span></legend><div>" . (JUSH == "sqlite" ? $Nh . "<input type='submit' name='check' value='" . 'Check' . "'> " . on_help("'PRAGMA integrity_check'") : (JUSH == "pgsql" ? $Nh . $Ne : (JUSH == "sql" ? "<input type='submit' value='" . 'Analyze' . "'> " . on_help("'ANALYZE TABLE'") . $Ne . "<input type='submit' name='check' value='" . 'Check' . "'> " . on_help("'CHECK TABLE'") . "<input type='submit' name='repair' value='" . 'Repair' . "'> " . on_help("'REPAIR TABLE'") : ""))) . "<input type='submit' name='truncate' value='" . 'Truncate' . "'> " . on_help(JUSH == "sqlite" ? "'DELETE'" : "'TRUNCATE" . (JUSH == "pgsql" ? "'" : " TABLE'")) . confirm() . "<input type='submit' name='drop' value='" . 'Drop' . "'>" . on_help("'DROP TABLE'") . confirm() . "\n";
          $h = (support("scheme") ? $b->schemas() : $b->databases());
          if (count($h) != 1 && JUSH != "sqlite") {
            $i = (isset($_POST["target"]) ? $_POST["target"] : (support("scheme") ? $_GET["ns"] : DB));
            echo "<p>" . 'Move to other database' . ": ", ($h ? html_select("target", $h, $i) : '<input name="target" value="' . h($i) . '" autocapitalize="off">'), " <input type='submit' name='move' value='" . 'Move' . "'>", (support("copy") ? " <input type='submit' name='copy' value='" . 'Copy' . "'> " . checkbox("overwrite", 1, $_POST["overwrite"], 'overwrite') : ""), "\n";
          }
          echo "<input type='hidden' name='all' value=''>", script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^(tables|views)\[/));" . (support("table") ? " selectCount('selected2', formChecked(this, /^tables\[/) || $S);" : "") . " }"), "<input type='hidden' name='token' value='$T'>\n", "</div></fieldset>\n", "</div></div>\n";
        }
        echo "</form>\n", script("tableCheck();");
      }
      echo '<p class="links"><a href="' . h(ME) . 'create=">' . 'Create table' . "</a>\n", (support("view") ? '<a href="' . h(ME) . 'view=">' . 'Create view' . "</a>\n" : "");
      if (support("routine")) {
        echo "<h3 id='routines'>" . 'Routines' . "</h3>\n";
        $cg = routines();
        if ($cg) {
          echo "<table class='odds'>\n", '<thead><tr><th>' . 'Name' . '<td>' . 'Type' . '<td>' . 'Return type' . "<td></thead>\n";
          foreach (
            $cg
            as $K
          ) {
            $D = ($K["SPECIFIC_NAME"] == $K["ROUTINE_NAME"] ? "" : "&name=" . urlencode($K["ROUTINE_NAME"]));
            echo '<tr>', '<th><a href="' . h(ME . ($K["ROUTINE_TYPE"] != "PROCEDURE" ? 'callf=' : 'call=') . urlencode($K["SPECIFIC_NAME"]) . $D) . '">' . h($K["ROUTINE_NAME"]) . '</a>', '<td>' . h($K["ROUTINE_TYPE"]), '<td>' . h($K["DTD_IDENTIFIER"]), '<td><a href="' . h(ME . ($K["ROUTINE_TYPE"] != "PROCEDURE" ? 'function=' : 'procedure=') . urlencode($K["SPECIFIC_NAME"]) . $D) . '">' . 'Alter' . "</a>";
          }
          echo "</table>\n";
        }
        echo '<p class="links">' . (support("procedure") ? '<a href="' . h(ME) . 'procedure=">' . 'Create procedure' . '</a>' : '') . '<a href="' . h(ME) . 'function=">' . 'Create function' . "</a>\n";
      }
      if (support("event")) {
        echo "<h3 id='events'>" . 'Events' . "</h3>\n";
        $L = get_rows("SHOW EVENTS");
        if ($L) {
          echo "<table>\n", "<thead><tr><th>" . 'Name' . "<td>" . 'Schedule' . "<td>" . 'Start' . "<td>" . 'End' . "<td></thead>\n";
          foreach (
            $L
            as $K
          ) echo "<tr>", "<th>" . h($K["Name"]), "<td>" . ($K["Execute at"] ? 'At given time' . "<td>" . $K["Execute at"] : 'Every' . " " . $K["Interval value"] . " " . $K["Interval field"] . "<td>$K[Starts]"), "<td>$K[Ends]", '<td><a href="' . h(ME) . 'event=' . urlencode($K["Name"]) . '">' . 'Alter' . '</a>';
          echo "</table>\n";
          $mc = get_val("SELECT @@event_scheduler");
          if ($mc && $mc != "ON") echo "<p class='error'><code class='jush-sqlset'>event_scheduler</code>: " . h($mc) . "\n";
        }
        echo '<p class="links"><a href="' . h(ME) . 'event=">' . 'Create event' . "</a>\n";
      }
      if ($Vg) echo
      script("ajaxSetHtml('" . js_escape(ME) . "script=db');");
    }
  }
}
page_footer();
