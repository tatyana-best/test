<?

AddEventHandler("main", "OnProlog", Array("MyClass", "MyOnPrologHandler"));

class MyClass
{
    function MyOnPrologHandler()
    {
        $arJsConfig = array(
            'custom_main' => array(
                'js' => '/local/php_interface/js_libs/main.js',
                'css' => '/local/php_interface/js_libs/main.css',
                'lang' => '/local/php_interface/js_libs/lang/'.LANGUAGE_ID.'/lang.php',
                'rel' => Array("main", "window")
            ),
            'custom_ajax' => array(
                'js' => '/local/php_interface/js_libs/ajax.js',
                'css' => '/local/php_interface/js_libs/main.css',
                'rel' => Array("ajax", "window")
            ),
            'custom_popup' => array(
                'js' => '/local/php_interface/js_libs/popup.js',
                'css' => '/local/php_interface/js_libs/main.css',
                'rel' => Array("ajax", "popup", "window")
            ),
            'custom_fx' => array(
                'js' => '/local/php_interface/js_libs/fx.js',
                'css' => '/local/php_interface/js_libs/main.css',
                'rel' => Array("fx", "ajax", "window")
            ),
            'custom_viewer' => array(
                'js' => '/local/php_interface/js_libs/viewer.js',
                'css' => '/local/php_interface/js_libs/main.css',
                'rel' => Array("window", "viewer")
            ),

        );

        foreach ($arJsConfig as $ext => $arExt) {
            \CJSCore::RegisterExt($ext, $arExt);
        }

    }
}