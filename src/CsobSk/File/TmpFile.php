<?php

namespace Omnipay\CsobSk\File;

class TmpFile implements ITmpFile{
    public function getTempFileName($key)
    {
        return tempnam(sys_get_temp_dir(), $key);
    }
}