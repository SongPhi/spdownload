<?php

class SPDOWNLOAD_CTRL_Deletes extends OW_ActionController
{
	const RATES_ENTITY_TYPE = 'spdownload-software';

	public function index($params)
	{
		
	}

	public function file( $params )
	{
		$page = empty($_GET['page']) ? 1 : $_GET['page'];
        $rpp = 5;
        $first = ($page - 1) * $rpp;
        $count = $rpp;

        if (empty($params) && !isset($params['fileId'])) throw new Redirect404Exception();

        if (!stripos($params['fileId'], "-"))  throw new Redirect404Exception();
        $check = $params['fileId'];
        $params['fileId'] = substr($params['fileId'],0,stripos($params['fileId'], "-"));
        $file = SPDOWNLOAD_BOL_FileService::getInstance()->getFileId($params['fileId']);

        if ($file->id.'-'.$file->slug != $check) throw new Redirect404Exception();

        $arrayCheckCategory = array();
        $CategoryIdList = SPDOWNLOAD_BOL_FileCategoryService::getInstance()->getCategoryId($params['fileId']);
    	foreach ($CategoryIdList as $key => $value) {
    		array_push($arrayCheckCategory, $value->categoryId);
    	}

        $cmpCategories = new SPDOWNLOAD_CMP_Category(false,$arrayCheckCategory);
        $this->addComponent('cmpCategories', $cmpCategories);

        $versions = SPDOWNLOAD_BOL_VersionService::getInstance()->getVersionList($params['fileId'], $first, $rpp);
        foreach ($versions as $key => $value) {
            $value->addedTime   = date("Y-m-d H:i:s", $value->addedTime);
            $value->size        = $this->splitFilesize($value->size);
        }

    	$itemCount = SPDOWNLOAD_BOL_VersionService::getInstance()->getcountVersion($params['fileId']);
        $pageCount = ceil($itemCount / $rpp);
        $this->addComponent('paging', new BASE_CMP_Paging($page, $pageCount, 5));


		$url = OW::getPluginManager()->getPlugin('spdownload')->getUserFilesUrl();
        $nameImage          = 'icon_large_'.$file->id.'.png';
        $file->icon = $url.$nameImage;


        $urlBack = OW::getRouter()->urlForRoute('spdownload.up_myfile', array('userId' => OW::getUser()->getId()));

        $this->assign('urlBack', $urlBack);
        $this->assign('versions', $versions);
        $this->assign('file', $file);
        $form = new Form('delete_file');
        $this->addForm($form);

        $submit = new Submit('delete');
        $submit->setValue(OW::getLanguage()->text('spdownload', 'delete-file-yes'));
        $form->addElement($submit);

        if ( OW::getRequest()->isPost() )
        {
            if ( $form->isValid($_POST) )
            {
                SPDOWNLOAD_BOL_FileThumbService::getInstance()->deleteIdThumb( $params['fileId'] );
                SPDOWNLOAD_BOL_FileCategoryService::getInstance()->deleteId( $params );
                SPDOWNLOAD_BOL_VersionService::getInstance()->deleteIdVer( $params['fileId'] );
                SPDOWNLOAD_BOL_FileService::getInstance()->deleteIdFile( $params['fileId'] );

                $this->redirect(OW::getRouter()->urlForRoute('spdownload.up_myfile', array('userId' => OW::getUser()->getId())));
            }
        }

	}

	public function category($params)
	{  

		SPDOWNLOAD_BOL_FileCategoryService::getInstance()->deleteId( $params );
	}

	private function text( $prefix, $key, array $vars = null )
	{
	  return OW::getLanguage()->text($prefix, $key, $vars);
	}

    public function splitFilesize($size)
    {
        if ($size >= 1024) {
            $size = $size / 1024 ;
            if ($size >= 1024) {
                $size = $size / 1024 ;
                if ($size >= 1024) {
                    $size = $size / 1024 ;
                    if ($size >= 1024) {

                    } else {
                        $size = round($size,2) . ' gb ';
                    }
                } else {
                    $size = round($size,2) . ' mb ';
                }
            } else {
                $size = round($size,2) . ' kb ';
            }
        } else {
            $size = round($size,2) . ' byte ';
        }
        return $size;
    }

	
}
