<?php

/*
 * Long-form service page content keyed by service slug (one file per service
 * in data/service-content/). Capability descriptions only — no client names,
 * statistics or guarantees.
 */

$content = [];

foreach (glob(__DIR__.'/service-content/*.php') ?: [] as $file) {
    $content[basename($file, '.php')] = require $file;
}

return $content;
