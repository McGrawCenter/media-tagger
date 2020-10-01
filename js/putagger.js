console.log('tagit loaded');

jQuery( document ).ready(function() {



	jQuery( "#tagitttt" ).submit(function(event) {

	  console.log(jQuery('#tag').val());

	  event.preventDefault();
	});




});

