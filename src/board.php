<?php
namespace Posttwo\FunnyJunk;

use Sunra\PhpSimple\HtmlDomParser;

class Board extends FunnyJunk
{
    protected $dom  = null;
    public $name = null;
    public $id   = null;
    public $dj   = [];

    public function getId() {
        $data = json_decode($this->requestPost('/ms/getByURL/', ['isAndroid' => true, 'urlToPost' => '/' . $this->name])[0] );
        $this->id   = $data->id;
        $this->name = $data->board_name;
        return $this;
    }

    public function getDom() {
        //NAME REQUIRED
        $b = $this->requestGet("/" . $this->name);
        $this->dom = HtmlDomParser::str_get_html($b[0]);
        
        return $this;
    }

    public function getDJs() {
        $elem = $this->dom->find('span[id=dj1]');
        $this->dj[1] = substr($elem[0]->plaintext, 7, -1);
        
        $elem = $this->dom->find('span[id=dj2]');
        $this->dj[2] = substr($elem[0]->plaintext, 7, -1);

        $elem = $this->dom->find('span[id=dj3]');
        $this->dj[3] = substr($elem[0]->plaintext, 7, -1);

        return $this;
    }

    public function setDJ($number, $username) {
        $response = $this->requestPost('/dj_controls/djChange', ['type' => $number, 'username' => $username, 'contentId' => $this->id]);
        $response = json_decode($response[0]);
        if($response->success == false)
            return false;
        $this->dj[$number] = $username;
        return true;
    }

    public function getCommentTree($url) {
        $coms = json_decode($this->requestPost('/ms/getByURL/', ['isAndroid' => true, 'urlToPost' => $url])[0] )->comments;
        return $coms;
    }

    public function postMessage($message, $getRoot = true) {
        return "/party/506308#506308";
        $x = $this->requestPost('/comment/add/content/' . $this->id, ['text' => $message]);
        $id = substr($x[0], 3);
        if(substr($x[0], 0, 2) != "OK")
            return false;
        //get comment url... shitty way
        if($getRoot){
            $coms = json_decode($this->requestPost('/ms/getByURL/', ['isAndroid' => true, 'urlToPost' => "/user/" . env("FJ_USERNAME")])[0] )->latest_comments;
            $x = $this->arraySearch($coms, $id);
            return $coms[$x]->comment_url;
        }
        return true;
    }

    protected function arraySearch($array, $id){
        foreach ($array as $key => $val) {
            if ($val->id == $id) {
                return $key;
            }
        }
    }
}