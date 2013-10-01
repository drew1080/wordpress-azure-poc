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
    jQuery("label[for='user_login']").html('<input type="text" name="log" id="user_login" class="input" value="" size="20" placeholder="Last 4 digits of your SSN">');  
    jQuery("label[for='user_pass']").html('<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" placeholder="Date of birth (mmddyyyy)">');
    
    // $html = '<p>
    //  <label for="user_login"><input type="text" name="log" id="user_login" class="input" value="" size="20" placeholder="Last 4 digits of your SSN"></label>
    //  </p>
    //  <p>
    //    <label for="user_pass"><input type="password" name="pwd" id="user_pass" class="input" value="" size="20" placeholder="Date of birth (mmddyyy)"></label>
    //  </p>
    //  <p class="lc_form_text">Need help logging in? Call 1-855-900-5947 or email <a href="mailto:OurNewTenet@TenetHealth.com">OurNewTenet@TenetHealth.com</a>.</p>  <p class="forgetmenot"><label for="rememberme"><input name="rememberme" type="checkbox" id="rememberme" value="forever"> Remember Me</label></p>
    //  <p class="submit">
    //    <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Log In">
    //    <input type="hidden" name="redirect_to" value="http://wordpresspoc.azurewebsites.net/employee-portal">
    //    <input type="hidden" name="testcookie" value="1">
    //  </p>';
    // 
    // 
    // jQuery("#loginform").html($html);
  }
  
  fauxPlaceholder();
  function fauxPlaceholder() {
    if(!elementSupportAttribute('input','placeholder')) {
        jQuery("input[placeholder]").each(function() {
            var $input = jQuery(this);
            $input.after('<input id="'+$input.attr('id')+'-faux" style="display:none;" type="text" value="' + $input.attr('placeholder') + '" />');
            var $faux = jQuery('#'+$input.attr('id')+'-faux');
  
            $faux.show().attr('class', $input.attr('class')).attr('style', $input.attr('style'));
            $input.hide();
  
            $faux.focus(function() {
                $faux.hide();
                $input.show().focus();
            });
  
            $input.blur(function() {
                if($input.val() === '') {
                    $input.hide();
                    $faux.show();
                }
            });
        });
    }
  }
  function elementSupportAttribute(elm, attr) {
      var test = document.createElement(elm);
      return attr in test;
  }
});

// jQuery(window).load(function() {
//   if( jQuery("html").hasClass("ie8")) {
//     if ( jQuery('body').hasClass('home') ) {    
//       
//       jQuery(jQuery(".tp-leftarrow")[0]).css("backgroundImage", "url(/wp-content/themes/our-new-tenet/images/large-left-IE.png)");  
// 
//       jQuery(jQuery(".tp-rightarrow")[0]).css("backgroundImage", "url(/wp-content/themes/our-new-tenet/images/large-right-IE.png)");
//       
//     }
//   };
// });