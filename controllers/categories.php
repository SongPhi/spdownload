<?php

class SPDOWNLOAD_CTRL_Categories extends OW_ActionController
{
	public function __construct() 
	{ 
		$checkpermissions = new SPDOWNLOAD_CLASS_Permissions();
       	$checkpermissions->getInstance()->checkpageurl('create_category');
		$arrayCheck = $checkpermissions->getInstance()->checkpageclick('create_category');
		$this->assign('addNew_promoted', $arrayCheck['promoted']);
		$this->assign('addNew_isAuthorized', $arrayCheck['isAuthorized']);
	}

	public function index( $params ) 
	{
		
		$this->setPageTitle(OW::getLanguage()->text('spdownload', 'category_index_page_title')); 
		$this->setPageHeading(OW::getLanguage()->text('spdownload', 'category_index_page_heading')); 

		$category = array();
		if (!empty($params) && isset($params['categoryId'])) {
			$category = SPDOWNLOAD_BOL_CategoryDao::getInstance()->findById($params['categoryId']);
		}

		$downloads = SPDOWNLOAD_BOL_CategoryService::getInstance()->getCategoryList();
		$downloadCategories = array();
		foreach ($downloads as $key => $value) {
			$downloadCategories[$value->id] = $value->name;
		}
	
		$form = new Form('add_category');
		$this->addForm($form);

		// Create selectbox 
		$fieldTo = new SelectBox('parent_category');
		foreach ( $downloadCategories as $key => $label )
		{
			$fieldTo->addOption($key, $label);
		}
		if (!empty($params) && isset($params['categoryId'])) $fieldTo->setValue($category->parentId);
		$fieldTo->setLabel(OW::getLanguage()->text('spdownload', 'ad_parent_category'));
        $form->addElement($fieldTo);

        $fieldCate = new TextField('category');
		$fieldCate->setLabel(OW::getLanguage()->text('spdownload', 'ad_label_category'));
		if (!empty($params) && isset($params['categoryId'])) $fieldCate->setValue($category->name);
		$fieldCate->setRequired();
		$fieldCate->setHasInvitation(true);
		$form->addElement($fieldCate);

		$submit = new Submit('add');
        $submit->setValue(OW::getLanguage()->text('spdownload', 'form_add_category_submit'));
        $form->addElement($submit);

        if ( OW::getRequest()->isPost() )
        {
            if ( $form->isValid($_POST) )
            {
                $data = $form->getValues();
                if ($data['parent_category'] == null) {
			        $data['parent_category'] = 0;
			    }

			    if (!empty($params) && isset($params['categoryId'])) 
			    {
			    	SPDOWNLOAD_BOL_CategoryService::getInstance()->addCategory($data['category'], $data['parent_category'], $params['categoryId']);
			    } else {
                	SPDOWNLOAD_BOL_CategoryService::getInstance()->addCategory($data['category'], $data['parent_category']);
			    }
                $this->redirect(OW::getRouter()->urlForRoute('spdownload.category_list'));
            }
        }
	}

	public function cateList()
	{
		$this->setPageTitle(OW::getLanguage()->text('spdownload', 'index_category_title')); 
        $this->setPageHeading(OW::getLanguage()->text('spdownload', 'index_category_heading')); 

		$categories = SPDOWNLOAD_BOL_CategoryService::getInstance()->getCategoryList();
		foreach ($categories as $key => $value) {
			if ($value->parentId == 0) {
				$value->parentId = null;
			} else {
				$value->parentId = SPDOWNLOAD_BOL_CategoryDao::getInstance()->findById($value->parentId)->name;

			}

			$value->urlDelete = OW::getRouter()->urlForRoute('spdownload.category_delete', array('categoryId' => $value->id));
			$value->countParent = SPDOWNLOAD_BOL_CategoryService::getInstance()->getCountCategoryParent($value->id);
		}
		$this->assign('categories', $categories);	
	}

	public function add()
	{

	}

	public function edit()
	{

	}

	public function delete( $params )
	{
		$countParent = SPDOWNLOAD_BOL_CategoryService::getInstance()->getCountCategoryParent($params['categoryId']);
		if ($countParent != 0) throw new Redirect404Exception();
		SPDOWNLOAD_BOL_FileCategoryService::getInstance()->deleteId( $params );
		SPDOWNLOAD_BOL_CategoryService::getInstance()->deleteIdCategory( $params['categoryId'] );

		$this->redirect(OW::getRouter()->urlForRoute('spdownload.category_list'));
	}
}