<?php

class Spaz_Urltitle
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
     * This is the constructor for the Spaz_Urltitle object
     *
     * @param string $url  The URL to get info about
     */
    public function __construct($url)
    {
        $this->server = new Memcache();
        $this->server->addServer('localhost');
        
        $this->url = $url;
        $this->url_hash = md5($url);
        $this->url_key  = 'urltitle_'.$this->url_hash;
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
            curl_setopt($this->curl, CURLOPT_FILETIME, true);
            curl_setopt($this->curl, CURLOPT_AUTOREFERER, true);
            curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($this->curl, CURLOPT_MAXREDIRS, 6);
            curl_setopt($this->curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_2; en-us) AppleWebKit/531.21.8 (KHTML, like Gecko) Version/4.0.4 Safari/531.21.10");
            
            $html = curl_exec($this->curl);
            // print_r(htmlentities($body));
            curl_close($this->curl);
            
            $tidy_config = array(
                'clean' => true,
                'output-html'=>true,
                'wrap' => 78,
                'quiet'=>1
            );

            $tidy = new tidy;
            $tidy->parseString($html, $tidy_config);
            $tidy->cleanRepair();

            $html = $tidy->html()->value;

            // Buffer DOM errors rather than emitting them as warnings
            $oldSetting = libxml_use_internal_errors(true);

            $dom = new DOMDocument();
            $dom->loadHTML($html);
            
            $xpath = new DOMXPath($dom);
            $titles = $xpath->evaluate('//*[name()="title"]');
            $title = $titles->item(0)->nodeValue;
            
            // Clear any existing errors from previous operations
            libxml_clear_errors();

            // Revert error buffering to its previous setting
            libxml_use_internal_errors($oldSetting);
            
            
            $res = array('title'=>$title);
            
            $res = json_encode($res);
            $this->server->add($this->url_key, $res, MEMCACHE_COMPRESSED, self::CACHE_LIMIT);
        }

        
        return json_decode($res, TRUE);
    }
}
