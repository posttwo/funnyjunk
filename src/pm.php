<?php
namespace Posttwo\FunnyJunk;
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
	
	public function send()
	{
	   $data = array('userId' => $this->user_id, 'userName' => $this->username, 'subject' => $this->subject, 'text' => $this->text);
       $x = $this->requestPost('/pm/send/', $data);
	}
}