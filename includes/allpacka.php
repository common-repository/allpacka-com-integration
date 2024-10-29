<?php

register_activation_hook( __FILE__, 'allpacka_mappa' );

function allpacka_mappa(){
    
    $upload_dir = wp_upload_dir();

    if (!is_dir( $upload_dir['basedir'].'/allpacka' )) {
        
        mkdir( $upload_dir['basedir'].'/allpacka', 0777, true );
        
    }
    
}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

add_filter('manage_edit-shop_order_columns' , 'allpacka_order_column_title', 10);
 
function allpacka_order_column_title( $columns ) {

    if(  get_option( 'allpacka_allapot' ) == 'yes' ){

        $columns['allpacka'] = '<span><img src="'.ALLPACKA_PLUGIN_DIR_URL.'/images/pactic_ikon_2.png"></span>';  
    
    }
    
    return $columns;
    
}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

add_action('manage_shop_order_posts_custom_column' , 'allpacka_order_column', 10, 2 );
 
function allpacka_order_column( $column_name, $order_id ) {
    
    if(  get_option( 'allpacka_allapot' ) == 'yes' ){
    
        switch ( $column_name ) {
            case 'allpacka' :
            
                allpacka_gombok( $order_id );

                break;
        }
    
    }
    
}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

function allpacka_gombok( $order_id, $string = false ){
    
    $kimenet = '';
    
    $upload_dir = wp_upload_dir();
                
    $allpacka_azonosito = get_post_meta( $order_id, '_allpacka_azonosito', true );
    $allpacka_fuvarlevelszam = get_post_meta( $order_id, '_allpacka_fuvarlevelszam', true );
    
    $kimenet .= '<div class="allpacka_megrendeles_gomb_box_'.$order_id.'">';
    
    if( empty( $allpacka_azonosito ) && empty( $allpacka_fuvarlevelszam ) ){

        $kimenet .= '<a class="button allpacka_megrendeles_gomb" order_id="'.$order_id.'" >'.__( 'Place order', 'allpacka' ).'</a>';
         
    }
    else if( !empty( $allpacka_azonosito ) && empty( $allpacka_fuvarlevelszam ) ){

        $kimenet .= '<a class="button allpacka_megrendeles_gomb allpacka_ujrarendeles_gomb" order_id="'.$order_id.'" >'.__( 'Reorder', 'allpacka' ).'</a>';

        $kimenet .= '<p><strong>'.__( 'Order ID:', 'allpacka' ).'</strong> '.$allpacka_azonosito.'</p>';
        
        $kimenet .= '<p><br/>'.__( 'The waybill will be sent to you by the Pactic Customer Service.', 'allpacka' ).'</p>';

    }
    else if( !empty( $allpacka_azonosito ) && !empty( $allpacka_fuvarlevelszam ) ){
        
        $kimenet .= '<a class="button allpacka_megrendeles_gomb allpacka_ujrarendeles_gomb" order_id="'.$order_id.'" >'.__( 'Reorder', 'allpacka' ).'</a>';
    
     
        $kimenet .= '<p><strong>'.__( 'Order ID:', 'allpacka' ).'</strong> '.$allpacka_azonosito.'</p>';
        
        $kimenet .= '<p>';
            
            $kimenet .= '<span><strong>'.__( 'Waybill number:', 'allpacka' ).'</strong> '.$allpacka_fuvarlevelszam.'</span>';
                       
            if( file_exists( $upload_dir['basedir'].'/allpacka/waybill_'.$order_id.'_'.$allpacka_fuvarlevelszam.'.pdf' ) ){
                
                $kimenet .= '<a target="_blank" class="button button-secondary allpacka_gomb allpacka_fuvarlevel_gomb" title="'.__( 'View', 'allpacka' ).'" href="'.$upload_dir['baseurl'].'/allpacka/waybill_'.$order_id.'_'.$allpacka_fuvarlevelszam.'.pdf'.'"></a>';
            
            }
            
            if( $csomag_statusz = allpacka_csomag_statusz( $allpacka_fuvarlevelszam ) ){

                $kimenet .= '<p><strong>'.__( 'Package status:', 'allpacka' ).'</strong> <a href="'.get_edit_post_link( $order_id ).'#allpacka_tracking_meta_box" title="'.$csomag_statusz['leiras'].'">'.$csomag_statusz['nev'].'</a></p>';
                
            
            }
            else if( $nyomkovetes_url = allpacka_nyomkovetes_url( $allpacka_fuvarlevelszam ) ){
                
                $kimenet .= '<a target="_blank" class="button button-secondary allpacka_gomb allpacka_nyomkovetes_gomb" title="'.__( 'Tracking', 'allpacka' ).'" href="'.$nyomkovetes_url.'"></a>';
           
            }
        
        $kimenet .= '</p>';

    }
    
    $kimenet .= '</div>';
    
    if( $string == true ){
        return $kimenet;
    }
    else{
        echo $kimenet;
    }

}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

function allpacka_nyomkovetes_url( $allpacka_fuvarlevelszam ){
    
    if( strlen( $allpacka_fuvarlevelszam ) == 11 && substr( $allpacka_fuvarlevelszam, 0, 3) == '002' ){
        
        $nyelv_kod = allpacka_nyelv();
        
        if( $nyelv_kod == 'RO' ){
            return 'https://gls-group.eu/RO/ro/urmarire-colet?match='.$allpacka_fuvarlevelszam;
        }
        else{
            return 'https://gls-group.eu/HU/en/parcel-tracking?match='.$allpacka_fuvarlevelszam;
        }
        
        
    }
    else if( strlen( $allpacka_fuvarlevelszam ) == 14 && substr( $allpacka_fuvarlevelszam, 0, 3) == '164' ){
        
        $nyelv_kod = allpacka_nyelv();
        
        if( $nyelv_kod == 'RO' ){
            return 'https://tracking.dpd.de/parcelstatus?query='.$allpacka_fuvarlevelszam.'&locale=ro_RO';
        }
        else{
            return 'https://tracking.dpd.de/parcelstatus?query='.$allpacka_fuvarlevelszam.'&locale=en_HU';
        }
        
        
    }
    else{
        return false;
    }
    
}


/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

add_action("add_meta_boxes", "allpacka_order_meta_box");

function allpacka_order_meta_box(){
    
    if(  get_option( 'allpacka_allapot' ) == 'yes' ){

        add_meta_box("allpacka_meta_box", '<div class="allpacka_order_meta_box_fejlec"><img src="'.ALLPACKA_PLUGIN_DIR_URL.'/images/pactic_ikon_2.png" /></div>', 'allpacka_order_meta_box_content', 'shop_order', 'side', 'high', null);
        
        add_meta_box("allpacka_tracking_meta_box", '<div class="allpacka_order_meta_box_fejlec"><img src="'.ALLPACKA_PLUGIN_DIR_URL.'/images/pactic_ikon_2.png" /><span>'.__( 'Tracking', 'allpacka' ).'</span></div>', 'allpacka_order_tracking_meta_box_content', 'shop_order', 'side', 'high', null);

    }
    
}


/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

function allpacka_order_meta_box_content( $order ){
    
    if(  get_option( 'allpacka_allapot' ) == 'yes' ){

        if( !empty( $order->ID ) ){

            allpacka_gombok( $order->ID );
            
        }
    
    }
    
}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/


function allpacka_order_tracking_meta_box_content( $order ){
    
    if(  get_option( 'allpacka_allapot' ) == 'yes' ){

        if( !empty( $order->ID ) ){
            
            $allpacka_fuvarlevelszam = get_post_meta( $order->ID, '_allpacka_fuvarlevelszam', true );

            $csomag_statuszok = allpacka_csomag_statusz( $allpacka_fuvarlevelszam, true );
            
            if( $csomag_statuszok ){
                
                echo '<div class="allpacka_tracking_tablazat">';
                
                    foreach( $csomag_statuszok as $csomag_statusz ){
                        
                        echo '<div class="allpacka_tracking_tablazat_sor">';
                        
                            echo '<div class="allpacka_tracking_tablazat_sor_cella allpacka_tracking_tablazat_sor_cella_datum">';

                                echo implode("<br/>", explode(" ", $csomag_statusz['datum'] ) );
                            
                            echo '</div>';
                            
                            echo '<div class="allpacka_tracking_tablazat_sor_cella allpacka_tracking_tablazat_sor_cella_nev" title="'.$csomag_statusz['leiras'].'">';
                        
                                echo $csomag_statusz['nev'].'<br/>';
                            
                            echo '</div>';
                            
                            echo '<div class="allpacka_tracking_tablazat_sor_cella allpacka_tracking_tablazat_sor_cella_ikon">';
                            
                                if( file_exists( ALLPACKA_PLUGIN_DIR_PATH.'/images/csomag_statusz_'.$csomag_statusz['id'].'.png' ) ){
                                    
                                    echo '<img src="'.ALLPACKA_PLUGIN_DIR_URL.'/images/csomag_statusz_'.$csomag_statusz['id'].'.png" title="'.$csomag_statusz['leiras'].'" />';
                                    
                                }
                                
                            echo '</div>';
                        
                        echo '</div>';
                        
                    }
                    
                echo '</div>';
                
            }
            
        }
    
    }
    
}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

function allpacka_konvertalas( $string ){
    
    if( $string == 'yes'  ){
        return 'true';
    }
    else{
        return 'false';
    }
    
}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/


function allpacka_nyelv(){
    
    $locale = get_locale();
    
    if( $locale && substr( $locale, 0, 2 ) == 'ro' ){
        return 'RO';
    }
    else if( $locale && substr( $locale, 0, 2 ) == 'en' ){
        return 'EN';
    }
    else{
        return 'EN';
    }
    
}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

add_filter( 'woocommerce_settings_tabs_array', 'allpacka_add_settings_tab', 50 );

function allpacka_add_settings_tab( $settings_tabs ) {
    
    $settings_tabs['allpacka'] = 'Pactic';
    
    return $settings_tabs;
    
}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

add_action( 'woocommerce_settings_tabs_allpacka', 'allpacka_settings_tab' );

function allpacka_settings_tab() {
    
    echo '<table class="form-table">';
    
        woocommerce_admin_fields( allpacka_get_settings() );
        
    echo '</table>';
    
}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'allpacka_action_links' );

function allpacka_action_links( $links ) {
    
   $links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=wc-settings&tab=allpacka') ) .'">'.__( 'Settings', 'allpacka' ).'</a>';

   return $links;
   
}


/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

add_action( 'woocommerce_update_options_allpacka', 'allpacka_update_settings' );

function allpacka_update_settings() {
    
    woocommerce_update_options( allpacka_get_settings() );
    
}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

function allpacka_get_settings() {

    $settings = array(
        'allpacka_section_1' => array(
            'name'     => __( 'General settings', 'allpacka' ),
            'type'     => 'title',
        ),
        'allpacka_allapot' => array(
            'title'   => __( 'Enable / Disable', 'allpacka' ),
            'type'    => 'checkbox',
            'default' => 'no',
            'desc' => __( 'Enable Pactic', 'allpacka' ),
            'desc_tip'    => true,
            'id'      => 'allpacka_allapot'
        ),
        'allpacka_tesztkornyezet' => array(
            'title'       => __( 'Test environment', 'allpacka' ),
            'type'        => 'checkbox',
            'default'     => 'yes',
            'desc' => __( 'Use test environment', 'allpacka' ),
            'desc_tip'    => true,
            'id'      => 'allpacka_tesztkornyezet'
        ),
        'allpacka_debug' => array(
            'title'       => __( 'Debug mode', 'allpacka' ),
            'type'        => 'checkbox',
            'default'     => 'no',
            'desc' => __( 'Show request sent to and response received from Pactic.', 'allpacka' ),
            'desc_tip'    => true,
            'id'      => 'allpacka_debug'
        ),
        'allpacka_fizetesi_modok' => array(
            'title'       => __( 'Additional payment methods', 'allpacka' ),
            'type'        => 'multiselect',
            //'default'     => 'cod',
            'options'     => allpacka_engedelyezett_fizetesi_modok(),
            'desc'        => __( 'The plug-in automatically chooses the default payment method:', 'allpacka' ).' <strong>'.allpacka_engedelyezett_fizetesi_modok( 'cod' ).'</strong>.'.
                             '<br/>'.__( 'Additional payment methods for COD in your webshop. Set up here.', 'allpacka' ),
            'id'      => 'allpacka_fizetesi_modok',
        ),
        'allpacka_section_1_end' => array(
             'type' => 'sectionend'
        ),
        
        
        'allpacka_section_2' => array(
            'name'     => __( 'Pactic account details', 'allpacka' ),
            'type'     => 'title',
            'desc'     => '',
        ),
        'allpacka_email' => array(
            'name' => __( 'Email address', 'allpacka' ),
            'type' => 'text',
            'desc' => __( 'Email address used to register on Pactic', 'allpacka' ),
            'desc_tip'    => true,
            'id'   => 'allpacka_email',
            'placeholder' => __( 'something@something.com', 'allpacka' ),
            'class' => 'input_width_30'
        ),
        'allpacka_jelszo' => array(
            'name' => __( 'Password', 'allpacka' ),
            'type' => 'text',
            'desc' => __( 'Password on Pactic', 'allpacka' ),
            'desc_tip'    => true,
            'id'   => 'allpacka_jelszo',
            'class' => 'input_width_30'
        ),
        'allpacka_section_2_end' => array(
             'type' => 'sectionend'
        ),
        
        
        'allpacka_section_3' => array(
            'name'     => __( 'Pactic terms and conditions', 'allpacka' ),
            'type'     => 'title',
            'desc'     => '',
        ),
        'allpacka_tiltott_aru' => array(
            'title'   => __( 'I declare that my package does not contain any dangerous and/or forbidden item.', 'allpacka' ),
            'type'    => 'checkbox',
            'default' => 'no',
            'desc' => __( 'List of dangerous and/or forbidden items can be found on Pactic:', 'allpacka' ).'<a target="_blank" href="https://allpacka.com/faq/prohibited-items/">https://allpacka.com/faq/prohibited-items/</a>',
            'desc_tip'    => true,
            'id'      => 'allpacka_tiltott_aru'
        ),
        'allpacka_aszf' => array(
            'title'   => __( "I've read and understood the Terms and Conditions of Pactic.", 'allpacka' ),
            'type'    => 'checkbox',
            'default' => 'no',
            'desc' => __( 'Terms and Conditions can be found on Pactic:', 'allpacka' ).' <a target="_blank" href="https://allpacka.com/terms-conditions-tc/">https://allpacka.com/terms-conditions-tc/</a>',
            'desc_tip'    => true,
            'id'      => 'allpacka_aszf'
        ),
        'allpacka_section_3_end' => array(
             'type' => 'sectionend'
        ),

        'allpacka_section_4' => array(
            'name'     => __( 'Collection details', 'allpacka' ),
            'type'     => 'title',
            'desc'     => __( 'If you already specified this default data on Pactic, it is not mandatory to provide them here.', 'allpacka' ),
        ),
        'allpacka_felado_neve' => array(
            'name' => __( "Sender's name", 'allpacka' ),
            'type' => 'text',
            'id'   => 'allpacka_felado_neve',
            'placeholder' => __( 'John Doe', 'allpacka' ),
            'class' => 'input_width_30'
        ),
        'allpacka_felado_bank_neve' => array(
            'name' => __( "Name of Sender's bank", 'allpacka' ),
            'type' => 'text',
            'id'   => 'allpacka_felado_bank_neve',
            'placeholder' => __( 'UniCredit', 'allpacka' ),
            'class' => 'input_width_30'
        ),
        'allpacka_felado_bankszamlaszama' => array(
            'name' => __( 'Bank account number of sender', 'allpacka' ),
            'type' => 'text',
            'id'   => 'allpacka_felado_bankszamlaszama',
            'placeholder' => __( 'XX1234567890 or 12345678-12345678-12345678', 'allpacka' ),
            'class' => 'input_width_30'
        ),
        'allpacka_section_4_end' => array(
             'type' => 'sectionend'
        ),
        
        'allpacka_section_5' => array(
            'name'     => __( 'Waybills', 'allpacka' ),
            'type'     => 'title',
            'desc'     => __( ' If you want to delete the PDF waybills you have generated, click here!', 'allpacka' ).'<br/><p><a class="button allpacka_waybills_torles_gomb">'.__( 'Waybills cancellation', 'allpacka' ).'</a></p>',
        ),
        'allpacka_section_5_end' => array(
             'type' => 'sectionend'
        ),
        
        
        
    );


    return apply_filters( 'allpacka_settings', $settings );
}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

function allpacka_ajax_megrendeles() {
    
    if ( isset($_REQUEST) ) {
        
        $order_id = $_REQUEST['order_id'];
        
        $allpacka_megrendeles_return = allpacka_megrendeles( $order_id );
               
        echo $allpacka_megrendeles_return;
        
   
    }
    
    die();
}

add_action( 'wp_ajax_allpacka_ajax_megrendeles', 'allpacka_ajax_megrendeles' );

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

function allpacka_megrendeles_torlese( $order_id ) {
    
    $upload_dir = wp_upload_dir();
                    
    $allpacka_azonosito = get_post_meta( $order_id, '_allpacka_azonosito', true );
    $allpacka_fuvarlevelszam = get_post_meta( $order_id, '_allpacka_fuvarlevelszam', true );  
    
    if( $allpacka_azonosito ){
    
        update_post_meta( $order_id, '_allpacka_azonosito', '' );
        
    }
    
    if( $allpacka_fuvarlevelszam ){
        
        update_post_meta( $order_id, '_allpacka_fuvarlevelszam', '' );
        
        $fajlnev_dir = $upload_dir['basedir'].'/allpacka/waybill_'.$order_id.'_'.$allpacka_fuvarlevelszam.'.pdf';
    
        if( file_exists( $fajlnev_dir ) ){
            
            unlink( $fajlnev_dir );
    
        }
        
    }
    
 
}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

function allpacka_megrendeles( $order_id ) {
    
    allpacka_megrendeles_torlese( $order_id );
    
    $order = wc_get_order( $order_id );

    $company = method_exists( $order, 'get_shipping_company' ) ? $order->get_shipping_company() : $order->shipping_company;
    $contact_first_name = method_exists( $order, 'get_shipping_first_name' ) ? $order->get_shipping_first_name() : $order->shipping_first_name;
    $contact_last_name = method_exists( $order, 'get_shipping_last_name' ) ? $order->get_shipping_last_name() : $order->shipping_last_name;
    
    if( !empty( $company ) ){
        $nmCompanyOrPerson = $company;
        $nmContact = $contact_last_name.' '.$contact_first_name;
    }
    else{
        $nmCompanyOrPerson = $contact_last_name.' '.$contact_first_name;
        $nmContact = $contact_last_name.' '.$contact_first_name;
    }
    
    $allpacka_kivalasztott_gls_csomagpont_json = get_post_meta( $order_id, '_allpacka_kivalasztott_gls_csomagpont', true );

    if( !empty( $allpacka_kivalasztott_gls_csomagpont_json ) ) {
      
        $allpacka_kivalasztott_gls_csomagpont = json_decode( str_replace( "'", '"', $allpacka_kivalasztott_gls_csomagpont_json ), true );
      
        $cdDropOffPoint = (string)$allpacka_kivalasztott_gls_csomagpont['Code'];
        
        $DESTINATIONADDRESS = array(
            'nmCompanyOrPerson' => $nmCompanyOrPerson,
            'nmContact' => $nmContact,
            'cdCountry' => method_exists( $order, 'get_shipping_country' ) ? $order->get_shipping_country() : $order->shipping_country,
            'cdDropOffPoint' => $cdDropOffPoint,
            'txAddress' => '',
            'txAddressNumber' => '.',
            'txPost' => '',
            'txCity' => '',
            'txPhoneContact' => method_exists( $order, 'get_billing_phone' ) ? $order->get_billing_phone() : $order->billing_phone,
            'txEmailContact' => method_exists( $order, 'get_billing_email' ) ? $order->get_billing_email() : $order->billing_email,
            'txInstruction' => (string)substr( ( method_exists( $order, 'get_customer_note' ) ? $order->get_customer_note() : $order->customer_note ), 0, 63 )
        );
          
    }
    else{
        
        $DESTINATIONADDRESS = array(
            'nmCompanyOrPerson' => $nmCompanyOrPerson,
            'nmContact' => $nmContact,
            'cdCountry' => method_exists( $order, 'get_shipping_country' ) ? $order->get_shipping_country() : $order->shipping_country,
            'txAddress' => method_exists( $order, 'get_shipping_address_1' ) ? $order->get_shipping_address_1() : $order->shipping_address_1,
            'txAddressNumber' => '.',
            'txPost' => method_exists( $order, 'get_shipping_postcode' ) ? $order->get_shipping_postcode() : $order->shipping_postcode,
            'txCity' => method_exists( $order, 'get_shipping_city' ) ? $order->get_shipping_city() : $order->shipping_city,
            'txPhoneContact' => method_exists( $order, 'get_billing_phone' ) ? $order->get_billing_phone() : $order->billing_phone,
            'txEmailContact' => method_exists( $order, 'get_billing_email' ) ? $order->get_billing_email() : $order->billing_email,
            'txInstruction' => (string)substr( ( method_exists( $order, 'get_customer_note' ) ? $order->get_customer_note() : $order->customer_note ), 0, 63 )
        );
        
    }

    $payment_method = method_exists( $order, 'get_payment_method' ) ? $order->get_payment_method() : $order->payment_method;
    
    if ( $payment_method == 'cod' || in_array( $payment_method, get_option( 'allpacka_fizetesi_modok' ) ) ) {				
        
        $PACKAGE = array(
            array(
                'idOrder' => (string)$order_id,
                'ctPackage' => '1',
                'amContent' => (string)$order->get_total(),
            )
        );
        
        $cod = 'true';
    
    }
    else{
        
        $PACKAGE = array(
            array(
                'idOrder' => (string)$order_id,
                'ctPackage' => '1',
            )
        );
        
        $cod = 'false';
        
    }


    $PACKAGES = array(
        'PACKAGE' => $PACKAGE
    );
    
    if( get_option( 'allpacka_felado_neve' ) != '' && get_option( 'allpacka_felado_bank_neve' ) != '' && get_option( 'allpacka_felado_bankszamlaszama' ) != '' ){
        $ORDER = array(
            'flCOD'  => $cod,
            'nmRecipientCOD'  => get_option( 'allpacka_felado_neve' ),
            'nmBankCOD'  => get_option( 'allpacka_felado_bank_neve' ),
            'txBankAccountNumberCOD'  => get_option( 'allpacka_felado_bankszamlaszama' ),
            'flNothingProhibited'  => allpacka_konvertalas( get_option( 'allpacka_tiltott_aru' ) ),
            'flAgreedToTermsAndConditions'  => allpacka_konvertalas( get_option( 'allpacka_aszf' ) ),
            'DESTINATIONADDRESS' => $DESTINATIONADDRESS,
            "PACKAGES" => $PACKAGES
        );
    }
    else{
        $ORDER = array(
            'flCOD'  => $cod,
            'flNothingProhibited'  => allpacka_konvertalas( get_option( 'allpacka_tiltott_aru' ) ),
            'flAgreedToTermsAndConditions'  => allpacka_konvertalas( get_option( 'allpacka_aszf' ) ),
            'DESTINATIONADDRESS' => $DESTINATIONADDRESS,
            "PACKAGES" => $PACKAGES
        );
    }

    $REQUEST = array(
        'flDebug' => allpacka_konvertalas( get_option( 'allpacka_tesztkornyezet' ) ),
        'cdLang' => allpacka_nyelv(),
        'txEmail' => get_option( 'allpacka_email' ),
        'txPassword' => get_option( 'allpacka_jelszo' ),
        'ORDER' => $ORDER
    );

    
    $JSON = array(
        'REQUEST' => $REQUEST,
    );
    
    $allpacka_megrendeles_return = array();
    
    $url = 'https://api.pactic.com/webservices/webshop2.ashx';
    
    if( extension_loaded( 'curl' ) ){

        $ch = curl_init();
        
        curl_setopt( $ch, CURLOPT_AUTOREFERER, TRUE );
        curl_setopt( $ch, CURLOPT_HEADER, FALSE );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, TRUE );    
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $JSON ) );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );   
        
        $allpacka_megrendeles_json = curl_exec( $ch );
        
        curl_close($ch);
    
    }
    else if( ini_get( 'allow_url_fopen' ) ){

        $options = array(
            'http' => array(
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'method'  => 'POST',
                'content' => json_encode( $JSON ) 
            )
        );

        $context  = stream_context_create( $options );
        
        $allpacka_megrendeles_json = file_get_contents( $url, false, $context );
    
    }
    
    $allpacka_megrendeles = json_decode( $allpacka_megrendeles_json, true);
    
    if( get_option( 'allpacka_debug' ) == 'yes' ){

        $allpacka_megrendeles_return['debug'] = '<strong>'.__( 'Request sent to Pactic:', 'allpacka' ).'</strong><pre>'.json_encode( $JSON, JSON_PRETTY_PRINT ).'</pre><strong>'.__( 'Response received from Pactic:', 'allpacka' ).'</strong><pre>'.json_encode( $allpacka_megrendeles, JSON_PRETTY_PRINT ).'</pre>';
    }
    
	if ( $allpacka_megrendeles_json == null ) {
		$allpacka_megrendeles_return['hiba'][] = '<strong>'.__( 'Error message from Pactic:', 'allpacka' ).'</strong> '.'NULL response';
	}
	
	if ( $allpacka_megrendeles == null ) {
	   
        $allpacka_megrendeles_return['sikertelen'] = '<strong>'.__( 'Unsuccesful Pactic order.', 'allpacka' ).'</strong> '.__( 'Webshop order ID:', 'allpacka' ).' '.$order_id.' '.__( 'No response received from Pactic for the ORDER request.', 'allpacka' ) .' '. __('Password or email address probably wrong.', 'allpacka' );

        $allpacka_megrendeles_return['hiba'][] = '<strong>'.__( 'Error message from Pactic:', 'allpacka' ).'</strong> '.$allpacka_megrendeles_json;

        $order->add_order_note( '<strong>'.__( 'Error message from Pactic:', 'allpacka' ).'</strong> '.__( 'No response received from Pactic for the ORDER request.', 'allpacka' ) );   
        
	}
    else if( $allpacka_megrendeles ){
        
        if( get_option( 'allpacka_tesztkornyezet' ) == 'yes' ){
            
            //TESZT

            if( $allpacka_megrendeles['Messages'] ){
                
                foreach( $allpacka_megrendeles['Messages'] as $message ){
                    
                    if( $message['Text'] != 'TEST: Quote added successfully' ){
                                                
                        $allpacka_megrendeles_return['hiba'][] = '<strong>'.__( 'Error message from Pactic:', 'allpacka' ).'</strong> '.$message['Text'];
                        
                    }
                    
                }
            
            }
            
            if( !empty( $allpacka_megrendeles['Order']['Labels'] ) && !empty( $allpacka_megrendeles['Order']['WayBills'] ) ){
                
                update_post_meta( $order_id, '_allpacka_azonosito', 'teszt_'.$order_id );
                update_post_meta( $order_id, '_allpacka_fuvarlevelszam', $allpacka_megrendeles['Order']['WayBills'][0] );
                
                $minta_fuvarlevel = ALLPACKA_PLUGIN_DIR_URL.'/pdf/minta_fuvarlevel.pdf';
                
                $upload_dir = wp_upload_dir();
                
                $fajlnev_dir = $upload_dir['basedir'].'/allpacka/waybill_'.$order_id.'_'.$allpacka_megrendeles['Order']['WayBills'][0].'.pdf';

                copy( $minta_fuvarlevel, $fajlnev_dir);

                $allpacka_megrendeles_return['sikeres'] = '<strong>'.__( 'Successful Pactic order.', 'allpacka' ).'</strong> '.__( 'Webshop order ID:', 'allpacka' ).' '.$order_id.'. '.__( 'Pactic order ID:', 'allpacka' ).' '.$allpacka_megrendeles['Order']['ID'].'. '.__( 'Waybill number:', 'allpacka' ).' '.$allpacka_megrendeles['Order']['WayBills'][0].'. <em>'.__( 'The waybill contains fake data in test mode!', 'allpacka' ).'</em>';
                
                $order->add_order_note( 
                    sprintf( 
						__( 'Successful Pactic order. Pactic order ID: %s. Waybill number: %s. <em>The waybill contains fake data in test mode!</em>', 'allpacka' ),
                        $allpacka_megrendeles['Order']['ID'], 
                        $allpacka_megrendeles['Order']['WayBills'][0]
                    )
                );
  
                
            }
            else{
                
                $allpacka_megrendeles_return['sikertelen'] = '<strong>'.__( 'Unsuccesful Pactic order.', 'allpacka' ).'</strong> '.__( 'Webshop order ID:', 'allpacka' ).' '.$order_id;
                
                $order->add_order_note( __( 'Unsuccesful Pactic order.', 'allpacka' ) );
                
            }
            
        }
        else{
            
            //ÉLES
            
            if( $allpacka_megrendeles['Messages'] ){
                    
                foreach( $allpacka_megrendeles['Messages'] as $message ){
                    
                    $allpacka_megrendeles_return['hiba'][] = '<strong>'.__( 'Error message from Pactic:', 'allpacka' ).'</strong> '.$message['Text'];

                }
            
            }
            
            // van idQuote, nincs Waybill szám, nincs PDF
			if( $allpacka_megrendeles['Order']['ID'] > 0 && empty( $allpacka_megrendeles['Order']['Labels'] ) && empty( $allpacka_megrendeles['Order']['WayBills'] ) ){
            
                update_post_meta( $order_id, '_allpacka_azonosito', $allpacka_megrendeles['Order']['ID'] );
                update_post_meta( $order_id, '_allpacka_fuvarlevelszam', '' );
                
                $allpacka_megrendeles_return['sikeres'] = '<strong>'.__( 'Successful Pactic order, but a label/waybill was not generated.', 'allpacka' ).'</strong> '.__( 'The Customer Service of Pactic corrects the mistake and sends you an email.', 'allpacka' ).' '.__('Webshop order ID:', 'allpacka' ).' '.$order_id.'. '.__( 'Pactic order ID:', 'allpacka' ).' '.$allpacka_megrendeles['Order']['ID'].'.';                        
 
                $order->add_order_note( __( 'Successful Pactic order, but a label/waybill was not generated. The Customer Service of Pactic corrects the mistake and sends you an email.', 'allpacka' ));
            
            }
			// van idQuote, van Waybill szám, de nincs PDF
            else if( $allpacka_megrendeles['Order']['ID'] > 0 && empty( $allpacka_megrendeles['Order']['Labels'] ) && !empty( $allpacka_megrendeles['Order']['WayBills'] ) ){
            
                update_post_meta( $order_id, '_allpacka_azonosito', $allpacka_megrendeles['Order']['ID'] );
                update_post_meta( $order_id, '_allpacka_fuvarlevelszam', $allpacka_megrendeles['Order']['WayBills'][0] );
                
                $allpacka_megrendeles_return['sikeres'] = '<strong>'.__( 'Successful Pactic order.', 'allpacka' ).'</strong>  '.__( 'Webshop order ID:', 'allpacka' ).' '.$order_id.'. '.
                                   __( 'Pactic order ID:', 'allpacka' ) . ' '.$allpacka_megrendeles['Order']['ID'].'.  '.
                                   __( 'Waybill number:', 'allpacka' ).' '.$allpacka_megrendeles['Order']['WayBills'][0].'.';

                $order->add_order_note( 
                    sprintf( 
							__( 'Successful Pactic order. Pactic order ID: %s. Waybill number: %s.', 'allpacka' ),
                            $allpacka_megrendeles['Order']['ID'], 
                            $allpacka_megrendeles['Order']['WayBills'][0]
                    )
                );
                
            }
			// van idQuote, van Waybill szám, és van PDF is
            else if( !empty( $allpacka_megrendeles['Order']['Labels'] ) && !empty( $allpacka_megrendeles['Order']['WayBills'] ) && $allpacka_megrendeles['Order']['ID'] > 0 ){

                $fuvarlevel = $allpacka_megrendeles['Order']['Labels'][0];
                
                if( !empty( $fuvarlevel ) ){
                    
                    update_post_meta( $order_id, '_allpacka_azonosito', $allpacka_megrendeles['Order']['ID'] );
                    update_post_meta( $order_id, '_allpacka_fuvarlevelszam', $allpacka_megrendeles['Order']['WayBills'][0] );
                    
                    $upload_dir = wp_upload_dir();
                    
                    $fuvarlevel = base64_decode($fuvarlevel);
                    
                    $fajlnev_dir = $upload_dir['basedir'].'/allpacka/waybill_'.$order_id.'_'.$allpacka_megrendeles['Order']['WayBills'][0].'.pdf';

                    file_put_contents( $fajlnev_dir, $fuvarlevel);
                    

                    $allpacka_megrendeles_return['sikeres'] = '<strong>'.__( 'Successful Pactic order.', 'allpacka' ).'</strong> '.__( 'Webshop order ID:', 'allpacka' ).' '.$order_id.'. '.
                                        __( 'Pactic order ID:', 'allpacka' ) . ' '.$allpacka_megrendeles['Order']['ID'].'.  '.
                                        __( 'Waybill number:', 'allpacka' ).' '.$allpacka_megrendeles['Order']['WayBills'][0].'.';

                    $order->add_order_note( 
                        sprintf( 
							__( 'Successful Pactic order. Pactic order ID: %s. Waybill number: %s.', 'allpacka' ),
                            $allpacka_megrendeles['Order']['ID'], 
                            $allpacka_megrendeles['Order']['WayBills'][0]
                        )
                    );
                
                }
                else{
                
                    $allpacka_megrendeles_return['sikertelen'] = '<strong>'.__( 'Unsuccesful Pactic order.', 'allpacka' ).'</strong> '.__( 'Webshop order ID:', 'allpacka' ).' '.$order_id;
                    
                    $order->add_order_note( __( 'Unsuccesful Pactic order.', 'allpacka' ) );
                    
                }
                
            }
            else{
                
                $allpacka_megrendeles_return['sikertelen'] = '<strong>'.__( 'Unsuccesful Pactic order.', 'allpacka' ).'</strong> '.__( 'Webshop order ID:', 'allpacka' ).' '.$order_id;
                
                $order->add_order_note( __( 'Unsuccesful Pactic order.', 'allpacka' ) );
                
            }
            
        }
        
    }

    $allpacka_megrendeles_return['allpacka_gombok'] = allpacka_gombok( $order_id, true );
           
    return json_encode( $allpacka_megrendeles_return );

} 
 
/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

add_filter( 'bulk_actions-edit-shop_order', 'allpacka_bulk_actions' );
 
function allpacka_bulk_actions( $bulk_array ) {
    
	$bulk_array['allpacka_megrendeles'] = __( 'Generate label via Pactic', 'allpacka' );
    $bulk_array['allpacka_megrendeles_fuvarlevel_letoltes'] = __( 'Generate and download labels via Pactic', 'allpacka' );
	$bulk_array['allpacka_fuvarlevel_letoltes'] = __( 'Download labels generated via Pactic', 'allpacka' );
    
	return $bulk_array;

}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

add_filter( 'handle_bulk_actions-edit-shop_order', 'allpacka_bulk_action_handler', 10, 3 );
 
function allpacka_bulk_action_handler( $redirect, $doaction, $order_ids ) {
 
	$redirect = remove_query_arg( array( 'allpacka_megrendeles_kesz', 'allpacka_fuvarlevel_letoltes_kesz', 'allpacka_megrendeles_fuvarlevel_letoltes_kesz' ), $redirect );

	if ( $doaction == 'allpacka_megrendeles' ) {

        $allpacka_tomeges_megrendeles_return = allpacka_tomeges_megrendeles( $order_ids );

		if( !session_id() ){
            session_start();
        }
        
        $_SESSION['allpacka_tomeges_megrendeles_return'] = $allpacka_tomeges_megrendeles_return;

        $redirect = add_query_arg(
			'allpacka_megrendeles_kesz', 
			1,
		$redirect );
        
 
	}
    
    if ( $doaction == 'allpacka_fuvarlevel_letoltes' ) {
        
        $allpacka_fuvarlevel_letoltes_return = allpacka_fuvarlevel_letoltes( $order_ids );
        
        if( !session_id() ){
            session_start();
        }
        
        $_SESSION['allpacka_fuvarlevel_letoltes_return'] = $allpacka_fuvarlevel_letoltes_return ;
        
        $redirect = add_query_arg(
			'allpacka_fuvarlevel_letoltes_kesz', 
			1,
		$redirect );
        
	}
    
    if ( $doaction == 'allpacka_megrendeles_fuvarlevel_letoltes' ) {
        
        $allpacka_tomeges_megrendeles_return = allpacka_tomeges_megrendeles( $order_ids );
        $allpacka_fuvarlevel_letoltes_return = allpacka_fuvarlevel_letoltes( $order_ids );

		if( !session_id() ){
            session_start();
        }
        
        $_SESSION['allpacka_tomeges_megrendeles_return'] = $allpacka_tomeges_megrendeles_return;
        
        $_SESSION['allpacka_fuvarlevel_letoltes_return'] = $allpacka_fuvarlevel_letoltes_return;
        
        $redirect = add_query_arg(
			'allpacka_megrendeles_fuvarlevel_letoltes_kesz', 
			1,
		$redirect );
        
    }
    
    

 
	return $redirect;
 
}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

add_action( 'admin_notices', 'allpacka_bulk_action_notices' );
 
function allpacka_bulk_action_notices() {
 
	if( !empty( $_REQUEST['allpacka_megrendeles_kesz'] ) || !empty( $_REQUEST['allpacka_megrendeles_fuvarlevel_letoltes_kesz'] ) ) {
        
        if( !session_id() ){
            session_start();
        }
        
        if( isset( $_SESSION['allpacka_tomeges_megrendeles_return'] )  ){
            
            foreach( json_decode( $_SESSION['allpacka_tomeges_megrendeles_return'], true ) as $order_id => $megrendeles_adatok ){
            
                if( isset( $megrendeles_adatok['sikeres'] ) ){
                    
                    $uzenet = $megrendeles_adatok['sikeres'];
                    
                    if( isset( $megrendeles_adatok['hiba'] ) ){
                        
                        $uzenet .= '<br/>';
                        
                        krsort( $megrendeles_adatok['hiba'] );
                        
                        foreach( $megrendeles_adatok['hiba'] as $hiba_id => $hiba_tartalom ){
 
                            $uzenet .= '<br/>'.$hiba_tartalom;
                            
                        }
  
                    }
                    
                    if( isset( $megrendeles_adatok['debug'] ) ){
                        
                        $uzenet .= '<br/><br/>';
                        
                        $uzenet .= $megrendeles_adatok['debug'];
                        
                    }
                    
                    echo '<div class="updated notice is-dismissible allpacka_notice"><div><img src="'.ALLPACKA_PLUGIN_DIR_URL.'images/pactic_ikon_3.png" />'.$uzenet.'</div></div>';
   
                }
                else if( isset( $megrendeles_adatok['sikertelen'] ) ){
                    
                    $uzenet = $megrendeles_adatok['sikertelen'];
                    
                    if( isset( $megrendeles_adatok['hiba'] ) ){
                        
                        $uzenet .= '<br/>';
                        
                        krsort( $megrendeles_adatok['hiba'] );
                        
                        foreach( $megrendeles_adatok['hiba'] as $hiba_id => $hiba_tartalom ){
 
                            $uzenet .= '<br/>'.$hiba_tartalom;
                            
                        }
  
                    }
                    
                    if( isset( $megrendeles_adatok['debug'] ) ){
                        
                        $uzenet .= '<br/><br/>';
                        
                        $uzenet .= $megrendeles_adatok['debug'];
                        
                    }
                    
                    echo '<div class="error notice is-dismissible allpacka_notice"><div><img src="'.ALLPACKA_PLUGIN_DIR_URL.'images/pactic_ikon_3.png" />'.$uzenet.'</div></div>';
   
                }
                else{
                    
                    $uzenet = '';
                    
                    if( isset( $megrendeles_adatok['hiba'] ) ){
                        
                        $uzenet .= '<br/>';
                        
                        krsort( $megrendeles_adatok['hiba'] );
                        
                        foreach( $megrendeles_adatok['hiba'] as $hiba_id => $hiba_tartalom ){
 
                            $uzenet .= '<br/>'.$hiba_tartalom;
                            
                        }
  
                    }
                    
                    if( isset( $megrendeles_adatok['debug'] ) ){
                        
                        $uzenet .= '<br/><br/>';
                        
                        $uzenet .= $megrendeles_adatok['debug'];
                        
                    }
                    
                    echo '<div class="error notice is-dismissible allpacka_notice"><div><img src="'.ALLPACKA_PLUGIN_DIR_URL.'images/pactic_ikon_3.png" />'.$uzenet.'</div></div>';
   
                }
                
            }

            unset( $_SESSION['allpacka_tomeges_megrendeles_return'] );
                        
        }
 
	}
    
    if( !empty( $_REQUEST['allpacka_fuvarlevel_letoltes_kesz'] ) || !empty( $_REQUEST['allpacka_megrendeles_fuvarlevel_letoltes_kesz'] ) ) {
        
        if( !session_id() ){
            session_start();
        }
        
        if( isset( $_SESSION['allpacka_fuvarlevel_letoltes_return'] ) ){
            
            if( isset( $_SESSION['allpacka_fuvarlevel_letoltes_return']['sikeres'] ) ){
                
                echo '<div class="updated notice is-dismissible allpacka_notice"><div><img src="'.ALLPACKA_PLUGIN_DIR_URL.'images/pactic_ikon_3.png" />'.$_SESSION['allpacka_fuvarlevel_letoltes_return']['sikeres'].'</div></div>';

            }
            else if( isset( $_SESSION['allpacka_fuvarlevel_letoltes_return']['sikertelen'] ) ){
            
                echo '<div class="error notice is-dismissible allpacka_notice"><div><img src="'.ALLPACKA_PLUGIN_DIR_URL.'images/pactic_ikon_3.png" />'.$_SESSION['allpacka_fuvarlevel_letoltes_return']['sikertelen'].'</div></div>';

            }
            
            
        }
        
        unset( $_SESSION['allpacka_fuvarlevel_letoltes_return'] );
        
    }
    
    
     
}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

function allpacka_tomeges_megrendeles( $order_ids ) {
    
    $allpacka_megrendeles_return = array();
    
    foreach ( $order_ids as $order_id ) {
        
        $allpacka_megrendeles_return[$order_id] = json_decode( allpacka_megrendeles( $order_id ) );
        
    }    
    
    return json_encode( $allpacka_megrendeles_return );

}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

function allpacka_fuvarlevel_letoltes( $order_ids ) {
    
    $allpacka_fuvarlevel_letoltes_return = array();
    
    if( !empty( $order_ids ) ){
        
        require_once( 'tcpdf/tcpdf.php' );
        require_once( 'FPDI/fpdi.php' );
    	require_once( 'mergepdf.php' );
        
        $upload_dir = wp_upload_dir();
        
        $pdf_fajlok = array();
        
        $letezo_fuvarlevel = array();
        $nem_letezo_fuvarlevel = array();
        
    
        foreach ( $order_ids as $order_id ) {
    		
            $allpacka_fuvarlevelszam = get_post_meta( $order_id, '_allpacka_fuvarlevelszam', true );
            
            if( !empty( $allpacka_fuvarlevelszam ) ){
                       
                if( file_exists( $upload_dir['basedir'].'/allpacka/waybill_'.$order_id.'_'.$allpacka_fuvarlevelszam.'.pdf' ) ){
                    
                    $letezo_fuvarlevel[$order_id] = $order_id;
    
                    $pdf_fajlok[$order_id] = $upload_dir['basedir'].'/allpacka/waybill_'.$order_id.'_'.$allpacka_fuvarlevelszam.'.pdf';
                    
                }
                else{
                    
                    $nem_letezo_fuvarlevel[$order_id] = $order_id;
                    
                }
            
            }
            else{
                    
                $nem_letezo_fuvarlevel[$order_id] = $order_id;
                
            }
            
    	}

        $fajlnev = 'waybills_'.strtotime( date( 'Y-m-d H:i:s' ) ).'.pdf';
            
        $fajlnev_dir = $upload_dir['basedir'].'/allpacka/'.$fajlnev;
        $fajlnev_url = $upload_dir['baseurl'].'/allpacka/'.$fajlnev;
        
        if( file_exists( $fajlnev_dir ) ){
            
            unlink( $fajlnev_dir );
            
        }
    
        if( !empty( $pdf_fajlok ) ){
 
            AP_MergePdf::merge( $pdf_fajlok, 'F', $fajlnev_dir );
            
            if( file_exists( $fajlnev_dir ) ){
    
                if( !empty( $nem_letezo_fuvarlevel ) ){
                    
                    $allpacka_fuvarlevel_letoltes_return['sikeres'] = 
                        '<strong>'.__( 'Label generated successfully via Pactic.', 'allpacka' ).'</strong> '.
                        '<a class="button fuvarlevel_letoltes" href="'.$fajlnev_url.'" download="'.$fajlnev.'" >'.__( 'Download label', 'allpacka' ).'</a><br/><br/>'.
                        '<strong>'.__( 'Contains the labels of the following orders:', 'allpacka' ).'</strong> '.implode(', ', $letezo_fuvarlevel).'<br/><br/>'.
                        '<strong>'.__( 'Does not contain the labels of the following orders:', 'allpacka' ).'</strong> '.implode(', ', $nem_letezo_fuvarlevel);
                }
                else{
                    
                    $allpacka_fuvarlevel_letoltes_return['sikeres'] = 
                        '<strong>'.__( 'Label generated successfully via Pactic.', 'allpacka' ).'</strong> '.
                        '<a class="button fuvarlevel_letoltes" href="'.$fajlnev_url.'" download="'.$fajlnev.'" >'.__( 'Download label', 'allpacka' ).'</a><br/><br/>'.
                        '<strong>'.__( 'Contains the labels of the following orders:', 'allpacka' ).'</strong> '.implode(', ', $letezo_fuvarlevel);
     
                }
                
            }       
    
        }
        else{
            
            $allpacka_fuvarlevel_letoltes_return['sikertelen'] = 
                '<strong>'.__( 'Label generation via Pactic failed.', 'allpacka' ).'</strong><br/><br/>'.
                '<strong>'.__( 'None of the selected orders has a label!', 'allpacka' ).'</strong> '.
                ''.__( 'Webshop order IDs:', 'allpacka' ).' '.implode(', ', $nem_letezo_fuvarlevel);
                
        }
        
    }
    
    
    return $allpacka_fuvarlevel_letoltes_return;

}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/


function allpacka_engedelyezett_fizetesi_modok( $filter = '' ){
    
    $fizetesi_modok = WC()->payment_gateways->payment_gateways();

    $allpacka_engedelyezett_fizetesi_modok = array();

    foreach ( $fizetesi_modok as $fizetesi_mod ) {
        
        if( $filter != '' && $fizetesi_mod->id == $filter ){
            
            return $fizetesi_mod->title;
            
        }
        
        if( $fizetesi_mod->id != 'cod' ){
		
            if( $fizetesi_mod->enabled == 'yes' ){
                $allpacka_engedelyezett_fizetesi_modok[ $fizetesi_mod->id ] = $fizetesi_mod->title.' ('.__( 'Licensed', 'allpacka' ).')';
            }
            else{
                $allpacka_engedelyezett_fizetesi_modok[ $fizetesi_mod->id ] = $fizetesi_mod->title.' ('.__( 'Not licensed', 'allpacka' ).')';
            }
            
        
        }
	
    }
    
	return $allpacka_engedelyezett_fizetesi_modok;
  
}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/
 
 
function allpacka_csomag_statusz( $allpacka_fuvarlevelszam, $osszes = false ) {

    $url = 'http://api.pactic.com/webservices/shipment/tracking/track.ashx?waybill='.$allpacka_fuvarlevelszam;
    
    if( extension_loaded( 'curl' ) ){

        $ch = curl_init();
    
        curl_setopt( $ch, CURLOPT_AUTOREFERER, TRUE );
        curl_setopt( $ch, CURLOPT_HEADER, FALSE );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, TRUE );    
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );   
        
        $allpacka_csomag_statusz_json = curl_exec( $ch );
        
        curl_close($ch);

    }
    else if( ini_get( 'allow_url_fopen' ) ){
       
        $allpacka_csomag_statusz_json = file_get_contents( $url, false );
    
    }
    
    $allpacka_csomag_statusz = json_decode( $allpacka_csomag_statusz_json, true);
    
    $csomag_statusz_nevek = array(
        1 => array(             
            'nev' => __( 'Data processed', 'allpacka' ),             
            'leiras' => __( 'Data transferred to carrier', 'allpacka' ),         
        ),
        2 => array(             
            'nev' => __( 'In transit', 'allpacka' ),             
            'leiras' => __( 'Parcel is in transit, this might mean being in a HUB (central or local) or being transferred between HUBs.', 'allpacka' ),        
        ),
        3 => array(             
            'nev' => __( 'Under delivery', 'allpacka' ),             
            'leiras' => __( 'Parcel is at courier, delivery expected soon.', 'allpacka' ),        
        ),
        4 => array(             
            'nev' => __( 'Delivered', 'allpacka' ),             
            'leiras' => __( 'Parcel was delivered successfully.', 'allpacka' ),         
        ),
        5 => array(             
            'nev' => __( 'Disruptions', 'allpacka' ),             
            'leiras' => __( 'Disruptions happened (delays, wrong address, delivery rescheduled ect.). Contact Pactic for more information.', 'allpacka' ),         
        ),
        6 => array(             
            'nev' => __( 'Return', 'allpacka' ),             
            'leiras' => __( 'Parcel sent back to original sender.', 'allpacka' ),         
        ),
        7 => array(             
            'nev' => __( 'Deleted', 'allpacka' ),             
            'leiras' => __( 'Parcel delivery cancelled at carrier company.', 'allpacka' ),         
        ),
        8 => array(             
            'nev' => __( 'No data available', 'allpacka' ),             
            'leiras' => __( 'Data transferred to carrier, but not acknowledged yet. Please note that this status exists only on the website for now. This web service returns nothing when no data is available.', 'allpacka' ),        
        ),
        9 => array(             
            'nev' => __( 'Damaged', 'allpacka' ),             
            'leiras' => __( 'Parcel got damanged.', 'allpacka' ),         
        ),
        10 => array(             
            'nev' => __( 'Dropoff point', 'allpacka' ),             
            'leiras' => __( 'Parcel is in dropoff point.', 'allpacka' ),         
        ),
        11 => array(             
            'nev' => __( 'Missing', 'allpacka' ),             
            'leiras' => __( 'Parcel went missing', 'allpacka' ),         
        ),
        12 => array(             
            'nev' => __( 'Refused', 'allpacka' ),             
            'leiras' => __( 'Refused', 'allpacka' ),        
        ),
        13 => array(             
            'nev' => __( 'Consignee absent', 'allpacka' ),             
            'leiras' => __( 'Consignee Absent', 'allpacka' ),         
        ),
        14 => array(             
            'nev' => __( 'Wrong address', 'allpacka' ),             
            'leiras' => '',         
        ),
        15 => array(             
            'nev' => __( 'HUB Storage', 'allpacka' ),             
            'leiras' => '',         
        ),
        16 => array(             
            'nev' => __( 'Rescheduled delivery', 'allpacka' ),             
            'leiras' => '',         
        ),
        17 => array(             
            'nev' => __( 'Missorted or delayed', 'allpacka' ),             
            'leiras' => '',         
        ),
        18 => array(             
            'nev' => __( 'Undeliverable - Driver ran out of time', 'allpacka' ),             
            'leiras' => '',         
        ),
        19 => array(             
            'nev' => __( 'Lack of Money', 'allpacka' ),             
            'leiras' => '',         
        ),
        20 => array(             
            'nev' => __( 'Undeliverable - Obstacle preventing delivery (e.g. gated community, closed entrance etc)', 'allpacka' ),             
            'leiras' => '',         
        ),
        21 => array(             
            'nev' => __( 'Undeliverable', 'allpacka' ),             
            'leiras' => '',         
        ),
        22 => array(             
            'nev' => __( 'Oversized', 'allpacka' ),             
            'leiras' => '',         
        ),
        23 => array(             
            'nev' => __( 'Change of delivery address', 'allpacka' ),             
            'leiras' => '',         
        ),
        24 => array(             
            'nev' => __( 'Consignee Contacted', 'allpacka' ),             
            'leiras' => '',         
        ),
        25 => array(             
            'nev' => __( 'Processing at customs (if necessary, sender/recipient will be contacted)', 'allpacka' ),             
            'leiras' => '',         
        ),
        26 => array(             
            'nev' => __( 'Picked up by the driver', 'allpacka' ),             
            'leiras' => '',         
        ),
        27 => array(             
            'nev' => __( 'Shipment arrived to sorting centre / Shipment in the sorting centre', 'allpacka' ),             
            'leiras' => '',         
        ),
        101 => array(             
            'nev' => __( 'Arrived To HUB', 'allpacka' ),             
            'leiras' => '',         
        ),
        102 => array(             
            'nev' => __( 'Linehaul Transit', 'allpacka' ),             
            'leiras' => '',         
        ),
        103 => array(             
            'nev' => __( 'Dropped Off', 'allpacka' ),             
            'leiras' => '',         
        ),
        104 => array(             
            'nev' => __( 'Final Return', 'allpacka' ),             
            'leiras' => '',         
        ),
        105 => array(             
            'nev' => __( 'Consolidated By Sender', 'allpacka' ),             
            'leiras' => '',         
        ),
        106 => array(             
            'nev' => __( 'Arrived and consolidated RO', 'allpacka' ),             
            'leiras' => '',         
        ),
        107 => array(             
            'nev' => __( 'Arrived and consolidated HU', 'allpacka' ),             
            'leiras' => '',         
        ),
        108 => array(             
            'nev' => __( 'Arrived to HU HUB', 'allpacka' ),             
            'leiras' => '',         
        ),
        109 => array(             
            'nev' => __( 'Arrived to RO HUB', 'allpacka' ),             
            'leiras' => '',        
        ),
        110 => array(             
            'nev' => __( 'Returned to Pactic', 'allpacka' ),             
            'leiras' => '',        
        ),
        111 => array(             
            'nev' => __( 'Returned to Customer', 'allpacka' ),             
            'leiras' => '',        
        ),
        112 => array(             
            'nev' => __( 'Non-standard parcel rejection at Pactic', 'allpacka' ),             
            'leiras' => '',         
        )
    );
    
    
    $csomag_statuszok = array();
    
    if( $allpacka_csomag_statusz ){
        
        if( $allpacka_csomag_statusz[0]['history'] ){
            
            foreach( $allpacka_csomag_statusz[0]['history'] as $history ){
                
                if( array_key_exists( $history['status'], $csomag_statusz_nevek ) ){
                
                    $csomag_statuszok[] = array(
                        'id' => $history['status'],
                        'nev' => $csomag_statusz_nevek[$history['status']]['nev'],
                        'leiras' => $csomag_statusz_nevek[$history['status']]['leiras'],
                        'datum' => $history['date'],
                    );
                
                }
                
            }
            
        }
    
    }
    
    if( $osszes == false ){
        return end( $csomag_statuszok );
    }
    else{
        return $csomag_statuszok;
    }
    
}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

function allpacka_waybills_ajax_torles() {
    
    $upload_dir = wp_upload_dir();
    
    $counter = 0;

    if ( isset($_REQUEST) ) {
                
        foreach ( glob( $upload_dir['basedir'].'/allpacka/waybills*.pdf' ) as $filename ) {

            unlink($filename);
            
            $counter++;
        }

    }
    
    echo $counter.' '.__( 'file deleted.', 'allpacka' );
    
    die();
}

add_action( 'wp_ajax_allpacka_waybills_ajax_torles', 'allpacka_waybills_ajax_torles' );


/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/


add_action( 'admin_notices', 'allpacka_kornyezet_ellenorzes' );
 
function allpacka_kornyezet_ellenorzes() {
    
    if( !extension_loaded( 'curl' ) && !ini_get( 'allow_url_fopen' ) ){
        
        if(  get_option( 'allpacka_allapot' ) == 'yes' ){
            
            update_option( 'allpacka_allapot', 'no' );
            
        }
        
        echo '<div class="error notice is-dismissible allpacka_notice">';
            echo '<div>';
                echo '<img src="'.ALLPACKA_PLUGIN_DIR_URL.'images/pactic_ikon_3.png" />';
                echo '<strong>';
                    echo __( 'Error message from Pactic', 'allpacka' );
                    echo '<br/><br/>';
                    echo __( 'To use this plugin, you need to install the Client URL Library or enable allow_url_fopen. Please contact the website operator.', 'allpacka' );
                echo '</strong>';
            echo '</div>';
            echo '<button type="button" class="notice-dismiss allpacka_notice_dismiss"></button>';
        echo '</div>';

    }
 
}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/


add_action( 'admin_notices', 'allpacka_utanvet_beallitas_ellenorzes' );
 
function allpacka_utanvet_beallitas_ellenorzes() {

    if( get_option( 'allpacka_allapot' ) == 'yes' && get_option( 'allpacka_cod_notice_dismiss' ) != 'yes'  ){
    
        echo '<div class="allpacka_warning notice notice-warning allpacka_notice">';
            
            echo '<div>';
                
                echo '<img src="'.ALLPACKA_PLUGIN_DIR_URL.'images/pactic_ikon_3.png" />';
                
                echo '<strong>';
                    echo __( 'Pactic warning', 'allpacka' );
                echo '</strong>';
     
                echo '<br/>';
                
                echo '<p>';
                    echo __( 'The plug-in automatically chooses the default payment method:', 'allpacka' ).' <strong>'.allpacka_engedelyezett_fizetesi_modok( 'cod' ).'</strong>.';
                echo '</p>';
                echo '<p>';
                    echo __( 'Additional payment methods for COD in your webshop.', 'allpacka' );
                    echo ' <a href="'.admin_url().'admin.php?page=wc-settings&tab=allpacka#allpacka_fizetesi_modok">'.__( 'Set up here.', 'allpacka' ).'</a>';
                
                echo '</p>'; 
                
                echo '<p>';  
                    echo '<button type="button" class="button button-secondary allpacka_cod_notice_dismiss">'.__( 'I understood', 'allpacka' ).'</button>';
                echo '</p>';  

            echo '</div>';
             
        echo '</div>';

    }

}

/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/

function allpacka_cod_notice_dismiss() {

    if ( isset($_REQUEST) ) {
                
         update_option( 'allpacka_cod_notice_dismiss', 'yes' );

    }

    die();
}

add_action( 'wp_ajax_allpacka_cod_notice_dismiss', 'allpacka_cod_notice_dismiss' );
     
/**********************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************/



?>