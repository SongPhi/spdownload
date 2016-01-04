<?php 


class SPDOWNLOAD_BOL_FileCategoryDao extends OW_BaseDao
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
        return 'SPDOWNLOAD_BOL_FileCategory';
    }
 
    public function getTableName()
    {
        return OW_DB_PREFIX . 'spdownload_files_categories';
    }

    public function findByFileId( $fileId )
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('fileId', (int)$fileId);

        return $this->findListByExample($ex);
    }

    public function getIdDelete( $params )
    {
        $categoryId = $params['categoryId'];
        $fileId = $params['fileId'];
        
        $select = '*';
        $from   = $this->getTableName();
        if ($categoryId == null && $fileId != null ) {
            $where  = '`fileId` = '.$fileId ;
        } else if ( $fileId == null && $categoryId != null ) {
            $where  = '`categoryId` = '.$categoryId ;
        } else {
            $where  = '`fileId` = '.$fileId.' and `categoryId` = '.$categoryId ;
        }
        $query  = "SELECT $select FROM $from WHERE $where ";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName());
    }

}