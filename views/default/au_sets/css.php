
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

.au-set-result.au-set-result-pinned {
  cursor: not-allowed;
  background-color: #cccccc;
}

.au-set-result.au-set-result-pinned a,
.au-set-result.au-set-result-pinned a:hover,
.au-set-result.au-set-result-pinned h4 {
  cursor: not-allowed;
  color: #454545;
  text-decoration: none;
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

.au-set {
  background-color: white;
}

.au-set .elgg-menu-title {
  margin-top: -35px;
  margin-bottom: 5px;
}

.au-set-widgets-wrapper {
  clear: both;
}

.au-sets-item-search-results {
  margin-top: 5px;
  max-height: 150px;
  overflow-y: auto;
}

.au-set-item-preview {
  padding: 4px;
  border-bottom: 1px dashed #cccccc;
  cursor: pointer;
}

.au-set-item-preview:hover {
  background-color: #FAFFA8
}