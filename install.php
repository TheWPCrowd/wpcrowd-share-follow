<?php

if(!class_exists('wpcrowdShareFollow') || !defined('ABSPATH')){
 header('HTTP/1.0 403 Forbidden');
 exit('Forbidden');   
}


class installwpcrowdShareFollow {
    
    protected $options;
    protected $options_name;
            
    function __construct() {   
        $this->options_name = "wpcrowd-share-follow-options";
        $this->set_options_defaults();        
    }
    
    protected function set_options_defaults(){
        $this->options = get_option($this->options_name);
        if($this->options === false || empty($this->options)){
            $this->options = array(
                "cache_bust"    => 'no',
                "production"     => "no",
                "youtube_count" => "no",
                "google_api"    => '',
                "share_enabled_networks" => array (
                    "facebook"      => "yes",
                    "twitter"       => "yes",
                    "googleplus"    => "yes",
                    "whatsapp"      => "yes",
                    "linkedin"      => "yes",
                    "pinterest"     => "yes",
                ),                
            );
            update_option($this->option_name, $this->options);
        }
        
    }    
}

$installwpcrowdShareFollow = new installwpcrowdShareFollow();