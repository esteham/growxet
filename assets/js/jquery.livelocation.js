(function(l){

	l.fn.livelocation = function(){

		var i = l(this);

		jQuery.ajax({

			url: "http://ip-api.com/json",
			success: function(l)
			{
				var n = "<div class='location'>";
				"success" == l.status ? ((n += "You are accessing this website from "+l.city+", "), (n += l.country+"<br>"),(n += "Your IP Address is "+l.query)) : (n +="Something went wrong"), (n += "</div>"), i.html(n);
			}
		});
	};
})(jQuery);