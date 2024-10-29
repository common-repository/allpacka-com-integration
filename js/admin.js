jQuery(document).ready(function(){
    
    jQuery('.allpacka_megrendeles_gomb').on('click', allpacka_megrendeles);
    
    function allpacka_megrendeles( event ){
        
        var gomb = jQuery( event.target );

        order_id = gomb.attr("order_id");

        jQuery('.allpacka_megrendeles_gomb').off( 'click' );

        jQuery( '<div class="spinner allpacka_spinner allpacka_spinner_'+order_id+'" ></div>').insertBefore( jQuery('.allpacka_megrendeles_gomb_box_'+order_id+'').eq(0) );
        
        jQuery('.allpacka_megrendeles_gomb_box_'+order_id+'').eq(0).hide();
        
        jQuery( ".allpacka_notice" ).remove();

        jQuery.ajax({
            url: allpacka_admin_adatok.ajax_url,
            data: {
                'action':'allpacka_ajax_megrendeles',
                'order_id' : order_id
            },
            error: function (err) {
                console.log("AJAX error: " + JSON.stringify(err, null, 2));
            },
            success:function(allpacka_megrendeles_return) {
   
                allpacka_megrendeles_return = jQuery.parseJSON(allpacka_megrendeles_return);
                

                if( allpacka_megrendeles_return['sikeres'] ){
                    
                    $uzenet = allpacka_megrendeles_return['sikeres'];
                    
                    if( allpacka_megrendeles_return['hiba']  ){
                        
                        $uzenet += '<br/>';
                        
                        for ( index = allpacka_megrendeles_return['hiba'].length - 1; index >= 0; index-- ) {

                            $uzenet += '<br/>'+allpacka_megrendeles_return['hiba'][index];
                            
                        }
                    
                        
                    }
                    
                    if( allpacka_megrendeles_return['debug']  ){
                        
                        $uzenet += '<br/><br/>';
                        
                        $uzenet += allpacka_megrendeles_return['debug'];
                        
                    }

                    allpacka_notice( 'updated', $uzenet );
                    
                    
   
                }
                else if( allpacka_megrendeles_return['sikertelen'] ){
                    
                    $uzenet = allpacka_megrendeles_return['sikertelen'];
                    
                    if( allpacka_megrendeles_return['hiba']  ){
                        
                        $uzenet += '<br/>';
                        
                        for ( index = allpacka_megrendeles_return['hiba'].length - 1; index >= 0; index-- ) {

                            $uzenet += '<br/>'+allpacka_megrendeles_return['hiba'][index];
                            
                        }
                    
                        
                    }
                    
                    if( allpacka_megrendeles_return['debug']  ){
                        
                        $uzenet += '<br/><br/>';
                        
                        $uzenet += allpacka_megrendeles_return['debug'];
                        
                    }

                    allpacka_notice( 'error', $uzenet );
                                       
                    
                }
                else{
                    
                    $uzenet = '';
                    
                    if( allpacka_megrendeles_return['hiba']  ){
    
                        for ( index = allpacka_megrendeles_return['hiba'].length - 1; index >= 0; index-- ) {

                            $uzenet += '<br/>'+allpacka_megrendeles_return['hiba'][index];
                            
                        }
                    
                        
                    }
                    
                    if( allpacka_megrendeles_return['debug']  ){
                        
                        $uzenet += '<br/><br/>';
                        
                        $uzenet += allpacka_megrendeles_return['debug'];
                        
                    }

                    allpacka_notice( 'error', $uzenet );
                    
                }
                
                
                /**/
                
                if( allpacka_megrendeles_return['allpacka_gombok'] ){
                        
                    jQuery('.allpacka_megrendeles_gomb_box_'+order_id+'').eq(0).after( allpacka_megrendeles_return['allpacka_gombok'] );
                    
                }
                
                jQuery('.allpacka_megrendeles_gomb').on( 'click', allpacka_megrendeles );
                
                jQuery('.allpacka_spinner_'+order_id+'').remove();   
                
                jQuery('.allpacka_megrendeles_gomb_box_'+order_id+'').eq(0).remove();
                
                /**/         
               

            }
        });
        
 
    }
    
    /******************************************************************************************************************************************************************/
    /******************************************************************************************************************************************************************/
    
    function allpacka_notice( tipus, tartalom ){
        
        jQuery( ".wp-header-end" ).after( 
           '<div  class="'+tipus+' notice is-dismissible allpacka_notice"><div><img src="'+allpacka_admin_adatok.plugin_dir_url+'images/pactic_ikon_3.png" />'+tartalom+'</div><button type="button" class="notice-dismiss allpacka_notice_dismiss"></button></div>' 
        );
                        
    }

    
    /******************************************************************************************************************************************************************/
    /******************************************************************************************************************************************************************/
    
    //jQuery('.allpacka_notice_dismiss').live('click', function( event ){
    jQuery( document ).on( 'click', '.allpacka_notice_dismiss', function( event ){

        var gomb = jQuery( event.target );

        gomb.parent( '.notice' ).fadeTo(100, 0, function() {
            
            gomb.parent( '.notice' ).slideUp(100, function() {
               
                gomb.parent( '.notice' ).remove();
            
            });
            
        });

    });
    
    /******************************************************************************************************************************************************************/
    /******************************************************************************************************************************************************************/
    
    jQuery('body.woocommerce_page_wc-settings a.nav-tab[href$="tab=allpacka"]').html('<img src="'+allpacka_admin_adatok.plugin_dir_url+'/images/pactic_ikon_2.png" />');
        
    /******************************************************************************************************************************************************************/
    /******************************************************************************************************************************************************************/
    /******************************************************************************************************************************************************************/
    /******************************************************************************************************************************************************************/
    /******************************************************************************************************************************************************************/
    /******************************************************************************************************************************************************************/
    
    //jQuery('span.allpacka_kivalasztott_gls_csomagpont_szerkesztes').live('click', function( ){
    jQuery( document ).on( 'click', 'span.allpacka_kivalasztott_gls_csomagpont_szerkesztes', function(  ){

        jQuery( 'div.allpacka_kivalasztott_gls_csomagpont_szerkesztes' ).toggle();
        
        jQuery( '.allpacka_gls_csomagpont_kivalasztott_kontener_cim' ).toggle();

    });
    
    /******************************************************************************************************************************************************************/
    /******************************************************************************************************************************************************************/
    
    
    //jQuery('span.allpacka_kivalasztott_gls_csomagpont_torles').live('click', function( ){
    jQuery( document ).on( 'click', 'span.allpacka_kivalasztott_gls_csomagpont_torles', function(  ){
        
        jQuery('#allpacka_kivalasztott_gls_csomagpont').val( '' );
        
        jQuery('.allpacka_gls_csomagpont_kivalasztott_kontener_adatok').remove();
        
        jQuery( '<div class="allpacka_gls_csomagpont_kivalasztott_kontener_adatok allpacka_gls_csomagpont_kivalasztott_kontener_adatok_ures">'+allpacka_admin_adatok.nincs_kivalasztott_csomagpont+'</div>' ).insertAfter( '#allpacka_kivalasztott_gls_csomagpont' );

        jQuery( '.allpacka_gls_csomagpont_lista_elem' ).removeClass( 'allpacka_gls_csomagpont_kivalasztott_lista_elem' );

    });
    
    
    
    
    
    /******************************************************************************************************************************************************************/
    /******************************************************************************************************************************************************************/
    
    if( jQuery( '.allpacka_gls_csomagpont_lista_elemek' ).length > 0 ) {

        allpacka_gls_csomagpont_legordulo_generalas();
    
    }
                    
    function allpacka_gls_csomagpont_legordulo_generalas( filter = '' ){
        
        jQuery.ajax({
            url: allpacka_admin_adatok.ajax_url,
            data: {
                'action':'allpacka_ajax_gls_csomagpontok',
                'filter': filter
            },
            success:function( allpacka_ajax_gls_csomagpontok_return ) {
                
                var allpacka_gls_csomagpontok = jQuery.parseJSON( allpacka_ajax_gls_csomagpontok_return );

                jQuery('.allpacka_gls_csomagpont_lista_elemek').find('.allpacka_gls_csomagpont_lista_elem').remove();

                jQuery.each( allpacka_gls_csomagpontok, function(key, allpacka_gls_csomagpont) {
                    
                    allpacka_gls_csomagpont_adatok = JSON.stringify(allpacka_gls_csomagpont);
 
                    allpacka_gls_csomagpont_adatok = allpacka_gls_csomagpont_adatok.replace( /"/g, "'" );
   
                    /**/
                    lista_class = '';
                    
    
                    if( jQuery('#allpacka_kivalasztott_gls_csomagpont').val() ){

                        if( jQuery('#allpacka_kivalasztott_gls_csomagpont').attr('allpacka_gls_csomagpont_kod') == allpacka_gls_csomagpont.Code ){
                            
                            lista_class = 'allpacka_gls_csomagpont_kivalasztott_lista_elem';
                            
                        }
                    
                    }
                    
                    
                    
                    allpacka_gls_csomagpont_cim = allpacka_gls_csomagpont.PostCode+' '+allpacka_gls_csomagpont.City+' '+allpacka_gls_csomagpont.StreetAndBuildingNumber;
                    
                    csomagpont = '<div class="allpacka_gls_csomagpont_lista_elem '+lista_class+'">';
                        csomagpont += allpacka_gls_csomagpont.Name;
                        csomagpont += '<div>';
                            csomagpont += allpacka_gls_csomagpont_cim;
                        csomagpont += '</div>';
                    csomagpont += '</div>';
                    
                    jQuery('.allpacka_gls_csomagpont_lista_elemek').append( jQuery(csomagpont).
                        attr("allpacka_gls_csomagpont_kod",allpacka_gls_csomagpont.Code).
                        attr("allpacka_gls_csomagpont_nev",allpacka_gls_csomagpont.Name).
                        attr("allpacka_gls_csomagpont_iranyitoszam",allpacka_gls_csomagpont.PostCode).
                        attr("allpacka_gls_csomagpont_varos",allpacka_gls_csomagpont.City).
                        attr("allpacka_gls_csomagpont_utca",allpacka_gls_csomagpont.StreetAndBuildingNumber).
                        attr("allpacka_gls_csomagpont_adatok",allpacka_gls_csomagpont_adatok) 
                    ); 
                    /**/
                    
                    
                });
   
            }
            
        });
        
    }
    
    /******************************************************************************************************************************************************************/
    /******************************************************************************************************************************************************************/
    
    //jQuery( '.allpacka_gls_csomagpont_lista_elem' ).live("click", function() { 
    jQuery( document ).on( 'click', '.allpacka_gls_csomagpont_lista_elem', function(  ){

        jQuery('#allpacka_kivalasztott_gls_csomagpont').val( jQuery( this ).attr( 'allpacka_gls_csomagpont_adatok' ) );
        
        jQuery('.allpacka_gls_csomagpont_kivalasztott_kontener_adatok').remove();

        jQuery( 
            '<div class="allpacka_gls_csomagpont_kivalasztott_kontener_adatok">'+
                jQuery( this ).attr( 'allpacka_gls_csomagpont_nev' )+'<br/>'+
                jQuery( this ).attr( 'allpacka_gls_csomagpont_iranyitoszam' )+'<br/>'+
                jQuery( this ).attr( 'allpacka_gls_csomagpont_varos' )+'<br/>'+
                jQuery( this ).attr( 'allpacka_gls_csomagpont_utca' )+
            '</div>' 
        ).insertAfter( '#allpacka_kivalasztott_gls_csomagpont' );

        jQuery( '.allpacka_gls_csomagpont_lista_elem' ).removeClass( 'allpacka_gls_csomagpont_kivalasztott_lista_elem' );
        jQuery( this ).addClass( 'allpacka_gls_csomagpont_kivalasztott_lista_elem' );
        
    });
    
    /******************************************************************************************************************************************************************/
    /******************************************************************************************************************************************************************/
    
    //jQuery( "#allpacka_gls_csomagpont_filter" ).live("keyup", function() { 
    jQuery( document ).on( 'keyup', "#allpacka_gls_csomagpont_filter", function(  ){
        
        filter = jQuery(this).val();
        
        if( filter.length == 4 || filter.length == 0 ){
        
            allpacka_gls_csomagpont_legordulo_generalas( filter );
        
        }

    });
    
    /******************************************************************************************************************************************************************/
    /******************************************************************************************************************************************************************/
    
    //jQuery('.allpacka_waybills_torles_gomb').live('click', function(){
    jQuery( document ).on( 'click', '.allpacka_waybills_torles_gomb', function(  ){

        jQuery.ajax({
            url: allpacka_admin_adatok.ajax_url,
            data: {
                'action':'allpacka_waybills_ajax_torles'
            },
            success:function(allpacka_waybills_ajax_torles_return) {
                
                jQuery( ".allpacka_notice" ).remove();
   
                jQuery( '.allpacka_waybills_torles_gomb' ).after( 
                   '<div class="updated notice is-dismissible allpacka_notice"><div><img src="'+allpacka_admin_adatok.plugin_dir_url+'images/pactic_ikon_3.png" />'+allpacka_waybills_ajax_torles_return+'</div><button type="button" class="notice-dismiss allpacka_notice_dismiss"></button></div>' 
                );

            }
        });

    });
    
    /******************************************************************************************************************************************************************/
    /******************************************************************************************************************************************************************/
    
    if( jQuery( "select#allpacka_fizetesi_modok" ).length > 0 ) {
        
        jQuery( "select#allpacka_fizetesi_modok" ).select2();
    
    }

    
    /******************************************************************************************************************************************************************/
    /******************************************************************************************************************************************************************/
    
    //jQuery( '.allpacka_cod_notice_dismiss' ).live('click', function( event ){
    jQuery( document ).on( 'click', '.allpacka_cod_notice_dismiss', function( event ){

        var gomb = jQuery( event.target );
        
        jQuery.ajax({
            url: allpacka_admin_adatok.ajax_url,
            data: {
                'action':'allpacka_cod_notice_dismiss'
            },
            success:function() {

                gomb.parent( 'p' ).parent( 'div' ).parent( '.notice' ).fadeTo(100, 0, function() {
            
                gomb.parent( 'p' ).parent( 'div' ).parent( '.notice' ).slideUp(100, function() {
                   
                    gomb.parent( 'p' ).parent( 'div' ).parent( '.notice' ).remove();
                
                });
                
            });

            }
        });

    });
    
    
    /******************************************************************************************************************************************************************/
    /******************************************************************************************************************************************************************/
    
    
    

});









