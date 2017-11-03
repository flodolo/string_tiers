<?php
namespace Tiers;

use Cache\Cache;

include realpath(__DIR__ . '/../app/inc/init.php');
include "{$root_folder}/app/inc/query_params.php";

// This view is valid only for one locale and one module.

if ($requested_locale == 'all' || $requested_module == 'all') {
    die('This view is available only for one locale and one module.');
}

// Load en-US cache, clean up unwanted strings.
$cache_file = "{$path}en-US/cache_en-US_gecko_strings.php";
if (! file_exists($cache_file)) {
    exit("File {$cache_file} does not exist.");
}
include $cache_file;
$tmx_reference = $tmx;
unset($tmx);

// Remove components and region.properties
foreach ($tmx_reference as $entity => $translation) {
    $filter = [
        'browser/branding/aurora',
        'browser/branding/nightly',
        'browser/branding/unofficial',
        'calendar/',
        'chat/',
        'editor/',
        'extensions/',
        'mail/',
        'mobile/android/branding/aurora',
        'mobile/android/branding/beta',
        'mobile/android/branding/nightly',
        'mobile/android/branding/official',
        'mobile/android/branding/unofficial',
        'other-licenses/',
        'suite/',
    ];

    if (Utils::startsWith($entity, $filter)) {
        unset($tmx_reference[$entity]);
    } elseif (strpos($entity, 'region.properties') !== false) {
        unset($tmx_reference[$entity]);
    }
}

$missing_strings = [];
$cache_id = "missing_locale_{$requested_locale}_{$requested_product}";
if (! $missing_strings = Cache::getKey($cache_id)) {
    // Include locale cache
    $cache_file = "{$path}{$requested_locale}/cache_{$requested_locale}_gecko_strings.php";
    if (! file_exists($cache_file)) {
        exit("File {$cache_file} does not exist.");
    }
    include $cache_file;
    $tmx_locale = $tmx;
    unset($tmx);

    // Store stats for this locale
    foreach ($tmx_reference as $reference_id => $reference_translation) {
        $file_name = explode(':', $reference_id)[0];
        $string_id = explode(':', $reference_id)[1];
        if (! isset($tiers_data['files'][$file_name])) {
            // echo "ERROR: {$file_name} is not defined in list.json\n";
        } else {
            if ($requested_product == 'all' || in_array($requested_product, $tiers_data['files'][$file_name]['products'])) {
                $module = $tiers_data['files'][$file_name]['module'];
                if (! isset($tmx_locale[$reference_id])) {
                    if (! isset($missing_strings[$module][$file_name])) {
                        $missing_strings[$module][$file_name] = [$string_id];
                    } else {
                        $missing_strings[$module][$file_name][] = $string_id;
                    }
                }
            }
        }
    }
    unset($tmx_locale);
    Cache::setKey($cache_id, $missing_strings);
}

include "{$root_folder}/app/controllers/diff.php";
include "{$root_folder}/app/templates/base.php";
