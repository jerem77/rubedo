(function($) {
    $.fn.slideDownOnly = function() { 
    var self=$(this);  
	if(self.css("display")=="block")
	{
		self.slideUp("slow",function(){
			$("#overflow").hide();
		});
		
	}
	else
	{
		self.slideDown("slow",function(){
			$("#overflow").show();
		});
		
	}
	$(".info").each(function(){
		if($(this).css("display")=="block" && $(this).attr("id")!=self.attr("id"))
		{
			$(this).slideUp("slow",function(){
				$("#overflow").hide();
			});
		}
	});
	$("#overflow").click(function(){
		$(".info").each(function(){
		if($(this).css("display")=="block")
		{
			$(this).slideUp("slow",function(){
				$("#overflow").hide();
			});
		}
	});
		
	});
    };
})(jQuery);  

jQuery(document).ready(function(){
	 jQuery('.bloc').css({

    'height': $('.bloc').height()

});
})
