
form#set-post-edit #description_parent #description_ifr {
	height:400px !important;
}

.au-sets-bookmark-icon {
  background-image: url(<?php echo elgg_get_site_url(); ?>mod/au_sets/graphics/bookmark.png);
  background-position: 0 0;
}

.au-sets-unpin-overlay {
  display: inline-block;
  float: right;
  padding: 4px;
  background-color: white;
  border: 1px solid black;
  border-radius: 4px;
  margin: 0 3px;
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

.au-set .au-set-title-menu {
  margin-top: -35px;
  margin-bottom: 5px;
}

.au-set-widgets-wrapper {
  clear: both;
}

.au-sets-widget-wrapper {
  float: right;
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

.au-sets-pinboard-help {
  padding-bottom: 10px;
}

/* Widget moving mods */
.au-sets-display-placeholder {
  height: 100px;
  border: 1px dashed #cccccc;
}

/* hide display on select widgets */
.au-sets-widgets .elgg-module-widget.au-sets-hide-style .elgg-head {
  display: none;
}

.au-sets-widgets .elgg-module-widget.au-sets-hide-style .elgg-body {
  border: 0;
}

.au-set-widgets .elgg-module-widget.au-sets-hide-style {
  background-color: transparent;
}

/*  Layout Preview  */
#au-set-layout-preview {
  width: 410px;
  float: right;
}

.au-sets-preview-wrapper {
  float: left;
  width: 150px;
  min-height: 50px;
  border: 1px solid black;
  background-color: #cccccc;
  padding: 5px;
  text-align: center;
  margin: 4px;
  cursor: pointer;
}

.au-sets-preview-wrapper.selected {
  border: 2px solid red;
  background-color: white;
  margin: 3px;
}

.au-sets-preview,
.au-sets-widget-view {
  float: right;
  background-color: #333333;
  border: 1px solid white;
  min-height: 50px;
  text-align: center;
  color: white;
}

.au-sets-widget-view {
  min-height: 0;
}

/* make the preview lighter if selected */
.au-sets-preview-wrapper.selected .au-sets-preview {
  background-color: #676767;
}

<?php
// generate a css class for each width 1-100%

for ($i=1; $i<101; $i++) {
?>
.au-sets-widget-width-<?php echo $i; ?> {
  width: <?php echo $i; ?>%;
}

<?php
}