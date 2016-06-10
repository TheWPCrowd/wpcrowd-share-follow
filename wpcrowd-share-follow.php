<?php
/*
Plugin Name: Sharing and Following for WP Crowd
Plugin URI: http://thewpcrowd.com
Description: Adds sharing stats and buttons, follow netowrks to admin and front end
Version: 1.0
Author: Andrew Killen   
*/

/*
 * simple autoloader !  might replace later
 */
spl_autoload_register( 'wpcrowdsharefollow_autoloader' );
function wpcrowdsharefollow_autoloader( $class_name ) {
  
  if ( false !== strpos( $class_name, 'wpcrowdShareFollow' ) ) {      
    $classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
    $class_file = str_replace( '_', DIRECTORY_SEPARATOR, $class_name ) . '.php';
    require_once $classes_dir . $class_file;
  }
}


class wpcrowdShareFollow {
    protected $options = array();
    protected $options_name = "wpcrowd-share-follow-options";
    protected $nonce = "wpCrowdLovesToNonce";
    
    /**
     * The unique instance of the plugin.
     *
     * @var handlebars_templating
     */
    private static $instance;
 
    /**
     * Gets an instance of our plugin.
     *
     * @return formSecruity
     */
    final public static function get_instance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }
    
    private function __construct() {
       $this->options = get_option($this->options_name);
       // $this->options = false;
//       if($this->options === false){
//           $this->set_defaults();
//       }
       
       
       add_action("admin_menu", array($this, "admin_menu"));
       
       add_action('wp_enqueue_scripts', array($this, "scripts_and_styles") );
       
       add_filter('user_contactmethods',  array($this, 'modify_contact_methods') );
       
       add_shortcode( 'bartag', array($this, "quote_shortcode" ) );
    }
    
//    protected function set_defaults(){
//        $this->options = array(
//                "cache_bust"    => 'no',
//                "production"     => "no",
//                "youtube_count" => "yes",
//                "google_api"    => '',
//                "share_enabled_networks" => array (
//                    "facebook"      => "yes",
//                    "twitter"       => "yes",
//                    "googleplus"    => "yes",
//                    "whatsapp"      => "yes",
//                    "linkedin"      => "yes",
//                    "pinterest"     => "yes",
//                ),                
//            );
//        update_option($this->option_name, $this->options);
//    }
            
    function admin_menu(){
        add_menu_page( 'Share and Follow', 
                       'Share and Follow', 
                       'manage_options', 
                       'wpcrowd-share-follow', 
                       array($this, 'admin_page') );
    }
    
    function admin_page(){        
        $admin = new wpcrowdShareFollowAdmin($this->options, $this->options_name, $this->nonce);        
    }
    
    function scripts_and_styles(){
        $prod = '';
        if($this->options['production'] == 'yes'){
            $prod = ".min";
        }
        
        if(!is_admin()){
            wp_enqueue_style('crowd-share-follow-stylesheet',  plugins_url(  "style". $prod .".css",__FILE__) , array(), $this->cache_bust("/style{$prod}.css") , 'all');
            wp_enqueue_script('crowd-share-follow-script',  plugins_url( "js/scripts". $prod .".js",__FILE__) , array('jquery'), $this->cache_bust("/js/scripts{$prod}.js"), 1 );
            
            $args = array(
                'ajax_url'          => admin_url( 'admin-ajax.php' ) ,
                'nonce'             => wp_create_nonce( $this->nonce ),
                'ajax_callback'     => "get_googleplus",
                'google_api'        => $this->options['google_api'],
                'youtube_count'     => $this->options['youtube_count'],                
            );
            
            wp_localize_script( 'crowd-share-follow-script', 'sharesettings', $args );
            
        }
    }
    
    function cache_bust($file){
        $bust = null;
        if($this->options['cache_bust'] == 'yes'){
           $bust =  filemtime( get_template_directory() . $file);
        }        
        return $bust;
    }
    
    function modify_contact_methods($profile_fields) {
        $profile_fields['instagram'] = "Instagram url";
        $profile_fields['whatsapp'] = "WhatsApp (mobile number)";
        $profile_fields['pinterest'] = "Pinterest";
        return $profile_fields;
    }
    
    public function activate_hook(){
       include_once dirname( __FILE__ ) . '/install.php';        
    }
    
    public function share($id = false, $get_count = true){
        if(!$id){
            $id = get_the_ID();
        }
        $count = 'show-count';
        if($get_count != true){
            $count = '';
        }
        
        $uri = urlencode(get_permalink($id));
        $img = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()));
        $image = $img[0];
        $title = urlencode(get_the_title($id));
        $excerpt = get_the_excerpt();
        if (is_array($this->options['share_enabled_networks']) ){
            ?><ul class="shareing-links <?php echo $count ?>"><?php
            foreach($this->options['share_enabled_networks'] as $network => $value ):
                if($value == 'yes'):
                ?><li class="net-<?php echo $network; ?>">
                    <?php $output = str_replace(array("IMAGE", "URI", "TITLE", "EXCERPT"), array($image, $uri , $title, $excerpt), $this->get_share_url($network)); ?>            
                    <a href="<?php echo $output ?>" target="_blank"  class="icon-<?php echo $network; ?> share-button" data-link="<?php echo $output ?>" data-id="<?php the_ID() ?>">
                        <span><?php echo str_replace("_", " ", $network ); ?></span> 
                        <small></small>
                    </a>    
                </li>
                <?php endif;
            endforeach; ?>
            </ul><?php 
        }
        
    }

    protected function get_share_url($net){
        $share = array(  
        'facebook'      =>"http://www.facebook.com/sharer.php?u=URI&amp;t=TITLE",
            
        'googleplus'    => "https://plusone.google.com/_/+1/confirm?hl=en&amp;url=URI&amp;title=TITLE",
            
        'twitter'       => "http://twitter.com/share?url=URI&amp;text=TITLE",                
            
        'whatsapp'      => "whatsapp://send?text=TITLE URI",                
            
        'linkedin'      =>  "https://www.linkedin.com/shareArticle?mini=true&url=URI&title=TITLE&summary=EXCERPT", 
            
        'pinterest'     =>  "https://pinterest.com/pin/create/button/?url=URI&media=IMAGE&description=EXCERPT",  
            
        );
        
        return $share[$net];
    }
    
    
    /**
 * performs shortcode function for [quote]
 * @param array $atts
 * @return string
 */
 public function quote_shortcode($atts){
    $a = _rnw_shortcode_atts( array(
        'via'     =>  $this->options['twitter_via'],
        'side'    => 'center',
        'text'    => '',
        'cite'    => '',
        'url'     => '',
        'share'   => true,
      ), $atts );
    extract($a);
    
    if(empty($text)){
        return ;
    }
    // set up container for quote
    $html = "<div class='quote quote-side-$side'>";
        $html .= "<blockquote>$text</blockquote>";
        // setup the $cite if available
        if($cite != ''){
            $cite_text .= t("by") . " " . $cite;
            if($url != ''){
                $html .= "<span class='cite-text'><a href='$url' class='cite-url'>$cite_text</a></span>";
            } else {
                $html .= "<span class='cite-text'>".$cite_text."</span>";
            }
        }
        // setup twitter share name if available
        $twitterNameShare = "";
        if($via!=''){
            $twitterNameShare = "&via=" . $via;
        }
        // add the sharing if needed
        if($share == 'true'){                        
            $nid = get_the_ID();
            $current_url = url(current_path(), array('absolute' => TRUE));
            $networks = array(
                              "facebook" => "http://www.facebook.com/sharer.php?u=".  urlencode( $base_root . "/fbsharing/".$nid."?t=".$text ), 
                              "twitter" => "http://twitter.com/share?url=".  urlencode($current_url) ."&text=".urlencode($text).$twitterNameShare , 
                              'whatsapp' => "whatsapp://send?text=".rawurlencode($text ." : " . get_permalink()) 
                             );
            $html .= "<ul class='quote-share'>";
                foreach ($networks as $net => $link){
                    $netExtention = '';
                    if($net == 'facebook'){
                        $netExtention = '-Quote';
                    }
                    $html .= "<li><a target='_blank' href='$link' title='".t("Share this quote on ")."$net' class='icon-$net blockquote-icon' data-socialnetwork='".ucfirst($net).$netExtention."'><span>".ucfirst($net)."</span></a></li>";
                }
            $html .= "</ul>";    
        }
    // close everything properly when floated
    if($cite != ''){
        $html .= "<div class='clean'></div>";
    }
    // close blockquote region
    $html .= "</div>"; 
    // return content to short code
    return $html;
}


    
}
//
$wpcrowdsharefollow = wpcrowdShareFollow::get_instance();
//
register_activation_hook( __FILE__, array( $wpcrowdsharefollow , 'activate_hook' ) );
//
register_uninstall_hook(__FILE__, 'wpcrowdShareFollowUninstall');
//
function wpcrowdShareFollowUninstall(){
    include_once dirname( __FILE__ ) . '/delete.php'; 
}


function wpcrowd_author_follow(){
    $array_of_possible_social_networks = array(
        "url" => "home",
        "twitter" => "twitter",
        'facebook' => 'facebook',
        "googleplus" => 'googleplus',
        "pinterest" => 'pinterest',
        "instagram" => 'instagram',
        "whatsapp" => 'whatsapp',                
    );
    ?><ul class="follow-links"><?php
        foreach($array_of_possible_social_networks as $net => $icon){
            $value = get_the_author_meta($net);
            if($value){
                switch($net){
                    case 'twitter':
                        $value = "https://twitter.com/" . $value;
                        break;
                    case 'whatsapp':
                        $value = "tel:" . $value;
                        break;
                }
                
                ?><li><a target="_blank" class="icon-<?php echo $icon ?>" href="<?php echo $value ?>"><span><?php echo $net ?></span></a></li><?php
            }
        }
    ?></ul><?php
}

function wpcrowd_share($id = false, $get_count = true ){
    global $wpcrowdsharefollow;
    $wpcrowdsharefollow->share($id, $get_count);
}

