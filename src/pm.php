<?php
namespace Posttwo\FunnyJunk;

use Debugbar;
class PM extends FunnyJunk
{
    public function set($data) {
        foreach ($data AS $key => $value) $this->{$key} = $value;
    }

    public function delete()
    {
        $x = $this->requestGet('/pm/remove/' . $this->pm_id);
    }

    public function reply($subject, $message)
    {
       $data = array('userId' => $this->author_id, 'userName' => $this->username, 'subject' => $subject, 'text' => $message);
       $x = $this->requestPost('/pm/send/', $data);
    }
	
    public function sendToUser($userid, $username, $topic, $text)
    {
        if($topic != '')
        {
            $text = '[big]' . $topic . "[big]\n\r" . $text
        }
        $data = array(
            'userId' => $userid,
            'key' => env('FJ_API_KEY'),
            'text' => $text
        )
        $x = $this->requestPost('/commapp/API', $data);
        
        //$data = array('userId' => $userid, 'userName' => $username, 'subject' => $topic, 'text' => $text);
        //Debugbar::info($data);
        //$x = $this->requestPost('/pm/send/', $data);
    }

	public function send()
	{
	   $data = array('userId' => $this->user_id, 'userName' => $this->username, 'subject' => $this->subject, 'text' => $this->text);
       $x = $this->requestPost('/pm/send/', $data);
	}
}