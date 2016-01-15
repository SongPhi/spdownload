<?php

class SPDOWNLOAD_CLASS_Permissions
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

    public function checkpageclick(  $author , $moderator = null )
    {
        if ( $moderator == null ) $moderator = $author;
        $addNew_promoted = false;
        $addNew_isAuthorized = false;
        if (OW::getUser()->isAuthenticated())
        {
            if (OW::getUser()->isAuthorized('spdownload', $author))
            {
                $addNew_isAuthorized = true;
            }
            else
            {
                $status = BOL_AuthorizationService::getInstance()->getActionStatus('spdownload', $moderator);

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
        $addNew_isAuthorized = ($addNew_isAuthorized ? '1':'0');
        $addNew_promoted = ($addNew_promoted ? '1':'0');
        $arrayaddNew = array(
            'isAuthorized'   => $addNew_isAuthorized,
            'promoted'       => $addNew_promoted
            );
        return $arrayaddNew;
    }

    public function checkpageurl( $author )
    {
        if ( !OW::getUser()->isAuthenticated() )
        {
            throw new AuthenticateException();
        }

        if ( !OW::getUser()->isAuthorized('spdownload', $author) )
        {
            $status = BOL_AuthorizationService::getInstance()->getActionStatus('spdownload', $author);
            throw new AuthorizationException($status['msg']);

            return;
        }
    }
}