<?php

class Spaz_Urlinfo
{
    
    const CACHE_LIMIT = 900; // 15 minutes
    
    /**
     * cURL resource
     *
     * @var resource
     */
    var $curl;
    
    
    /**
     * the URL
     *
     * @var string
     */
    var $url;
    
    /**
     * the url's md5 hash
     *
     * @var string
     */
    var $url_hash;
    
    /**
     * the key used to store the value in apc
     *
     * @var string
     */
    var $url_key; 
    
    /**
     * Constructor
     *
     * This is the constructor for the Spaz_Urlinfo object
     *
     * @param string $url  The URL to get info about
     */
    public function __construct($url)
    {
        $this->server = new Memcache();
        $this->server->addServer('localhost');
        
        $this->url = $url;
        $this->url_hash = md5($url);
        $this->url_key  = 'urlinfo_'.$this->url_hash;
        $this->curl = curl_init();
    }

    /**
     * Get URL info
     *
     * This method fetches info about the URL, like the HTTP response code and content type.
     *
     * @return array  Info about the URL
     */
    public function get()
    {
        $res = $this->server->get($this->url_key);
        if ($res === false) {
            curl_setopt($this->curl, CURLOPT_URL, $this->url);
            curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->curl, CURLOPT_HEADER, true);
            curl_setopt($this->curl, CURLOPT_FILETIME, true);
            curl_setopt($this->curl, CURLOPT_NOBODY, true);
            curl_setopt($this->curl, CURLOPT_AUTOREFERER, true);
            curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($this->curl, CURLOPT_MAXREDIRS, 6);
            curl_setopt($this->curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_2; en-us) AppleWebKit/531.21.8 (KHTML, like Gecko) Version/4.0.4 Safari/531.21.10");
            
            $headers = curl_exec($this->curl);
            $url_info = curl_getinfo($this->curl);
            $url_info['headers'] = $headers;
            curl_close($this->curl);
            
            $res['resolved_url'] = $url_info['url'];
            $res['content_type'] = $url_info['content_type'];
            $res['http_code'] = $url_info['http_code'];
            $res['filetime'] = $url_info['filetime'];
            $res['download_content_length'] = $url_info['download_content_length'];
            
            $res = json_encode($res);
            $this->server->add($this->url_key, $res, MEMCACHE_COMPRESSED, self::CACHE_LIMIT);
        }
        
        return json_decode($res, TRUE);
    }
}
