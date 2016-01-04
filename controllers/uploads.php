<?php

class SPDOWNLOAD_CTRL_Uploads extends OW_ActionController
{
	const RATES_ENTITY_TYPE = 'spdownload-software';
	public function index($params)
	{
		$filevernew = array();
		$file = array();
		$flag = false;

		$this->setPageTitle(OW::getLanguage()->text('spdownload', 'index_upload_title')); 
        $this->setPageHeading(OW::getLanguage()->text('spdownload', 'index_upload_heading')); 

		if ( !OW::getUser()->isAuthenticated() )
        {
            throw new AuthenticateException();
        }

        if ( !OW::getUser()->isAuthorized('spdownload', 'upload') )
        {
            $status = BOL_AuthorizationService::getInstance()->getActionStatus('spdownload', 'upload');
            throw new AuthorizationException($status['msg']);

            return;
        }

        $arrayCheckCategory = array();
        if (!empty($params) && isset($params['fileId'])) 
        {
	        if (!strrpos($params['fileId'], "-"))  throw new Redirect404Exception();
	        $check = $params['fileId'];
	        $params['fileId'] = substr($params['fileId'],0,strrpos($params['fileId'], "-"));
	        $file = SPDOWNLOAD_BOL_FileService::getInstance()->getFileId($params['fileId']);

	        if ($file->id.'-'.$file->slug != $check) throw new Redirect404Exception();
        	$CategoryIdList = SPDOWNLOAD_BOL_FileCategoryService::getInstance()->getCategoryId($params['fileId']);
        	foreach ($CategoryIdList as $key => $value) {
        		array_push($arrayCheckCategory, $value->categoryId);
        	}
        	$flag = true;
	        $file = SPDOWNLOAD_BOL_FileService::getInstance()->getFileId( $params['fileId'] );
	        if ( $file === NULL )
	        {
	            throw new Redirect404Exception();
	        }
	        $filevernew = SPDOWNLOAD_BOL_VersionService::getInstance()->getFileVerNew($params['fileId']);
	        $url = OW::getPluginManager()->getPlugin('spdownload')->getUserFilesUrl();

			$file->icon = $url.'icon_small_'.$params['fileId'].'.png';
			$thumbnails = SPDOWNLOAD_BOL_FileService::getInstance()->getThumbnailList($params['fileId']);
	        foreach ($thumbnails as $key => $value) {
	            $value->image = $url.$value->fileId.'_thumb_small_'.$value->uri.'.jpg';
	        }
	        $this->assign('file', $file);
	        $this->assign('thumbnails', $thumbnails);
        }

        $form = new Form('upload_form');
        $form->setEnctype(Form::ENCTYPE_MULTYPART_FORMDATA);

        $cmpCategories = new SPDOWNLOAD_CMP_Category(false,$arrayCheckCategory);
        $this->addComponent('cmpCategories', $cmpCategories);

        $fieldName = new TextField('upname');
		$fieldName->setLabel($this->text('spdownload', 'form_label_name_up'));
		$fieldName->setRequired();
		if (!empty($params) && isset($params['fileId'])) $fieldName->setValue($file->name);
		$form->addElement($fieldName);

		$fieldSlug = new TextField('upslug');
		$fieldSlug->setLabel($this->text('spdownload', 'form_label_slug_up'));
		$fieldSlug->setRequired();
		if (!empty($params) && isset($params['fileId'])) $fieldSlug->setValue($file->slug);
		$form->addElement($fieldSlug);

		$fieldFile = new FileField('upfile');
		$fieldFile->setLabel($this->text('spdownload', 'form_label_file_up'));
		$form->addElement($fieldFile);

		$fieldIcon = new FileField('upicon');
		$fieldIcon->setLabel($this->text('spdownload', 'form_label_icon_up'));
		$form->addElement($fieldIcon);

		$fieldThumb = new MultiFileField('upthumb',5);
		$fieldThumb->setLabel($this->text('spdownload', 'form_label_thumb_up'));
		$form->addElement($fieldThumb);

		$fieldDescription = new Textarea('updescription');
		$fieldDescription->setLabel($this->text('spdownload', 'form_label_description_up'));
		if (!empty($params) && isset($params['fileId'])) $fieldDescription->setValue($file->description);
		$form->addElement($fieldDescription);

		$fieldCategory = new CheckboxGroup('ct');
		$form->addElement($fieldCategory);

		$submit = new Submit('upload');
		$submit->setValue($this->text('spdownload', 'form_label_submit_up'));
		$form->addElement($submit);

		$this->addForm($form);


		$this->assign('flag', $flag);

		if ( OW::getRequest()->isPost() )
		{
			if ( $form->isValid($_POST) )
			{
				if (!empty($params) && isset($params['fileId']))
				{
					if (!empty($_POST['ct'])) 
					{
						$arrayAdd = array_diff($_POST['ct'], $arrayCheckCategory);
						$arrayDelete = array_diff($arrayCheckCategory, $_POST['ct']);
						var_dump($arrayAdd);
						var_dump($arrayDelete);
						foreach ($arrayDelete as $key => $value) {
							SPDOWNLOAD_BOL_FileCategoryService::getInstance()->deleteId( $params['fileId'], $value );
						}
						foreach ($arrayAdd as $key => $value) {
							SPDOWNLOAD_BOL_FileCategoryService::getInstance()->addFileCategory( $params['fileId'], $value );
						}
					} else {
						SPDOWNLOAD_BOL_FileCategoryService::getInstance()->deleteId( $params['fileId'] , null );
					}
				}

				$arrayFile = array();
				$arrayFile['id'] = null;
				if (!empty($params) && isset($params['fileId'])) $arrayFile['id'] = $params['fileId'];
				$arrayFile['name'] 			= $_POST['upname'];
				$arrayFile['description'] 	= $_POST['updescription'];
				$arrayFile['slug'] 			= $_POST['upslug'];
				$arrayFile['authorId'] 		= OW::getUser()->getId();
				$arrayFile['addedTime'] 	= time();
				SPDOWNLOAD_BOL_FileService::getInstance()->addFile($arrayFile);

				if (isset($arrayFile['id']) && !empty($arrayFile['id']))
				{

				} else {
					$fileNew = SPDOWNLOAD_BOL_FileService::getInstance()->getIdNew( $arrayFile );
					$arrayFile['id'] = $fileNew[0]->id;
				}
				if (empty($params) && !isset($params['fileId']))
				{
					if (isset($_POST['ct']) && !empty($_POST['ct']))
					{
						foreach ($_POST['ct'] as $key => $value) {
							SPDOWNLOAD_BOL_FileCategoryService::getInstance()->addFileCategory( $arrayFile['id'], $value );
						}
					}
				}
				
				$arrayInputFile = array();
				$arrayInputFile['size'] 		= $_FILES['upfile']['size'];
				$arrayInputFile['name'] 		= $_FILES['upfile']['name'];
				$arrayInputFile['type'] 		= $_FILES['upfile']['type'];
				$arrayInputFile['tmp_name']		= $_FILES['upfile']['tmp_name'];
				$arrayInputFile['error']		= $_FILES['upfile']['error'];
				$arrayInputFile['addedTime'] 	= $arrayFile['addedTime'];
				$arrayInputFile['fileId']		= $arrayFile['id'];
				
				$path = SPDOWNLOAD_BOL_FileService::getInstance()->getFilePath();

				if ( $arrayInputFile['error'] == 0 )
				{
					SPDOWNLOAD_BOL_VersionService::getInstance()->addVersion( $arrayInputFile );
					$verNew = SPDOWNLOAD_BOL_VersionService::getInstance()->getIdVer( $arrayInputFile );
					$arrayInputFile['id'] = $verNew[0]->id;
					SPDOWNLOAD_BOL_FileService::getInstance()->move_file( $arrayInputFile['tmp_name'], $path, $arrayInputFile['id'] );
				}
				

				if ($_FILES['upicon']['error'] == 0 && !empty($_FILES['upicon']['name']))
				{
					SPDOWNLOAD_BOL_FileService::getInstance()->copy_resize_image($_FILES['upicon']['tmp_name'], 'icon_small', $arrayFile['id'], 'png', 48, 48);
					SPDOWNLOAD_BOL_FileService::getInstance()->copy_resize_image($_FILES['upicon']['tmp_name'], 'icon_large', $arrayFile['id'], 'png', 128, 128);
				}

				foreach ($_FILES['upthumb']['tmp_name'] as $key => $value) {
					if ( $_FILES['upthumb']['error'][$key] == 0 && !empty($_FILES['upthumb']['name'][$key]) )
					{
						SPDOWNLOAD_BOL_FileThumbService::getInstance()->addThumb($arrayFile['id']	, $key);
						SPDOWNLOAD_BOL_FileService::getInstance()->copy_resize_image($_FILES['upthumb']['tmp_name'][$key], $arrayFile['id'].'_thumb_small', $key, 'jpg', 360, 180);
						SPDOWNLOAD_BOL_FileService::getInstance()->copy_resize_image($_FILES['upthumb']['tmp_name'][$key], $arrayFile['id'].'_thumb_large', $key, 'jpg', 720, 360);

					}
				}
			}
			$this->redirect(OW::getRouter()->urlForRoute('spdownload.up_myfile', array('userId' => OW::getUser()->getId())));
		}
	}

	public function myFile($params)
	{
		if ( $params['userId'] != OW::getUser()->getId() )
        {
            throw new Redirect404Exception();
        }
        $page = empty($_GET['page']) ? 1 : $_GET['page'];
        $rpp = 5;
        $first = ($page - 1) * $rpp;
        $count = $rpp;

        $fileId = 0;
        $softs = SPDOWNLOAD_BOL_FileService::getInstance()->getFileListPage( OW::getUser()->getId(), $first, $rpp );
        $itemCount = SPDOWNLOAD_BOL_FileService::getInstance()->getCountFile( OW::getUser()->getId() );
        $pageCount = ceil($itemCount / $rpp);
        $this->addComponent('paging', new BASE_CMP_Paging($page, $pageCount, 5));

		$url = OW::getPluginManager()->getPlugin('spdownload')->getUserFilesUrl();
    	foreach ($softs as $key => $value) {
    		$value->addedTime   = date("Y-m-d H:i:s", $value->addedTime);
    		$value->updated  = date("Y-m-d H:i:s", $value->updated);
            $nameImage          = 'icon_small_'.$value->id.'.png';
            $value->icon        = $url.$nameImage;
        	$rate = BOL_RateService::getInstance()->findRateInfoForEntityItem($value->id, self::RATES_ENTITY_TYPE);
        	if (!empty($rate))
    			$value->avg_score = $rate["avg_score"];

    		$value->url = OW::getRouter()->urlForRoute('spdownload.uploadId', array('fileId' => $value->id.'-'.$value->slug ));
    	}
    	$this->assign('softs', $softs);
        
	}

	private function text( $prefix, $key, array $vars = null )
	{
	  return OW::getLanguage()->text($prefix, $key, $vars);
	}


}
