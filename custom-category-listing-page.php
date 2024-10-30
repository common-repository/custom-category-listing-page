<?php
/**
 * Plugin Name: Custom Category Listing Page
 * Plugin URI: https://wordpress.org/plugins/custom-category-listing-page/
 * Description: A simple shortcode to list the Products And Posts for each Category by Order ex: [post_listing]
 * Version: 2.0.5
 * Author: TRooInbound Pvt. Ltd
 * Author URI: https://www.trooinbound.com
 * License: GPL3
 */
class TROO_CCL{
  public function __construct() {
    add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_front_scripts' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'register_plugin_admin_styles' ) );
    add_action( 'add_meta_boxes', array( $this, 'plc_post_type_metabox') ); 
    add_action('save_post', array( $this, 'plc_post_type_save_meta')); 
    add_action( 'add_meta_boxes', array( $this, 'plc_number_post_metabox') ); 
    add_action('save_post', array( $this, 'plc_number_post_save_meta'));
    add_action( 'add_meta_boxes', array( $this, 'plc_category_metabox') );
    add_action('save_post', array( $this, 'plc_category_save_meta'));
    add_action( 'add_meta_boxes', array( $this, 'plc_order_by_metabox') );
    add_action('save_post', array( $this, 'plc_order_by_save_meta'));
    add_action( 'add_meta_boxes', array( $this, 'plc_order_metabox') ); 
    add_action('save_post', array( $this, 'plc_order_save_meta'));
    add_shortcode('post_listing', array( $this, 'cat_list_taxonomy'));
  }
  public function register_plugin_admin_styles(){
    wp_enqueue_style( 'troo-style', plugin_dir_url( __FILE__ )  . 'assets/admin/css/style.css', array(), 1.0 );
    // wp_enqueue_style( 'toast-popup', TROO_NOTIFICATION_CSS . 'jquery.toast.css', array(), 1.0 );
    // wp_enqueue_script( 'toast-popup', TROO_NOTIFICATION_JS . 'jquery.toast.js', array( 'jquery' ), 1.0,true );
    wp_enqueue_script( 'admin-custom', plugin_dir_url( __FILE__ )  . 'assets/admin/js/custom.js', array( 'jquery' ), 1.0 ,true);
}
  public function register_plugin_front_scripts(){
    wp_enqueue_style( 'font-awesome-style', plugin_dir_url( __FILE__ )  . 'assets/front-end/css/font-awesome.min.css', array(), 1.0 );
    
    wp_enqueue_style( 'troo-pro-style', plugin_dir_url( __FILE__ )  . 'assets/front-end/css/style.css', array(), 1.5,'all' );
  }

  public function plc_post_type_metabox() {
  add_meta_box( 'plc_post_type_metabox', // ID attribute of metabox
                  'Select Post Type',// Title of metabox visible to user
                  array( $this, 'plc_post_type_metabox_callback' ),// public function that prints box in wp-admin
                  'page',// Show box for posts, pages, custom, etc.
                  'side',// Where on the page to show the box
                  'high' );   
  }
  public function plc_post_type_metabox_callback($post){
    wp_nonce_field( 'plc_post_type_metabox_nonce', 'plc_post_type_nonce' ); 
    global $post;
      $troo_post_type=get_post_meta($post->ID,'page_post_type',true); ?>
      <span class="box-radio"><input type="radio" name="troo_post_type" id="post" value="post" <?php echo ($troo_post_type=='post')?'checked':'' ?><?php echo ($troo_post_type=='')?'checked':'' ?>>Post</span>
      <?php  if (class_exists('WooCommerce')) { ?>
      <span class="box-radio"><input type="radio" name="troo_post_type" id="product" value="product" <?php echo ($troo_post_type=='product')?'checked':'' ?>>Product</span>
      <?php } ?>
    <?php
  }
  public function plc_post_type_save_meta($post_id){
    if( !isset( $_POST['plc_post_type_nonce'] ) || !wp_verify_nonce( $_POST['plc_post_type_nonce'],'plc_post_type_metabox_nonce') ) 
     return;
    if ( !current_user_can( 'edit_post', $post_id ))
     return;
    if ( isset($_POST['troo_post_type']) ) {
        $troo_post_type=sanitize_text_field($_POST['troo_post_type']);
       update_post_meta($post_id, 'page_post_type', $troo_post_type);
    }
 }
  
  public function plc_number_post_metabox() {
    add_meta_box( 'plc_number_post_metabox', // ID attribute of metabox
                    'Enter Number of listed posts',// Title of metabox visible to user
                    array( $this, 'plc_number_post_metabox_callback' ),// public function that prints box in wp-admin
                    'page',// Show box for posts, pages, custom, etc.
                    'side',// Where on the page to show the box
                    'high' );   
    }
    public function plc_number_post_metabox_callback($post){
      wp_nonce_field( 'plc_number_post_metabox_nonce', 'plc_number_post_nonce' ); 
       global $post;
         $troo_number_of_posts=get_post_meta($post->ID,'troo_number_of_posts',true); 
         if($troo_number_of_posts){
           $troo_number_of_posts=$troo_number_of_posts;
         }else{
          $troo_number_of_posts='6';
         }?>
        <div class="components-panel__row">
          <div class="components-base-control editor-page-attributes__order">
            <div class="components-base-control__field">
              <label class="components-base-control__label" for="inspector-text-control-1">Number of listed posts</label>
              <input class="components-text-control__input" type="number" name="troo_number_of_posts" id="inspector-text-control-1" size="6" value="<?= $troo_number_of_posts; ?>">
             </div>
           </div>
         </div>
       <?php
     }
     public function plc_number_post_save_meta($post_id){
      if( !isset( $_POST['plc_number_post_nonce'] ) || !wp_verify_nonce( $_POST['plc_number_post_nonce'],'plc_number_post_metabox_nonce') ) 
       return;
      if ( !current_user_can( 'edit_post', $post_id ))
       return;
      if ( isset($_POST['troo_number_of_posts']) ) {
          $troo_post_type=sanitize_text_field($_POST['troo_number_of_posts']);
         update_post_meta($post_id, 'troo_number_of_posts', $troo_post_type);
      }
   }
 
  public function plc_category_metabox() {
  add_meta_box( 'plc_category_metabox', // ID attribute of metabox
                  'Categories',// Title of metabox visible to user
                  array( $this, 'plc_category_metabox_callback' ),
                  'page',// Show box for posts, pages, custom, etc.
                  'side',// Where on the page to show the box
                  'high' );   
  }
public function plc_category_metabox_callback( $post ) { 
  wp_nonce_field( 'plc_category_metabox_nonce', 'plc_category_nonce' ); 
      global $post;
      $troo_post_type=get_post_meta($post->ID,'page_post_type',true);
     
      $defaults_taxonomy=get_option('troo_taxonomy');
      
      if($troo_post_type){
        if($troo_post_type=="post"){
          $troo_taxonomy="category";
        }
        else{
          $troo_taxonomy="product_cat";
        }
      }
      else{
        $troo_taxonomy=get_option('troo_taxonomy');
      }
      $defaults = array( 'taxonomy' => 'category' );
      if ( ! isset( $box['args'] ) || ! is_array( $box['args'] ) ) {
      $args = array();
      } else {
        $args = $box['args'];
      }
      $r = wp_parse_args( $args, $defaults );
      $tax_name = esc_attr( $r['taxonomy'] );
      $taxonomy = get_taxonomy( $r['taxonomy'] );
    ?>
     <div id="taxonomy-<?php echo $tax_name; ?>" class="categorydiv" <?php if($troo_taxonomy=="product_cat"){echo "style='display:none'";} ?>>
        <ul id="<?php echo $tax_name; ?>-tabs" class="category-tabs">
          <li class="tabs"><a href="#<?php echo $tax_name; ?>-all"><?php echo $taxonomy->labels->all_items; ?></a></li>
        </ul>
        <div id="<?php echo $tax_name; ?>-pop" class="tabs-panel" style="display: none;">
          <ul id="<?php echo $tax_name; ?>checklist-pop" class="categorychecklist form-no-clear" >
            <?php $popular_ids = wp_popular_terms_checklist( $tax_name ); ?>
          </ul>
        </div>
        <div id="<?php echo $tax_name; ?>-all" class="tabs-panel">
          <?php
              $name = ( $tax_name == 'category' ) ? 'post_category' : 'tax_input[' . $tax_name . ']';
              //echo "<input type='hidden' name='{$name}[]' value='' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.          
              $c_data=get_post_meta($post->ID, 'page_category_id', true);
          ?>
          <ul id="<?php echo $tax_name; ?>checklist" data-wp-lists="list:<?php echo $tax_name; ?>" class="categorychecklist form-no-clear">
            <?php wp_terms_checklist( $post->ID, array( 'taxonomy' => $tax_name, 'popular_cats' => $popular_ids,'selected_cats'=>$c_data,'checked_ontop' => false) ); ?>
          </ul>
        </div>
    </div><?php
   if (class_exists('WooCommerce')) { 
      $defaults = array( 'taxonomy' => 'product_cat' );
        if ( ! isset( $box['args'] ) || ! is_array( $box['args'] ) ) {
        $args = array();
        } else {
          $args = $box['args'];
        }
        $r = wp_parse_args( $args, $defaults );
        $tax_name = esc_attr( $r['taxonomy'] );
       // echo $tax_name;
        $taxonomy = get_taxonomy( $r['taxonomy'] );
      ?>
      <div id="taxonomy-<?php echo $tax_name; ?>" class="categorydiv" <?php if($troo_taxonomy=="category"){echo "style='display:none'";} ?>>
          <ul id="<?php echo $tax_name; ?>-tabs" class="category-tabs">
            <li class="tabs"><a href="#<?php echo $tax_name; ?>-all"><?php echo $taxonomy->labels->all_items; ?></a></li>
          </ul>
          <div id="<?php echo $tax_name; ?>-pop" class="tabs-panel" style="display: none;">
            <ul id="<?php echo $tax_name; ?>checklist-pop" class="categorychecklist form-no-clear" >
              <?php $popular_ids = wp_popular_terms_checklist( $tax_name ); ?>
            </ul>
          </div>
          <div id="<?php echo $tax_name; ?>-all" class="tabs-panel">
            <?php
            
                //echo "<input type='hidden' name='{$name}[]' value='' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.          
                $c_data=get_post_meta($post->ID, 'page_category_id', true);
            ?>
            <ul id="<?php echo $tax_name; ?>checklist" data-wp-lists="list:<?php echo $tax_name; ?>" class="categorychecklist form-no-clear">
              <?php wp_terms_checklist( $post->ID, array( 'taxonomy' => $tax_name, 'popular_cats' => $popular_ids,'selected_cats'=>$c_data,'checked_ontop' => false) ); ?>
            </ul>
          </div>
      </div>
    <?php }
  }
  public function plc_get_cat_slug($cat_id) {
    $cat_id = (int) $cat_id;
    $category = &get_category($cat_id);
    return $category->slug;
  }
  public function plc_get_product_cat_slug($cat_id){
    if( $term = get_term_by( 'id', $cat_id, 'product_cat' ) ){
      return $term->slug;
    }
  }
  public function plc_category_save_meta( $post_id ) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
    return;
    }
    if( !isset( $_POST['plc_category_nonce'] ) || !wp_verify_nonce( $_POST['plc_category_nonce'],'plc_category_metabox_nonce') ) 
      return;
    if ( !current_user_can( 'edit_post', $post_id ))
      return;
      if(isset($_POST['troo_post_type']) && $_POST['troo_post_type']=="post"){
        if ( isset($_POST['post_category']) ) {  
            $cat_data=$_POST['post_category'];
            array_walk($cat_data, function(&$value, &$key) {
              $key = sanitize_text_field($value);
          });
            if (count($cat_data) > 1){
                $stop = true;
                update_post_meta($post_id, 'page_category_id', $cat_data);
            }
            if (is_array($cat_data)) {
                foreach ($cat_data as &$c) {
                    $cat_slug[]=$this->plc_get_cat_slug($c);
                }
                $cat_list=implode(",",$cat_slug);
                update_post_meta($post_id, 'page_category_slug', $cat_list);  
                unset($cat_data );
            } else {
                $cat_data = $cat_data;
            }   
        }
      }
      else if(isset($_POST['troo_post_type']) && $_POST['troo_post_type']=="product"){
        if ( isset($_POST['tax_input']['product_cat']) ) {  
          $cat_data=$_POST['tax_input']['product_cat'];
            array_walk($cat_data, function(&$value, &$key) {
              $key = sanitize_text_field($value);
          });
            if (count($cat_data) > 1){
                $stop = true;
                update_post_meta($post_id, 'page_category_id', $cat_data);
            }
            if (is_array($cat_data)) {
                foreach ($cat_data as &$c) {
                    $cat_slug[]=$this->plc_get_product_cat_slug($c);
                }
                $cat_list=implode(",",$cat_slug);
                update_post_meta($post_id, 'page_category_slug', $cat_list);  
                unset($cat_data );
            } else {
                $cat_data = $cat_data;
            }   
        }
    }
  }
  public function plc_order_by_metabox() {
        add_meta_box( 'plc_order_by_metabox',// ID attribute of metabox
                      'Categories Order By',// Title of metabox visible to user
                      array( $this, 'plc_order_by_metabox_callback' ),
                      'page',// Show box for posts, pages, custom, etc.
                      'side',// Where on the page to show the box
                      'high');   
    }
    public function plc_order_by_metabox_callback($post){
        wp_nonce_field( 'plc_order_by_metabox_nonce', 'plc_order_by_nonce' );
        $post_id=$post->ID;
        $order_by=array('date','title','ID','rand');
        $selected_order_by=get_post_meta($post_id, 'page_cat_order_by', true);
        //echo $selected_order_by;
        ?>
        <select style="width:100%" name="cat_order_by" id="cat_order_by">
          <option value="">-- Select Order by --</option>
          <?php foreach($order_by as $o){ 
            ?>
          <option value="<?php echo $o; ?>" <?php if($selected_order_by==$o){echo "selected='selected'";} ?>><?php echo $o; ?></option>
          <?php } ?>
         </select>
        <?php
    }
    public function plc_order_by_save_meta($post_id){
       if( !isset( $_POST['plc_order_by_nonce'] ) || !wp_verify_nonce( $_POST['plc_order_by_nonce'],'plc_order_by_metabox_nonce') ) 
        return;
       if ( !current_user_can( 'edit_post', $post_id ))
        return;
       if ( isset($_POST['cat_order_by']) ) {
          $cat_order_by=sanitize_text_field($_POST['cat_order_by']);
          update_post_meta($post_id, 'page_cat_order_by', $cat_order_by);
       }
    }
    public function plc_order_metabox_callback($post){
      wp_nonce_field( 'plc_order_metabox_nonce', 'plc_order_nonce' );
      $post_id=$post->ID;
      $order=array('desc','asc');
      $selected_order=get_post_meta($post_id, 'page_cat_order', true);
      //echo $selected_order;
      ?>
      <select style="width:100%" name="cat_order" id="cat_order">
        <option value="">-- Select Order --</option>
        <?php foreach($order as $o){ 
          ?>
        <option value="<?php echo $o; ?>" <?php   if($selected_order==$o){echo "selected='selected'";} ?>><?php echo $o; ?></option>
        <?php } ?>
       </select>
      <?php
  }
  public function plc_order_save_meta($post_id){
     if( !isset( $_POST['plc_order_nonce'] ) || !wp_verify_nonce( $_POST['plc_order_nonce'],'plc_order_metabox_nonce') ) 
      return;
     if ( !current_user_can( 'edit_post', $post_id ))
      return;
     if ( isset($_POST['cat_order']) ) {
         $cat_order=sanitize_text_field($_POST['cat_order']);
        update_post_meta($post_id, 'page_cat_order', $cat_order);
     }
  }
  public function plc_order_metabox() {
    add_meta_box( 'plc_order_metabox',// ID attribute of metabox
                  'Categories Order',// Title of metabox visible to user
                  array( $this, 'plc_order_metabox_callback' ),
                  'page',// Show box for posts, pages, custom, etc.
                  'side',// Where on the page to show the box
                  'high');   
  }
  public function cat_list_taxonomy(){
   global $post;
   $selected_template= get_post_meta( $post->ID, 'selected-template', true );
   $selected_cat= get_post_meta( $post->ID, 'page_category_slug', true );
   $selected_order_by=get_post_meta($post->ID, 'page_cat_order_by', true);
   $selected_order=get_post_meta($post->ID, 'page_cat_order', true);
   $selected_cat = explode(',', $selected_cat);
   global $post;
   $troo_post_type=get_post_meta($post->ID,'page_post_type',true);
  
   $defaults_taxonomy=get_option('troo_taxonomy');
   
   if($troo_post_type){
     if($troo_post_type=="post"){
       $troo_taxonomy="category";
       $troo_post_type="post";
     }
     else{
       $troo_taxonomy="product_cat";
       $troo_post_type="product";
     }
   }
   else{
     $troo_taxonomy=get_option('troo_taxonomy');
     $troo_post_type=get_option('troo_post_type');
   }
   $troo_number_of_posts=get_post_meta($post->ID,'troo_number_of_posts',true); 
   if($troo_number_of_posts){
    $troo_number_of_posts=$troo_number_of_posts;
    }else{
    $troo_number_of_posts='6';
    }
   $paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
       $args = array(
           'post_type' => $troo_post_type,
           'orderby'=>$selected_order_by,
           'order'=> $selected_order,
           'posts_per_page'	=> $troo_number_of_posts,
           'paged' => $paged,
           'post_status' => 'publish',
           'tax_query' => array(
               array(
                   'taxonomy' => $troo_taxonomy,
                   'field' => 'slug',
                   'terms' =>$selected_cat,
                   'operator' => 'IN'
               ),
            ),
        );
  $loop = new WP_Query($args);
  if($loop->have_posts() && (is_page())) {
           while($loop->have_posts()) : $loop->the_post();
             if(is_page()):
               include plugin_dir_path(__FILE__) . '/templates/default.php';
             endif;
           endwhile; ?>
            <?php 
                  // Protect against arbitrary paged values
                  $troo_number_of_posts=get_post_meta($post->ID,'troo_number_of_posts',true); 
                  if($troo_number_of_posts){
                   $troo_number_of_posts=$troo_number_of_posts;
                   }else{
                   $troo_number_of_posts='6';
                   }
                  $paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
                  
                  $args = array(
                      'post_type' => $troo_post_type,
                      'post_status'=>'publish',
                      'posts_per_page' => $troo_number_of_posts,
                      'paged' => $paged,
                      'tax_query' => array(
                        array(
                            'taxonomy' => $troo_taxonomy,
                            'field' => 'slug',
                            'terms' =>$selected_cat,
                            'operator' => 'IN'
                        ),
                    ),
                  );
                  
                  $the_query = new WP_Query($args);
                  ?>
                  
                  <?php if ( $the_query->have_posts() ) : ?>
                      
                      <?php while ( $the_query->have_posts() ) : $the_query->the_post();
                          // Post content goes here...
                      endwhile; ?>
                  
                      <div class="pagination">
                          <?php
                          echo paginate_links( array(
                              'format'  => 'page/%#%',
                              'current' => $paged,
                              'total'   => $the_query->max_num_pages,
                              'mid_size'        => 2,
                              'type'=> 'list',
                              'prev_text'       => __('<i class="fa fa-arrow-left"></i>'),
                              'next_text'       => __('<i class="fa fa-arrow-right"></i>')
                          ) );
                          ?>
                      </div>
                      
                  <?php endif; ?>
           <?php
  }
  }
}
new TROO_CCL;
function troo_ccl_options_install(){
  add_option('troo_post_type','post');
  add_option('troo_taxonomy','category');
}
register_activation_hook(__FILE__,'troo_ccl_options_install');