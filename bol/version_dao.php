<?php 


class SPDOWNLOAD_BOL_VersionDao extends OW_BaseDao
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
        return 'SPDOWNLOAD_BOL_Version';
    }
 
    public function getTableName()
    {
        return OW_DB_PREFIX . 'spdownload_versions';
    }

    public function getFilePath($fileId = NULL)
    {
        return OW::getPluginManager()->getPlugin('spdownload')->getUserFilesDir(). $fileId ;
    }

    public function getFileHome($id)
    {
        return $this->findByIdList();
    }

    public function getFileId($id)
    {
        return $this->findById($id);
    }

    public function getSoftFile($fileId)
    {
        $tableFileCategory = OW_DB_PREFIX . 'spdownload_files';
        $select = ' * ';
        $inner  = 'INNER JOIN '.$tableFileCategory.' AS f ';
        $on     = 'ON v.fileId = f.id ';
        $where  = 'WHERE `fileId` = '. $fileId; 
        $query  = "SELECT $select FROM `" . $this->getTableName() . "` AS `v` $inner $on $where";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName());
    }

    public function getFileVerNew($fileId)
    {
        $select = ' * ';
        $where  = 'WHERE `fileId` = '. $fileId; 
        $order  = 'ORDER BY id DESC ';
        $limit  = 'LIMIT 1 ';
        $query  = "SELECT $select FROM `" . $this->getTableName() . "` AS `v` $where $order $limit";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName());
    }

    public function getFileOldVersion( $fileId, $idver, $limit )
    {
        $select = '*';
        $from   = $this->getTableName();
        $where  = '`fileId` = '.$fileId.' and id NOT IN ('.$idver.')';
        $order  = 'addedTime DESC ';
        $limit  = $limit;
        $query  = "SELECT $select FROM $from WHERE $where ORDER BY $order LIMIT $limit ";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName());
    }

    public function getIdVer( $array = array() )
    {
        $select = '*';
        $from   = $this->getTableName();
        $where  = '`size` = "'.$array['size'].'" and `filename` = "'.$array['name'].'" and `mimeType` = "'.$array['type'].'" and  `addedTime` = "'.$array['addedTime'].'"';
        $query  = "SELECT $select FROM $from WHERE $where ";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName());
    }

    public function findByFileId( $fileId )
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('fileId', (int)$fileId);

        return $this->findListByExample($ex);
    }

    public function findVersionList( $fileId, $first, $count )
    {
        if ($first < 0)
        {
            $first = 0;
        }

        if ($count < 0)
        {
            $count = 1;
        }

        $ex = new OW_Example();
        $ex->andFieldEqual('fileId', $fileId)
            ->setOrder('`addedTime` DESC')
            ->setLimitClause($first, $count);


        return $this->findListByExample($ex);
    }

    public function countVersion( $fileId )
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('fileId', $fileId);

        return $this->countByExample($ex);
    }

    public function getIdVerDelete( $fileId )
    {
        $select = '*';
        $from   = $this->getTableName();
        $where  = '`fileId` = '.$fileId ;
        $query  = "SELECT $select FROM $from WHERE $where ";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName());
    }
}