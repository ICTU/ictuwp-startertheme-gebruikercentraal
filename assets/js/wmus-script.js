jQuery( document ).ready( function( $ ) { 
    $( '.wmus-check-uncheck' ).on( 'change', function() {
        var checked = $( this ).prop( 'checked' );
        $( '.wmus-sites input[type="checkbox"]' ).each( function() {
            if ( checked ) {
                $( this ).prop( 'checked', true );
            } else {
                $( this ).prop( 'checked', false );
            }
        });                   
    });
    
    $( 'input[type="radio"][name="wmus_auto_sync_type"]' ).on( 'change', function() {
        var type = $( this ).val();
        if ( type == 'main-site-to-sub-sites' ) {
            $( '.wmus-hide-show' ).show();     
        } else {
            $( '.wmus-hide-show' ).hide();
        }
    });   
});