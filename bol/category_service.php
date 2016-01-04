<?php 

class SPDOWNLOAD_BOL_CategoryService
{
    private static $classInstance;
 
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

    public function getCategoryLabel( $id )
    {
        return OW::getLanguage()->text('spdownload', $this->getCategoryKey($id));
    }

    public function getCategoryParentId($parentId)
    {
        return SPDOWNLOAD_BOL_CategoryDao::getInstance()->findByParentId($parentId);
    }

    public function getCategoryList()
    {
        return SPDOWNLOAD_BOL_CategoryDao::getInstance()->findAll();
    }

    private function getCategoryKey( $name )
    {
        return 'cate_' . trim($name);
    }

    public function addCategory($label, $parent, $id = null)
    {
        $category = new SPDOWNLOAD_BOL_Category();
        $category->id = $id;
        $category->name = $label;
        $category->parentId = $parent;
        SPDOWNLOAD_BOL_CategoryDao::getInstance()->save($category);

        if ($id == null) 
        {
            BOL_LanguageService::getInstance()->addValue(
                OW::getLanguage()->getCurrentId(),
                'spdownload',
                $this->getCategoryKey($category->id),
                trim($label));
        }
    }

    public function deleteIdCategory( $id )
    {
        $category = new SPDOWNLOAD_BOL_Category();
        $category->id = $id;
        SPDOWNLOAD_BOL_CategoryDao::getInstance()->delete( $category );
    }

    public function getCountCategoryParent( $categoryId )
    {
        return SPDOWNLOAD_BOL_CategoryDao::getInstance()->countCategoryParent( $categoryId );
    }
}   
