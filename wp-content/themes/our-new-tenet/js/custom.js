$(function() {
  if ( $('body').hasClass('page-id-3131') ) {
    if ( $($('.gde-link')[0]).length > 0) {
      $($('.gde-link')[0]).click( function(e){
        window.open(this.href, '_blank');
        e.preventDefault();
        return false;
      });
    }
  }
});