<?php

$html_detail_body = '';
foreach ($results[$locale] as $module_name => $data) {
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
		<td>{$data['missing']}</td>
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
