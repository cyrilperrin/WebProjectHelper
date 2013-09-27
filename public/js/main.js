// When DOM is ready
$(document).ready(function() {
	// Cut help by columns
	$('#help > div').columnize({width: 500});
});

/**
 * Compile definitions
 * @param url generation url 
 */
function compile(url) {
	// Call generate script and display result
	$('#links').fadeOut(250,function() {
		// Call generate script
		$.post(url,{
			definitions: $('#definitions').val(),
			zipphp: $('input[name="zipphp"]').attr('checked'),
			zipsql: $('input[name="zipsql"]').attr('checked'),
			compile: '1'
		},
		// Display result
		function (data) {
			// Add data to result zone
			$('#links').html(data);
			
			// Display result zone
			$('#links').fadeIn(250);
		});
	});
}

/**
 * Clear links 
 */
function clearLinks() {
	var links = $('#links');
	if(links.html() != '...') {
		links.fadeOut(250,function() {
			links.html('...');
			links.fadeIn(250);
		});
	}
}

/**
 * Generate PHP
 */
function generatePHP() {
	var div = $('#generate');
	var form = $('#generatePHP');
	form.find('input[name="definitions"]').val($('#definitions').val());
	form.find('input[name="zip"]').val(div.find('input[name="zipphp"]').attr('checked') ? 'true' : 'false');
	form.find('input[name="fieldnamesbase"]').val(div.find('input[name="fieldnamesbase"]').attr('checked') ? 'true' : 'false');
	form.find('input[name="onlybase"]').val(div.find('input[name="onlybase"]').attr('checked') ? 'true' : 'false');
	form.find('input[name="prefixdatatables"]').val(div.find('input[name="prefixdatatables"]').val());
	form.find('input[name="phpclassesprefix"]').val(div.find('input[name="phpclassesprefix"]').val());
	form.find('input[name="phpfilesprefix"]').val(div.find('input[name="phpfilesprefix"]').val());
	form.find('input[type="submit"]').click();
}

/**
 * Generate MySQL
 */
function generateSQL() {
	var div = $('#generate');
	var form = $('#generateSQL');
	form.find('input[name="definitions"]').val($('#definitions').val());
	form.find('input[name="zip"]').val(div.find('input[name="zipsql"]').attr('checked') ? 'true' : 'false');
	form.find('input[name="prefixdatatables"]').val(div.find('input[name="prefixdatatables"]').val());
	form.find('input[type="submit"]').click();
}

// Google analytics
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-9937933-8']);
_gaq.push(['_trackPageview']);

(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();