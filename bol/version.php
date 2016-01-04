<?php

class SPDOWNLOAD_BOL_Version extends OW_Entity
{
    /**
     * @var string
     */
    public $id;

    public $size;

    public $filename;

    public $mimeType;

    public $downloads = 0;

    public $addedTime;
    
    public $fileId;
}