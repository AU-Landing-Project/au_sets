<?php

$layout = json_decode($vars['layout']);


if (!is_array($layout)) {
  echo elgg_echo('au_sets:invalid:layout');
  return;
}

$class = $vars['selected'] ? "au-sets-preview-wrapper selected" : "au-sets-preview-wrapper";

echo '<div class="' . $class . '" data-layout="' . $vars['layout'] . '">';

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
	$width = round(($width * 1.5)) - 3;
	echo "<div class=\"au-sets-preview\" style=\"width: {$width}px\">{$index}</div>";
  }
  echo '<div style="clear: both;"></div>';
}

echo '</div>';