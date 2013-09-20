// ajaxLoop.js
var $data = new Array();
console.log('Data intialized ' + $data);

jQuery(function($){
    var $host = window.location.host;
    var page = 1;
    var loading = true;
    var $window = $(window);
    var $content = $("#fast-facts-wrapper");
    var $fast_fact_total_count = 0;
    function load_posts() {
            $.ajax({
                type       : "GET",
                data       : {numPosts : 1},
                dataType   : "html",
                //DEV NOTE...if localhost add /wordpress-azure-poc to URL
                //url        : "http://" + $host + "/wordpress-azure-poc/wp-content/themes/our-new-tenet/loopHandler.php",
                url        : "http://" + $host + "/wp-content/themes/our-new-tenet/loopHandler.php",
                success    : function(data){
                    $data = $(data);
                    console.log('Data Length line 20 (AJAX): ' + $data.length);
                    $fast_fact_total_count = $data.length - 1;
                }
        });
    };
    
    load_posts();
    
    var $fast_fact_counter = 0;
    
    var load_fast_facts = function(){
      console.log('Data Length line 31 (load_fast_facts function): ' + $data.length);
      console.log('Data value 0 innerHTML line 31 (load_fast_facts function): ' + $data[0].innerHTML);
      if($data.length){
        $('#fast-fact').fadeOut('slow');
        $data.hide();
        $content.html('');
        $content.append($data[$fast_fact_counter]);//data at +2
        $data.fadeIn(1500, function(){
            //$("#temp_load").remove();
            loading = false;
        });
        
        if ($fast_fact_counter >= $fast_fact_total_count) {
          $fast_fact_counter = 0;
        } else {
          $fast_fact_counter += 2;
        }
      }
    }
    
    if ( $content.length > 0 ) {
      //setInterval(load_fast_facts, 2000);
      setInterval(load_fast_facts, 2000);
    }
});