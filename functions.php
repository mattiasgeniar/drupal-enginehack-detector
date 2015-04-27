<?php

function is_hacked_drupal ($headers) {
  // Loop all the headers, find a set-cookie header
  $headers = explode("\n", $headers);

  if (is_array($headers) && count($headers) > 0) {
    foreach ($headers as $header) {
      $matches = array();
      if (preg_match('/Set-Cookie:.*engine_(ssid|ssl)_.*/i', $header, $matches, PREG_OFFSET_CAPTURE)) {
        return true;
      }
    }
  }

  // Nothing found? Probably clean.
  return false;
}