<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Admin
 *
 * @author Andrew Killen
 */
class wpcrowdShareFollowAdmin {
    protected $options = array();
    protected $options_name = '';
    protected $nonce = '';
    protected $nonce_field = 'cmn';
    
    
    
    function __construct($options = array(), $options_name = "", $nonce =""){
        
        if( empty( $options ) || $options_name == '' || $nonce == '' ){
      
         }
        
        $this->options = $options;
        $this->options_name = $options_name;
        $this->nonce = $nonce;  
        $this->check_save();
        $this->load_form();
        
    }
    
    protected function check_save(){
        if(isset($_POST)  && wp_verify_nonce( filter_input(INPUT_POST, $this->nonce_field) , $this->nonce) ){            
            $args = array(
                "cache_bust"    => FILTER_SANITIZE_STRING,
                "production"     => FILTER_SANITIZE_STRING,
                "youtube_count" => FILTER_SANITIZE_STRING,
                "google_api"    => FILTER_SANITIZE_STRING,
                "share_enabled_networks" => array (                    
                        'filter' => FILTER_SANITIZE_STRING, 
                        'flags' => FILTER_REQUIRE_ARRAY
                    ),
                );
            $this->options = filter_input_array(INPUT_POST, $args);         
            update_option($this->options_name, $this->options);
        }
    }
    
    protected function load_form(){
        ?><div class='wrap share-follow'>
            <h2>Share and Follow settings</h2>
            <form method='POST' action=''>
            <?php $this->radio_button_group("Implement Cache Busting", "cache_bust", $this->options['cache_bust']) ?>
            <?php $this->radio_button_group("Load minified scripts", "production",$this->options['production']) ?>
                <p>
                    <label for='google_api'>Youtube API key</label>
                    <input type='text' name='google_api' id="google_api" value="<?php echo $this->options['google_api']; ?>" />
                </p>   
                <?php $this->radio_button_group("Add youtube play count", "youtube_count",$this->options['youtube_count']) ?>
                <h2>Share networks enabled</h2>    
            <?php $this->radio_button_group("Facebook", "share_enabled_networks[facebook]", $this->options['share_enabled_networks']['facebook']) ?>    
            <?php $this->radio_button_group("Twitter", "share_enabled_networks[twitter]", $this->options['share_enabled_networks']['twitter']) ?>    
            <?php $this->radio_button_group("Google Plus ", "share_enabled_networks[googleplus]", $this->options['share_enabled_networks']['googleplus']) ?>    
            <?php $this->radio_button_group("Whats App", "share_enabled_networks[whatsapp]", $this->options['share_enabled_networks']['whatsapp']) ?>    
            <?php $this->radio_button_group("Linked In", "share_enabled_networks[linkedin]", $this->options['share_enabled_networks']['linkedin']) ?>    
            <?php $this->radio_button_group("Pinterest", "share_enabled_networks[pinterest]", $this->options['share_enabled_networks']['pinterest']) ?>                    
            <?php  wp_nonce_field( $this->nonce, $this->nonce_field) ?>   
                <input type='submit' value='save' class="button-primary" />    
            </form>
        </div>
<style>
    .share-follow h3, .share-follow label {font-size:14px; font-weight: normal; display: block}
    .share-follow .radio-buttons label{display:inline}
    .share-follow input[type='submit'] {margin-top: 2em}
</style>    
            
    <?php
    }
    
    protected function radio_button_group($title, $name, $setting = 'no'){
        
        ?><div class='radio-buttons'>
            <h3><?php echo ucfirst(str_replace("_", "", $title)) ?></h3>
            <input type="radio" name='<?php echo $name ?>' id='<?php echo $name ?>-yes' value='yes' 
                <?php if($setting == 'yes'){ echo "checked"; } ?>/> &nbsp; <label for='<?php echo $name ?>-yes'>Yes</label> &nbsp; 
            <input type="radio" name='<?php echo $name ?>' id='<?php echo $name ?>-no' value='no' 
                   <?php if($setting != 'yes'){ echo "checked"; } ?>/> &nbsp; <label for='<?php echo $name ?>-no'>No</label>
          </div><?php
    }
    
}
