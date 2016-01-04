<?php 


class SPDOWNLOAD_BOL_CategoryDao extends OW_BaseDao
{
    protected function __construct()
    {
        parent::__construct();
    }
 
    private static $classInstance;
 
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }
 
        return self::$classInstance;
    }
 
    public function getDtoClassName()
    {
        return 'SPDOWNLOAD_BOL_Category';
    }
 
    public function getTableName()
    {
        return OW_DB_PREFIX . 'spdownload_categories';
    }

    public function findByParentId( $parentId )
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('parentId', (int)$parentId);

        return $this->findListByExample($ex);
    }

    public function countCategoryParent( $categoryId )
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('parentId', $categoryId);

        return $this->countByExample($ex);
    }
}