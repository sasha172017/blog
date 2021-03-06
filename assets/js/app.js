/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
import '../scss/app.scss';

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// import $ from 'jquery';

require('bootstrap');
require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');

$(function () {
	$('[data-toggle="tooltip"]').tooltip()
});

$(function () {
	$('[data-toggle="popover"]').popover()
});
