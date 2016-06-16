<?php

/*
 * Common Component
 *
 */
App::uses('Component', 'Controller');
App::uses('Security', 'Utility');

/**
 * Common component
 *
 * @author Sanjeev
 */
class CommonComponent extends Component {

    /**
     * Other components used by this component
     *
     * @var array
     */
    public $components = array();

    /**
     * Stores current controller object
     *
     * @var Controller
     */
    public $controller;

    /**
     * initialize
     *
     * @param string $controller controller to use;
     *
     * @return string controller
     */
    public function initialize(Controller $controller) {
        parent::initialize($controller);
        $this->controller = $controller;
    }

    /**
     * Generates an API access key
     *
     * @author Sanjeev
     * @return string Access Key
     */
    public function generateApiAccessKey() {
        $accessKey = uniqid(md5(time()));
        return $accessKey;
    }

    /**
     * Generate a random password for user
     *
     * @param string $length must be number
     *
     * @return $randomString;
     */
    public function generateRandomValue($length = 10) {
        $characters = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    /**
     * Generate a random password for user
     *
     * @param string $length must be number
     *
     * @return $randomString;
     */
    public function generateRandomValueWithTime($length = 10) {
        $randomName = str_replace(".", "", strtotime("now"));
        $characters = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
        $characters = $randomName . md5($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        settype($fileName, "string");
        return $randomString;
    }

    /**
     * Generates a random user key string for user
     *
     * @param String $identifier Unique identifier i.e username
     *
     * @author Sanjeev
     * @return String Useq Key
     */
    public function generateUserKey($identifier = null) {
        $uString = !empty($identifier) ? $identifier : time();
        return uniqid(md5($uString));
    }

    /**
     * Generate a random user acount verification code
     *
     * @author Sanjeev
     *
     * @return unique key;
     */
    public function generateUserVerifyKey() {
        $digits = 4;
        $verifyString = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
        return uniqid(md5($verifyString));
    }

    /**
     * Parses a user agent string into its important parts
     *
     * @param string $uAgent for u agent
     *
     * @author Jesse G. Donat <donatj@gmail.com>
     * @link   https://github.com/donatj/PhpUserAgent
     * @link   http://donatstudios.com/PHP-Parser-HTTP_USER_AGENT
     * @return array an array with browser, version and platform keys
     */
    public function parseUserAgent($uAgent = null) {
        if (is_null($uAgent) && isset($_SERVER['HTTP_USER_AGENT'])) {
            $uAgent = $_SERVER['HTTP_USER_AGENT'];
        }

        $empty = array(
            'platform' => null,
            'browser' => null,
            'version' => null,
        );

        $data = $empty;

        if (!$uAgent) {
            return $data;
        }

        if (preg_match('/\((.*?)\)/im', $uAgent, $parentMatches)) {

            preg_match_all('/(?P<platform>Android|CrOS|iPhone|iPad|Linux|Macintosh|Windows(\ Phone\ OS)?|Silk|linux-gnu|BlackBerry|PlayBook|Nintendo\ (WiiU?|3DS)|Xbox)
				(?:\ [^;]*)?
				(?:;|$)/imx', $parentMatches[1], $result, PREG_PATTERN_ORDER);

            $priority = array('Android', 'Xbox');
            $result['platform'] = array_unique($result['platform']);
            if (count($result['platform']) > 1) {
                if ($keys = array_intersect($priority, $result['platform'])) {
                    $data['platform'] = reset($keys);
                } else {
                    $data['platform'] = $result['platform'][0];
                }
            } elseif (isset($result['platform'][0])) {
                $data['platform'] = $result['platform'][0];
            }
        }

        if ($data['platform'] == 'linux-gnu') {
            $data['platform'] = 'Linux';
        }
        if ($data['platform'] == 'CrOS') {
            $data['platform'] = 'Chrome OS';
        }

        preg_match_all('%(?P<browser>Camino|Kindle(\ Fire\ Build)?|Firefox|Safari|MSIE|AppleWebKit|Chrome|IEMobile|Opera|OPR|Silk|Lynx|Version|Wget|curl|NintendoBrowser|PLAYSTATION\ (\d|Vita)+)
				(?:;?)
				(?:(?:[/ ])(?P<version>[0-9A-Z.]+)|/(?:[A-Z]*))%ix', $uAgent, $result, PREG_PATTERN_ORDER);

        $key = 0;

        // If nothing matched, return null (to avoid undefined index errors)
        if (!isset($result['browser'][0]) || !isset($result['version'][0])) {
            return $empty;
        }

        $data['browser'] = $result['browser'][0];
        $data['version'] = $result['version'][0];

        if ($key = array_search('Playstation Vita', $result['browser']) !== false) {
            $data['platform'] = 'PlayStation Vita';
            $data['browser'] = 'Browser';
        } elseif (($key = array_search('Kindle Fire Build', $result['browser'])) !== false || ($key = array_search('Silk', $result['browser'])) !== false) {
            $data['browser'] = $result['browser'][$key] == 'Silk' ? 'Silk' : 'Kindle';
            $data['platform'] = 'Kindle Fire';
            if (!($data['version'] = $result['version'][$key]) || !is_numeric($data['version'][0])) {
                $data['version'] = $result['version'][array_search('Version', $result['browser'])];
            }
        } elseif (($key = array_search('NintendoBrowser', $result['browser'])) !== false || $data['platform'] == 'Nintendo 3DS') {
            $data['browser'] = 'NintendoBrowser';
            $data['version'] = $result['version'][$key];
        } elseif (($key = array_search('Kindle', $result['browser'])) !== false) {
            $data['browser'] = $result['browser'][$key];
            $data['platform'] = 'Kindle';
            $data['version'] = $result['version'][$key];
        } elseif (($key = array_search('OPR', $result['browser'])) !== false || ($key = array_search('Opera', $result['browser'])) !== false) {
            $data['browser'] = 'Opera';
            $data['version'] = $result['version'][$key];
            if (($key = array_search('Version', $result['browser'])) !== false) {
                $data['version'] = $result['version'][$key];
            }
        } elseif ($result['browser'][0] == 'AppleWebKit') {
            if (($data['platform'] == 'Android' && !($key = 0)) || $key = array_search('Chrome', $result['browser'])) {
                $data['browser'] = 'Chrome';
                if (($vkey = array_search('Version', $result['browser'])) !== false) {
                    $key = $vkey;
                }
            } elseif ($data['platform'] == 'BlackBerry' || $data['platform'] == 'PlayBook') {
                $data['browser'] = 'BlackBerry Browser';
                if (($vkey = array_search('Version', $result['browser'])) !== false) {
                    $key = $vkey;
                }
            } elseif ($key = array_search('Safari', $result['browser'])) {
                $data['browser'] = 'Safari';
                if (($vkey = array_search('Version', $result['browser'])) !== false) {
                    $key = $vkey;
                }
            }

            $data['version'] = $result['version'][$key];
        } elseif ($result['browser'][0] == 'MSIE') {
            if ($key = array_search('IEMobile', $result['browser'])) {
                $data['browser'] = 'IEMobile';
            } else {
                $data['browser'] = 'MSIE';
                $key = 0;
            }
            $data['version'] = $result['version'][$key];
        } elseif ($key = array_search('PLAYSTATION 3', $result['browser']) !== false) {
            $data['platform'] = 'PlayStation 3';
            $data['browser'] = 'NetFront';
        }

        return $data;
    }

    /**
     * fetchImage fuction for fetch image
     *
     * @param String $url url for get url.
     *
     * @description download file from given url and save its to temparary location for uploading purpose.
     * @return String $filePath location of downloaded file or false if file not available.
     */
    public function fetchImage($url = null) {
        $filePath = WWW_ROOT . "files/temp";

        if (!is_dir($filePath)) {
            //mkdir($filePath, 0777, true);
        }

        if ($url == null) {
            return false;
        }
        $ext = self::getFileExtension($url);
        //$ext = pathinfo($url, PATHINFO_EXTENSION);
        $name = self::generateRandomValue(40);
        $name = $name . '.' . $ext;

        if (null != $name) {

            $fileUrl = $url;

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $fileUrl,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_CONNECTTIMEOUT => 0,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_FOLLOWLOCATION => 1,
                CURLOPT_REFERER => 'http://www.google.com',
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13'
            ));

            $resp = curl_exec($curl);

            if (!curl_errno($curl)) {
                $info = curl_getinfo($curl);
                if ($info['http_code'] == '200') {
                    $filePath = $filePath . '/' . $name;
                    file_put_contents($filePath, $resp);
                }
            }
            curl_close($curl);
        }
        return $name;
    }

    /**
     * fetchImage fuction for fetch image
     *
     * @param String $url url for get url.
     *
     * @description download file from given url and save its to temparary location for uploading purpose.
     * @return String $filePath location of downloaded file or false if file not available.
     */
    public function fetchImageFromUrlAndUpload($url = null) {
        $bigUploadDir = WWW_ROOT . "files/users";
        $smallUploadDir = WWW_ROOT . "files/users/thumbs";
        $randomName = str_replace(".", "", strtotime("now"));
        $fileName = $randomName . md5('facebook');
        settype($fileName, "string");
        $fileName .= ".";
        $fileName .= "jpg";
        $content = file_get_contents($url);
        file_put_contents($bigUploadDir . '/' . $fileName, $content);
        file_put_contents($smallUploadDir . '/' . $fileName, $content);
        return $fileName;
    }

    /**
     * This method is used to get file extension.
     *
     * @param string $str file name
     *
     * @return string
     */
    public function getFileExtension($str) {
        $i = strrpos($str, ".");
        if (!$i) {
            return "";
        }
        $l = strlen($str) - $i;
        $ext = substr($str, $i + 1, $l);
        return $ext;
    }

    /**
     * This will execute $cmd in the background (no cmd window) without PHP waiting for it to finish, on both Windows
     * and Unix.
     *
     * @param string $cmd file name
     *
     * @return null
     */
    public function execInBackground($cmd) {
        if (substr(php_uname(), 0, 7) == "Windows") {
            pclose(popen("start /B " . $cmd, "r"));
        } else {
            exec($cmd . " > /dev/null &");
        }
    }

}
