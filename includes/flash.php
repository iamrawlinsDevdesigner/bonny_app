<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
function set_flash($name, $message, $type = 'success') {
  $_SESSION['flash'][$name] = ['message' => $message, 'type' => $type];
}
function display_flash($name) {
  if (isset($_SESSION['flash'][$name])) {
    $msg = $_SESSION['flash'][$name];
    echo "<div class='flash {$msg['type']}'>{$msg['message']}</div>";
    unset($_SESSION['flash'][$name]);
  }
}

function has_flash($key) {
  return isset($_SESSION['flash'][$key]);
}

function get_flash($key) {
  if (!isset($_SESSION['flash'][$key])) return null;
  $msg = $_SESSION['flash'][$key]['message'];
  unset($_SESSION['flash'][$key]); // Clear after reading
  return $msg;
}

function get_flash_type($key) {
  return $_SESSION['flash'][$key]['type'] ?? 'info';
}

?>