<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of wpcrowdShareFollowStatsGeneral
 *
 * @author Andrew Killen
 */
class wpcrowdShareFollowStatsGeneral {
    
    protected $nonce = "wpCrowdLovesToNonce";
    protected $ok = false;
    protected $reply = array("success" => false);
    
    function __construct($nonce_text = false){
          
//        // forcing success
//          $this->ok = true;    
//          $this->reply['success'] = true;
            
            
        // checks if nonce is ok
        if($nonce_text === false){
            $this->reply['message']='security fail';
        } 
        else if(wp_verify_nonce( $nonce_text, $this->nonce )){
            $this->ok = true;    
            $this->reply['success']= true;
        } else {
            $this->reply['message']='security fail';
        }    
    }
    
    function add_stat_to_twitter($id){
        if(!$this->ok){
            return false;
        }
        $count = get_post_meta($id, "twitter_shares", true);
        update_post_meta($id, "twitter_shares", $count++ );
        return true;
    }
    
    function get_twitter_share_count($id){
        $count = get_post_meta($id, "twitter_shares", true);
        if($count == false){
            return 0;
        }
        return $count;
    }
    
    function get_google_share_count($id, $url){ 
        
        
        $wpcrowdShareFollowGoogleStats = new wpcrowdShareFollowGoogleStats($id, $url );
        
        return $wpcrowdShareFollowGoogleStats->return_count();
    }
    
    function get_linkedin_share_count($id, $url){    
        
        $wpcrowdShareFollowlinkedinStats = new wpcrowdShareFollowlinkedinStats($id, $url );
        
        return $wpcrowdShareFollowlinkedinStats->return_count();
    }
    
    function get_stats($id = false, $url = false){
        if($id === false || $url === false){
            $this->reply['success'] = false;
            $this->reply['message'] = "url or id missing";                                  
            return $this->reply;            
        } 
        $this->reply['data']['twitter'] = $this->get_twitter_share_count($id);
        // $this->reply['data']['googleplus'] = $this->get_google_share_count($id, $url);
        // $this->reply['data']['linkedin'] = $this->get_linkedin_share_count($id, $url);
        
        $this->reply['message']=  $url;
        return $this->reply;
    }
    
    public function get_success(){
        return $this->reply['success'];
    }
    
    public function get_reply(){
        return $this->reply;
    }
    
}
