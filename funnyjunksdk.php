<?php
namespace FunnyJunk;
use FunnyJunk\PM;
class FunnyJunk{
    public $siteUrl = 'https://funnyjunk.com';
    private $endPoints = NULL;
    protected static $cookie = '';

    public function __construct() 
    {
        $this->endPoints = new \stdClass();
        $this->endPoints->pm = new \stdClass();
        $this->endPoints->login = '/members/ajaxlogin';
        $this->endPoints->pm->inbox = '/pm/folder/inbox/0/0/0/15';
    }

    public function login($login, $password)
    {
        FunnyJunk::$cookie = '';
        $data = array('username' => $login, 'password' => $password);
        $r = $this->requestPost($this->endPoints->login, $data);
        $this->dump($r[0]);
        foreach ($r[1] as $hdr) {
            if (preg_match('/^Set-Cookie:\s*([^;]+)/', $hdr, $matches)) {
                parse_str($matches[1], $tmp);
                $key = key($tmp);
                $value = $tmp[$key];
                FunnyJunk::$cookie = FunnyJunk::$cookie . $key . '=' . $value . '; ';
            }
        }
        FunnyJunk::$cookie = substr(FunnyJunk::$cookie, 0, -2);
    }
    public function getInbox()
    {
        $x = $this->requestGet($this->endPoints->pm->inbox);
        $pms = json_decode($x[0], true);
        $messages = array();
        foreach($pms as $pm)
        {
            if(isset($pm['pm_id']))
            {
                $class = new PM();
                $class->set($pm);
                $messages[] = $class;
            }
        }
        return $messages;
    }

    protected function requestGet($endpoint)
    {
        $url = $this->siteUrl . $endpoint;
        $options = array(
            'http' => array(
                'header'  => "accept:application/json, text/javascript, */*; q=0.01\r\nx-requested-with: XMLHttpRequest\r\n" . 
                             "Cookie: " . FunnyJunk::$cookie . '',
                'method'  => 'GET'
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $headers = $http_response_header;
        return [$result, $headers];
    }
    protected function requestPost($endpoint, $data, $cookie = '')
    {
        $url = $this->siteUrl . $endpoint;
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n" . 
                             "Cookie: " . FunnyJunk::$cookie . '',
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $headers = $http_response_header;
        return [$result, $headers];
    }

    public function dump($x)
    {
        var_dump($x);
    }   
}