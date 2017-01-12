<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset=utf-8>
	<title>Tiers Experiment</title>
    <link rel="stylesheet" href="assets/bower/bootstrap/dist/css/bootstrap.min.css" type="text/css" media="all" />
    <link rel="stylesheet" href="assets/bower/bootstrap/dist/css/bootstrap-theme.min.css" type="text/css" media="all" />
    <link rel="stylesheet" href="assets/bower/DataTables/media/css/dataTables.bootstrap.min.css" type="text/css" media="all" />
    <link rel="stylesheet" href="assets/css/main.css" type="text/css" media="all" />
    <script src="assets/bower/jquery/dist/jquery.min.js"></script>
    <script src="assets/bower/DataTables/media/js/jquery.dataTables.min.js"></script>
    <script src="assets/bower/DataTables/media/js/dataTables.bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>
</head>
<body>
  <div class="container">
	<h1>String Tiers - Experimental Dashboard</h1>
	<p>See the <a href="https://github.com/flodolo/string_tiers/">GitHub repository</a> for background information.</p>
    <h2>Locale: <?php echo $locale; ?></h2>
	<div class="list locale_list">
      <p>
        Display localization status for a specific locale<br/>
        <?php echo $html_supported_locales; ?>
      </p>
    </div>
	<h2>Product: <?php echo $supported_products[$product]; ?></h2>
	<div class="list product_list">
      <p>
        Display localization status for a specific product<br/>
        <a href="?product=all&amp;locale=<?=$locale?>">All Products</a>
        <a href="?product=mobile&amp;locale=<?=$locale?>">Firefox for Android</a>
        <a href="?product=desktop&amp;locale=<?=$locale?>">Firefox Desktop</a>
      </p>
    </div>


    <h2>Details</h2>
    <table class="table table-bordered">
      <thead>
        <tr>
            <th>Module</th>
            <th>Tier</th>
            <th>Total</th>
            <th>%</th>
            <th>Translated</th>
            <th>Missing</th>
            <th>Identical</th>
        </tr>
      </thead>
      <tbody>
          <?php echo $html_detail_body; ?>
      </tbody>
    </table>

    <h2>Tiers Data - Translated strings</h2>
    <table class="table table-bordered ">
      <thead>
        <tr>
            <th>Component</th>
            <th>Tier</th>
            <th>Total</th>
            <th>% Translated</th>
        </tr>
      </thead>
      <tbody>
          <?php echo $html_translated_body; ?>
      </tbody>
    </table>

    <h2>Tiers Data - Identical Strings</h2>
	<p>Accesskeys and shortcuts are ignored in the count of identical strings by excluding string IDs
		including <code>.key</code>, <code>.accesskey</code>, and <code>.commandkey</code>.</p>
    <table class="table table-bordered ">
      <thead>
        <tr>
            <th>Component</th>
            <th>Tier</th>
            <th>Total</th>
            <th>% Identical</th>
        </tr>
      </thead>
      <tbody>
          <?php echo $html_identical_body; ?>
      </tbody>
  </table>
</div>
</body>
