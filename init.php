<?php 



OW::getRouter()->addRoute(new OW_Route('spdownload.index', 'downloads', "SPDOWNLOAD_CTRL_Downloads", 'index')); 
OW::getRouter()->addRoute(new OW_Route('spdownload.browse', 'downloads/browse', "SPDOWNLOAD_CTRL_Downloads", 'browse')); 
OW::getRouter()->addRoute(new OW_Route('spdownload.filedetail', 'downloads/:fileId', "SPDOWNLOAD_CTRL_Downloads", 'detail')); 
OW::getRouter()->addRoute(new OW_Route('spdownload.getfile', 'downloads/:fileId/get/:versionId/:vFileName', "SPDOWNLOAD_CTRL_Downloads", 'getfile')); 
OW::getRouter()->addRoute(new OW_Route('spdownload.getlatestfile', 'downloads/:fileId/get', "SPDOWNLOAD_CTRL_Downloads", 'getlatestfile')); 
OW::getRouter()->addRoute(new OW_Route('spdownload.admin', 'admin/plugins/spdownload', "SPDOWNLOAD_CTRL_Admin", 'index'));
OW::getRouter()->addRoute(new OW_Route('spdownload.download_file', 'downloads/download-file/:id', 'SPDOWNLOAD_CTRL_Download', 'downloadFile'));

OW::getRouter()->addRoute(new OW_Route('spdownload.uploads', 'uploads', "SPDOWNLOAD_CTRL_Uploads", 'index')); 
OW::getRouter()->addRoute(new OW_Route('spdownload.uploadId', 'uploads/:fileId', "SPDOWNLOAD_CTRL_Uploads", 'index')); 
OW::getRouter()->addRoute(new OW_Route('spdownload.up_myfile', 'uploads/my-file/:userId', "SPDOWNLOAD_CTRL_Uploads", 'myFile')); 
OW::getRouter()->addRoute(new OW_Route('spdownload.update', 'uploads/:fileId/update', "SPDOWNLOAD_CTRL_Uploads", 'update')); 

OW::getRouter()->addRoute(new OW_Route('spdownload.file', 'deletes/file/:fileId', "SPDOWNLOAD_CTRL_Deletes", 'file')); 

OW::getRouter()->addRoute(new OW_Route('spdownload.category', 'categories', "SPDOWNLOAD_CTRL_Categories", 'index')); 
OW::getRouter()->addRoute(new OW_Route('spdownload.category_edit', 'categories/:categoryId', "SPDOWNLOAD_CTRL_Categories", 'index')); 
OW::getRouter()->addRoute(new OW_Route('spdownload.category_list', 'categories/list', "SPDOWNLOAD_CTRL_Categories", 'cateList')); 
OW::getRouter()->addRoute(new OW_Route('spdownload.category_delete', 'categories/:categoryId/delete', "SPDOWNLOAD_CTRL_Categories", 'delete')); 

OW::getRouter()->addRoute(new OW_Route('spdownload.imgpng', 'downloads/imgpng', "SPDOWNLOAD_CTRL_Downloads", 'imgpng')); 
SPDOWNLOAD_CLASS_EventHandler::getInstance()->init();