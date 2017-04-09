<?php
namespace Posttwo\FunnyJunk;
use Debugbar;
use Illuminate\Support\Facades\Cache;

class FunnyJunk{
    public static $siteUrl = 'https://funnyjunk.com';
    protected static $endPoints = NULL;
    protected static $cookie = '';

    public function __construct() 
    {
        FunnyJunk::$endPoints = new \stdClass();
        FunnyJunk::$endPoints->pm = new \stdClass();
        FunnyJunk::$endPoints->login = '/members/ajaxlogin';
        FunnyJunk::$endPoints->pm->inbox = '/pm/folder/inbox/0/0/0/15';
		FunnyJunk::$endPoints->onlineMods = '/ajax/getOnlineModList';
		FunnyJunk::$endPoints->allMods = '/ajax/getModRanksList';
		FunnyJunk::$endPoints->getUserId = '/ajax/getUserId';
    }

    public function login($login, $password)
    {
        FunnyJunk::$cookie = '';
        $cache = Cache::get($login . "-cookie");
        if($cache == null){
            Debugbar::info("Missed cache for login");
            $data = array('username' => $login, 'password' => $password);
            $r = $this->requestPost(FunnyJunk::$endPoints->login, $data);
            Debugbar::info($r[0]);
            foreach ($r[1] as $hdr) {
                if (preg_match('/^Set-Cookie:\s*([^;]+)/', $hdr, $matches)) {
                    parse_str($matches[1], $tmp);
                    $key = key($tmp);
                    $value = $tmp[$key];
                    FunnyJunk::$cookie = FunnyJunk::$cookie . $key . '=' . $value . '; ';
                }
            }
            FunnyJunk::$cookie = substr(FunnyJunk::$cookie, 0, -2);
            Cache::put($login . "-cookie", FunnyJunk::$cookie, 180);
        }else{
            Debugbar::info("Logging in with cached cookie");
            FunnyJunk::$cookie = $cache;
        }
        
    }
	
    public function getInbox()
    {
        $x = $this->requestGet(FunnyJunk::$endPoints->pm->inbox);
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
	
	public function getOnlineMods()
	{
		$mods = $this->requestGet(FunnyJunk::$endPoints->onlineMods);
		$mods = json_decode($mods[0]);
		$moderators = array();
        foreach($mods as $mod)
        {
			$class = new User();
			$class->set($mod);
			$class->isMod = true;
			$moderators[] = $class;
        }
        return $moderators;
	}
	
	public function getMods()
	{
		$mods = $this->requestGet(FunnyJunk::$endPoints->allMods);
		$mods = json_decode($mods[0]);
		$moderators = array();
        foreach($mods as $mod)
        {
			$class = new User();
			$class->set($mod);
			$class->isMod = true;
			$moderators[] = $class;
        }
        return $moderators;
	}


   public function acceptFriends()
   {
        $data = ['user' => 'posttwo'];
        $this->requestPost('/userbar/acceptallfriendrequests/', $data);
   }

    protected function requestGet($endpoint)
    {
        $url = FunnyJunk::$siteUrl . $endpoint;
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
        Debugbar::info("FunnyJunk POST: " . $endpoint);
        $url = FunnyJunk::$siteUrl . $endpoint;
        $options = array(
            'http' => array(
                'header'  => "User-Agent:PosttwoFJSDK/1.0\r\n" .
							 "Content-type: application/x-www-form-urlencoded\r\n" . 
                             "Cookie: " . FunnyJunk::$cookie . '',
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        Debugbar::info("Response: " . $result);
        $headers = $http_response_header;
        return [$result, $headers];
    }

    public function dump($x)
    {
        var_dump($x);
    }   
}
