<?php

/**
 * Description of wpcrowdShareFollowGoogleStats
 *
 * @author Andrew Killen
 */
class wpcrowdShareFollowGoogleStats {
    
    protected $transient_name = "gplusc";
    
    
    function __construct($id = false,$url = false){
        error_log('construct');
        if ($url == false || $id == false){
            return 0;
        }

        $count = '';                
        
        $cache = get_transient($this->transient_name . $id);
        
        if($cache !== false){
            $count = $cache;
        
        }else{
            $count = $this->ask_google( $url );
            
            set_transient($this->transient_name. $id, $count, 60*30);
        }

        $this->count = $count;    
    }
    
    function return_count(){
        return $this->count;
    }


    function ask_google( $url ) {
        $contents = file_get_contents( 
            'https://plusone.google.com/_/+1/fastbutton?url=' 
            . urlencode( $url ) 
        );                

        preg_match( '/window\.__SSR = {c: ([\d]+)/', $contents, $matches );

        if( isset( $matches[0] ) ) 
            return (int) str_replace( 'window.__SSR = {c: ', '', $matches[0] );
        return 0;
    }
}
