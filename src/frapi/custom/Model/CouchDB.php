<?php
class CouchDBException extends Exception {}

class CouchDB {

    public $username;
    public $password;
    private $responseObject;

    function __construct($db, $host = 'localhost', $port = 5555, $username = null, $password = null) {
        $this->db = $db;
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
    }
    
    static function decode_json($str) {
        return json_decode($str);
    }
    
    static function encode_json($str) {
        return json_encode($str);
    }
    
    function send($url, $method = 'get', $data = NULL) {
        $url = '/'.$this->db.(substr($url, 0, 1) == '/' ? $url : '/'.$url);
        $request = new CouchDB_Request($this->host, $this->port, $url, $method, $data, $username, $password);
        return $request->send();
    }
    
    function get_all_docs() {
        return $this->send('/_all_docs');
    }
    
    function get_item($id) {
        return $this->send('/'.$id);
    }

    function put($documentName, $data = null)
    {
        $url = '/' .  $this->db . '/' . $documentName;
        $request = new CouchDB_Request($this->host, $this->port, $url, 'PUT', json_encode($data));
        return $request->send();
    }

    function delete($documentName, $revision)
    {
        $url = '/' . $this->db . '/' . $documentName . '?rev=' . $revision;


        $request = new CouchDB_Request($this->host, $this->port, $url, 'DELETE', '');

        return $request->send();


    }

    function view($design, $view,  $method = 'get', $data = NULL, $extra = NULL) {

        $url = '/'.$this->db.'/_design/' . $design . '/_view/' . $view . (!is_null($extra) ? '?' .$extra : '');
        $request = new CouchDB_Request($this->host, $this->port, $url, $method, $data, $username, $password);
        $this->responseObject = $request->send();

        return $this->responseObject;
    }

    public function get($design, $view, $method = 'get', $data = NULL, $extra = NULL) {
        $res = $this->view($design, $view, $method, $data, $extra);
        return $res;
    }

    function getDoc($documentId, $data = null)
    {
        $url = '/' .  $this->db . '/' . $documentId;

        $request = new CouchDB_Request(
            $this->host, $this->port, $url, 'GET', json_encode($data)
        );

        return $request->send();
    }

}
