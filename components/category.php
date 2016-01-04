<?php

class SPDOWNLOAD_CMP_Category extends OW_Component
{
    public function __construct($formWrap = true, $arrayId = array())
    {
        $document = OW::getDocument();
        $plugin = OW::getPluginManager()->getPlugin('spdownload');
        $document->addStyleSheet($plugin->getStaticCssUrl() . 'category.css');
        
        parent::__construct();

        $categories = SPDOWNLOAD_BOL_CategoryService::getInstance()->getCategoryParentId(0);
        $arResult = array();
        $number = 0;

        $flatten_categories = $this->getFlattenCategories();
        $this->assign('categories', $flatten_categories);

        
        if (empty($_GET['ct']) || !isset($_GET['ct'])) {
            $this->assign('arrayId', $arrayId);
        } else {
            $arrayId = $_GET['ct'];
            $this->assign('arrayId', $arrayId);
        }
        $script = "
            $('.checkboxclass').click(function(){
                $('#browseForm').submit();            
            });
        ";
        OW::getDocument()->addOnloadScript($script);

        $this->assign('formWrap', $formWrap);

        $addNew_promoted = false;
        $addNew_isAuthorized = false;
        if (OW::getUser()->isAuthenticated())
        {
            if (OW::getUser()->isAuthorized('spdownload', 'create_category'))
            {
                $addNew_isAuthorized = true;
            }
            else
            {
                $status = BOL_AuthorizationService::getInstance()->getActionStatus('spdownload', 'create_category');

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


        $this->assign('addNew_isAuthorized', $addNew_isAuthorized);
        $this->assign('addNew_promoted', $addNew_promoted);
    }

    public function getFlattenCategories($parentId = 0, $level = 0, $categories = array()) {
        $fetchCategories = SPDOWNLOAD_BOL_CategoryService::getInstance()->getCategoryParentId($parentId);
        foreach ($fetchCategories as $category) {
            $category->level = $level;
            array_push($categories, $category);
            $categories = $this->getFlattenCategories($category->id, $level + 1, $categories);
        }
        return $categories;
    }

}
