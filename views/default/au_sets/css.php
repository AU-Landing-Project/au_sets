
form#set-post-edit #description_parent #description_ifr {
	height:400px !important;
}

.au-sets-selector {
  position: absolute;
  width: 250px;
  min-height: 50px;
  background-color: white;
  border: 1px solid black;
  color: black;
  border-radius: 5px;
  padding: 4px;
  box-shadow: 5px 5px 2px #888;
  z-index: 9999;
}

.au-set-result:hover {
  cursor: pointer;
  background-color: #FAFFA8;
}

.elgg-button.au-sets-selector-close {
  display: inline-block;
  margin-top: 4px;
  float: right;
}

.au-sets-selector-close-top {
  display: inline-block;
  float: right;
}

.au-sets-throbber {
  background-image: url('<?php echo elgg_get_site_url(); ?>/_graphics/ajax_loader_bw.gif');
  background-position: center center;
  background-repeat: no-repeat;
  min-height: 35px;
}