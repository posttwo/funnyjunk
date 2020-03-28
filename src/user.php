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
	
	public function permaBan($msg)
	{
		$info = $this->requestPost('/ajax/ban', ["key" => env("FJ_API_KEY"), "user_id" => $this->id, 'ban_ip' => 0, "ban_reason" => "Permabanned $msg", 'remove_content' => 0, 'lifetime' => 0]);
	}
	
	public function getUserLevel()
	{
		$thumbs = $this->total_comments_thumbs_up + $this->total_content_thumbs_up;
		if($thumbs <= 0) $thumbs = 1;
		$level = (46.119 * (log ($thumbs) )) - 105.19;
		if($level < 0) $level = 0;
		$this->level = (int)round($level);
		return $this;
	}

	public function getUserInfo()
	{
		$info = $this->requestPost(env("FJ_API_ENDPOINT") . $this->username, ["key" => env("FJ_API_KEY")]);
		$info = json_decode($info[0]);
		$this->set($info);
	}
	
	public function getUsersSameId()
	{
		$info = $this->requestPost('/ajax/get_users_with_same_ip/', ['user_id' => $this->id]);
		$info = json_decode($info[0]);
		return $info;
	}

	public function getUsername()
	{
		$x = $this->requestGet('/find/user/' . $this->id);
		$x = $x[0];
		$x = substr($x, 0, -4);
		$x = substr(strrchr(rtrim($x, '/'), '/'), 1);
		$this->username = $x;
		return $this;
	}

	public function populate()
	{
		$this->getId();
		$this->getUserInfo();
		$this->getUserLevel();
		$this->getIsMod();
	}
}
