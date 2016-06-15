<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of wpcrowdShareFollowLinkedinStats
 *
 * @author Andrew Killen
 */
class wpcrowdShareFollowLinkedinStats {
    protected $transient_name = "linkedinc";
    protected $count = 0;
    //put your code here
    function __construct($id = false, $url = false){
        if($id == false || $url == false){
            return 0;
        }
        
        $count = '';                
        
        $cache = get_transient($this->transient_name . $id);
        
        if($cache !== false){
            $count = $cache;
        
        }else{
            $count = $this->ask_linkedin( $url );
            
            set_transient($this->transient_name. $id, $count, 60*30);
        }

        $this->count = $count;   
    }
    
    function return_count(){
        return $this->count;
    }

    function ask_linkedin( $url ){
        $endurl = "https://www.linkedin.com/countserv/count/share?format=json&url={$url}";
        
        $json = file_get_contents($endurl);
        
        $array = json_decode($json);
        if(isset($array['count'])){
            return $array['count'];
        } else{
            return 0;
        }
         
    }
}
