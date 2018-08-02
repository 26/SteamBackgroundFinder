/*

	Steam Background Finder
    Copyright (C) 2018 Xxmarijnw

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published
    by the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
	
*/

$(document).ready(function(){
	$(".center").fadeIn(660).css("display","table");
	
	// Check if the form has been submitted
	$("#submit-button").click(function(e) { e.preventDefault(); ajax(); });
	$("#form").submit(function(e) { e.preventDefault(); ajax(); });
	
	function ajax() {
		// Change text in upload button
		$('.submit-text').html('<div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');
		
		var formData = $("form").serialize();
		// Send AJAX request
		$.ajax({
			url: 'handler',
			type: 'POST',

			// Form data
			data: formData,

			// Execute function on success
			success: function(result){
				if(~result.indexOf("Oops")) {
					$("#status").html(result);
					$("ul").css('display', 'none');
					$("#result").fadeIn(660);
					$(".submit-text").html('<i class="fas fa-search"></i>');
				} else {
					var results = $.parseJSON(result);
					$("#result").fadeOut(330);
					$("form").fadeOut(330);
					setTimeout (function() {
						$("#status").html('');
						$("body").css('background-image', 'url(' + results['background'] + ')');
						$("#smp").html('<a target="_blank" rel="noopener noreferrer" href="' + results['market'] + '">Buy it' + results['price'] + '</a>');
						$("#sce").html('<a target="_blank" rel="noopener noreferrer" href="' + results['steam_card_exchange'] + '">Steam Card Exchange</a>');
						$("#gis").html('<a target="_blank" rel="noopener noreferrer" href="' + results['steamdesign'] + '">Steam Design</a>');
						$("#dil").html('<a target="_blank" rel="noopener noreferrer" href="' + results['background'] + '">Direct Link</a>');
						$("#game").html(results['app_name']);
					}, 330);
					$("ul").delay(660).fadeIn();
					$("#result").delay(620).fadeIn(660);
					$('.center').delay(660).animate({bottom: '15'}, 660);
				}
			}
		});
    }
});