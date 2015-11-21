
var shares = 0;
$(document).ready(function () {
    $('.progress-meter').css('width','15%');
    $('.progress-meter-invert').css('width','85%');
    
    $(".social-share").sociallocker({
        demo:true,
        events:{
            unlock:function(typeSender, sender){
                shares++;
                $('.progress-meter').css('width',(16+shares*26-shares*5)+'%');
                if (hash > '') $.get( fullURL+"/site/shared?hash="+hash, function( data ) { });
                //$('.progress-meter-invert').css('width',100-$('.progress-meter').css('width'));
                },
            lock:function(){
                $('.jo-sociallocker-starter').hide();
            }
        },

        locker: {
            close: false,
            timer: 0
        },

        buttons: {
            counter:false,
            order: ["facebook-like", "twitter-tweet", "google-plus", "linkedin-share"]
        },

        facebook: {  
            /*appId: "206841902768508",*/
            class:'.share-buttons-fb',
            count:'none',
            like: {
                    title: "Like us",
                    url: "http://crowdfundingrss.com/"
                }
        },

        twitter: {
            class:'.share-buttons-tw',
            tweet: {
                title: "Tweet",
                text: "Best crowdfunding projects delivered to you",
                url: "http://crowdfundingrss.com/"
            }
        },

        google: {
            class:'.share-buttons-gp',
            plus: {
                title: "Plus +1",
                url: "http://crowdfundingrss.com/"
            }
        },

        linkedin: {
            class:'.share-buttons-li',
            url: "http://crowdfundingrss.com/",                                
            share: {
                title: "Share"
            }
        }
    });


    

});