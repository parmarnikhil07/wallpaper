<?php

/**
 * File /app/controller/components/SignatureUpload.php
 */
class SignatureUploadComponent extends Component {

    /**
     * This method is used to generate random string.
     *
     * @param string $datakey It is required
     *
     * @return string
     */
    public function uploadSign($datakey) {
        $path = WWW_ROOT . "img/signature/";
        $fileName = str_replace(",", "_", strtolower($datakey['name'])); //uploaded file name
        $fileName = substr($fileName, strrpos($fileName, '.'));
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';
        for ($i = 0; $i < 36; $i++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        $finalFileName = $string . $fileName;
        $booles = move_uploaded_file($datakey["tmp_name"], $path . $finalFileName);
        return $finalFileName;
    }

}
