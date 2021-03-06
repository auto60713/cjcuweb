jQuery(document).ready(function(){

        //焦點新聞
        $.ajax({
          type: 'POST',
          async: false,
          url: 'cjcu_career/cc/index.php/news',
          data:{},
          success: function (data) { 
            
            if(data!=null||data!="false") var article_array = JSON.parse(data);
            else var article_array = 0;

            for (var i = 0; i < article_array.length; i++) { 
          
                //<li><a href="新聞頁面1"><img src="cjcu_career/cc/product_img/新聞照1" alt="新聞標題1" /></a></li>
                //<li><img src="slider/images/chicago_illinois-wallpaper-1920x1080-tn.jpg" /></li>
                var img = $('<img>').attr({"alt":article_array[i].title,"src":"cjcu_career/cc/product_img/"+article_array[i].pic}),
                    link = $('<a>').attr("href",'news.php?type=1&article_id='+article_array[i].id).append(img),
                    li = $('<li>').append(link);

                var img2 = $('<img>').attr("src","cjcu_career/cc/product_img/"+article_array[i].pic),
                    li2 = $('<li>').append(img2);

                $('.amazingslider-slides').append(li);
                $('.amazingslider-thumbnails').append(li2);
            }
          }
        });

            


    var scripts = document.getElementsByTagName("script");
    var jsFolder = "";
    for (var i= 0; i< scripts.length; i++)
    {
        if( scripts[i].src && scripts[i].src.match(/initslider-1\.js/i))
            jsFolder = scripts[i].src.substr(0, scripts[i].src.lastIndexOf("/") + 1);
    }
    jQuery("#amazingslider-1").amazingslider({
        jsfolder:jsFolder,
        width:380,
        height:280,
        skinsfoldername:"",
        watermarkstyle:"text",
        loadimageondemand:false,
        watermarktext:"amazingslider.com",
        isresponsive:false,
        autoplayvideo:false,
        watermarkimage:"",
        pauseonmouseover:false,
        watermarktextcss:"font:12px Arial,Tahoma,Helvetica,sans-serif;color:#333;padding:2px 4px;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px;background-color:#fff;opacity:0.9;filter:alpha(opacity=90);",
        addmargin:true,
        randomplay:false,
        playvideoonclickthumb:true,
        showwatermark:false,
        watermarklinkcss:"text-decoration:none;font:12px Arial,Tahoma,Helvetica,sans-serif;color:#333;",
        slideinterval:5000,
        watermarktarget:"_blank",
        watermarkpositioncss:"display:block;position:absolute;bottom:4px;right:4px;",
        watermarklink:"http://amazingslider.com?source=watermark",
        enabletouchswipe:true,
        transitiononfirstslide:false,
        loop:0,
        autoplay:true,
        navplayvideoimage:"play-32-32-0.png",
        navpreviewheight:60,
        timerheight:2,
        shownumbering:false,
        skin:"FeatureList",
        textautohide:true,
        addgooglefonts:true,
        navshowplaypause:true,
        navshowplayvideo:true,
        navshowplaypausestandalonemarginx:8,
        navshowplaypausestandalonemarginy:8,
        navbuttonradius:0,
        navthumbnavigationarrowimageheight:32,
        navmarginy:16,
        showshadow:false,
        navfeaturedarrowimagewidth:8,
        navpreviewwidth:120,
        googlefonts:"Inder",
        textpositionmarginright:24,
        bordercolor:"#ffffff",
        navthumbnavigationarrowimagewidth:32,
        navthumbtitlehovercss:"",
        arrowwidth:32,
        texteffecteasing:"easeOutCubic",
        texteffect:"slide",
        navspacing:4,
        playvideoimage:"playvideo-64-64-0.png",
        ribbonimage:"ribbon_topleft-0.png",
        navwidth:70,
        navheight:70,
        arrowimage:"arrows-32-32-0.png",
        timeropacity:0.6,
        navthumbnavigationarrowimage:"carouselarrows-32-32-1.png",
        navshowplaypausestandalone:false,
        navpreviewbordercolor:"#ffffff",
        ribbonposition:"topleft",
        navthumbdescriptioncss:"display:block;position:relative;padding:2px 4px;text-align:left;font:normal 11px Arial,Helvetica,sans-serif;color:#333;",
        navborder:2,
        navthumbtitleheight:18,
        textpositionmargintop:24,
        navswitchonmouseover:false,
        navarrowimage:"navarrows-28-28-0.png",
        arrowtop:50,
        textstyle:"static",
        playvideoimageheight:64,
        navfonthighlightcolor:"#666666",
        showbackgroundimage:false,
        navpreviewborder:4,
        navopacity:0.8,
        shadowcolor:"#aaaaaa",
        navbuttonshowbgimage:true,
        navbuttonbgimage:"navbuttonbgimage-28-28-0.png",
        textbgcss:"display:block; position:absolute; top:0px; left:0px; width:100%; height:100%; background-color:#333333; opacity:0.6; filter:alpha(opacity=60);",
        navpreviewarrowwidth:16,
        playvideoimagewidth:64,
        navshowpreviewontouch:false,
        bottomshadowimagewidth:110,
        showtimer:true,
        navradius:0,
        navshowpreview:false,
        navpreviewarrowheight:8,
        navmarginx:16,
        navfeaturedarrowimage:"featuredarrow-8-16-0.png",
        showribbon:false,
        navstyle:"thumbnails",
        textpositionmarginleft:24,
        descriptioncss:"display:block; position:relative; margin-top:4px; font:12px Inder,Arial,Tahoma,Helvetica,sans-serif; color:#fff;",
        navplaypauseimage:"navplaypause-28-28-0.png",
        backgroundimagetop:-10,
        timercolor:"#ffffff",
        numberingformat:"%NUM/%TOTAL ",
        navfontsize:12,
        navhighlightcolor:"#333333",
        navimage:"bullet-24-24-5.png",
        navshowplaypausestandaloneautohide:false,
        navbuttoncolor:"#999999",
        navshowarrow:true,
        navshowfeaturedarrow:true,
        lightboxbarheight:48,
        titlecss:"display:block; position:relative; font: 14px Inder,Arial,Tahoma,Helvetica,sans-serif; color:#fff;",
        ribbonimagey:0,
        ribbonimagex:0,
        navshowplaypausestandaloneposition:"bottomright",
        shadowsize:5,
        arrowhideonmouseleave:1000,
        navshowplaypausestandalonewidth:28,
        navfeaturedarrowimageheight:16,
        navshowplaypausestandaloneheight:28,
        backgroundimagewidth:120,
        navcolor:"#999999",
        navthumbtitlewidth:100,
        navpreviewposition:"top",
        arrowheight:32,
        arrowmargin:8,
        texteffectduration:1000,
        bottomshadowimage:"bottomshadow-110-95-4.png",
        border:6,
        timerposition:"bottom",
        navfontcolor:"#333333",
        navthumbnavigationstyle:"auto",
        borderradius:0,
        navbuttonhighlightcolor:"#333333",
        textpositionstatic:"bottom",
        navthumbstyle:"imageandtitledescription",
        textcss:"display:block; padding:12px; text-align:left;",
        navbordercolor:"#ffffff",
        navpreviewarrowimage:"previewarrow-16-8-0.png",
        showbottomshadow:true,
        navdirection:"vertical",
        textpositionmarginstatic:0,
        backgroundimage:"",
        navposition:"right",
        arrowstyle:"mouseover",
        bottomshadowimagetop:95,
        textpositiondynamic:"bottomleft",
        navshowbuttons:false,
        navthumbtitlecss:"display:block;position:relative;padding:2px 4px;text-align:left;font:bold 12px Arial,Helvetica,sans-serif;color:#333;",
        textpositionmarginbottom:24,
        slice: {
            duration:1500,
            easing:"easeOutCubic",
            checked:true,
            effects:"up,down,updown",
            slicecount:10
        },
        transition:"slice"
    });
});