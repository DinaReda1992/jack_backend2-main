$(document).ready(function(){


    $('nav li a, footer li a').click(function(){

        $('html , body').animate({scrollTop: $($(this).attr('href')).offset().top},500);
    });


    $('.video-container').on('click',function () {
        if($(this).children("#video").get(0).paused){
            $(this).children("#video").get(0).play();
            $(this).children(".playpause").fadeOut(1);
            $('.video-container').addClass('play')
        }else{
            $(this).children("#video").get(0).pause();
            $(this).children(".playpause").fadeIn(1);
            $('.video-container').removeClass('play')

        }
    });
});