<?php 

class SPDOWNLOAD_BOL_FileCategoryService
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

    public function addFileCategory( $fileId, $categoryId  )
    {
        $filecategory = new SPDOWNLOAD_BOL_FileCategory();
        $filecategory->fileId = $fileId;
        $filecategory->categoryId = $categoryId;
        SPDOWNLOAD_BOL_FileCategoryDao::getInstance()->save($filecategory);
    }

    public function getCategoryId( $fileId )
    {
        return SPDOWNLOAD_BOL_FileCategoryDao::getInstance()->findByFileId( $fileId );
    }

    public function deleteId( $params )
    {
        $category = new SPDOWNLOAD_BOL_FileCategory();
        $cate = SPDOWNLOAD_BOL_FileCategoryDao::getInstance()->getIdDelete( $params );
        if (!empty($cate))
        {
            foreach ($cate as $key => $value) {
                $category->id = $value->id;
                SPDOWNLOAD_BOL_FileCategoryDao::getInstance()->delete($category);
            }
        } 
    }
}   
