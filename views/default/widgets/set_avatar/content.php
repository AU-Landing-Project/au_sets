<?php

$set = $vars['entity']->getContainerEntity();
$size = $vars['entity']->avatar_size ? $vars['entity']->avatar_size : 'large';
$align = $vars['entity']->avatar_align ? $vars['entity']->avatar_align : 'center';

echo "<div style=\"text-align: {$align};\">";
echo elgg_view_entity_icon($set, $size, array('href' => false));
echo "</div>";
