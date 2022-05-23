<?php

class FileHandler {

    /**
     * uploads a file to the 'uploads/' directory
     * @param string $target_dir - e.g. 'assignments/submissions/'
     * @param array|null $file_types - specifies the allowed file types
     * @return string relative path to uploaded file as string, e.g. 'uploads/tasks/img.jpg'
     * @throws ErrorException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function uploadFile(string $param, string $target_dir, array $file_types = null): string {

        if (empty($_FILES[$param])){
            throw new InvalidArgumentException("Invalid file argument - '" . $param . "' expected.");
        }

        if ($_FILES[$param]["error"] != 0) {
            if ($_FILES[$param]["error"] == 4) {
                throw new ErrorException("File not specified!");
            } else {
                throw new ErrorException("PHP Upload Error " . $_FILES[$param]["error"]);
            }
        }

        $upload_dir = $_SERVER["DOCUMENT_ROOT"] . "/ss22-itp-g02/uploads/" . $target_dir;

        if (!preg_match("/\A([a-zA-Z0-9]+\/)+\z/", $target_dir)){
            throw new InvalidArgumentException("Target directory is not well formed.");
        }


        if (!file_exists($upload_dir)){
            if (!@mkdir("../uploads/" . $target_dir, 0766, true)){
                throw new Exception("Error creating directory.");
            }
        }

        $target_file_name = basename($_FILES[$param]["name"]);
        $fileExtension = strtolower(pathinfo($target_file_name, PATHINFO_EXTENSION));
        
        //check filetypes
        if (isset($file_types)){
            if (!in_array($fileExtension, $file_types)) {
                throw new ErrorException("Ungültige Dateiendung! Nur " . implode(',', $file_types) . " erlaubt!");
            }
        }

        // Check file size
        if ($_FILES[$param]["size"] > 10000000) {
            throw new ErrorException("Die hochgeladene Datei ist zu groß! Max. 10MB");
        }

        // Check if file already exists and add number to file until name is unique
        if (file_exists($upload_dir . $target_file_name)) {
            $counter = 1;
            while (true){
                if(!file_exists($upload_dir . $counter . $target_file_name)){
                    $target_file_name = $counter . $target_file_name;
                    break;
                }
                $counter++;
            }
        }

        //Upload files to right directory
        $target_file_path = $upload_dir . $target_file_name;
        if (move_uploaded_file($_FILES[$param]["tmp_name"], $target_file_path)) {
            return "uploads/" . $target_dir . $target_file_name;
        } else {
            throw new ErrorException("Fehler beim Hochladen der Datei!");
        }
    }

}