<?php

class SPDOWNLOAD_BOL_File extends OW_Entity
{
	public $id;
	
    public $name;

    public $description;

    public $slug;

    public $downloads = 0;

    public $authorId;

}