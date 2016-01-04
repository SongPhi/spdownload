
<?php
BOL_LanguageService::getInstance()->addPrefix('spdownload','Simple Downloads');

$sql = "CREATE TABLE IF NOT EXISTS`" . OW_DB_PREFIX . "spdownload_categories` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(200) NOT NULL,
    `parentId` INT(11) DEFAULT 0,
    `thumbnail` TEXT,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM
ROW_FORMAT=DEFAULT";
 
OW::getDbo()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS`" . OW_DB_PREFIX . "spdownload_files` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(200) NOT NULL,
    `description` TEXT,
    `slug` VARCHAR(200),
    `downloads` INT(11) DEFAULT 0,
    `authorId` INT(11) NOT NULL,
    `addedTime` INT(11),
    `updated` INT(11),
    
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM
ROW_FORMAT=DEFAULT";
 
OW::getDbo()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS`" . OW_DB_PREFIX . "spdownload_versions` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `size` INT(11),
    `filename` VARCHAR(200) NOT NULL,
    `mimeType` VARCHAR(200),
    `downloads` INT(11) DEFAULT 0,
    `addedTime` INT(11),
    `fileId` INT(11) NOT NULL,

    PRIMARY KEY (`id`)
)
ENGINE=MyISAM
ROW_FORMAT=DEFAULT";
 
OW::getDbo()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS`" . OW_DB_PREFIX . "spdownload_files_categories` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `fileId` INT(11),
    `categoryId` INT(11),

    PRIMARY KEY (`id`)
)
ENGINE=MyISAM
ROW_FORMAT=DEFAULT";
 
OW::getDbo()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS`" . OW_DB_PREFIX . "spdownload_thumbnails` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `fileId` INT(11),
    `uri` TEXT,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM
ROW_FORMAT=DEFAULT";
 
OW::getDbo()->query($sql);

$authorization = OW::getAuthorization();
$groupName = 'spdownload';
$authorization->addGroup($groupName);
$authorization->addAction($groupName, 'create_category');
$authorization->addAction($groupName, 'add_comment');
$authorization->addAction($groupName, 'upload');
$authorization->addAction($groupName, 'download');
$authorization->addAction($groupName, 'view', true);


OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('spdownload')->getRootDir().'langs.zip', 'download');

OW::getPluginManager()->addPluginSettingsRouteName('spdownload', 'spdownload.admin');