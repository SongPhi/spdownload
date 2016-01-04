<?php

class SPDOWNLOAD_CMP_Thumbnail extends OW_Component
{
	public function __construct($params)
    {
        $document = OW::getDocument();
        $plugin = OW::getPluginManager()->getPlugin('spdownload');
        $document->addStyleSheet($plugin->getStaticCssUrl() . 'jsslide-skin.css');
        $document->addStyleSheet($plugin->getStaticCssUrl() . 'font-awesome.min.css');
        OW::getDocument()->addScript($plugin->getStaticJsUrl().'jquery.slides.min.js');

        $url = OW::getPluginManager()->getPlugin('spdownload')->getUserFilesUrl();
        $thumbnails = SPDOWNLOAD_BOL_FileService::getInstance()->getThumbnailList($params);
        foreach ($thumbnails as $key => $value) {
            $value->uri = $url.$value->fileId.'_thumb_small_'.$value->uri.'.jpg';
        }

        $script = "
            $(function(){
              $('#slides').slidesjs({
                width: 362,
                height: 182,
                navigation: false,
                pagination: false
              });
                var countThumb = $('img.slidesjs-slide').length;
                if (countThumb <= 1) {
                    $('.slidesjs-navigation').css('display','none');
                }
            });
        ";
        OW::getDocument()->addOnloadScript($script);

        $this->assign('thumbnails', $thumbnails);
    }
}