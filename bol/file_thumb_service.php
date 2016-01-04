<?php 

class SPDOWNLOAD_BOL_FileThumbService
{
    /**
     * Singleton instance.
     *
     * @var CONTACTUS_BOL_Service
     */
    private static $classInstance;
 
    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return CONTACTUS_BOL_Service
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }
 
        return self::$classInstance;
    }
 
    private function __construct()
    {
 
    }

    public function addThumb( $fileId, $uri )
    {
        $check = $this->checkIdAddThumb( $fileId, $uri);
        $filethumb = new SPDOWNLOAD_BOL_FileThumb();
        if (!empty($check)) {
            $filethumb->id = $check[0]->id;
        }
        $filethumb->fileId = $fileId;
        $filethumb->uri = $uri;
        SPDOWNLOAD_BOL_FileThumbDao::getInstance()->save($filethumb);
    }

    public function checkIdAddThumb( $fileId, $uri)
    {
        return SPDOWNLOAD_BOL_FileThumbDao::getInstance()->checkIdAddThumb( $fileId, $uri);
    }

    public function deleteIdThumb( $fileId )
    {
        $thumbs = new SPDOWNLOAD_BOL_FileThumb();
        $thumb = SPDOWNLOAD_BOL_FileThumbDao::getInstance()->getIdThumbDelete( $fileId );
        if (!empty($thumb))
        {
            foreach ($thumb as $key => $value) {
                $thumbs->id = $value->id;
                SPDOWNLOAD_BOL_FileThumbDao::getInstance()->delete($thumbs);
            }
        }
    }

}   
