function sniperStart() {
	$('.post-sniper').removeClass('d-none').addClass('d-flex');
}

function sniperEnd() {
	$('.post-sniper').removeClass('d-flex').addClass('d-none');
}

function postAjax(url) {
	return $.ajax({
		async: false,
		url: url,
		beforeSend: () => sniperStart(),
		success: (data) => $('#posts').html(data),
		error: () => alert('Error'),
		complete: () => sniperEnd()
	});
}


$(document).on("click", "#sort-top, #all-post, .knp-pagination", function (e) {
	postAjax($(this).attr('href'));
	return e.preventDefault();
});

$(document).on("click", ".pagination a.page-link", function (e) {
	postAjax($(this).attr('href'));
	return e.preventDefault();
});
