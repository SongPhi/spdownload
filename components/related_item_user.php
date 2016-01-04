<?php

class SPDOWNLOAD_CMP_RelatedItemUser extends OW_Component
{
	const RATES_ENTITY_TYPE = 'spdownload-software';
	public function __construct($params)
    {
    	$softs = SPDOWNLOAD_BOL_FileService::getInstance()->getFileItemUser($params['fileId'], $params['authorId'], $params['quantitySoft']);
    	$url = OW::getPluginManager()->getPlugin('spdownload')->getUserFilesUrl();

    	foreach ($softs as $key => $value) {
            $nameImage          = 'icon_small_'.$value->id.'.png';
            $value->icon        = $url.$nameImage;
        	$rate = BOL_RateService::getInstance()->findRateInfoForEntityItem($value->id, self::RATES_ENTITY_TYPE);
        	if (!empty($rate))
    			$value->avg_score = $rate["avg_score"];
    	}
    	$this->assign('softs', $softs);
    }
}