<?php

if (elgg_get_context() != 'au_sets_list') {
  return true;
}
?>

<script>
  $(document).ready( function() {
	$('.elgg-item').hover(
	  // mouseover
	  function() {
		var guid = $(this).attr('id').substr(12);
		var text = elgg.echo('au_sets:unpin');
		
		$(this).prepend('<a href="javascript:void(0);" id="au-sets-unpin-'+guid+'" class="au-sets-unpin au-sets-unpin-overlay"><span data-guid="'+guid+'">'+text+'</span></a>');
	  },
	  // mouseout
	  function() {
		var guid = $(this).attr('id').substr(12);
		$('#au-sets-unpin-'+guid).remove();
	  }
	);
  });
</script>