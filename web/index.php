<?php
namespace Tiers;

use Cache\Cache;

include realpath(__DIR__ . '/../app/inc/init.php');
include "{$root_folder}/app/inc/query_params.php";

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

$identical_exclusions = [
    '.key',
    '.accesskey',
    '.commandkey',
];

$controller = $requested_module != 'all'
    ? 'module'
    : 'locale';

/*
    If I'm only checking one locale, create results for this locale to reduce
    the time needed to generate the page.
*/
$locales_list = $requested_module != 'all'
    ? $supported_locales
    : [$requested_locale];

$results = [];
foreach ($locales_list as $supported_locale) {
    $cache_id = "results_locale_{$supported_locale}_{$requested_product}";
    if (! $results[$supported_locale] = Cache::getKey($cache_id)) {
        // Include locale cache
        $cache_file = "{$path}{$supported_locale}/cache_{$supported_locale}_gecko_strings.php";
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
                if ($requested_product == 'all' || in_array($requested_product, $tiers_data['files'][$file_name]['products'])) {
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
        Cache::setKey($cache_id, $results[$supported_locale]);
    }
}

include "{$root_folder}/app/controllers/{$controller}.php";
include "{$root_folder}/app/templates/base.php";
