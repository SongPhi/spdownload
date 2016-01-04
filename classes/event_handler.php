<?php

class SPDOWNLOAD_CLASS_EventHandler
{
    private static $classInstance;

    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    public function __construct() { }

    public function addAuthLabels( BASE_CLASS_EventCollector $event )
    {
        $language = OW::getLanguage();
        $event->add(
            array(
                'spdownload' => array(
                    'label' => $language->text('spdownload', 'label'),
                    'actions' => array(
                        'download' => $language->text('spdownload', 'auth_action_label_download'),
                        'upload' => $language->text('spdownload', 'auth_action_label_upload'),
                        'view' => $language->text('spdownload', 'auth_action_label_view'),
                        'add_comment' => $language->text('spdownload', 'auth_action_label_add_comment'),
                        'create_category' => $language->text('spdownload', 'auth_action_label_create_category'),
                    )
                )
            )
        );
    }

    public function init()
    {
        $this->genericInit();
        $em = OW::getEventManager();

    }

    public function genericInit()
    {
        $em = OW::getEventManager();
        $em->bind('admin.add_auth_labels', array($this, 'addAuthLabels'));
    }
}