<?php

class SPDOWNLOAD_CTRL_Admin extends ADMIN_CTRL_Abstract
{
 
    public function index()
    {
        $this->setPageTitle(OW::getLanguage()->text('spdownload', 'admin_index_page_title')); 
		$this->setPageHeading(OW::getLanguage()->text('spdownload', 'admin_index_page_heading')); 


		$downloads = SPDOWNLOAD_BOL_CategoryService::getInstance()->getCategoryList();
		$downloadCategories = array();
		foreach ($downloads as $key => $value) {
			$downloadCategories[$value->id] = $value->name;
		}
	
		$form = new Form('add_category');
		$this->addForm($form);

		// Create selectbox 
		$fieldTo = new Selectbox('parent_category');
		foreach ( $downloadCategories as $key => $label )
		{
		  $fieldTo->addOption($key, $label);
		}
		$fieldTo->setLabel(OW::getLanguage()->text('spdownload', 'ad_parent_category'));
        $form->addElement($fieldTo);

        $fieldCate = new TextField('category');
		$fieldCate->setLabel(OW::getLanguage()->text('spdownload', 'ad_label_category'));
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
                SPDOWNLOAD_BOL_CategoryService::getInstance()->addCategory($data['category'], $data['parent_category']);
                $this->redirect();
            }
        }

    }
 
}