<?php

// Get locale
$supported_locales = [
    'ach', 'af', 'an', 'ar', 'as', 'ast', 'az', 'bg', 'bn-BD', 'bn-IN', 'br',
    'bs', 'ca', 'cak', 'cs', 'cy', 'da', 'de', 'dsb', 'el', 'en-GB', 'en-ZA',
    'eo', 'es-AR', 'es-CL', 'es-ES', 'es-MX', 'et', 'eu', 'fa', 'ff', 'fi',
    'fr', 'fy-NL', 'ga-IE', 'gd', 'gl', 'gn', 'gu-IN', 'he', 'hi-IN', 'hr',
    'hsb', 'hu', 'hy-AM', 'id', 'is', 'it', 'ja', 'ja-JP-mac', 'ka', 'kab',
    'kk', 'km', 'kn', 'ko', 'lij', 'lo', 'lt', 'ltg', 'lv', 'mai', 'mk', 'ml',
    'mr', 'ms', 'my', 'nb-NO', 'ne-NP', 'nl', 'nn-NO', 'or', 'pa-IN', 'pl',
    'pt-BR', 'pt-PT', 'rm', 'ro', 'ru', 'si', 'sk', 'sl', 'son', 'sq', 'sr',
    'sv-SE', 'ta', 'te', 'th', 'tl', 'tr', 'tsz', 'uk', 'ur', 'uz', 'vi', 'wo',
    'xh', 'zh-CN', 'zh-TW',
];

$locale = isset($_REQUEST['locale']) ? htmlspecialchars($_REQUEST['locale']) : 'it';
if (! in_array($locale, $supported_locales)) {
    exit("Locale {$locale} is not supported");
}

$supported_products = [
    'all'     => 'All products',
    'mobile'  => 'Firefox for Android',
    'desktop' => 'Firefox Desktop',
];
$product = isset($_REQUEST['product']) ? htmlspecialchars($_REQUEST['product']) : 'all';
if (! in_array($product, array_keys($supported_products))) {
    exit("Product {$product} is not supported");
}

// Include en-US and remove some strings
if (! file_exists('../config/settings.inc.php')) {
    exit('File config/settings.inc.php is missing');
}
include '../config/settings.inc.php';

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

function startsWith($haystack, $needles)
{
    foreach ((array) $needles as $prefix) {
        if (! strncmp($haystack, $prefix, mb_strlen($prefix))) {
            return true;
        }
    }

    return false;
}

function inString($haystack, $needles)
{
    foreach ((array) $needles as $needle) {
        if (mb_strpos($haystack, $needle, $offset = 0, 'UTF-8') !== false) {
            return true;
        }
    }
}

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

    if (startsWith($entity, $filter)) {
        unset($tmx_reference[$entity]);
    } elseif (strpos($entity, 'region.properties') !== false) {
        unset($tmx_reference[$entity]);
    }
}

// Include locale cache
$cache_file = "{$path}{$locale}/cache_{$locale}_aurora.php";
if (! file_exists($cache_file)) {
    exit("File {$cache_file} does not exist.");
}
include $cache_file;
$tmx_locale = $tmx;
unset($tmx);

// Load list_meta.json_file
if (! file_exists('../data/list_meta.json')) {
    exit('Folder list_meta.json does not exist.');
}

$json_file = file_get_contents('../data/list_meta.json');
$tiers_data = json_decode($json_file, true);

$results = [];
$identical_exclusions = [
    '.key',
    '.accesskey',
    '.commandkey',
];
foreach ($tmx_reference as $reference_id => $reference_translation) {
    $file_name = explode(':', $reference_id)[0];
    if (! isset($tiers_data['files'][$file_name])) {
        // echo "ERROR: {$file_name} is not defined in list.json\n";
    } else {
        if ($product == 'all' || in_array($product, $tiers_data['files'][$file_name]['products'])) {
            $module = $tiers_data['files'][$file_name]['module'];
            if (! isset($results[$module])) {
                $results[$module] = [
                    'translated' => 0,
                    'missing'    => 0,
                    'total'      => 0,
                    'identical'  => 0,
                    'percentage' => 0,
                ];
            }

            // Add to total strings
            $results[$module]['total'] += 1;

            if (! isset($tmx_locale[$reference_id])) {
                $results[$module]['missing'] += 1;
            } else {
                $results[$module]['translated'] += 1;
                if ($tmx_locale[$reference_id] == $reference_translation && ! inString($reference_id, $identical_exclusions)) {
                    $results[$module]['identical'] += 1;
                }
            }
        }
    }
}

$html_supported_locales = '';
foreach ($supported_locales as $supported_locale) {
    $supported_locale_label = str_replace('-', '&#8209;', $supported_locale);
    $html_supported_locales .= "<a href=\"?product={$product}&amp;locale={$supported_locale}\">{$supported_locale_label}</a> ";
}

$html_detail_body = '';
foreach ($results as $module_name => $data) {
    $component = explode(':', $reference_id)[0];
    $module_tier = $tiers_data['modules'][$module_name];
    $data['percentage'] = round($data['translated'] / $data['total'] * 100, 0);
    if ($data['percentage'] == 100) {
        $class = 'success';
    } elseif ($data['percentage'] > 50) {
        $class = 'warning';
    } else {
        $class = 'danger';
    }
    $html_detail_body .= "
    <tr class=\"{$class}\">
        <td>{$module_name}</td>
        <td>{$module_tier}</td>
        <td>{$data['total']}</td>
        <td>{$data['percentage']}&nbsp;%</td>
        <td>{$data['translated']}</td>
        <td>{$data['missing']}</td>
        <td>{$data['identical']}</td>
    </tr>
    ";
}

// Generate overall stats
$overall_stats = [];
foreach ($results as $module_name => $data) {
    $component = explode('/', $module_name)[0];
    if (! isset($overall_stats[$component])) {
        $overall_stats[$component] = [];
        for ($i = 1; $i < 4; $i++) {
            $overall_stats[$component][$i] = [
                'total'                => 0,
                'translated'           => 0,
                'percentage'           => 0,
                'identical'            => 0,
                'percentage_identical' => 0,
            ];
        }
        $overall_stats[$component]['all'] = [
            'total'                => 0,
            'translated'           => 0,
            'percentage'           => 0,
            'identical'            => 0,
            'percentage_identical' => 0,
        ];
    }

    $module_tier = $tiers_data['modules'][$module_name];
    // Increment tier data
    $overall_stats[$component][$module_tier]['total'] += $data['total'];
    $overall_stats[$component][$module_tier]['translated'] += $data['translated'];
    $overall_stats[$component][$module_tier]['identical'] += $data['identical'];
    $overall_stats[$component][$module_tier]['percentage'] = round($overall_stats[$component][$module_tier]['translated'] / $overall_stats[$component][$module_tier]['total'] * 100, 0);
    $overall_stats[$component][$module_tier]['percentage_identical'] = round($overall_stats[$component][$module_tier]['identical'] / $overall_stats[$component][$module_tier]['total'] * 100, 0);

    // Increment component data
    $overall_stats[$component]['all']['total'] += $data['total'];
    $overall_stats[$component]['all']['translated'] += $data['translated'];
    $overall_stats[$component]['all']['identical'] += $data['identical'];
    $overall_stats[$component]['all']['percentage'] = round($overall_stats[$component][$module_tier]['translated'] / $overall_stats[$component][$module_tier]['total'] * 100, 0);
    $overall_stats[$component]['all']['percentage_identical'] = round($overall_stats[$component][$module_tier]['identical'] / $overall_stats[$component][$module_tier]['total'] * 100, 0);
}

$html_translated_body = '';
foreach ($overall_stats as $component_name => $component_data) {
    foreach ($component_data as $tier => $tier_data) {
        if ($tier_data['percentage'] == 100) {
            $class = 'success';
        } elseif ($tier_data['percentage'] > 50) {
            $class = 'warning';
        } else {
            $class = 'danger';
        }
        if ($tier_data['total'] > 0 && $tier != 'all') {
            $html_translated_body .= "
            <tr class=\"{$class}\">
                <td>{$component_name}</td>
                <td>{$tier}</td>
                <td>{$tier_data['total']}</td>
                <td>{$tier_data['percentage']}&nbsp;%</td>
            </tr>
            ";
        }
    }
}

$html_identical_body = '';
foreach ($overall_stats as $component_name => $component_data) {
    foreach ($component_data as $tier => $tier_data) {
        if ($tier_data['percentage_identical'] < 20) {
            $class = 'success';
        } elseif ($tier_data['percentage_identical'] < 40) {
            $class = 'warning';
        } else {
            $class = 'danger';
        }
        if ($tier_data['total'] > 0 && $tier != 'all') {
            $html_identical_body .= "
            <tr class=\"{$class}\">
                <td>{$component_name}</td>
                <td>{$tier}</td>
                <td>{$tier_data['total']}</td>
                <td>{$tier_data['percentage_identical']}&nbsp;%</td>
            </tr>
            ";
        }
    }
}

include 'template.php';
