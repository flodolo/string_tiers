<?php
namespace Tiers;

use Cache\Cache;
use Json\Json;

include realpath(__DIR__ . '/../app/inc/init.php');

// Get supported locales from external service
$json_object = new Json;
$cache_id = 'supported_locales';
if (! $supported_locales = Cache::getKey($cache_id, 60 * 60)) {
    $response = $json_object
        ->setURI('https://l10n.mozilla-community.org/~flod/mozilla-l10n-query/?repo=aurora')
        ->fetchContent();
    $supported_locales = array_values($response['locales']);
    Cache::setKey($cache_id, $supported_locales);
}

$supported_products = [
    'all'     => 'all products',
    'mobile'  => 'Firefox for Android',
    'desktop' => 'Firefox Desktop',
];
$product = isset($_REQUEST['product'])
    ? htmlspecialchars($_REQUEST['product'])
    : 'all';
if (! in_array($product, array_keys($supported_products))) {
    exit("Product {$product} is not supported");
}

$locale = isset($_REQUEST['locale'])
    ? htmlspecialchars($_REQUEST['locale'])
    : Utils::detectLocale($supported_locales, 'it');
if (! in_array($locale, $supported_locales) && $locale != 'all') {
    exit("Locale {$locale} is not supported");
}
$html_supported_locales = '';
foreach ($supported_locales as $supported_locale) {
    // Add to locale selector
    $supported_locale_label = str_replace('-', '&#8209;', $supported_locale);
    $html_supported_locales .= "<a href=\"?product={$product}&amp;locale={$supported_locale}\">{$supported_locale_label}</a> ";
}

if (! file_exists("{$root_folder}/app/data/list_meta.json")) {
    exit('Folder list_meta.json does not exist.');
}
$json_file = file_get_contents("{$root_folder}/app/data/list_meta.json");
$tiers_data = json_decode($json_file, true);

$requested_module = isset($_REQUEST['module'])
    ? htmlspecialchars($_REQUEST['module'])
    : 'all';
$supported_modules = array_keys($tiers_data['modules']);
if ($requested_module != 'all' && ! in_array($requested_module, $supported_modules)) {
    exit("Unknown module {$requested_module}");
}
$html_supported_modules = '';
foreach ($supported_modules as $supported_module) {
    // Add to module selector
    $html_supported_modules .= "<a href=\"?module={$supported_module}&amp;locale=all\">{$supported_module}</a> ";
}

/*
    Load en-US cache, clean up unwanted strings.
*/
$cache_file = "{$path}en-US/cache_en-US_aurora.php";
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

$identical_exclusions = [
    '.key',
    '.accesskey',
    '.commandkey',
];

$results = [];
foreach ($supported_locales as $supported_locale) {
    $cache_id = "results_locale_{$supported_locale}";
    if (! $results[$supported_locale] = Cache::getKey($cache_id)) {
        // Include locale cache
        $cache_file = "{$path}{$supported_locale}/cache_{$supported_locale}_aurora.php";
        if (! file_exists($cache_file)) {
            exit("File {$cache_file} does not exist.");
        }
        include $cache_file;
        $tmx_locale = $tmx;
        unset($tmx);

        // Store stats for this locale
        foreach ($tmx_reference as $reference_id => $reference_translation) {
            $file_name = explode(':', $reference_id)[0];
            if (! isset($tiers_data['files'][$file_name])) {
                // echo "ERROR: {$file_name} is not defined in list.json\n";
            } else {
                if ($product == 'all' || in_array($product, $tiers_data['files'][$file_name]['products'])) {
                    $module = $tiers_data['files'][$file_name]['module'];
                    if (! isset($results[$supported_locale][$module])) {
                        $results[$supported_locale][$module] = [
                            'translated' => 0,
                            'missing'    => 0,
                            'total'      => 0,
                            'identical'  => 0,
                            'percentage' => 0,
                        ];
                    }

                    // Add to total strings
                    $results[$supported_locale][$module]['total'] += 1;

                    if (! isset($tmx_locale[$reference_id])) {
                        $results[$supported_locale][$module]['missing'] += 1;
                    } else {
                        $results[$supported_locale][$module]['translated'] += 1;
                        if ($tmx_locale[$reference_id] == $reference_translation && ! Utils::inString($reference_id, $identical_exclusions)) {
                            $results[$supported_locale][$module]['identical'] += 1;
                        }
                    }
                }
            }
        }
        unset($tmx_locale);
    }
    Cache::setKey($cache_id, $results[$supported_locale]);
}

$controller = $requested_module != 'all'
    ? 'module'
    : 'locale';

include "{$root_folder}/app/controllers/{$controller}.php";
