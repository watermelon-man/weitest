<?php

class CacheLock
{
    private $path = null;
    private $fp = null;
    private $hashNum = 100;
    private $name;
    private  $eAccelerator = false;
     
    public function __construct($name,$path='lock\\')
    {
        $this->eAccelerator = function_exists("eaccelerator_lock");
        if(!$this->eAccelerator)
        {
            $this->path = $path.($this->_mycrc32($name) % $this->hashNum).'.txt';
        }
        $this->name = $name;
    }
     
    private function _mycrc32($string)
    {
        $crc = abs (crc32($string));
        if ($crc & 0x80000000) {
            $crc ^= 0xffffffff;
            $crc += 1;
        }
        return $crc;
    }

    public function lock()
    {
        if(!$this->eAccelerator)
        {
            $this->fp = fopen($this->path, 'w+');
            if($this->fp === false)
            {
                return false;
            }
            return flock($this->fp, LOCK_EX);
        }else{
            return eaccelerator_lock($this->name);
        }
    }
     
    public function unlock()
    {
        if(!$this->eAccelerator)
        {
            if($this->fp !== false)
            {
                flock($this->fp, LOCK_UN);
                clearstatcache();
            }
            fclose($this->fp);
        }else{
            return eaccelerator_unlock($this->name);
        }
    }
}