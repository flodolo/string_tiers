<?php
namespace Tiers;

use Cache\Cache;
use Json\Json;

// Get supported locales from external service
$json_object = new Json;
$cache_id = 'supported_locales';
if (! $supported_locales = Cache::getKey($cache_id, 60 * 60)) {
    $response = $json_object
        ->setURI("{$query_service}?repo=central")
        ->fetchContent();
    $supported_locales = array_values($response['locales']);
    Cache::setKey($cache_id, $supported_locales);
}

$supported_products = [
    'all'     => 'all products',
    'mobile'  => 'Firefox for Android',
    'desktop' => 'Firefox Desktop',
];
$requested_product = isset($_REQUEST['product'])
    ? htmlspecialchars($_REQUEST['product'])
    : 'all';
if (! in_array($requested_product, array_keys($supported_products))) {
    exit("Product {$requested_product} is not supported");
}

$requested_locale = isset($_REQUEST['locale'])
    ? htmlspecialchars($_REQUEST['locale'])
    : Utils::detectLocale($supported_locales, 'it');
if (! in_array($requested_locale, $supported_locales) && $requested_locale != 'all') {
    exit("Locale {$requested_locale} is not supported");
}
$html_supported_locales = '';
foreach ($supported_locales as $supported_locale) {
    // Add to locale selector
    $supported_locale_label = str_replace('-', '&#8209;', $supported_locale);
    $html_supported_locales .= "<a href=\"?product={$requested_product}&amp;locale={$supported_locale}\">{$supported_locale_label}</a> ";
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
