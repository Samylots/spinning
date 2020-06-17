/**
 * Created by administrator on 2016-03-17.
 */
// Animates the dimensional changes resulting from altering element contents
(function($){
    $.fn.setHtml = function(html, speed, callback){
        return this.each(function(){
            var el = $(this);
            $('body').css({'cursor' : 'wait'});
            el.slideUp('slow', (function(html){
                el.html(html).promise().done(function(){
                        el.slideDown('slow', callback);
                        $('.loading').slideUp('slow');
                        $('body').css({'cursor' : 'default'});
                    });
            }).bind(el, html));
        });
    };

    $.fn.setPopHtml = function(html, callback){
        return this.each(function(){
            var el = $(this);
            $('body').css({'cursor' : 'wait'});
            el.html(html).promise().done(function(){
                if(typeof callback === 'function') {
                    callback();
                }
                $('body').css({'cursor' : 'default'});
            });
        });
    };
})(jQuery);