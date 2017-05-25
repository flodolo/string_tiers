<?php

use Cache\Cache;

// Generate stats per root module
$cache_id = "overall_stats_{$requested_locale}";
if (! $overall_stats = Cache::getKey($cache_id)) {
    $overall_stats = [];
    foreach ($results[$requested_locale] as $module_name => $data) {
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
        $overall_stats[$component][$module_tier]['percentage'] = $overall_stats[$component][$module_tier]['total'] != 0
            ? round($overall_stats[$component][$module_tier]['translated'] / $overall_stats[$component][$module_tier]['total'] * 100, 0)
            : 0;
        $overall_stats[$component][$module_tier]['percentage_identical'] = $overall_stats[$component][$module_tier]['total'] != 0
            ? round($overall_stats[$component][$module_tier]['identical'] / $overall_stats[$component][$module_tier]['total'] * 100, 0)
            : 0;

        // Increment component data
        $overall_stats[$component]['all']['total'] += $data['total'];
        $overall_stats[$component]['all']['translated'] += $data['translated'];
        $overall_stats[$component]['all']['identical'] += $data['identical'];
        $overall_stats[$component]['all']['percentage'] = $overall_stats[$component][$module_tier]['total'] != 0
            ? round($overall_stats[$component][$module_tier]['translated'] / $overall_stats[$component][$module_tier]['total'] * 100, 0)
            : 0;
        $overall_stats[$component]['all']['percentage_identical'] = $overall_stats[$component][$module_tier]['total'] != 0
            ? round($overall_stats[$component][$module_tier]['identical'] / $overall_stats[$component][$module_tier]['total'] * 100, 0)
            : 0;
    }
    Cache::setKey($cache_id, $overall_stats);
}

$html_detail_body = '';
foreach ($results[$requested_locale] as $module_name => $data) {
    $module_tier = $tiers_data['modules'][$module_name];
    $data['percentage'] = $data['total'] != 0
        ? round($data['translated'] / $data['total'] * 100, 0)
        : 0;
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
        <td>";

    // Link to Diff view only if there are missing strings
    if ($data['missing'] > 0) {
        $html_detail_body .= "<a href=\"diff.php?locale={$requested_locale}&amp;module={$module_name}&amp;product={$requested_product}\">{$data['missing']}</a>";
    } else {
        $html_detail_body .= $data['missing'];
    }

    $html_detail_body .= "</td>
        <td>{$data['identical']}</td>
	</tr>
	";
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

$page_title = 'Locale View';
$selectors_enabled = true;
$sub_template = 'locale.php';
