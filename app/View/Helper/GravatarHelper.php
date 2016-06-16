<?php
/*
 * CakePHP View Helper for displaying Gravatar Images
 *
 * PHP 5
 * 
 * Copyright 2012, Scott Harwell
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Scott Harwell
 * @since         7/2/2012
 * @package	  app.View.Helper
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses ('AppHelper', 'View/Helper');
class GravatarHelper extends AppHelper
{
    /*
     * Function to retrieve any gravatar URL
     * 
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 512 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boole $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @param bool $secure Use https
     * @return String containing either just a URL or a complete image tag
     * @source http://gravatar.com/site/implement/images/php/
     */
    
    public static function gravatar ($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array(), $secure = false)
    {
	$url = ($secure ? "https://" : "http://") . 'www.gravatar.com/avatar/';
	$url .= md5 (strtolower (trim ($email)));
	$url .= "?s=$s&d=$d&r=$r";
	if ($img)
	{
	    $url = '<img src="' . $url . '"';
	    foreach ($atts as $key => $val)
		$url .= ' ' . $key . '="' . $val . '"';
	    $url .= ' />';
	}
	return $url;
    }
    
    /*
     * Alias to return an image by default
     * 
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 512 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @param bool $secure Use https
     * @return String containing either just a URL or a complete image tag
     */
    
    public static function image ($email, $s = 80, $d = 'mm', $r = 'g', $atts = array(), $secure = false)
    {
	return static::gravatar($email, $s, $d, $r, true, $atts, $secure);
    }
    
    /*
     * Alias to return a link by default
     * 
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 512 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @param bool $secure Use https
     * @return String containing either just a URL or a complete image tag
     */
    
    public static function link ($email, $s = 80, $d = 'mm', $r = 'g', $atts = array (), $secure = false)
    {
	return static::gravatar($email, $s, $d, $r, false, $atts, $secure);
    }
}