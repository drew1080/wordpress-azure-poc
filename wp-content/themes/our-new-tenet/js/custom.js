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
  
  if ( jQuery('body').hasClass('login') ) {
    //jQuery("label[for='user_login']").html('<input type="text" name="log" id="user_login" class="input" value="" size="20" placeholder="Last 4 digits of your SSN">');  
    //jQuery("label[for='user_pass']").html('<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" placeholder="Date of birth (mmddyyy)">');
  }
});