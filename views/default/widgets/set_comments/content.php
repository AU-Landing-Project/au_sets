<?php

$set = $vars['entity']->getContainerEntity();
$new_comments = ($vars['entity']->new_comments != 'no') ? true : false;

if ($set->comments_on != 'Off') {
  $context = elgg_get_context();
  elgg_set_context('pinboards'); // remove widget context so pagination can happen
  echo elgg_view_comments($set, $new_comments);
  elgg_set_context($context);
}
else {
  echo elgg_echo('au_sets:comments:off');
}