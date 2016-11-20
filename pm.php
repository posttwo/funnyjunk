<?php
namespace FunnyJunk;
use FunnyJunk\FunnyJunk;
class PM extends FunnyJunk
{
    public function set($data) {
        foreach ($data AS $key => $value) $this->{$key} = $value;
    }

    public function delete()
    {
        $x = $this->requestGet('/pm/remove/' . $this->pm_id);
    }
}