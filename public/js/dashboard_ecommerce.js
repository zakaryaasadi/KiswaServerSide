    var info_this_week;
    var info_last_week;
    var info_year;

$(function(){
    
    $('.easypie').each(function(){
        $(this).easyPieChart({
          trackColor: $(this).attr('data-trackColor') || '#f2f2f2',
          scaleColor: false,
        });
    }); 


});

