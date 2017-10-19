<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf2a9b219479bdb80ee3144e97f64fea9
{
    public static $files = array (
        '2fb9d6f23c8e8faefc193a4cde0cab4f' => __DIR__ . '/..' . '/joomla/string/src/phputf8/utf8.php',
        'e6851e0ae7328fe5412fcec73928f3d9' => __DIR__ . '/..' . '/joomla/string/src/phputf8/ord.php',
        'd9ad1b7c85c100a18c404a13824b846e' => __DIR__ . '/..' . '/joomla/string/src/phputf8/str_ireplace.php',
        '62bad9b6730d2f83493d2337bf61519d' => __DIR__ . '/..' . '/joomla/string/src/phputf8/str_pad.php',
        'c4d521b8d54308532dce032713d4eec0' => __DIR__ . '/..' . '/joomla/string/src/phputf8/str_split.php',
        'fa973e71cace925de2afdc692b861b1d' => __DIR__ . '/..' . '/joomla/string/src/phputf8/strcasecmp.php',
        '0c98c2f1295d9f4d093cc77d5834bb04' => __DIR__ . '/..' . '/joomla/string/src/phputf8/strcspn.php',
        'a52639d843b4094945115c178a91ca86' => __DIR__ . '/..' . '/joomla/string/src/phputf8/stristr.php',
        '73ee7d0297e683c4c2e7798ef040fb2f' => __DIR__ . '/..' . '/joomla/string/src/phputf8/strrev.php',
        'd55633c05ddb996e0005f35debaa7b5b' => __DIR__ . '/..' . '/joomla/string/src/phputf8/strspn.php',
        '944e69d23b93558fc0714353cf0c8beb' => __DIR__ . '/..' . '/joomla/string/src/phputf8/trim.php',
        '31264bab20f14a8fc7a9d4265d91ee98' => __DIR__ . '/..' . '/joomla/string/src/phputf8/ucfirst.php',
        '05d739a990f75f0c44ebe1f032b33148' => __DIR__ . '/..' . '/joomla/string/src/phputf8/ucwords.php',
        '4292e2fa66516089e6006723267587b4' => __DIR__ . '/..' . '/joomla/string/src/phputf8/utils/ascii.php',
        '87465e33b7551b401bf051928f220e9a' => __DIR__ . '/..' . '/joomla/string/src/phputf8/utils/validation.php',
    );

    public static $prefixLengthsPsr4 = array (
        'J' => 
        array (
            'Joomla\\String\\' => 14,
            'Joomla\\Language\\' => 16,
            'Joomla\\Date\\' => 12,
        ),
        'C' => 
        array (
            'Curl\\' => 5,
            'CodeCampaign\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Joomla\\String\\' => 
        array (
            0 => __DIR__ . '/..' . '/joomla/string/src',
        ),
        'Joomla\\Language\\' => 
        array (
            0 => __DIR__ . '/..' . '/joomla/language/src',
        ),
        'Joomla\\Date\\' => 
        array (
            0 => __DIR__ . '/..' . '/joomla/date/src',
        ),
        'Curl\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-curl-class/php-curl-class/src/Curl',
        ),
        'CodeCampaign\\' => 
        array (
            0 => __DIR__ . '/..' . '/maxpumpe/code-champain/src/Curl',
        ),
    );

    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'SimpleValidator\\' => 
            array (
                0 => __DIR__ . '/..' . '/simple-validator/simple-validator/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf2a9b219479bdb80ee3144e97f64fea9::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf2a9b219479bdb80ee3144e97f64fea9::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitf2a9b219479bdb80ee3144e97f64fea9::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
