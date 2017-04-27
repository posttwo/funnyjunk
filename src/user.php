<?php
namespace Posttwo\FunnyJunk;

use Sunra\PhpSimple\HtmlDomParser;

class User extends FunnyJunk
{	

	protected $dom;

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

	public function getUserLevel()
	{
		$levelString = $this->dom->find('.permissionsLink b')[0]->plaintext;
		$level = filter_var($levelString, FILTER_SANITIZE_NUMBER_INT);
		$this->level = $level;
		return $this;
	}

	public function getIsPatreon()
	{
		$patronElement = $this->dom->find('.profileHat');
		if($patronElement == null)
			$this->patreon = false;
		else
			$this->patreon = true;
		return $this;
	}

	public function getIsOCCreator()
	{
		$patronElement = $this->dom->find('#useritemsrewards_content');
		if($patronElement == null)
			$this->occreator = false;
		else
			$this->occreator = true;
		return $this;
	}

	public function getDom()
	{
		$this->dom = HtmlDomParser::str_get_html( $this->requestGet('/user/' . $this->username)[0] );
		return $this;
	}

	public function populate()
	{
		$this->getDom();
		$this->getId();
		$this->getIsMod();
		$this->getIsPatreon();
		$this->getUserLevel();
		$this->getIsOCCreator();
	}
}