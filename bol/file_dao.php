<?php 


class SPDOWNLOAD_BOL_FileDao extends OW_BaseDao
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
        return 'SPDOWNLOAD_BOL_File';
    }
 
    public function getTableName()
    {
        return OW_DB_PREFIX . 'spdownload_files';
    }

    public function getFileUploadDir()
    {
        return OW::getPluginManager()->getPlugin('spdownload')->getUserFilesDir();
    }

    public function getFilePath( $fileId = null)
    {
        return $this->getFileUploadDir() . $fileId ;
    }

    public function getFileHome($id)
    {
        return $this->findByIdList();
    }

    public function findByCategoryList($idList)
    {
        $tableFileCategory = OW_DB_PREFIX . 'spdownload_files_categories';
        $where = 'WHERE `categoryId` IN ('.implode(',', $idList).')'; 

        $query = "SELECT DISTINCT `f`.* FROM `" . $this->getTableName() . "` AS `f` INNER JOIN `" . $tableFileCategory . "` AS `fc` ON `f`.Id = `fc`.fileId $where";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName());
    }

    public function getFileId( $id )
    {
        return $this->findById( $id );
    }

    public function getThumbnailList($fileId)
    {
        $tableFileCategory = OW_DB_PREFIX . 'spdownload_thumbnails';
        $where = 'WHERE `fileId` = '. $fileId; 
        $order = 'ORDER BY uri ASC';
        $query = "SELECT DISTINCT `fc`.* FROM `" . $tableFileCategory . "` AS `fc` $where $order";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName());

    }

    public function getFileItemUser($fileId, $authorId, $quantitySoft)
    {
        $select = '*';
        $from   = $this->getTableName();
        $where  = '`authorId` = '.$authorId.' and id NOT IN ('.$fileId.')';
        $order_by = '`addedTime` DESC';
        $limit  = $quantitySoft;
        $query  = "SELECT $select FROM $from WHERE $where ORDER BY $order_by LIMIT $limit ";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName());
    }


    public function getIdNew( $array = array() )
    {
        $select = '*';
        $from   = $this->getTableName();
        $where  = '`name` = "'.$array['name'].'" and `description` = "'.$array['description'].'" and `slug` = "'.$array['slug'].'" and `authorId` = "'.$array['authorId'].'" and `addedTime` = "'.$array['addedTime'].'"';
        $query  = "SELECT $select FROM $from WHERE $where ";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName());
    }

    public function findFileListPage( $authorId, $first, $count )
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
        $ex->andFieldEqual('authorId', $authorId)
            ->setOrder('`addedTime` DESC')
            ->setLimitClause($first, $count);
        return $this->findListByExample($ex);
    }

    public function findFileListNewUpload( $first, $count )
    {
        $ex = new OW_Example();
        $ex->setOrder('`addedTime` DESC')
            ->setLimitClause($first, $count);
        return $this->findListByExample($ex);
    }

    public function findFileListMostDown( $first, $count )
    {
        $ex = new OW_Example();
        $ex->setOrder('`downloads` DESC')
            ->setLimitClause($first, $count);
        return $this->findListByExample($ex);
    }

    public function countFile( $authorId )
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('authorId', $authorId);

        return $this->countByExample($ex);
    }
}