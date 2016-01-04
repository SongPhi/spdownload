<?php

class SPDOWNLOAD_CTRL_Downloads extends OW_ActionController
{
    public function index()
    {
        $document = OW::getDocument();
        $plugin = OW::getPluginManager()->getPlugin('spdownload');
        $this->setPageTitle(OW::getLanguage()->text('spdownload', 'index_page_title')); 
        $this->setPageHeading(OW::getLanguage()->text('spdownload', 'index_page_heading')); 

        OW::getDocument()->addScript($plugin->getStaticJsUrl().'masonry.pkgd.min.js');

        $cmpCategories = new SPDOWNLOAD_CMP_Category();
        $this->addComponent('cmpCategories', $cmpCategories);

        $this->assign('myId', OW::getUser()->getId());

        $addNew_promoted = false;
        $addNew_isAuthorized = false;
        if (OW::getUser()->isAuthenticated())
        {
            if (OW::getUser()->isAuthorized('spdownload', 'upload'))
            {
                $addNew_isAuthorized = true;
            }
            else
            {
                $status = BOL_AuthorizationService::getInstance()->getActionStatus('spdownload', 'upload');
                if ($status['status'] == BOL_AuthorizationService::STATUS_PROMOTED)
                {
                    $addNew_promoted = true;
                    $addNew_isAuthorized = true;
                }
                else
                {
                    $addNew_isAuthorized = false;
                }
                $script = '$(".error_permit").click(function(){
                        OW.authorizationLimitedFloatbox('.json_encode($status['msg']).');
                        return false;
                    });';
                OW::getDocument()->addOnloadScript($script);
            }
        }

        $filesnewup = array();
        $filesnewup[0] = SPDOWNLOAD_BOL_FileService::getInstance()->getFileListNewUpload(0,5);
        $filesnewup[1] = SPDOWNLOAD_BOL_FileService::getInstance()->getFileListNewUpload(5,10);
       
        $url = OW::getPluginManager()->getPlugin('spdownload')->getUserFilesUrl();
        $noimage = OW::getPluginManager()->getPlugin('spdownload')->getStaticUrl().'images/' . 'icon_large.png';
        foreach ($filesnewup as $file) {
            foreach ($file as $value) {
                $nameImage      = 'icon_large_'.$value->id.'.png';
                $value->icon    = $url.$nameImage;
                $file_headers   = @get_headers($value->icon);
                if($file_headers[0] == 'HTTP/1.1 404 Not Found')
                {
                    $value->icon  = $noimage;
                }
                $value->urldetail = OW::getRouter()->urlForRoute('spdownload.filedetail', array('fileId' => $value->id.'-'.$value->slug));
            }
        }

        $filesmostdow = array();
        $filesmostdow[0] = SPDOWNLOAD_BOL_FileService::getInstance()->getFileListMostDown(0,5);
        $filesmostdow[1] = SPDOWNLOAD_BOL_FileService::getInstance()->getFileListMostDown(5,10);
       
        foreach ($filesmostdow as $file) {
            foreach ($file as $value) {
                $nameImage      = 'icon_large_'.$value->id.'.png';
                $value->icon    = $url.$nameImage;
                $file_headers   = @get_headers($value->icon);
                if($file_headers[0] == 'HTTP/1.1 404 Not Found')
                {
                    $value->icon  = $noimage;
                }
                $value->urldetail = OW::getRouter()->urlForRoute('spdownload.filedetail', array('fileId' => $value->id.'-'.$value->slug));
            }
        }
        

        $this->assign('filesmostdow', $filesmostdow);
        $this->assign('filesnewup', $filesnewup);
        $this->assign('addNew_isAuthorized', $addNew_isAuthorized);
        $this->assign('addNew_promoted', $addNew_promoted);
    }

	public function browse()
	{
		$categoryList = array();
        $form = new Form('download_form');
        $this->addForm($form);

        $cmpCategories = new SPDOWNLOAD_CMP_Category();
        $this->addComponent('cmpCategories', $cmpCategories);		
		$arraylabel = array(
			'label_table_th1' => OW::getLanguage()->text('spdownload', 'label_table_th1'),
			'label_table_th2' => OW::getLanguage()->text('spdownload', 'label_table_th2'),
			'label_table_th3' => OW::getLanguage()->text('spdownload', 'label_table_th3')
		);

        $files = null ;
        $arrayId = array();
		if (empty($_GET['ct']) || !isset($_GET['ct'])) {
            OW::getApplication()->redirect(OW::getRouter()->urlForRoute('spdownload.index'));
		} else {
            $arrayId = $_GET['ct'];
			$files = SPDOWNLOAD_BOL_FileService::getInstance()->getFileCategoryIdList($arrayId);
            $this->assign('arrayId', $arrayId);
		}
        $url = OW::getPluginManager()->getPlugin('spdownload')->getUserFilesUrl();
        foreach ($files as $key => $value) {
            $value->addedTime   = date("Y-m-d H:i:s", $value->addedTime);
            $nameImage          = 'icon_small_'.$value->id.'.png';
            $value->icon        = $url.$nameImage;
        }
		$this->assign('files', $files);
		$this->assign('arraylabel', $arraylabel);
		
	}

	public function getfile($params)
	{
        if ( !OW::getUser()->isAuthenticated() )
        {
            throw new AuthenticateException();
        }

        if ( !OW::getUser()->isAuthorized('spdownload', 'download') )
        {
            $status = BOL_AuthorizationService::getInstance()->getActionStatus('spdownload', 'download');
            throw new AuthorizationException($status['msg']);

            return;
        }

        $versionId = $params['versionId'];
        $vFileName = $params['vFileName'];
        $arraylabellicense = array(
            'label_license' => OW::getLanguage()->text('spdownload', 'label_license'),
            'title_license' => OW::getLanguage()->text('spdownload', 'title_license')
        );

        $form = new Form('download_form');
        $this->addForm($form);
        $cmpCategories = new SPDOWNLOAD_CMP_Category();
        $this->addComponent('cmpCategories', $cmpCategories);
        $file = SPDOWNLOAD_BOL_VersionService::getInstance()->getFileId($versionId);


        $fieldLicense = new Textarea('message');
        $fieldLicense->setValue(OW::getLanguage()->text('spdownload', 'content_license'));
        $fieldLicense->setRequired();
        $form->addElement($fieldLicense);
         
        $fieldAgree = new CheckboxField('agreeCheck');
        $fieldAgree->setLabel(OW::getLanguage()->text('spdownload', 'label_content_license'));
        $fieldAgree->setValue(1);
        $form->addElement($fieldAgree);

        $submit = new Submit('downloadfile');
        $submit->setValue($this->text('spdownload', 'form_label_download'));
        $form->addElement($submit);

        $this->addForm($form);
        $this->assign('arraylabellicense',$arraylabellicense);
        if ( $file === NULL )
        {
            throw new Redirect404Exception();
        }
       
        if ( OW::getRequest()->isPost() ) {
            $data = $form->getValues();
            if ($data['agreeCheck']) {
                $event = new OW_Event('spdownload.onDownloadFile', array('id' => $file->id));
                OW::getEventManager()->trigger($event);
                $data = $event->getData();

                if ( $data !== null )
                {
                    $path = $data;
                }
                else
                {
                    $path = SPDOWNLOAD_BOL_FileService::getInstance()->getFilePath($file->id);
                }


                
                if ( ini_get('zlib.output_compression') )
                {
                    ini_set('zlib.output_compression', 'Off');
                }
                
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: private', false);
                header('Content-Type:'.$file->mimeType);
                header('Content-Description: File Transfer');
                header('Content-Disposition: attachment; filename="' . basename($file->filename) . '";');
                header('Content-Transfer-Encoding: binary');
                header('Content-Length: ' . filesize($path));
                ob_end_clean();
                readfile($path);
                die();
            }
        }
        $urlback = OW::getRouter()->urlForRoute('spdownload.index');
        $script = "
            $('#submit_downloadfile').click(function(){
                setTimeout(function(){ window.location.href = '$urlback'; }, 1000);
                
            });
        ";
        OW::getDocument()->addOnloadScript($script);
	}

    public function getlatestfile( $params ) 
    {
        if ( !OW::getUser()->isAuthenticated() )
        {
            throw new AuthenticateException();
        }

        if ( !OW::getUser()->isAuthorized('spdownload', 'download') )
        {
            $status = BOL_AuthorizationService::getInstance()->getActionStatus('spdownload', 'download');
            throw new AuthorizationException($status['msg']);

            return;
        }
        $params['fileId'] = substr($params['fileId'],0,strrpos($params['fileId'], "-"));
        $filevernew = SPDOWNLOAD_BOL_VersionService::getInstance()->getFileVerNew($params['fileId']);
        $params['versionId'] = $filevernew[0]->id;
        $params['vFileName'] = $filevernew[0]->filename;

        $this->getfile($params);
    }

    public function detail($params)
    {

        $document = OW::getDocument();
        $plugin = OW::getPluginManager()->getPlugin('spdownload');
        $document->addStyleSheet($plugin->getStaticCssUrl() . 'file_detail.css');
        $check = $params['fileId'];

        if (!strrpos($params['fileId'], "-"))  throw new Redirect404Exception();

        $params['fileId'] = substr($params['fileId'],0,strrpos($params['fileId'], "-"));
        $file = SPDOWNLOAD_BOL_FileService::getInstance()->getFileId($params['fileId']);

        if ($file->id.'-'.$file->slug != $check) throw new Redirect404Exception();

        $cmpThumbnails = new SPDOWNLOAD_CMP_Thumbnail($params['fileId']);
        $this->addComponent('cmpThumbnails', $cmpThumbnails);

        $params['authorId'] = $file->authorId;
        $params['quantitySoft'] = 2;
        $cmpRelatedItemUser = new SPDOWNLOAD_CMP_RelatedItemUser($params);
        $this->addComponent('cmpRelatedItemUser', $cmpRelatedItemUser);

        $filevernew = SPDOWNLOAD_BOL_VersionService::getInstance()->getFileVerNew($params['fileId']);
        $limit = 3;
        $fileverold = SPDOWNLOAD_BOL_VersionService::getInstance()->getFileOldVersion($params['fileId'], $filevernew[0]->id, $limit);
        $filevernew[0]->size = $this->splitFilesize($filevernew[0]->size);

        foreach ($fileverold as $key => $value) {
            $value->size = $this->splitFilesize($value->size);
        }
        // rate
        $rateInfo = new BASE_CMP_Rate('spdownload', 'spdownload-software', $file->getId(), $file->authorId);
        $this->addComponent('rate', $rateInfo);
        // comments

        $allow_comments = true;
       
        if ( $file->authorId != OW::getUser()->getId() && !OW::getUser()->isAuthorized('spdownload') )
        {
            $eventParams = array(
                'action' => 'spdownload_comment_spdownload-posts',
                'ownerId' => $file->authorId,
                'viewerId' => OW::getUser()->getId()
            );

            try
            {
                OW::getEventManager()->getInstance()->call('privacy_check_permission', $eventParams);
            }
            catch ( RedirectException $ex )
            {
                $allow_comments = false;
            }
        }

        $cmpParams = new BASE_CommentsParams('spdownload', 'spdownload-post'); 
        $cmpParams->setEntityId($file->getId())
            ->setOwnerId($file->authorId)
            ->setDisplayType(BASE_CommentsParams::DISPLAY_TYPE_BOTTOM_FORM_WITH_FULL_LIST)
            ->setAddComment($allow_comments);
        $this->addComponent('comments', new BASE_CMP_Comments($cmpParams));

        $arraylabel = array(
            "filename" => OW::getLanguage()->text('spdownload', 'label_name'),
            "filesize" => OW::getLanguage()->text('spdownload', 'label_size'),
            "filetype" => OW::getLanguage()->text('spdownload', 'label_type'),
            "filedown" => OW::getLanguage()->text('spdownload', 'label_download')
        );
        $url = OW::getPluginManager()->getPlugin('spdownload')->getUserFilesUrl();
        $nameImage          = 'icon_large_'.$file->id.'.png';
        $file->icon = $url.$nameImage;

        $addNew_promoted = false;
        $addNew_isAuthorized = false;
        if (OW::getUser()->isAuthenticated())
        {
            if (OW::getUser()->isAuthorized('spdownload', 'download'))
            {
                $addNew_isAuthorized = true;
            }
            else
            {
                $status = BOL_AuthorizationService::getInstance()->getActionStatus('spdownload', 'download');
                if ($status['status'] == BOL_AuthorizationService::STATUS_PROMOTED)
                {
                    $addNew_promoted = true;
                    $addNew_isAuthorized = true;
                }
                else
                {
                    $addNew_isAuthorized = false;
                }
                $script = '$("#btn-download-file").click(function(){
                        OW.authorizationLimitedFloatbox('.json_encode($status['msg']).');
                        return false;
                    });';
                OW::getDocument()->addOnloadScript($script);
            }
        }

        $CategoryIdList = SPDOWNLOAD_BOL_FileCategoryService::getInstance()->getCategoryId($params['fileId']);
        $arrayCategory = array();
        foreach ($CategoryIdList as $key => $value) {
            $categories = SPDOWNLOAD_BOL_CategoryDao::getInstance()->findById($value->categoryId);
            $arrayCategory[$value->categoryId] = $categories->name;
        }
        
        $this->assign('arrayCategory', $arrayCategory);
        $this->assign('addNew_isAuthorized', $addNew_isAuthorized);
        $this->assign('addNew_promoted', $addNew_promoted);
        
        $this->assign('file', $file);
        $this->assign('var', $file->id);
        $this->assign('filevernew', $filevernew[0]);
        $this->assign('fileverold', $fileverold);
        $this->assign('arraylabel', $arraylabel);
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

    private function text( $prefix, $key, array $vars = null )
    {

      return OW::getLanguage()->text($prefix, $key, $vars);
    }

}