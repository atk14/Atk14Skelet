// A generic way to fade out a just deleted record

// The destroy link can be inside a table row,...
$link.closest( "tr" ).fadeOut(500, function() { $(this).remove(); });

// ... or it can be inside a <ul> or <ol> list
if ( $link.closest( "ul.dropdown-menu" ).length > 0 ) {

	// It's a destroy link in a dropdown-menu
	$link.closest( "ul.dropdown-menu" ).closest( "li" ).fadeOut(500, function() { $(this).remove(); });
} else {
	$link.closest( "li" ).fadeOut(500, function() { $(this).remove(); });
}
