<?php
namespace Posttwo\FunnyJunk;

class User extends FunnyJunk
{	
    public function set($data) {
        foreach ($data AS $key => $value) $this->{$key} = $value;
    }
	
	public function getId()
	{
		$x = $this->requestGet(FunnyJunk::$endPoints->getUserId . $this->username);
		$id = filter_var($x[0], FILTER_SANITIZE_NUMBER_INT);
		$this->id = (int)$id;
		return $this;
	}
	
	public function getIsMod()
	{
		if(0 === strpos($this->role_name, 'flag_moderator'))
		{
			$this->isMod = true;
		}
	}

	public function getUserLevel()
	{
		$levelString = $this->group_name;
		$level = filter_var($levelString, FILTER_SANITIZE_NUMBER_INT);
		$this->level = (int)$level;
		return $this;
	}

	public function getUserInfo()
	{
		$info = $this->requestPost(env("FJ_API_ENDPOINT") . $this->username, ["key" => env("FJ_API_KEY")]);
		$info = json_decode($info[0]);
		$this->set($info);
	}

	public function populate()
	{
		$this->getId();
		$this->getUserInfo();
		$this->getUserLevel();
		$this->getIsMod();
	}
}
