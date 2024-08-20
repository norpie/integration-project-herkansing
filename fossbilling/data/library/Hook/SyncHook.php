<?php
namespace Hook;

class SyncHook
{
    public static function onBeforeAdminCreateClient($data)
    {
        error_log("onBeforeAdminCreateClient: " . print_r($data['params'], true));
    }

    public static function onBeforeAdminClientUpdate($data)
    {
        error_log("onBeforeAdminClientUpdate: " . print_r($data['params'], true));
    }

    public static function onBeforeAdminClientDelete($data)
    {
        error_log("onBeforeAdminClientDelete: " . print_r($data['params'], true));
    }
}
?>
