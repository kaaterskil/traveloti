var TravelotiLanding = {
	init : function() {
		if (TravelotiLanding.supportsInputPlaceholder() == false) {
			$('input').each(
					function() {
						if ($(this).attr('placeholder') != null) {
							var e = '<div class="placeholder">'
									+ $(this).attr('placeholder') + '</div>';
							$(this).before(e);
						}
					});
		}
	},
	supportsInputPlaceholder : function() {
		var e = document.createElement('input');
		return 'placeholder' in e;
	}
};

$('document').ready(function() {
	TravelotiLanding.init();
});