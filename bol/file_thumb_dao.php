<?php 


class SPDOWNLOAD_BOL_FileThumbDao extends OW_BaseDao
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
        return 'SPDOWNLOAD_BOL_FileThumb';
    }
 
    public function getTableName()
    {
        return OW_DB_PREFIX . 'spdownload_thumbnails';
    }

    public function checkIdAddThumb( $fileId , $uri )
    {
        $select = '*';
        $from   = $this->getTableName();
        $where  = '`fileId` = '.$fileId.' and `uri` = '.$uri ;
        $query  = "SELECT $select FROM $from WHERE $where ";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName());
    }

    public function getIdThumbDelete( $fileId )
    {
        $select = '*';
        $from   = $this->getTableName();
        $where  = '`fileId` = '.$fileId ;
        $query  = "SELECT $select FROM $from WHERE $where ";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName());
    }
}