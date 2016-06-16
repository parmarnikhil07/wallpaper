<?php

App::uses('Component', 'Controller');
App::import(
        'Vendor', 's3', array('file' => 'aws' . DS . 'autoload.php')
);

/**
 * AWS Upload image component.
 *
 */
class AwsComponent extends Component
{

    /**
     * Component Name
     *
     * @var string
     */
    public $name = 'Aws';

    /**
     * Other components used by this component
     *
     * @var array
     */
    public $components = array();

    /**
     * Controller object to store its instantiating controller
     *
     * @var object
     */
    public $controller;

    /**
     * Stores errors
     *
     * @var array
     */
    public $Errors = array();

    /**
     * Global settings
     *
     * @var array
     */
    public $GlobalSettings = array(
        'version' => 'latest',
        'region' => 'us-west-2',
    );
    public $s3Obj = null;

    /**
     * Called before the Controller::beforeFilter().
     *
     * @param Controller $controller Controller with components to initialize
     */
    public function initialize(Controller $controller, $settings = array())
    {
        parent::initialize($controller);
    }

    /**
     * This function upload images to AWS
     * @param type $fileArr
     * @param type $newFileName
     * @param type $s3DirectoryName
     * @param type $version
     * @param type $region
     * @return boolean
     */
    public function s3UploadImage($fileArr, $newFileName, $s3DirectoryName = '', $version = 'latest', $region = 'us-west-2')
    {
        try {
            $this->s3Obj = new Aws\S3\S3Client(
                    [
                'version' => !empty($version) ? $version : $this->GlobalSettings['version'],
                'region' => !empty($region) ? $region : $this->GlobalSettings['region'],
                'credentials' => [
                    'key' => AWS_ACCESS_KEY_ID,
                    'secret' => AWS_SECRET_ACCESS_KEY,
                ],
                    ]
            );

            $filePath = !empty($s3DirectoryName) ? $s3DirectoryName . "/" . $newFileName : $newFileName;
            $result = $this->s3Obj->upload(S3_BUCKET_NAME, S3_BUCKET_FOLDER_NAME . "/" . $filePath, fopen($fileArr['tmp_name'], 'r'), 'public-read', array('params' => array('ContentType' => $fileArr['type'])));
            return true;
        } catch (Exception $e) {
            $this->Errors[] = $e->getMessage();
            print_r($this->Errors);
            //return false;
        }
    }

}
