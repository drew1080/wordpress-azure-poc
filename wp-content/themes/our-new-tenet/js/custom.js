jQuery(function() {
  if ( jQuery('body').hasClass('page-id-3131') ) {
    if ( jQuery(jQuery('.gde-link')[0]).length > 0) {
      jQuery(jQuery('.gde-link')[0]).click( function(e){
        window.open(this.href, '_blank');
        e.preventDefault();
        return false;
      });
    }
  }
});