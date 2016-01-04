<?php 

class SPDOWNLOAD_BOL_VersionService
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

    public function getFileUploadDir()
    {
        return SPDOWNLOAD_BOL_VersionDao::getInstance()->getFileUploadDir();
    }

    public function getFilePath( $photoId )
    {
        return SPDOWNLOAD_BOL_VersionDao::getInstance()->getFilePath($photoId);
    }

    public function getFileList()
    {
        return SPDOWNLOAD_BOL_VersionDao::getInstance()->findAll();
    }

    public function getFileCategoryId($categoryId)
    {
        return SPDOWNLOAD_BOL_VersionDao::getInstance()->findByCategoryId($categoryId);
    }

    public function getFileCategoryIdList($idList)
    {
        return SPDOWNLOAD_BOL_VersionDao::getInstance()->findMostPupularList($idList);
    }

    public function getFileId($id)
    {
        return SPDOWNLOAD_BOL_VersionDao::getInstance()->getFileId($id);
    }

    public function getSoftFile($id)
    {
        return SPDOWNLOAD_BOL_VersionDao::getInstance()->getSoftFile($id);
    }

    public function getFileVerNew($fileId)
    {
        return SPDOWNLOAD_BOL_VersionDao::getInstance()->getFileVerNew($fileId);    
    }

    public function getFileOldVersion( $fileId, $id, $limit )
    {
        return SPDOWNLOAD_BOL_VersionDao::getInstance()->getFileOldVersion( $fileId, $id, $limit );
    }

    public function addVersion( $array = array() )
    {
        $version = new SPDOWNLOAD_BOL_Version();
        $version->size      = $array['size'];
        $version->filename  = $array['name'];
        $version->mimeType  = $array['type'];
        $version->addedTime = $array['addedTime'];
        $version->fileId    = $array['fileId'];

        SPDOWNLOAD_BOL_VersionDao::getInstance()->save( $version );
    }

    public function getIdVer( $array = array() )
    {
        return SPDOWNLOAD_BOL_VersionDao::getInstance()->getIdVer( $array );
    }

    public function getVersionFileId( $fileId )
    {
        return SPDOWNLOAD_BOL_VersionDao::getInstance()->findByFileId( $fileId );
    }

    public function getVersionList( $fileId, $first, $count )
    {
        return SPDOWNLOAD_BOL_VersionDao::getInstance()->findVersionList( $fileId, $first, $count );
    }

    public function getcountVersion( $fileId )
    {
        return SPDOWNLOAD_BOL_VersionDao::getInstance()->countVersion( $fileId );
    }

    public function deleteIdVer( $fileId )
    {
        $versions = new SPDOWNLOAD_BOL_Version();
        $ver = SPDOWNLOAD_BOL_VersionDao::getInstance()->getIdVerDelete( $fileId );
        if (!empty($ver))
        {
            foreach ($ver as $key => $value) {
                $versions->id = $value->id;
                SPDOWNLOAD_BOL_VersionDao::getInstance()->delete($versions);
            }
        }
    }
}   
