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
?>
