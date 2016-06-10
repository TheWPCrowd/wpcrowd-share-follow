<?php

/**
 * Description of wpcrowdShareFollowGoogleStats
 *
 * @author Andrew Killen
 */
class wpcrowdShareFollowGoogleStats {
    
    protected $transient_name = "gplusc";
    
    function __contstruct($url = false){
        if ($url === false){
            return 0;
        }

        $count = '';

        if(parse_url($url, PHP_URL_HOST) == parse_url(get_bloginfo('url'), PHP_URL_HOST)){        
            $cache = get_transient($this->transient_name . parse_url($url, PHP_URL_PATH));
            if($cache !== false){
                $count = $cache;
            }else{
                $count = ask_google( $url );
                set_transient($this->transient_name.parse_url($url, PHP_URL_PATH), $count, 60*30);
            }
        }    
        return $count;    
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
