<?php
namespace CasaSync;

class Import {
  public $lastTranscript = '';
  public $importFile = false;
  public $main_lang = false;
  public $max_auto_create = 10;
  public $xmlOffers = array();
  public $WPML = null;
  public $transcript = array();
  public $curtrid = false;
  public $meta_keys = array(
     #'surface_living'              ,
      'surface_property'            ,
      #'surface_usable'              ,

      'area_bwf'                    ,
      'area_nwf'                    ,
      'area_sia_gf'                 ,
      'area_sia_nf'                 ,

      'volume'                      ,
      'ceiling_height'              ,
      'hall_height'                 ,
      'maximal_floor_loading'       ,
      'carrying_capacity_crane'     ,
      'carrying_capacity_elevator'  ,
      'floor'                       ,
      'year_built'                  ,
      'year_renovated'              ,
      'number_of_rooms'             ,
      'number_of_apartments'        ,
      'number_of_floors'            ,

      'casasync_visitInformation'                    ,
      'casasync_property_url'                        ,
      'casasync_property_address_country'            ,
      'casasync_property_address_locality'           ,
      'casasync_property_address_region'             ,
      'casasync_property_address_postalcode'         ,
      'casasync_property_address_streetaddress'      ,
      'casasync_property_address_streetnumber'       ,
      'casasync_urls'                                ,
      'casasync_start'                               ,
      'casasync_referenceId'                         ,
      'availability'                                 ,
      'availability_label'                           ,
      'offer_type'                                   ,
      'price_currency'                               ,
      'price_timesegment'                            ,
      'price_propertysegment'                        ,
      'grossPrice_timesegment'                       ,
      'grossPrice_propertysegment'                   ,
      'netPrice_timesegment'                         ,
      'netPrice_propertysegment'                     ,
      'priceForOrder'                                ,
      'seller_org_address_country'                   ,
      'seller_org_address_locality'                  ,
      'seller_org_address_region'                    ,
      'seller_org_address_postalcode'                ,
      'seller_org_address_postofficeboxnumber'       ,
      'seller_org_address_streetaddress'             ,
      'seller_org_legalname'                         ,
      'seller_org_email'                             ,
      'seller_org_fax'                               ,
      'seller_org_phone_direct'                      ,
      'seller_org_phone_central'                     ,
      'seller_org_phone_mobile'                      ,
      'seller_org_brand'                             ,
      'seller_person_function'                       ,
      'seller_person_givenname'                      ,
      'seller_person_familyname'                     ,
      'seller_person_email'                          ,
      'seller_person_fax'                            ,
      'seller_person_phone_direct'                   ,
      'seller_person_phone_central'                  ,
      'seller_person_phone_mobile'                   ,
      'seller_person_gender'                         ,
      'seller_inquiry_person_function'               ,
      'seller_inquiry_person_givenname'              ,
      'seller_inquiry_person_familyname'             ,
      'seller_inquiry_person_email'                  ,
      'seller_inquiry_person_fax'                    ,
      'seller_inquiry_person_phone_direct'           ,
      'seller_inquiry_person_phone_central'          ,
      'seller_inquiry_person_phone_mobile'           ,
      'seller_inquiry_person_gender'                 ,
      'casasync_property_geo_latitude'               ,
      'casasync_property_geo_longitude'              ,
      'price'                                        ,
      'grossPrice'                                   ,
      'netPrice'                                     ,
      'the_urls'                                     ,
      'the_tags'                                     ,
      'extraPrice'                                   ,

      'distance_public_transport'                    ,
      'distance_shop'                                ,
      'distance_kindergarten'                        ,
      'distance_motorway'                            ,
      'distance_school1'                             ,
      'distance_school2'                             ,
      'distance_bus_stop',
      'distance_train_station',
      'distance_post',
      'distance_bank',
      'distance_cable_railway_station',
      'distance_boat_dock',
      'distance_airport',

      'casasync_features'                            ,
  );

  public function __construct(){
    $this->conversion = new Conversion;
    add_action( 'init', array($this, 'casasyncImport') );
    //$this->casasyncImport();
  }

  public function getImportFile(){
    if (!$this->importFile) {
      $good_to_go = false;
      if (!is_dir(CASASYNC_CUR_UPLOAD_BASEDIR . '/casasync/import')) {
        mkdir(CASASYNC_CUR_UPLOAD_BASEDIR . '/casasync/import');
      }
      $file = CASASYNC_CUR_UPLOAD_BASEDIR  . '/casasync/import/data.xml';
      if (file_exists($file)) {
        $good_to_go = true;
      } else {
        //if force last check for last
        if (isset($_GET['force_last_import'])) {
          $file = CASASYNC_CUR_UPLOAD_BASEDIR  . '/casasync/import/data-done.xml';
          if (file_exists($file)) {
            $good_to_go = true;
          }
        }
      }
      if ($good_to_go) {
        $this->importFile = $file;
      }
    }
    return $this->importFile;
  }

  public function renameImportFileTo($to){
    if ($this->importFile != $to) {
      rename($this->importFile, $to);
      $this->importFile = $to;
    }
  }

  public function backupImportFile(){
    copy ( $this->getImportFile() , CASASYNC_CUR_UPLOAD_BASEDIR  . '/casasync/done/' . date('Y_m_d_H_i_s') . '_completed.xml');
    return true;
  }

  public function extractDescription($offer){
    $the_description = '';
    foreach ($offer->description as $description) {
      $the_description .= ($the_description ? '<hr class="property-separator" />' : '');
      if ($description['title']) {
        $the_description .= '<h2>' . $description['title']->__toString() . '</h2>';
      }
      $the_description .= $description->__toString();
    }
    return $the_description;
  }

  public function getLastTranscript(){
    return $this->lastTranscript;
  }

  public function setCasasyncCategoryTerm($term_slug, $label = false) {
    $label = (!$label ? $term_slug : $label);
    $term = get_term_by('slug', $term_slug, 'casasync_category', OBJECT, 'raw' );
    //$existing_term_id = term_exists( $label, 'casasync_category');
    $existing_term_id = false;
    if ($term) {
      if (
        $term->slug != $term_slug
        || $term->name != $label
      ) {
        wp_update_term($term->term_id, 'casasync_category', array(
          'name' => $label,
          'slug' => $term_slug
        ));
      }
    } else {
      $options = array(
        'description' => '',
        'slug' => $term_slug
      );
      $id = wp_insert_term(
        $label,
        'casasync_category',
        $options
      );
      return $id;
    }
  }

  public function convertXmlPublisherOptions($publisher_options_xml){
    $publisher_options = array();
      foreach ($publisher_options_xml as $settings) {
        if ($settings['id'] == 'casasync') {
          if ($settings->options) {
            foreach ($settings->options->option as $option) {
              $key   = (isset($option['name']) ? $option['name'] : false);
              $value = $option->__toString();
              if ($key && $value) {
                if (strpos($key, 'custom_category_') === 0) {
                  $parts = explode('_', $key);
                  $sort = (isset($parts[2]) && is_numeric($parts[2]) ? $parts[2] : false);
                  $slug = (isset($parts[3]) && $parts[3] == 'slug' ? true : false);
                  $label = (isset($parts[3]) && $parts[3] == 'label' ? true : false);
                  if ($slug) {
                    $publisher_options['custom_categories'][$sort]['slug'] = $value;
                  } elseif ($label) {
                    $publisher_options['custom_categories'][$label]['label'] = $value;
                  }
                }
              }
            }
          }
        }
      }

    return $publisher_options;
  }

  public function casasyncUploadAttachment($the_mediaitem, $post_id, $property_id) {
    if ($the_mediaitem['file']) {
      $filename = '/casasync/import/attachment/'. $the_mediaitem['file'];
    } elseif ($the_mediaitem['url']) { //external
      $filename = '/casasync/import/attachment/externalsync/' . $property_id . '/' . basename($the_mediaitem['url']);

      //extention is required
      $file_parts = pathinfo($filename);
      if (!isset($file_parts['extension'])) {
          $filename = $filename . '.jpg';
      }
      if (!is_file(CASASYNC_CUR_UPLOAD_BASEDIR . $filename)) {
        if (!is_dir(CASASYNC_CUR_UPLOAD_BASEDIR . '/casasync/import/attachment/externalsync')) {
          mkdir(CASASYNC_CUR_UPLOAD_BASEDIR . '/casasync/import/attachment/externalsync');
        }
        if (!is_dir(CASASYNC_CUR_UPLOAD_BASEDIR . '/casasync/import/attachment/externalsync/' . $property_id)) {
          mkdir(CASASYNC_CUR_UPLOAD_BASEDIR . '/casasync/import/attachment/externalsync/' . $property_id);
        }
        if (is_file(CASASYNC_CUR_UPLOAD_BASEDIR . $filename )) {
          $could_copy = copy($the_mediaitem['url'], CASASYNC_CUR_UPLOAD_BASEDIR . $filename );
        } else {
          $could_copy = false;
        }

        if (!$could_copy) {
          $filename = false;
        }
      }
    } else { //missing
      $filename = false;
    }

    if ($filename && is_file(CASASYNC_CUR_UPLOAD_BASEDIR . $filename)) {
      //new file attachment upload it and attach it fully
      $wp_filetype = wp_check_filetype(basename($filename), null );
      $attachment = array(
        'guid'           => CASASYNC_CUR_UPLOAD_BASEURL . $filename,
        'post_mime_type' => $wp_filetype['type'],
        'post_title'     =>  preg_replace('/\.[^.]+$/', '', ( $the_mediaitem['title'] ? $the_mediaitem['title'] : basename($filename)) ),
        'post_content'   => '',
        'post_excerpt'   => $the_mediaitem['caption'],
        'post_status'    => 'inherit',
        'menu_order'     => $the_mediaitem['order']
      );

      $attach_id = wp_insert_attachment( $attachment, CASASYNC_CUR_UPLOAD_BASEDIR . $filename, $post_id );
      // you must first include the image.php file
      // for the function wp_generate_attachment_metadata() to work
      require_once(ABSPATH . 'wp-admin/includes/image.php');
      $attach_data = wp_generate_attachment_metadata( $attach_id, CASASYNC_CUR_UPLOAD_BASEDIR . $filename );
      wp_update_attachment_metadata( $attach_id, $attach_data );

      //category
      $term = get_term_by('slug', $the_mediaitem['type'], 'casasync_attachment_type');
      $term_id = $term->term_id;
      wp_set_post_terms( $attach_id,  array($term_id), 'casasync_attachment_type' );

      //alt
      update_post_meta($attach_id, '_wp_attachment_image_alt', $the_mediaitem['alt']);

      //orig
      update_post_meta($attach_id, '_origin', ($the_mediaitem['file'] ? $the_mediaitem['file'] : $the_mediaitem['url']));

      return $attach_id;
    } else {
      return $filename . " could not be found!";
    }
  }

  public function getMainLang(){
    global $sitepress;
    if (!$this->main_lang) {
      $main_lang = 'de';
      if($this->hasWPML()) {
          if (function_exists("wpml_get_default_language")) {
            $main_lang = wpml_get_default_language();
            $this->WPML = true;
          }
      } else {
        if (get_bloginfo('language')) {
          $main_lang = substr(get_bloginfo('language'), 0, 2);
        } 
      }
      $this->main_lang = $main_lang;
    }
    return $this->main_lang;
  }

  public function hasWPML(){
    if ($this->WPML !== true && $this->WPML !== false) {
      $this->WPML = $this->loadWPML();
    }
    return $this->WPML;
  }

  public function loadWPML(){
    global $sitepress;
    if( $sitepress && is_object($sitepress) && method_exists($sitepress, 'get_language_details' )) {
      if (is_file( WP_PLUGIN_DIR . '/sitepress-multilingual-cms/inc/wpml-api.php' )) {
        require_once( WP_PLUGIN_DIR . '/sitepress-multilingual-cms/inc/wpml-api.php' );
      }
      return true;
    }
    return false;
  }

  public function updateInsertWPMLconnection($offer_pos, $wp_post, $lang, $casasync_id){
    if ($this->hasWPML()) {

      if ($this->getMainLang() == $lang) {
        $this->curtrid = wpml_get_content_trid('post_casasync_property', $wp_post->ID);
      }

      $_POST['icl_post_language'] = $lang; 
      
      global $sitepress;
      if ($this->getMainLang() != $lang) {
        $sitepress->set_element_language_details($wp_post->ID, 'post_casasync_property', $this->curtrid, $lang, $sitepress->get_default_language(), true);
      } else {
        $sitepress->set_element_language_details($wp_post->ID, 'post_casasync_property', $this->curtrid, $lang, NULL, true);
      }
    }
  }

  public function personsXMLtoArray($seller){
    $r_persons = array();
    if ($seller && $seller->person) {
      foreach ($seller->person as $person) {
        $type = $person['type']->__toString();
        if ($type && in_array($type, array('inquiry', 'view'))) {
          $prefix = 'seller' . ($type != 'view' ? '_' . $type : '') . '_person_';

          $r_persons[$prefix.'function']   = $this->simpleXMLget($person->function);
          $r_persons[$prefix.'givenname']  = $this->simpleXMLget($person->givenName);
          $r_persons[$prefix.'familyname'] = $this->simpleXMLget($person->familyName);
          $r_persons[$prefix.'email']      = $this->simpleXMLget($person->email);
          $r_persons[$prefix.'fax']        = $this->simpleXMLget($person->faxNumber);
          $r_persons[$prefix.'phone_direct'] = '';
          $r_persons[$prefix.'phone_central'] = '';
          $r_persons[$prefix.'phone_mobile'] = '';
          if ($person->phone) {
            $central = false;
            foreach ($person->phone as $phone) {
              if ($phone['type']) {
                switch ($phone['type']->__toString()) {
                  case 'direct':
                    $r_persons[$prefix.'phone_direct'] = $phone->__toString();
                    break;
                  case 'central':
                    $r_persons[$prefix.'phone_central'] = $phone->__toString();
                    break;
                  case 'mobile':
                    $r_persons[$prefix.'phone_mobile'] = $phone->__toString();
                    break;
                  default:
                    if (!$phone_central) {
                      $r_persons[$prefix.'phone_central'] = $phone->__toString();
                    }
                    break;
                }
              } else {
                if (!$central) {
                  $r_persons[$prefix.'phone_central'] = $phone->__toString();
                }
              }
            }
          }
          $r_persons[$prefix.'gender'] = $this->simpleXMLget($person->gender);
        }
      }
    }

    return $r_persons;
  }

  public function numvalsXMLtoArray($numericValues){
    //set numericValues
    $the_numvals = array();
    if ($numericValues && $numericValues->value) {
      foreach ($numericValues->value as $numval) {
        if ($numval->__toString() && $numval['key']) {
          $values = explode('+', $numval->__toString());
          $the_values = array();
          foreach ($values as $value) {
            $numval_parts = explode('to', $value);
            $numval_from = $numval_parts[0];
            $numval_to = (isset($numval_parts[1]) ? $numval_parts[1] : false);
            $the_values[] = array(
              'from' => $this->conversion->casasync_numStringToArray($numval['key'], $numval_from),
              'to' => $this->conversion->casasync_numStringToArray($numval['key'], $numval_to)
            );
          }
          $the_numvals[(string)$numval['key']] = $the_values;
        }
      }
    }
    $all_distance_keys     = $this->conversion->casasync_get_allDistanceKeys();
    $all_numval_keys       = $this->conversion->casasync_get_allNumvalKeys();
    $r_distances         = array();
    $r_numvals            = array();

    foreach ($the_numvals as $key => $numval) {
      if (in_array($key, $all_distance_keys)) {
        $the_value = '';
        foreach ($numval as $key2 => $value) {
          $the_value .= ($key2 != 0 ? '+' : '') . '[' . $value['from']['value'] . $value['from']['si'] . ']';
        }
        $r_distances[$key] = $the_value;
      }
      if (in_array($key, $all_numval_keys)) {
        switch ($key) {
          //multiple simple values
          case 'multiple':
            /*$the_value = '';
            foreach ($numval as $key2 => $value) {
              $the_value .= ($key2 != 0 ? '+' : '') . '[' . $value['from']['value'] . ']';
            }
            $r_numvals[$key] = $the_value;*/
            break;
          //simple value with si
          #case 'surface_living':
          case 'surface_property':
          #case 'surface_usable':
          case 'area_bwf':
          case 'area_nwf':
          case 'area_sia_gf':
          case 'area_sia_nf':
          case 'volume':
          case 'ceiling_height':
          case 'hall_height':
          case 'maximal_floor_loading':
          case 'carrying_capacity_crane':
          case 'carrying_capacity_elevator':
            $the_value = '';
            foreach ($numval as $key2 => $value) {
              $the_value = $value['from']['value'] . $value['from']['si'];
            }
            $r_numvals[$key] = $the_value;
            break;
          //INT
          case 'floor':
          case 'year_built':
          case 'year_renovated':
            $the_value = '';
            foreach ($numval as $key2 => $value) {
              $the_value = round($value['from']['value']);
            }
            $r_numvals[$key] = $the_value;
            break;
          //float
          case 'number_of_rooms':
          case 'number_of_apartments':
          case 'number_of_floors':
            $the_value = '';
            foreach ($numval as $key2 => $value) {
              $the_value = $value['from']['value'];
            }
            $r_numvals[$key] = $the_value;
            break;
          default:
            break;
        }
      }
    }


    return array_merge($r_numvals, $r_distances);

  }


  public function featuresXMLtoJson($features){
    $the_features = array();
    if ($features && $features->feature) {
      $set_orders = array();
      foreach ($features->feature as $feature) {
        if ($feature['key']) {
          $key = $feature['key']->__toString();
          $value = $feature->__toString();
          if ($set_orders) {
            $next_key_available = max($set_orders) + 1;
          } else {
            $next_key_available = 0;
          }
          $order = ($feature['order'] && !in_array($feature['order']->__toString(), $set_orders) ? $feature['order']->__toString() : $next_key_available);
          $set_orders[] = $order;

          $the_features[$order] = array(
            'key' => $key,
            'value' => $value,
          );
        }
      }
    }
    if ($the_features) {
      ksort($the_features);
      $the_features_json = json_encode($the_features);
    } else {
      $the_features_json = '';
    }
    return $the_features_json;
  }


  /*
    KEYS: custom_category_{$i}
  */
  public function publisherOptionsXMLtoArray($publisher_options_xml){
    $publisher_options = array();
      foreach ($publisher_options_xml as $settings) {
        if ($settings['id'] == 'casasync') {
          if ($settings->options) {
            foreach ($settings->options->option as $option) {
              $key   = (isset($option['name']) ? $option['name'] : false);
              $value = $option->__toString();
              if ($key && $value) {
                if (strpos($key, 'custom_category_') === 0) {
                  $parts = explode('_', $key);
                  $sort = (isset($parts[2]) && is_numeric($parts[2]) ? $parts[2] : false);
                  $slug = (isset($parts[3]) && $parts[3] == 'slug' ? true : false);
                  $label = (isset($parts[3]) && $parts[3] == 'label' ? true : false);
                  if ($slug) {
                    $publisher_options['custom_categories'][$sort]['slug'] = $value;
                  } elseif ($label) {
                    $publisher_options['custom_categories'][$label]['label'] = $value;
                  }
                }
              }
            }
          }
        }
      }

    return $publisher_options;
  }

  public function setOfferAttachments($xmlattachments, $wp_post, $property_id, $casasync_id){
    ### future task: for better performace compare new and old data ###

    
    //get xml media files
    $the_casasync_attachments = array();
    if ($xmlattachments) {
      foreach ($xmlattachments->media as $media) {
        if (in_array($media['type']->__toString(), array('image', 'document', 'plan', 'offer-logo'))) {
          $filename = ($media->file->__toString() ? $media->file->__toString() : $media->url->__toString());
          $the_casasync_attachments[] = array(
            'type'    => $media['type']->__toString(),
            'alt'     => $media->alt->__toString(),
            'title'   => preg_replace('/\.[^.]+$/', '', ( $media->title->__toString() ? $media->title->__toString() : basename($filename)) ),
            'file'    => $media->file->__toString(),
            'url'     => $media->url->__toString(),
            'caption' => $media->caption->__toString(),
            'order'   => $media['order']->__toString()
          );
        }
      }
    }

    //get post attachments already attached
    $wp_casasync_attachments = array();
    $args = array(
      'post_type'   => 'attachment',
      'numberposts' => -1,
      'post_status' => null,
      'post_parent' => $wp_post->ID,
      'tax_query'   => array(
        'relation'  => 'AND',
        array(
          'taxonomy' => 'casasync_attachment_type',
          'field'    => 'slug',
          'terms'    => array( 'image', 'plan', 'document', 'offer-logo' )
        )
      )
    );
    $attachments = get_posts($args);
    if ($attachments) {
      foreach ($attachments as $attachment) {
        $wp_casasync_attachments[] = $attachment;
      }
    }

    //upload necesary images to wordpress
    if (isset($the_casasync_attachments)) {
      $wp_casasync_attachments_to_remove = $wp_casasync_attachments;
      foreach ($the_casasync_attachments as $the_mediaitem) {
        //look up wp and see if file is already attached
        $existing = false;
        $existing_attachment = array();
        foreach ($wp_casasync_attachments as $key => $wp_mediaitem) {
          $attachment_customfields = get_post_custom($wp_mediaitem->ID);
          $original_filename = (array_key_exists('_origin', $attachment_customfields) ? $attachment_customfields['_origin'][0] : '');
          $alt = '';
          if ($original_filename == ($the_mediaitem['file'] ? $the_mediaitem['file'] : $the_mediaitem['url'])) {
            $existing = true;

            //its here to stay
            unset($wp_casasync_attachments_to_remove[$key]);

            $types = wp_get_post_terms( $wp_mediaitem->ID, 'casasync_attachment_type');
            if (array_key_exists(0, $types)) {
              $typeslug = $types[0]->slug;
              $alt = get_post_meta($wp_mediaitem->ID, '_wp_attachment_image_alt', true);
              //build a proper array out of it
              $existing_attachment = array(
                'type'    => $typeslug,
                'alt'     => $alt,
                'title'   => $wp_mediaitem->post_title,
                'file'    => $the_mediaitem['file'],
                'url'     => $the_mediaitem['url'],
                'caption' => $wp_mediaitem->post_excerpt,
                'order'   => $wp_mediaitem->menu_order
              );
            }

            //have its values changed?
            if($existing_attachment != $the_mediaitem ){
              $changed = true;
              $this->transcript[$casasync_id]['attachments']["updated"] = 1;
              //update attachment data
              if ($existing_attachment['caption'] != $the_mediaitem['caption']
                || $existing_attachment['title'] != $the_mediaitem['title']
                || $existing_attachment['order'] != $the_mediaitem['order']
                ) {
                $att['post_excerpt'] = $the_mediaitem['caption'];
                $att['post_title']   = preg_replace('/\.[^.]+$/', '', ( $the_mediaitem['title'] ? $the_mediaitem['title'] : basename($filename)) );
                $att['ID']           = $wp_mediaitem->ID;
                $att['menu_order']   = $the_mediaitem['order'];
                $insert_id           = wp_update_post( $att);
              }
              //update attachment category
              if ($existing_attachment['type'] != $the_mediaitem['type']) {
                $term = get_term_by('slug', $the_mediaitem['type'], 'casasync_attachment_type');
                $term_id = $term->term_id;
                wp_set_post_terms( $wp_mediaitem->ID,  array($term_id), 'casasync_attachment_type' );
              }
              //update attachment alt
              if ($alt != $the_mediaitem['alt']) {
                update_post_meta($wp_mediaitem->ID, '_wp_attachment_image_alt', $the_mediaitem['alt']);
              }
            }
          }

          
        }

        if (!$existing) {
          //insert the new image
          $new_id = $this->casasyncUploadAttachment($the_mediaitem, $wp_post->ID, $property_id);
          if (is_int($new_id)) {
            $this->transcript[$casasync_id]['attachments']["created"] = $the_mediaitem['file'];
          } else {
            $this->transcript[$casasync_id]['attachments']["failed_to_create"] = $new_id;
          }
        }
        

      } //foreach ($the_casasync_attachments as $the_mediaitem) {

      //featured image
      $args = array(
        'post_type'   => 'attachment',
        'numberposts' => -1,
        'post_status' => null,
        'post_parent' => $wp_post->ID,
        'tax_query'   => array(
          'relation'  => 'AND',
          array(
            'taxonomy' => 'casasync_attachment_type',
            'field'    => 'slug',
            'terms'    => array( 'image', 'plan', 'document', 'offer-logo' )
          )
        )
      );
      $attachments = get_posts($args);
      if ($attachments) {
        unset($wp_casasync_attachments);
        foreach ($attachments as $attachment) {
          $wp_casasync_attachments[] = $attachment;
        }
      }

      $attachment_image_order = array();
      foreach ($the_casasync_attachments as $the_mediaitem) {
        if ($the_mediaitem['type'] == 'image') {
          $attachment_image_order[$the_mediaitem['order']] = $the_mediaitem;
        }
      }
      if (isset($attachment_image_order) && !empty($attachment_image_order)) {
        ksort($attachment_image_order);
        $attachment_image_order = reset($attachment_image_order);
        if (!empty($attachment_image_order)) {
          foreach ($wp_casasync_attachments as $wp_mediaitem) {
            $attachment_customfields = get_post_custom($wp_mediaitem->ID);
            $original_filename = (array_key_exists('_origin', $attachment_customfields) ? $attachment_customfields['_origin'][0] : '');
            if ($original_filename == ($attachment_image_order['file'] ? $attachment_image_order['file'] : $attachment_image_order['url'])) {
              $cur_thumbnail_id = get_post_thumbnail_id( $wp_post->ID );
              if ($cur_thumbnail_id != $wp_mediaitem->ID) {
                set_post_thumbnail( $wp_post->ID, $wp_mediaitem->ID );
                $this->transcript[$casasync_id]['attachments']["featured_image_set"] = 1;
              }
            }
          }
        }
      }

      //images to remove
      foreach ($wp_casasync_attachments_to_remove as $attachment) {
        $this->transcript[$casasync_id]['attachments']["removed"] = $attachment;

        $attachment_customfields = get_post_custom($attachment->ID);
        $original_filename = (array_key_exists('_origin', $attachment_customfields) ? $attachment_customfields['_origin'][0] : '');
        wp_delete_attachment( $attachment->ID );
      }


    } //(isset($the_casasync_attachments)

   
  }

  public function setOfferSalestype($wp_post, $salestype, $casasync_id){
    $new_salestype = null;
    $old_salestype = null;

    if ($salestype) {
      $new_salestype = get_term_by('slug', $salestype, 'casasync_salestype', OBJECT, 'raw' );
      if (!$new_salestype) {
        $options = array(
          'description' => '',
          'slug' => $salestype
        );
        $id = wp_insert_term(
          $salestype,
          'casasync_salestype',
          $options
        );
        $new_salestype = get_term($id, 'casasync_salestype', OBJECT, 'raw');

      }
    }

    $wp_salestype_terms = wp_get_object_terms($wp_post->ID, 'casasync_salestype');
    if ($wp_salestype_terms) {
      $old_salestype = $wp_salestype_terms[0];
    }
    
    if ($old_salestype != $new_salestype) {
      $this->transcript[$casasync_id]['salestype']['from'] = ($old_salestype ? $old_salestype->name : 'none');
      $this->transcript[$casasync_id]['salestype']['to'] =   ($new_salestype ? $new_salestype->name : 'none');
      wp_set_object_terms( $wp_post->ID, ($new_salestype ? $new_salestype->term_id : NULL), 'casasync_salestype' );
    }
    
  }


  public function setOfferAvailability($wp_post, $availability, $casasync_id){
    $new_term = null;
    $old_term = null;

    //backward compadable
    if ($availability == 'available') {
      $availability = 'active';
    }

    if (!in_array($availability, array(
      'active',
      'taken',
      'reserved',
      'reference'
    ))) {
      $availability = null;
    }

    if ($availability) {
      $new_term = get_term_by('slug', $availability, 'casasync_availability', OBJECT, 'raw' );
      if (!$new_term) {
        $options = array(
          'description' => '',
          'slug' => $availability
        );
        $id = wp_insert_term(
          $availability,
          'casasync_availability',
          $options
        );
        $new_term = get_term($id, 'casasync_availability', OBJECT, 'raw');

      }
    }

    $wp_post_terms = wp_get_object_terms($wp_post->ID, 'casasync_availability');
    if ($wp_post_terms) {
      $old_term = $wp_post_terms[0];
    }
    
    if ($old_term != $new_term) {
      $this->transcript[$casasync_id]['availability']['from'] = ($old_term ? $old_term->name : 'none');
      $this->transcript[$casasync_id]['availability']['to'] =   ($new_term ? $new_term->name : 'none');
      wp_set_object_terms( $wp_post->ID, ($new_term ? $new_term->term_id : NULL), 'casasync_availability' );
    }
    
  }

  public function setOfferLocalities($wp_post, $xml_address, $casasync_id){
    $country  = strtoupper( $this->simpleXMLget($xml_address->country, 'CH'));
    $region   = $this->simpleXMLget($xml_address->region, false);
    $locality = $this->simpleXMLget($xml_address->locality, false);

    $country_arr = array($country, 'country_'.strtolower($country));
    $lvl1_arr = false;
    $lvl2_arr = false;
    if ($region) {
      $lvl1_arr = array($region, 'region_'.sanitize_title_with_dashes($region));
      if ($locality) {
        $lvl2_arr = array($locality, 'locality_'.sanitize_title_with_dashes($locality));
      }
    } elseif($locality) {
      $lvl1_arr = array($locality, 'locality_'.sanitize_title_with_dashes($locality));
      $lvl2_arr = false;
    }

    
    //make sure country exists
    $wp_country = false;
    if ($country_arr) {
      $wp_country = get_term_by('slug', $country_arr[1], 'casasync_location', OBJECT, 'raw' );

      if (!$wp_country || $wp_country instanceof WP_Error) {
        $options = array(
          'description' => '',
          'slug' => $country_arr[1]
        );
        $new_term = wp_insert_term(
          $country_arr[0],
          'casasync_location',
          $options
        );
        delete_option("casasync_location_children");
        $wp_country = get_term($new_term['term_id'], 'casasync_location', OBJECT, 'raw');
        $this->transcript['new_locations'][] = $country_arr;
      }
    }
    
    //make sure lvl1 exists
    $wp_lvl1 = false;
    if ($lvl1_arr) {
      $wp_lvl1 = get_term_by('slug', $lvl1_arr[1], 'casasync_location', OBJECT, 'raw' );

      if (!$wp_lvl1 || $wp_lvl1 instanceof WP_Error) {

        $options = array(
          'description' => '',
          'slug' => $lvl1_arr[1],
          'parent'=> ($wp_country ? (int) $wp_country->term_id : 0)
        );
        $new_term = wp_insert_term(
          $lvl1_arr[0],
          'casasync_location',
          $options
        );
        delete_option("casasync_location_children");
        $wp_lvl1 = get_term($new_term['term_id'], 'casasync_location', OBJECT, 'raw');
        $this->transcript['new_locations'][] = $lvl1_arr;
      }
    }

    //make sure lvl2 exists
    $wp_lvl2 = false;
    if ($lvl2_arr) {
      $wp_lvl2 = get_term_by('slug', $lvl2_arr[1], 'casasync_location', OBJECT, 'raw' );
      if (!$wp_lvl2 || $wp_lvl2 instanceof WP_Error) {
        $options = array(
          'description' => '',
          'slug' => $lvl2_arr[1],
          'parent' => ($wp_lvl1 ? (int) $wp_lvl1->term_id : 0)
        );
        $new_term = wp_insert_term(
          $lvl2_arr[0],
          'casasync_location',
          $options
        );
        delete_option("casasync_location_children");
        $wp_lvl2 = get_term($new_term['term_id'], 'casasync_location', OBJECT, 'raw');
        $this->transcript['new_locations'][] = $lvl2_arr;
      }
    }

    $new_terms = array();
    if ($wp_country) {
      $new_terms[] = $wp_country->term_id;
    }
    if ($wp_lvl1) {
      $new_terms[] = $wp_lvl1->term_id;
    }
    if ($wp_lvl2) {
      $new_terms[] = $wp_lvl2->term_id;
    }
    asort($new_terms);
    $new_terms = array_values($new_terms);

    $old_terms = array();
    $old_terms_obj = wp_get_object_terms($wp_post->ID, 'casasync_location');
    foreach ($old_terms_obj as $old_term) {
      $old_terms[] = $old_term->term_id;
    }
    asort($old_terms);
    $old_terms = array_values($old_terms);

    if ($new_terms != $old_terms) {
      $this->transcript[$casasync_id]['locations'][]['from'] = $old_terms;
      $this->transcript[$casasync_id]['locations'][]['to'] = $new_terms;
      wp_set_object_terms( $wp_post->ID, $new_terms, 'casasync_location' );
    }
    
  }

  public function setOfferCategories($wp_post, $categories, $publisher_options, $casasync_id){
    $new_categories = array();;
    $old_categories = array();

    //set post category
    $old_categories = array();
    $wp_category_terms = wp_get_object_terms($wp_post->ID, 'casasync_category');
    foreach ($wp_category_terms as $term) {
      $old_categories[] = $term->slug;
    }

    //supported
    if ($categories) {
      foreach ($categories as $category) {
        $new_categories[] = $category->__toString();
      }
    }
    //custom
    if (isset($publisher_options['custom_categories'])) {
      $custom_categories = $publisher_options['custom_categories'];
      sort($custom_categories);
      foreach ($custom_categories as $custom_category) {
        $new_categories[] = 'custom_' . $custom_category['slug'];
      }
    }

    //have categories changed?
    if (array_diff($new_categories, $old_categories) || array_diff($old_categories, $new_categories)) {
      $slugs_to_remove = array_diff($old_categories, $new_categories);
      $slugs_to_add    = array_diff($new_categories, $old_categories);
      $this->transcript[$casasync_id]['categories_changed']['removed_category'] = $slugs_to_remove;
      $this->transcript[$casasync_id]['categories_changed']['added_category'] = $slugs_to_add;

      //get the custom labels they need them
      $custom_categorylabels = array();
      if (isset($publisher_options['custom_categories'])) {
        foreach ($publisher_options['custom_categories'] as $custom) {
          $custom_categorylabels[$custom['slug']] = $custom['label'];
        }
      }

      //make sure the categories exist first
      foreach ($slugs_to_add as $new_term_slug) {
        $label = (array_key_exists($new_term_slug, $custom_categorylabels) ? $custom_categorylabels[$new_term_slug] : false);
        $this->setCasasyncCategoryTerm($new_term_slug, $label);
      }

      //add the new ones
      $category_terms = get_terms( array('casasync_category'), array('hide_empty' => false));
      foreach ($category_terms as $term) {
        if (in_array($term->slug, $new_categories)) {
          $connect_term_ids[] = (int) $term->term_id;
        }
      }
      if ($connect_term_ids) {
        wp_set_object_terms( $wp_post->ID, $connect_term_ids, 'casasync_category' );
      }
    }

  }

  public function addToLog($transcript){
    $dir = CASASYNC_CUR_UPLOAD_BASEDIR  . '/casasync/logs';
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents($dir."/".date('Y M').'.log', "\n".json_encode(array(date('Y-m-d H:i') => $transcript)), FILE_APPEND);
  }

  public function casasyncImport(){
    if ($this->getImportFile()) {
      
      
      
      if (is_admin()) {
        $this->updateOffers();
        echo '<div id="message" class="updated"><p>Casasync <strong>updated</strong>.</p><pre>' . print_r($this->transcript, true) . '</pre></div>';
      } else {
        //do task in the background
        add_action('asynchronous_import', array($this,'updateOffers'));
        wp_schedule_single_event(time(), 'asynchronous_import');
      }
    }
  }


  public function updateOffers(){
    $this->renameImportFileTo(CASASYNC_CUR_UPLOAD_BASEDIR  . '/casasync/import/data-done.xml');
    set_time_limit(300);
    global $wpdb;
    $found_posts = array();

    $xml = simplexml_load_file($this->getImportFile(), 'SimpleXMLElement', LIBXML_NOCDATA);
    foreach ($xml->property as $property) {
      //make main language first and single out if not multilingual
      $xmloffers = array();
      $i = 0;
      foreach ($property->offer as $offer) {
        $i++;
        if ($offer['lang'] == $this->getMainLang()) {
          $xmloffers[0] = $offer;
        } else {
          if ($this->hasWPML()) {
            $xmloffers[$i] = $offer;
          }
        }
      }
      $offer_pos = 0;
      $first_offer_trid = false;
      foreach ($xmloffers as $xmloffer) {
        $offer_pos++;


        //is it already in db
        $casasync_id = $property['id'] . $xmloffer['lang'];

        $the_query = new \WP_Query( 'post_type=casasync_property&suppress_filters=true&meta_key=casasync_id&meta_value=' . $casasync_id );
        $wp_post = false;
        while ( $the_query->have_posts() ) :
          $the_query->the_post();
          global $post;
          $wp_post = $post;
        endwhile;
        wp_reset_postdata();

        //if not create a basic property
        if (!$wp_post) {
          $this->transcript[$casasync_id]['action'] = 'new';
          $the_post['post_title'] = 'unsaved property';
          $the_post['post_content'] = 'unsaved property';
          $the_post['post_status'] = 'pending';
          $the_post['post_type'] = 'casasync_property';
          $insert_id = wp_insert_post($the_post);
          update_post_meta($insert_id, 'casasync_id', $casasync_id);
          $wp_post = get_post($insert_id, OBJECT, 'raw');
        }
        $found_posts[] = $wp_post->ID;
        $this->updateOffer($casasync_id, $offer_pos, $property, $xmloffer, $wp_post);

      }
    }

    //3. remove all the unused properties
    $properties_to_remove = get_posts(  array(
      'suppress_filters'=>true,
      'language'=>'ALL',
      'numberposts' =>  100,
      'exclude'     =>  $found_posts,
      'post_type'   =>  'casasync_property',
      'post_status' =>  'publish'
      )
    );
    foreach ($properties_to_remove as $prop_to_rm) {
      //remove the attachments
      $attachments = get_posts( array(
        'suppress_filters'=>true,
        'language'=>'ALL',
        'post_type'      => 'attachment',
        'posts_per_page' => -1,
        'post_parent'    => $prop_to_rm->ID,
        'exclude'        => get_post_thumbnail_id()
      ) );
      if ( $attachments ) {
        foreach ( $attachments as $attachment ) {
          $attachment_id = $attachment->ID;
        }
      }
      wp_trash_post($prop_to_rm->ID);
      $this->transcript['properties_removed'] = count($properties_to_remove);
    }

    //flush_rewrite_rules();

    $this->addToLog($this->transcript);
  }

  public function simpleXMLget($node, $fallback = false){
    if ($node) {
      $result = $node->__toString();
      if ($result) {
        return $result;
      }
    }
    return $fallback;
  }


  public function updateOffer($casasync_id, $offer_pos, $property, $xmloffer, $wp_post){
    $publisher_options = $this->publisherOptionsXMLtoArray($xmloffer->publisherSettings);

    //lang
    $this->updateInsertWPMLconnection($offer_pos, $wp_post, $xmloffer['lang'], $casasync_id);

    /* main post data */
    $new_main_data = array(
      'ID'            => $wp_post->ID,
      'post_title'    => $xmloffer->name->__toString(),
      'post_content'  => $this->extractDescription($xmloffer),
      'post_status'   => 'publish',
      'post_type'     => 'casasync_property',
      'post_excerpt'  => $xmloffer->excerpt->__toString(),
      'post_date'     => date('Y-m-d H:i:s', strtotime(($property->software->creation->__toString() ? $property->software->creation->__toString() : $property->software->lastUpdate->__toString() ) )),
      //'post_modified' => date('Y-m-d H:i:s', strtotime($property->software->lastUpdate->__toString())),
    );

    $old_main_data = array(
      'ID'            => $wp_post->ID,
      'post_title'    => $wp_post->post_title   ,
      'post_content'  => $wp_post->post_content ,
      'post_status'   => $wp_post->post_status  ,
      'post_type'     => $wp_post->post_type    ,
      'post_excerpt'  => $wp_post->post_excerpt ,
      'post_date'     => $wp_post->post_date    ,
      //'post_modified' => $wp_post->post_modified,
    );
    if ($new_main_data != $old_main_data) {
      foreach ($old_main_data as $key => $value) {
        if ($new_main_data[$key] != $old_main_data[$key]) {
          $this->transcript[$casasync_id]['main_data'][$key]['from'] = $old_main_data[$key];
          $this->transcript[$casasync_id]['main_data'][$key]['to'] = $new_main_data[$key];
        }
      }
      //persist change
      $newPostID = wp_insert_post($new_main_data);

    }


    /* Post Metas */
    $old_meta_data = array();
    $meta_values = get_post_meta($wp_post->ID, null, true);
    foreach ($meta_values as $key => $meta_value) {
      $old_meta_data[$key] = $meta_value[0];
    }

    $new_meta_data = array();
    $casasync_visitInformation = $property->visitInformation->__toString();
    $casasync_property_url = $property->url->__toString();
    $new_meta_data['casasync_property_address_country']       = $this->simpleXMLget($property->address->country);
    $new_meta_data['casasync_property_address_locality']      = $this->simpleXMLget($property->address->locality);
    $new_meta_data['casasync_property_address_region']        = $this->simpleXMLget($property->address->region);
    $new_meta_data['casasync_property_address_postalcode']    = $this->simpleXMLget($property->address->postalCode);
    $new_meta_data['casasync_property_address_streetaddress'] = $this->simpleXMLget($property->address->street);
    $new_meta_data['casasync_property_address_streetnumber']  = $this->simpleXMLget($property->address->streetNumber);
    if ($property->geo) {
      $new_meta_data['casasync_property_geo_latitude']          = (float) $this->simpleXMLget($property->geo->latitude);
      $new_meta_data['casasync_property_geo_longitude']         = (float) $this->simpleXMLget($property->geo->longitude);
    }
    $new_meta_data['casasync_start']                          = $this->simpleXMLget($xmloffer->start);
    $new_meta_data['casasync_referenceId']                    = $this->simpleXMLget($property->referenceId);
    if ($xmloffer->seller && $xmloffer->seller->organization) {
      foreach ($xmloffer->seller->organization->phone as $number) {
        if ($number['type'] == 'direct') {
          $new_meta_data['seller_org_phone_direct'] = $number->__toString();
        } elseif($number['type'] == 'central') {
          $new_meta_data['seller_org_phone_central'] = $number->__toString();
        } elseif ($number['type'] == 'mobile') {
          $new_meta_data['seller_org_phone_mobile'] = $number->__toString();
        } else {
          
        }
      }

      $new_meta_data['seller_org_legalname']                     = $this->simpleXMLget($xmloffer->seller->organization->legalName          );
      $new_meta_data['seller_org_brand']                         = $this->simpleXMLget($xmloffer->seller->organization->brand              );
      if ($xmloffer->seller->organization->address) {
        $new_meta_data['seller_org_address_country']               = $this->simpleXMLget($xmloffer->seller->organization->address->Country            );
        $new_meta_data['seller_org_address_locality']              = $this->simpleXMLget($xmloffer->seller->organization->address->locality           );
        $new_meta_data['seller_org_address_region']                = $this->simpleXMLget($xmloffer->seller->organization->address->region             );
        $new_meta_data['seller_org_address_postalcode']            = $this->simpleXMLget($xmloffer->seller->organization->address->postalCode         );
        $new_meta_data['seller_org_address_postofficeboxnumber']   = $this->simpleXMLget($xmloffer->seller->organization->address->postOfficeBoxNumber);
        $new_meta_data['seller_org_address_streetaddress']         = $this->simpleXMLget($xmloffer->seller->organization->address->street             ).' '.
                                                                     $this->simpleXMLget($xmloffer->seller->organization->address->streetNumber       );
      }
    }


    //urls
    $the_urls = array();
    if ($xmloffer->url) {
      foreach ($xmloffer->url as $url) {
        $href = $url->__toString();
        $label = (isset($url['label']) && $url['label'] ? $url['label'] : false);
        $title = (isset($url['title']) && $url['title'] ? $url['title'] : false);
        $rank =  (isset($url['rank'])  && (int) $url['rank'] ? (int) $url['rank'] : false);
        if ($rank ) {
          $the_urls[$rank] = array(
            'href' => $href,
            'label' => ($label ? $label : $href),
            'title' => ($title ? $title : $href)
          );
        } else {
          $the_urls[] = array(
            'href' => $href,
            'label' => ($label ? $label : $href),
            'title' => ($title ? $title : $href)
          );
        }
      }
      ksort($the_urls);
      $new_meta_data['the_urls'] = $the_urls;
    }


    //tags
    $the_tags = array();
    if ($xmloffer->tags) {
      foreach ($xmloffer->tags->tag as $tag) {
        $the_tags[] = $this->simpleXMLget($tag);
      }
    }
    $new_meta_data['the_tags'] = $the_tags;



    $offer_type     = $this->simpleXMLget($xmloffer->type);
    $new_meta_data['price_currency'] = $this->simpleXMLget($xmloffer->priceCurrency);

    if ($xmloffer->availability) {
      $new_meta_data['availability'] = $this->simpleXMLget($xmloffer->availability);
      /*if ($xmloffer->availability['title']) {
        $new_meta_data['availability_label'] = $this->simpleXMLget($xmloffer->availability['title']);
      }*/
    }

    //prices 
    // is_object($new_meta_data['price_timesegment']) should be fixed!!!!!!!!!!!!
    if ($xmloffer->price) {
      $new_meta_data['price_timesegment'] = ($xmloffer->price['timesegment'] ? $xmloffer->price['timesegment']->__toString() : '');
      if (!in_array($new_meta_data['price_timesegment'], array('m','w','d','y','h','infinite')) || is_object($new_meta_data['price_timesegment'])) {
        $new_meta_data['price_timesegment'] = ($offer_type == 'rent' ? 'm' : 'infinite');
      }
      $new_meta_data['price_propertysegment'] = ($xmloffer->price['propertysegment'] ? $xmloffer->price['propertysegment']->__toString() : '');
      if (!in_array($new_meta_data['price_propertysegment'], array('m2','km2','full')) || is_object($new_meta_data['price_propertysegment'])) {
        $new_meta_data['price_propertysegment'] = 'full';
      }
      $new_meta_data['price'] = (float) $xmloffer->price->__toString();
    }

    if ($xmloffer->netPrice) {
      $new_meta_data['netPrice_timesegment'] = ($xmloffer->netPrice['timesegment'] ? $xmloffer->netPrice['timesegment']->__toString() : '');
      if (!in_array($new_meta_data['netPrice_timesegment'], array('m','w','d','y','h','infinite')) || is_object($new_meta_data['netPrice_timesegment'])) {
        $new_meta_data['netPrice_timesegment'] = ($offer_type == 'rent' ? 'm' : 'infinite');
      }
      $new_meta_data['netPrice_propertysegment'] = ($xmloffer->netPrice['propertysegment'] ? $xmloffer->netPrice['propertysegment']->__toString() : '');
      if (!in_array($new_meta_data['netPrice_propertysegment'], array('m2','km2','full')) || is_object($new_meta_data['netPrice_propertysegment'])) {
        $new_meta_data['netPrice_propertysegment'] = 'full';
      }
      $new_meta_data['netPrice'] = (float) $xmloffer->netPrice->__toString();
    }

    if ($xmloffer->grossPrice) {
      $new_meta_data['grossPrice_timesegment'] = ($xmloffer->grossPrice['timesegment'] ? $xmloffer->grossPrice['timesegment']->__toString() : '');
      if (!in_array($new_meta_data['grossPrice_timesegment'], array('m','w','d','y','h','infinite')) || is_object($new_meta_data['grossPrice_timesegment'])) {
        $new_meta_data['grossPrice_timesegment'] = ($offer_type == 'rent' ? 'm' : 'infinite');
      }
      $new_meta_data['grossPrice_propertysegment'] = ($xmloffer->grossPrice['propertysegment'] ? $xmloffer->grossPrice['propertysegment']->__toString() : '');
      if (!in_array($new_meta_data['grossPrice_propertysegment'], array('m2','km2','full')) || is_object($new_meta_data['grossPrice_propertysegment'])) {
        $new_meta_data['grossPrice_propertysegment'] = 'full';
      }
      $new_meta_data['grossPrice'] = (float) $xmloffer->grossPrice->__toString();
    }


    $extraPrice = array();
    if($xmloffer->extraCost){
      foreach ($xmloffer->extraCost as $extraCost) {
        $propertysegment = '';
        $timesegment     = $extraCost['timesegment'];

        if (!in_array($timesegment, array('m','w','d','y','h','infinite'))) {
          $timesegment = ($offer_type == 'rent' ? 'm' : 'infinite');
        }
        $propertysegment = $extraCost['propertysegment'];
        if (!in_array($propertysegment, array('m2','km2','full'))) {
          $propertysegment = 'full';
        }
        if (is_object($propertysegment)) {
          $propertysegment = $propertysegment->__toString(); 
        }
        $the_extraPrice = (float) $extraCost->__toString();

        $extraPrice[] = array(
          'price' => $the_extraPrice,
          'title' => (string) $extraCost['title'],
          'timesegment' => $timesegment->__toString(),
          'propertysegment' => $propertysegment,
          'currency' => $new_meta_data['price_currency'],
          'frequency' => 1
        );
      }
      $new_meta_data['extraPrice'] = $extraPrice;
    }

    //price for order
    $tmp_price      = (array_key_exists('price', $new_meta_data)      && $new_meta_data['price'] !== 0)      ? ($new_meta_data['price'])      :(9999999999);
    $tmp_grossPrice = (array_key_exists('grossPrice', $new_meta_data) && $new_meta_data['grossPrice'] !== 0) ? ($new_meta_data['grossPrice']) :(9999999999);
    $tmp_netPrice   = (array_key_exists('netPrice', $new_meta_data)   && $new_meta_data['netPrice'] !== 0)   ? ($new_meta_data['netPrice'])   :(9999999999);
    $new_meta_data['priceForOrder'] = str_pad($tmp_netPrice, 10, 0, STR_PAD_LEFT) . str_pad($tmp_grossPrice, 10, 0, STR_PAD_LEFT) . str_pad($tmp_price, 10, 0, STR_PAD_LEFT);

    //persons
    $persons = $this->personsXMLtoArray($xmloffer->seller);
    $new_meta_data = array_merge($new_meta_data, $persons);

    //nuvals
    $numericValues = $this->numvalsXMLtoArray($property->numericValues);
    $new_meta_data = array_merge($new_meta_data, $numericValues);

    //features
    $new_meta_data['casasync_features'] = $this->featuresXMLtoJson($property->features);

    //clean up arrays
    foreach ($old_meta_data as $key => $value) {
      if (!in_array($key, $this->meta_keys)) {
        unset($old_meta_data[$key]);
      }
    }
    ksort($old_meta_data);
    foreach ($new_meta_data as $key => $value) {
      if (!in_array($key, $this->meta_keys)) {
        $this->transcript['error']['unknown_metakeys'][$key] = $value;
      }
      if (!$value || !in_array($key, $this->meta_keys)) {
        unset($new_meta_data[$key]);
      }
    }
    ksort($new_meta_data);


    if ($new_meta_data != $old_meta_data) {
      foreach ($this->meta_keys as $key) {
        if (in_array($key, array('the_urls', 'the_tags', 'extraPrice'))) {
          if (isset($new_meta_data[$key])) {
            $new_meta_data[$key] = $new_meta_data[$key];
          }
        }
        $newval = (isset($new_meta_data[$key]) ? $new_meta_data[$key] : '');
        $oldval = (isset($old_meta_data[$key]) ? maybe_unserialize($old_meta_data[$key]) : '');
        if (($oldval || $newval) && $oldval != $newval) {
          update_post_meta($wp_post->ID, $key, $newval);
          $this->transcript[$casasync_id]['meta_data'][$key]['from'] = $oldval;
          $this->transcript[$casasync_id]['meta_data'][$key]['to'] = $newval;
        }
      }

      //remove supurflous meta_data
      foreach ($old_meta_data as $key => $value) {
        if (!isset($new_meta_data[$key])) {
          //remove
          delete_post_meta($wp_post->ID, $key, $value);
          $this->transcript[$casasync_id]['meta_data'][$key] = 'removed';

        }
      }
    }

    $this->setOfferCategories($wp_post, $property->category, $publisher_options, $casasync_id);
    $this->setOfferSalestype($wp_post, $this->simpleXMLget($xmloffer->type, false), $casasync_id);
    $this->setOfferAvailability($wp_post, $this->simpleXMLget($xmloffer->availability, false), $casasync_id);
    $this->setOfferLocalities($wp_post, $property->address, $casasync_id);
    $this->setOfferAttachments($xmloffer->attachments , $wp_post, $property['id']->__toString(), $casasync_id);
    

  }
}
