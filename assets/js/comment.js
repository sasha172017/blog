const commentBlockClass = '.bd-callout';

function parent($obj) {
	return $obj.closest(commentBlockClass);
}

$(document).on('click', '[data-comment-action="edit"]', function (e) {
	let $this = $(this);

	$.ajax({
		async: false,
		url: $this.data('ajax-href'),
		success: (response) => parent($this).find('.comment-content').html(response),
		error: () => alert('Error'),
	});

	return e.preventDefault();
});

$(document).on('submit', '[name="comment_ajax_form"]', function (e) {
	let $this = $(this);

	$.ajax({
		async: false,
		method: $this.attr('method'),
		url: $this.attr('action'),
		data: $this.serialize(),
		success: function (response) {
			parent($this).find('.comment-updated').html(response.updated);
			parent($this).find('.comment-content').html(response.content);
		},
		error: () => alert('Error'),
	});

	return e.preventDefault();

});

$(document).on('click', '[data-comment-action="delete"]', function (e) {
	let $this = $(this);

	$.ajax({
		async: false,
		url: $this.attr('href'),
		success: function (response) {
			$('.tooltip').remove();
			$('#comments').html(response);
		},
		error: () => alert('Error'),
	});

	return e.preventDefault();
});

$(document).on('submit', '#comment-new-form', function (e) {

	$.ajax({
		async: false,
		method: $(this).attr('method'),
		url: $(this).attr('action'),
		data: $(this).serialize(),
		success: function (response) {
			$('#comment_content').val('');
			$('.tooltip').remove();
			$('#comments').html(response);
		},
		error: () => alert('Error'),
	});

	return e.preventDefault();
});