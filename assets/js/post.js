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

function markText()
{
	let q = $('input[name="q"]').val();

	if (q.length > 0) {
		$('.js-mark').mark(q);
	}
}

$(document).ready(function () {
	markText();
});

$(document).on('click', '#all-post, .knp-pagination', function (e) {
	postAjax($(this).attr('href'));
	return e.preventDefault();
});

$(document).on('click', '.pagination a.page-link', function (e) {
	postAjax($(this).attr('href'));
	return e.preventDefault();
});

$(document).on('click', '#reset-search', function (e) {
	postAjax($(this).attr('href'));
	$('input[name="q"]').val(null);

	return e.preventDefault();
});

$(document).on('submit', '#search-form', function (e) {

	let $this = $(this);

	$.ajax({
		async: false,
		method: $this.attr('method'),
		url: $this.attr('action'),
		data: $this.serialize(),
		beforeSend: () => sniperStart(),
		success: function (data) {
			$('#posts').html(data);
			markText();
		},
		error: () => alert('Error'),
		complete: () => sniperEnd()
	});

	return e.preventDefault();

});
