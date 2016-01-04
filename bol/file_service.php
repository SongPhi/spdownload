<?php 

class SPDOWNLOAD_BOL_FileService
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
        return SPDOWNLOAD_BOL_FileDao::getInstance()->getFileUploadDir();
    }

    public function getFilePath( $fileId = null )
    {
        return SPDOWNLOAD_BOL_FileDao::getInstance()->getFilePath($fileId);
    }

    public function getFileList()
    {
        return SPDOWNLOAD_BOL_FileDao::getInstance()->findAll();
    }

    public function getFileCategoryId($categoryId)
    {
        return SPDOWNLOAD_BOL_FileDao::getInstance()->findByCategoryId($categoryId);
    }

    public function getFileCategoryIdList($idList)
    {
        return SPDOWNLOAD_BOL_FileDao::getInstance()->findByCategoryList($idList);
    }

    public function getFileId( $id )
    {
        return SPDOWNLOAD_BOL_FileDao::getInstance()->getFileId( $id );
    }

    public function getThumbnailList($id)
    {
        return SPDOWNLOAD_BOL_FileDao::getInstance()->getThumbnailList($id);
    }

    public function getFileItemUser($fileId, $authorId, $quantitySoft)
    {
        return SPDOWNLOAD_BOL_FileDao::getInstance()->getFileItemUser($fileId, $authorId, $quantitySoft);
    }

    public function move_file($tmp_name, $destination, $name)
    {
        if (isset($tmp_name) && !empty($tmp_name) ) 
        {
            $dir = $destination.$name;
            move_uploaded_file($tmp_name, $dir);
        }
    }

    public function copy_resize_image($tmp_name, $slugimage, $idThumb, $extension, $width = null, $height = null)
    {
        if ($extension == null) 
        {
            $extension = '';
        }
        $storage = OW::getStorage();

        $imagesDir = OW::getPluginManager()->getPlugin('spdownload')->getUserFilesDir();
        $imageName = $slugimage.'_'.$idThumb.'.'.$extension;
        $imagePath = $imagesDir . $imageName;

        $pluginfilesDir = Ow::getPluginManager()->getPlugin('spdownload')->getPluginFilesDir();
        $tmpImgPath = $slugimage.'_'.$idThumb.'.'.$extension;

        $image = new UTIL_Image($tmp_name);
        $image->resizeImage($width, $height)->saveImage($tmpImgPath);


        //Copy file into storage folder
        $storage->copyFile($tmpImgPath, $imagePath);

        unlink($tmpImgPath);
    }
    
    public function getIdNew( $array = array())
    {
        return SPDOWNLOAD_BOL_FileDao::getInstance()->getIdNew( $array );
    }

    public function addFile( $array = array() )
    {
        if (!empty($array['authorId'] ) && $array['authorId']  == OW::getUser()->getId())
        {
            $file = new SPDOWNLOAD_BOL_File();
            $file->id           = $array['id'];
            $file->name         = $array['name'];
            $file->description  = $array['description'];
            $file->slug         = $array['slug'];
            $file->authorId     = $array['authorId'];
            if (empty($file->id)) {
                $file->addedTime    = $array['addedTime'];
            } else {
                $file->updated      = $array['addedTime'];
            }

            SPDOWNLOAD_BOL_FileDao::getInstance()->save($file);
        }
    }

    public function addFileCategory( $idFile, $idCategory )
    {
        if ( isset($idFile) && !empty($idFile) ) 
        {
            $tableFileCategory = OW_DB_PREFIX . 'spdownload_files_categories';
            $file = new SPDOWNLOAD_CTRL_Object();

            $file->fileId = $idFile;
            $file->categoryId = $idCategory;
            OW_Database::insert($tableFileCategory, $file );
        }
    }

    public function deleteIdFile( $id )
    {
        $file = new SPDOWNLOAD_BOL_File();
        $file->id = $id;
        SPDOWNLOAD_BOL_FileDao::getInstance()->delete( $file );
    }

    public function getFileListPage( $authorId, $first, $count )
    {
        return SPDOWNLOAD_BOL_FileDao::getInstance()->findFileListPage( $authorId, $first, $count );
    }

    public function getFileListNewUpload( $first, $count )
    {
        return SPDOWNLOAD_BOL_FileDao::getInstance()->findFileListNewUpload( $first, $count );
    }

    public function getFileListMostDown( $first, $count )
    {
        return SPDOWNLOAD_BOL_FileDao::getInstance()->findFileListMostDown( $first, $count );
    }
    
    public function getCountFile( $authorId )
    {
        return SPDOWNLOAD_BOL_FileDao::getInstance()->countFile( $authorId );
    }
}   


