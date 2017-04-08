<?php
namespace Posttwo\FunnyJunk;
class User extends FunnyJunk
{	
    public function set($data) {
        foreach ($data AS $key => $value) $this->{$key} = $value;
    }
	
	public function getId()
	{
		$data = array('username' => $this->username);
		$x = $this->requestPost(FunnyJunk::$endPoints->getUserId, $data);
		$this->id = (int)$x[0];
		return $this;
	}
	
	public function getIsMod()
	{
		$mods = $this->requestGet(FunnyJunk::$endPoints->allMods);
		$mods = json_decode($mods[0]);
		$key = array_search($this->username, array_column($mods, 'username'));
		
		$this->isMod = true;
		if($key == false)
			$this->isMod = false;
		
		return $this;
	}
}