// ajaxLoop.js
jQuery(function($){
    var $host = window.location.host;
    var page = 1;
    var loading = true;
    var $window = $(window);
    var $content = $("#fast-facts-wrapper");
    var load_posts = function(){
            $.ajax({
                type       : "GET",
                data       : {numPosts : 1},
                dataType   : "html",
                //DEV NOTE...if localhost add /wordpress-azure-poc to URL
                url        : "http://" + $host + "/wp-content/themes/our-new-tenet/loopHandler.php",
                success    : function(data){
                    $data = $(data);
                    if($data.length){
                        $('#fast-fact').fadeOut('slow');
                        $data.hide();
                        $content.html('');
                        $content.append($data);
                        $data.fadeIn(1500, function(){
                            //$("#temp_load").remove();
                            loading = false;
                        });
                    } else {
                        //$("#temp_load").remove();
                    }
                },
                error     : function(jqXHR, textStatus, errorThrown) {
                    //$("#temp_load").remove();
                    //alert(jqXHR + " :: " + textStatus + " :: " + errorThrown);
                }
        });
    }
    ;
    
    setInterval(load_posts, 6000);
});