<?php

class UploadFile {

    public $uploaded = false;
    public $name;
    public $tmpName;
    public $error;
    public $size;
    public $targetPath;
    public $allowedExtensions;


    public function __construct($file, $dst_path, $extensions) {
        $this->name              = $file['name'];
        $this->tmpName           = $file['tmp_name'];
        $this->error             = $file['error'];
        $this->size              = $file['size'];
        $this->targetPath        = $dst_path;
        $this->allowedExtensions = $extensions;
    }

    public function upload() {
        $ext      = strtolower(pathinfo($this->name, PATHINFO_EXTENSION));
        $new_name = date('YmdHis'). ".".  $ext;
        switch ($this->error) {
            case UPLOAD_ERR_OK:
                $valid = true;
                //validate file extensions
                if ( !in_array($ext, $this->allowedExtensions) ) {
                    $valid = false;
                    $error = 'Invalid file extension.';
                }
                if ( $this->size/1024/1024 > 2 ) {
                    $valid = false;
                    $error = 'File size is exceeding maximum allowed size.';
                }
               if ($valid) {

                    move_uploaded_file($this->tmpName, $this->targetPath. "/". $new_name);
                    $this->uploaded = true;
                }
                break;
            case UPLOAD_ERR_INI_SIZE:
                $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
                break;
            case UPLOAD_ERR_PARTIAL:
                $error = 'The uploaded file was only partially uploaded.';
                break;
            case UPLOAD_ERR_NO_FILE:
                $error = 'No file was uploaded.';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $error = 'Missing a temporary folder.';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $error = 'Failed to write file to disk. ';
                break;
            case UPLOAD_ERR_EXTENSION:
                $error = 'File upload stopped by extension.';
                break;
            default:
                $error = 'Unknown error';
                break;
        }

        return array("file_path" => $this->targetPath. "/". $new_name, "error" => $error);
    }


}