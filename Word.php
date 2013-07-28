<?php

class Word {

    /**
     * [getWords description]
     * 
     * @param  [type] $doc [description]
     * 
     * @return [type]      [description]
     */
    public function getWords($doc) {
        $words = array();
        foreach(preg_split('@\W@', $doc) as $word) {
            if(strlen($word) > 2 && strlen($word) < 20) {
                $words[] = strtolower($word);
            }
        }
        return $words;
    }
}