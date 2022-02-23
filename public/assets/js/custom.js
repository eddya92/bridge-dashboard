/*
Note for bootstrap grid system
-sm Max container width: 576 px
-md Max container width: 768 px
-lg Max container width: 992 px
-xl Max container width: 1200 px
-xxl Max container width: 1400 px
*/
(function ($) {
	"use strict";
	/*----------------------------------------
     Show / Hide characters changing input type (text - password)
     ----------------------------------------*/
	$('#show_characters').click(function(){
		$(this).is(':checked') ? $('.type_password').attr('type', 'text') : $('.type_password').attr('type', 'password');
	});
	$( document ).ready( same_heights );
	$( window ).resize( same_heights );
	$( document ).ready( sign_obliged_fields );

})(jQuery);

function sign_obliged_fields(){
	$( "form input,form select,form textarea" ).each(function( index ) {
		if($(this).prop('required')){
			$(this).css('background-image', 'url(/assets/images/asterisk.gif)');
			$(this).css('background-repeat', 'no-repeat');
			$(this).css('background-position', 'right 10px center');
		}
	});
}

function same_heights(){
	/*----------------------------------------
     Set same height for each class in each row
     ----------------------------------------*/
	let window_width = $( window ).width();
	let class_for_resize = "";
	if(window_width >=  576){
		class_for_resize = "resize_equal_height_sm";
	}
	if(window_width >=  768){
		class_for_resize = "resize_equal_height_md";
	}
	if(window_width >=  992){
		class_for_resize = "resize_equal_height_lg";
	}
	if(window_width >=  1200){
		class_for_resize = "resize_equal_height_xl";
	}
	if(window_width >=  1400){
		class_for_resize = "resize_equal_height_xxl";
	}

	let num_element_with_class = $('.' +  class_for_resize).length;
	if(num_element_with_class > 0){
		$( ".row" ).each(function( index ) {
			let max_height = 0;
			var cur_row_id = $(this).attr("id");
			$("#" + cur_row_id + " ." + class_for_resize ).each(function( index ) {
				let cur_height = $(this).height();
				if(cur_height > max_height){
					max_height = cur_height;
				}
			});
			$("#" + cur_row_id + " ." + class_for_resize  ).each(function( index ) {
				$(this).css("height" , max_height + "px");
			});
			max_height = 0;
		});
	}else{
		$( ".resize_equal_height" ).each(function( index ) {
			$(this).css("height" , "auto");
		});
	}
}