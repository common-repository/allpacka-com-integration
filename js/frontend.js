jQuery(document).ready(function(){
    
    jQuery( 'body' ).bind( 'updated_checkout', function() {
        
        jQuery.ajax({
            url: allpacka_frontend_adatok.ajax_url,
            data: {
                'action':'allpacka_ajax_szallitasi_mod_ellenorzese'
            },
            success:function( allpacka_ajax_szallitasi_mod_ellenorzese_return ) {

                szallitasi_mod = jQuery.parseJSON( allpacka_ajax_szallitasi_mod_ellenorzese_return );

                if( szallitasi_mod['nev']  == 'allpacka_gls_csomagpont' ){
                    
                    if( szallitasi_mod['tipus'] == 'terkepes' ){
                        
                        if( jQuery( '#allpacka_gls_csomagpont_kontener' ).length == 0 ) {

                            allpacka_gls_csomagpont_kontener = '<div id="allpacka_gls_csomagpont_kontener">';
                                                
                                allpacka_gls_csomagpont_kontener += '<h3>'+allpacka_frontend_adatok.csomagpont_cim+'</h3>';

                                allpacka_gls_csomagpont_kontener += '<div class="allpacka_gls_csomagpont_terkep_kontener">';
                                
                                    allpacka_gls_csomagpont_kontener += '<div id="allpacka_gls_csomagpont_terkep" style="height:500px;">';
                                    allpacka_gls_csomagpont_kontener += '</div>';
                                
                                allpacka_gls_csomagpont_kontener += '</div>';  
                                
                                allpacka_gls_csomagpont_kontener += '<div class="allpacka_gls_csomagpont_kivalasztott_kontener">';
                                
                                    allpacka_gls_csomagpont_kontener += '<div class="allpacka_gls_csomagpont_kivalasztott_kontener_cim"><strong>'+allpacka_frontend_adatok.kivalasztott_csomagpont+'</strong></div>';
                                
                                    allpacka_gls_csomagpont_kontener += '<input type="text" id="allpacka_kivalasztott_gls_csomagpont" name="allpacka_kivalasztott_gls_csomagpont" value="">';
                                
                                    allpacka_gls_csomagpont_kontener += '<div class="allpacka_gls_csomagpont_kivalasztott_kontener_adatok allpacka_gls_csomagpont_kivalasztott_kontener_adatok_ures">'+allpacka_frontend_adatok.nincs_kivalasztott_csomagpont+'</div>';
                                    
                                allpacka_gls_csomagpont_kontener += '</div>';
                                
                            allpacka_gls_csomagpont_kontener += '</div>';
   
                            jQuery( allpacka_gls_csomagpont_kontener ).insertAfter( jQuery(".shop_table") );

                            allpacka_gls_csomagpont_terkep_generalas();

                            
                        }

                        
                    }
                    else if( szallitasi_mod['tipus'] == 'legordulo' ){

                        if( jQuery( '#allpacka_gls_csomagpont_kontener' ).length == 0 ) {

                            allpacka_gls_csomagpont_kontener = '<div id="allpacka_gls_csomagpont_kontener">';
                                                
                                allpacka_gls_csomagpont_kontener += '<h3>'+allpacka_frontend_adatok.csomagpont_cim+'</h3>';
                                
                                allpacka_gls_csomagpont_kontener += '<div class="allpacka_gls_csomagpont_filter_kontener">';
                                
                                    allpacka_gls_csomagpont_kontener += '<input type="text" id="allpacka_gls_csomagpont_filter" name="allpacka_gls_csomagpont_filter" value="" placeholder="'+allpacka_frontend_adatok.csomagpont_kereses+'">';
  
                                allpacka_gls_csomagpont_kontener += '</div>';
                                
                                allpacka_gls_csomagpont_kontener += '<div class="allpacka_gls_csomagpont_lista_kontener">';
                                
                                    allpacka_gls_csomagpont_kontener += '<div class="allpacka_gls_csomagpont_lista_elemek">';
  
                                    allpacka_gls_csomagpont_kontener += '</div>';  
  
                                allpacka_gls_csomagpont_kontener += '</div>';  
                                
                                allpacka_gls_csomagpont_kontener += '<div class="allpacka_gls_csomagpont_kivalasztott_kontener">';
                                
                                    allpacka_gls_csomagpont_kontener += '<div class="allpacka_gls_csomagpont_kivalasztott_kontener_cim"><strong>'+allpacka_frontend_adatok.kivalasztott_csomagpont+'</strong></div>';
                                
                                    allpacka_gls_csomagpont_kontener += '<input type="text" id="allpacka_kivalasztott_gls_csomagpont" name="allpacka_kivalasztott_gls_csomagpont" value="">';
                                
                                    allpacka_gls_csomagpont_kontener += '<div class="allpacka_gls_csomagpont_kivalasztott_kontener_adatok allpacka_gls_csomagpont_kivalasztott_kontener_adatok_ures">'+allpacka_frontend_adatok.nincs_kivalasztott_csomagpont+'</div>';
                                
                                allpacka_gls_csomagpont_kontener += '</div>';
                                
                            allpacka_gls_csomagpont_kontener += '</div>';
   
                            jQuery( allpacka_gls_csomagpont_kontener ).insertAfter( jQuery(".shop_table") );
            
                            allpacka_gls_csomagpont_legordulo_generalas();
         
                            
                        }

                    }
                    
                }
                else{
                    
                    if( jQuery( '#allpacka_gls_csomagpont_kontener' ).length > 0 ) {

                        jQuery("#allpacka_gls_csomagpont_kontener").html('').remove();
                    
                    }
                    
                }
                
                
                
            }
        });

	});
    
    /******************************************************************************************************************************************************************/
    /******************************************************************************************************************************************************************/
    
    function allpacka_gls_csomagpont_legordulo_generalas( filter = '' ){
        
        jQuery.ajax({
            url: allpacka_frontend_adatok.ajax_url,
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
                    
                    lista_class = '';
                    
                    if( jQuery('#allpacka_kivalasztott_gls_csomagpont').val() == allpacka_gls_csomagpont_adatok ){
                        
                        lista_class = 'allpacka_gls_csomagpont_kivalasztott_lista_elem';
                        
                    }
                    
                    allpacka_gls_csomagpont_cim = allpacka_gls_csomagpont.PostCode+' '+allpacka_gls_csomagpont.City+' '+allpacka_gls_csomagpont.StreetAndBuildingNumber;
                    
                    csomagpont = '<div class="allpacka_gls_csomagpont_lista_elem '+lista_class+'">';
                        csomagpont += allpacka_gls_csomagpont.Name;
                        csomagpont += '<div>';
                            csomagpont += allpacka_gls_csomagpont_cim;
                        csomagpont += '</div>';
                    csomagpont += '</div>';
                    
                    jQuery('.allpacka_gls_csomagpont_lista_elemek').append( jQuery(csomagpont).
                        attr("allpacka_gls_csomagpont_nev",allpacka_gls_csomagpont.Name).
                        attr("allpacka_gls_csomagpont_cim",allpacka_gls_csomagpont_cim).
                        attr("allpacka_gls_csomagpont_adatok",allpacka_gls_csomagpont_adatok) 
                    ); 
                    
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

        jQuery( '<div class="allpacka_gls_csomagpont_kivalasztott_kontener_adatok">'+jQuery( this ).attr( 'allpacka_gls_csomagpont_nev' )+'<br/>'+jQuery( this ).attr( 'allpacka_gls_csomagpont_cim' )+'</div>' ).insertAfter( '#allpacka_kivalasztott_gls_csomagpont' );

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
    
    function allpacka_gls_csomagpont_terkep_generalas(){

        var currentInfoWindow = null;
        
        var map;
        
        var bounds = new google.maps.LatLngBounds();
        
        var markers = [];

        map = new google.maps.Map(document.getElementById('allpacka_gls_csomagpont_terkep'), {
          center: {lat: 47.180086, lng: 19.503736},
          zoom: 8,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var allpacka_gls_csomagpont_kep = {
          url: allpacka_frontend_adatok.plugin_dir_url+'/images/gls_csomagpont.png',
          size: new google.maps.Size(50, 35),
          origin: new google.maps.Point(0, 0),
          anchor: new google.maps.Point(25, 0)
        };
        
                
        var markerClusterOptions = {
            imagePath:allpacka_frontend_adatok.plugin_dir_url+'/images/markercluster',
            imageExtension:'png',
        };

        jQuery.ajax({
            url: allpacka_frontend_adatok.ajax_url,
            data: {
                'action':'allpacka_ajax_gls_csomagpontok'
            },
            success:function( allpacka_ajax_gls_csomagpontok_return ) {
                
                var allpacka_gls_csomagpontok = jQuery.parseJSON( allpacka_ajax_gls_csomagpontok_return );

                jQuery.each( allpacka_gls_csomagpontok, function(key, allpacka_gls_csomagpont) {
                                        
                    allpacka_gls_csomagpont.Latitude = allpacka_gls_csomagpont.Latitude.replace(",", ".");
                    allpacka_gls_csomagpont.Longitude = allpacka_gls_csomagpont.Longitude.replace(",", ".");
            
                    var position = new google.maps.LatLng( allpacka_gls_csomagpont.Latitude, allpacka_gls_csomagpont.Longitude );
                    
                    bounds.extend( position );
                    
                    var marker = new google.maps.Marker( { 
                        position: position, 
                        map: map,
                        icon: allpacka_gls_csomagpont_kep,
                        title: allpacka_gls_csomagpont.Name,
                    });
                    
                    allpacka_gls_csomagpont.marker = allpacka_gls_csomagpont_kep['url'];

        
                     markers.push(marker);

                     allpacka_gls_csomagpont_adatok = JSON.stringify( allpacka_gls_csomagpont );
                   
                     allpacka_gls_csomagpont_adatok =  allpacka_gls_csomagpont_adatok.replace( /"/g, "'" );
                     
                     allpacka_gls_csomagpont_cim = allpacka_gls_csomagpont.PostCode+' '+allpacka_gls_csomagpont.City+' '+allpacka_gls_csomagpont.StreetAndBuildingNumber;

                     var tartalom =
                        '<div class="allpacka_gls_csomagpont_infowindow">'+
                            '<div class="allpacka_gls_csomagpont_infowindow_nev">'+allpacka_gls_csomagpont.Name+'</div>'+
                            '<div class="allpacka_gls_csomagpont_infowindow_cim" >'+
                                allpacka_gls_csomagpont_cim +
                            '</div>'+
                            '<div class="allpacka_gls_csomagpont_infowindow_gomb" >'+
                                '<div class="allpacka_gls_csomagpont_kivalasztas" allpacka_gls_csomagpont_nev="'+allpacka_gls_csomagpont.Name+'" allpacka_gls_csomagpont_cim="'+allpacka_gls_csomagpont_cim+'" allpacka_gls_csomagpont_adatok="'+allpacka_gls_csomagpont_adatok+'"  >'+allpacka_frontend_adatok.kivalasztom+'</div>' +
                            '</div>'+
                        '</div>';
                    
                    var infowindow = new google.maps.InfoWindow({
                      content: tartalom
                    });
                    
                    marker.addListener('click', function() {
 
                        if (currentInfoWindow != null) {
                            currentInfoWindow.close();
                        } 
        
                      infowindow.open(map, marker);
                  
                      currentInfoWindow = infowindow; 
                    });

                });

                var markerCluster = new MarkerClusterer( map, markers, markerClusterOptions );

                if(!bounds.isEmpty()) {
                    map.fitBounds(bounds);
                }

            }
            
        });

    }
    
    /******************************************************************************************************************************************************************/
    /******************************************************************************************************************************************************************/
     
    //jQuery( '.allpacka_gls_csomagpont_kivalasztas' ).live("click", function() { 
    jQuery( document ).on( 'click', '.allpacka_gls_csomagpont_kivalasztas', function(  ){
        
        jQuery('#allpacka_kivalasztott_gls_csomagpont').val( jQuery( this ).attr( 'allpacka_gls_csomagpont_adatok' ) );
        
        jQuery('.allpacka_gls_csomagpont_kivalasztott_kontener_adatok').remove();

        jQuery( '<div class="allpacka_gls_csomagpont_kivalasztott_kontener_adatok">'+jQuery( this ).attr( 'allpacka_gls_csomagpont_nev' )+'<br/>'+jQuery( this ).attr( 'allpacka_gls_csomagpont_cim' )+'</div>' ).insertAfter( '#allpacka_kivalasztott_gls_csomagpont' );

    });
    
    /******************************************************************************************************************************************************************/
    /******************************************************************************************************************************************************************/
    
    if ( jQuery( 'body' ).hasClass( "woocommerce-cart" ) ) {
        
        allpacka_gls_csomagpont_szallitas_kalkulator_eltuntetese();
        
    }

    
    jQuery( 'body' ).bind( 'updated_cart_totals', function() {
        
        allpacka_gls_csomagpont_szallitas_kalkulator_eltuntetese();
        
    });
    
    /******************************************************************************************************************************************************************/
    /******************************************************************************************************************************************************************/
    
    function allpacka_gls_csomagpont_szallitas_kalkulator_eltuntetese(){
        
        jQuery.ajax({
            url: allpacka_frontend_adatok.ajax_url,
            data: {
                'action':'allpacka_ajax_szallitasi_mod_ellenorzese'
            },
            success:function( allpacka_ajax_szallitasi_mod_ellenorzese_return ) {

                szallitasi_mod = jQuery.parseJSON( allpacka_ajax_szallitasi_mod_ellenorzese_return );

                if( szallitasi_mod['nev']  == 'allpacka_gls_csomagpont' ){
  
                    if ( !jQuery( '.woocommerce-shipping-calculator' ).hasClass( "allpacka_gls_csomagpont_szallitas_kalkulator_eltuntetese" ) ) {
                    
                        jQuery( '.woocommerce-shipping-calculator' ).addClass( 'allpacka_gls_csomagpont_szallitas_kalkulator_eltuntetese' );
						jQuery( '.woocommerce-shipping-destination' ).addClass( 'allpacka_gls_csomagpont_szallitas_kalkulator_eltuntetese' );
                        
                        jQuery( '<span="allpacka_gls_csomagpont_szallitas_kalkulator_uzenet">'+allpacka_frontend_adatok.kosar_csomagpont_valasztas+'</span>' ).insertAfter( '.woocommerce-shipping-calculator' );
                        
                    
                    }
                    
                }
                else{
                    
                    if ( jQuery( '.woocommerce-shipping-calculator' ).hasClass( "allpacka_gls_csomagpont_szallitas_kalkulator_eltuntetese" ) ) {

                        jQuery( '.woocommerce-shipping-calculator' ).removeClass( 'allpacka_gls_csomagpont_szallitas_kalkulator_eltuntetese' );
                        jQuery( '.woocommerce-shipping-destination' ).removeClass( 'allpacka_gls_csomagpont_szallitas_kalkulator_eltuntetese' );
						
                        jQuery( '.allpacka_gls_csomagpont_szallitas_kalkulator_uzenet' ).remove();
                    
                    }
                    
                }
   
            }
            
        });
        
    }
    
    /******************************************************************************************************************************************************************/
    /******************************************************************************************************************************************************************/
    
    

});









