<h2>Module Details</h2>
<table class="table table-bordered" id="locale_details">
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

<h2>Tiers and Root Modules - Translated Strings</h2>
<table class="table table-bordered" id="locale_root_translated">
  <thead>
    <tr>
        <th>Root Module</th>
        <th>Tier</th>
        <th>Total</th>
        <th>% Translated</th>
    </tr>
  </thead>
  <tbody>
      <?php echo $html_translated_body; ?>
  </tbody>
</table>

<h2>Tiers and Root Modules - Identical Strings</h2>
<p>Accesskeys and shortcuts are ignored in the count of identical strings by excluding string IDs
	including <code>.key</code>, <code>.accesskey</code>, and <code>.commandkey</code>.</p>
<table class="table table-bordered" id="locale_root_identical">
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
