<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset=utf-8>
	<title>Tiers Experiment - <?php echo $page_title; ?></title>
    <link rel="stylesheet" href="assets/bower/bootstrap/dist/css/bootstrap.min.css" type="text/css" media="all" />
    <link rel="stylesheet" href="assets/bower/bootstrap/dist/css/bootstrap-theme.min.css" type="text/css" media="all" />
    <link rel="stylesheet" href="assets/bower/DataTables/media/css/dataTables.bootstrap.min.css" type="text/css" media="all" />
    <link rel="stylesheet" href="assets/css/main.css" type="text/css" media="all" />
    <script src="assets/bower/jquery/dist/jquery.min.js"></script>
	<script src="assets/bower/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="assets/bower/DataTables/media/js/jquery.dataTables.min.js"></script>
    <script src="assets/bower/DataTables/media/js/dataTables.bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>
</head>
<body>
  <div class="container">
	<?php
        if ($selectors_enabled):
    ?>
	<h1>String Tiers - Experimental Dashboard</h1>
	<p>See the <a href="https://github.com/flodolo/string_tiers/">GitHub repository</a> for background information.</p>
    <h2>Locale: <?php echo $requested_locale; ?></h2>
	<div class="list locale_list">
      <p>
        Display localization status for a specific locale<br/>
        <?php echo $html_supported_locales; ?>
      </p>
    </div>
	<?php
		if ($sub_template != 'module.php'):
		// Don't display products selector if I'm looking at one module
	?>
	<h2>Product: <?php echo $supported_products[$requested_product]; ?></h2>
	<div class="list product_list">
      <p>
        Display localization status for a specific product<br/>
        <a href="?product=all&amp;locale=<?=$requested_locale?>">All Products</a>
        <a href="?product=mobile&amp;locale=<?=$requested_locale?>">Firefox for Android</a>
        <a href="?product=desktop&amp;locale=<?=$requested_locale?>">Firefox Desktop</a>
      </p>
    </div>
	<?php
		endif;
	?>
	<h2>Module: <?php echo $requested_module; ?></h2>
	<div class="list module_list">
      <p>
        Display localization status for a specific module<br/>
        <?php echo $html_supported_modules; ?>
      </p>
    </div>
	<?php
        endif;
    ?>

	<?php include "{$root_folder}/app/templates/{$sub_template}"; ?>

  </div>
</body>
