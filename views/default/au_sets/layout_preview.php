<?php

$layout = json_decode($vars['layout']);
//echo "<pre>" . print_r(json_decode($vars['layout']),1) . "</pre>";

if (!is_array($layout)) {
  echo elgg_echo('au_sets:invalid:layout');
  return;
}

echo '<div class="au-sets-preview-wrapper">';

$index = 0;
foreach ($layout as $row) {
  if (!is_array($row)) {
	continue;
  }
  
  foreach ($row as $width) {
	if (!is_numeric($width) || $width < 1) {
	  continue;
	}
	$index++;
	$width = ($width * 4) - 4;
	echo "<div class=\"au-sets-preview\" style=\"width: {$width}px\">{$index}</div>";
  }
  echo '<div style="clear: both;"></div>';
}

echo '</div>';