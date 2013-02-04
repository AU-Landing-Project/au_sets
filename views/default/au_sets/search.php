<?php
/**
 * This view is called via ajax
 * $vars:
 * 'entity' => the entity being pinned
 * 'pageowner' => the guid of the pageowner
 */

if (!elgg_is_logged_in() || !$vars['entity']) {
  return;
}


echo elgg_view('output/url', array(
	'text' => '<span class="elgg-icon elgg-icon-delete-alt"></span>',
	'href' => '#',
	'class' => 'au-sets-selector-close au-sets-selector-close-top'
));

echo "<h3>" . elgg_echo('au_sets:pin:to') . "</h3>";

echo elgg_view('output/url', array(
	'text' => elgg_echo('au_sets:create:new:set:with:pin'),
	'href' => elgg_get_site_url() . 'sets/add/' . elgg_get_logged_in_user_guid() . '?pin=' . $vars['entity']->guid,
));

echo '<br>';

echo '<div id="au-sets-selector-results-' . $vars['entity']->guid . '">';

echo elgg_view('au_sets/search_results', $vars);

echo '</div>';

echo elgg_echo('au_sets:search');
echo elgg_view('input/text', array(
	'name' => 'query',
	'class' => 'au-sets-query',
	'data-guid' => $vars['entity']->guid
));
echo elgg_view('output/longtext', array(
	'value' => elgg_echo('au_sets:search:help'),
	'class' => 'elgg-subtext'
));

echo '<br>';

echo '<input class="au-sets-query-mine" type="checkbox" name="mine" value="1" checked="checked"> ' . elgg_echo('au_sets:search:mine');

echo elgg_view('output/url', array(
	'text' => elgg_echo('close'),
	'href' => '#',
	'class' => 'au-sets-selector-close elgg-button elgg-button-delete'
));