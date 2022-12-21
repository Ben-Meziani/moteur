<?php
function listParamTypeMIME(){
    $tabXlsFields = array();
    $tabXlsFields["TypeMIME"] = array(

        "listeTypeMIME"=>array(
                /*----------------------------------------------------------------------------------------------------*/
                /* Fichier Image -------------------------------------------------------------------------------------*/
                /*----------------------------------------------------------------------------------------------------*/
                "jpg" => array(
                    "image/jpeg" => ".jpg",
                ),
                "jpeg" => array(
                    "image/jpeg" => ".jpeg",
                ),
                "png" => array(
                    "image/png" => ".png",
                ),
                "heif" => array(
                    "image/heif" => ".heif",
                    "image/heif-sequence" => ".heif",
                ),
                "heic" => array(
                    "image/heic" => ".heic",
                    "image/heic-sequence" => ".heic",
                ),
                /*----------------------------------------------------------------------------------------------------*/
                /* Microsoft Excel -----------------------------------------------------------------------------------*/
                /*----------------------------------------------------------------------------------------------------*/
                "xls" => array(
                    "application/vnd.ms-excel" => ".xls",
                ),
                "xlt" => array(
                    "application/vnd.ms-excel" => ".xlt",
                ),
                "xla" => array(
                    "application/vnd.ms-excel" => ".xla",
                ),
                "xlsx" => array(
                    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" => ".xlsx",
					"application/octet-stream" => ".xlsx",
                ),
                "xltx" => array(
                    "application/vnd.openxmlformats-officedocument.spreadsheetml.template" => ".xltx",
                ),
                "xlsm" => array(
                    "application/vnd.ms-excel.sheet.macroEnabled.12" => ".xlsm",
                ),
                "xltm" => array(
                    "application/vnd.ms-excel.template.macroEnabled.12" => ".xltm",
                ),
                "xlam" => array(
                    "application/vnd.ms-excel.addin.macroEnabled.12" => ".xlam",
                ),
                "xlsb" => array(
                    "application/vnd.ms-excel.sheet.binary.macroEnabled.12" => ".xlsb",
                ),
                "csv" => array(
                    "application/vnd.ms-excel" => ".csv",
                ),
                /*----------------------------------------------------------------------------------------------------*/
                /* Microsoft powerpoint ------------------------------------------------------------------------------*/
                /*----------------------------------------------------------------------------------------------------*/
                "pot" => array(
                    "application/vnd.ms-powerpoint" => ".pot",
                ),
                "pps" => array(
                    "application/vnd.ms-powerpoint" => ".pps",
                ),
                "ppa" => array(
                    "application/vnd.ms-powerpoint" => ".ppa",
                ),
                "ppt" => array(
                    "application/vnd.ms-powerpoint" => ".ppt",
                ),
                "pptx" => array(
                    "application/vnd.openxmlformats-officedocument.presentationml.presentation" => ".pptx",
                ),
                "potx" => array(
                    "application/vnd.openxmlformats-officedocument.presentationml.template" => ".potx",
                ),
                "ppsx" => array(
                    "application/vnd.openxmlformats-officedocument.presentationml.slideshow" => ".ppsx",
                ),
                "ppam" => array(
                    "application/vnd.ms-powerpoint.addin.macroEnabled.12" => ".ppam",
                ),
                "pptm" => array(
                    "application/vnd.ms-powerpoint.presentation.macroEnabled.12" => ".pptm",
                ),
                "potm" => array(
                    "application/vnd.ms-powerpoint.template.macroEnabled.12" => ".potm",
                ),
                "ppsm" => array(
                    "application/vnd.ms-powerpoint.slideshow.macroEnabled.12" => ".ppsm",
                ),
                /*----------------------------------------------------------------------------------------------------*/
                /* Microsoft Word ------------------------------------------------------------------------------------*/
                /*----------------------------------------------------------------------------------------------------*/
                "doc" => array(
                    "application/msword" => ".doc",
                ),
                "docx" => array(
                    "application/vnd.openxmlformats-officedocument.wordprocessingml.document" => ".docx",
                ),
                "docm" => array(
                    "application/vnd.ms-word.document.macroEnabled.12" => ".docm",
                ),
                "dot" => array(
                    "application/msword" => ".dot",
                ),
                "dotx" => array(
                    "application/vnd.openxmlformats-officedocument.wordprocessingml.template" => ".dotx",
                ),
                "dotm" => array(
                    "application/vnd.ms-word.template.macroEnabled.12" => ".dotm",
                ),
                /*----------------------------------------------------------------------------------------------------*/
                /* Fichier WEB ---------------------------------------------------------------------------------------*/
                /*----------------------------------------------------------------------------------------------------*/
                "xml" => array(
                    "text/xml" => ".xml",
                ),
                "html" => array(
                    "text/html" => ".html",
                ),
                "css" => array(
                    "text/css" => ".css",
                ),
                "js" => array(
                    "text/javascript" => ".js",
                ),
                "php" => array(
                    "application/octet-stream" => ".php",
                ),
                "sql" => array(
                    "application/octet-stream" => ".sql",
                ),
                "json" => array(
                    "application/json" => ".json",
                ),
                "jsonp" => array(
                    "application/javascript" => ".jsonp",
                ),
                /*----------------------------------------------------------------------------------------------------*/
                /* Fichier Divers ------------------------------------------------------------------------------------*/
                /*----------------------------------------------------------------------------------------------------*/
                "pdf" => array(
                    "application/x-pdf" => ".Xpdf",
                    "application/pdf" => ".pdf",
                ),
                "zip" => array(
                    "application/zip" => ".zip",
                    "application/x-zip-compressed" => ".zip",
                    "multipart/x-zip" => ".zip",
                ),
                "rar" => array(
                    "application/rar" => ".rar",
                    "application/x-rar-compressed" => ".rar",
                    "multipart/x-rar" => ".rar",
                    "application/octet-stream" => ".rar",
                ),
                "txt" => array(
                    "text/plain" => ".txt",
                ),
                "p12" => array(
                    "application/x-pkcs12" => ".p12",
                ),
        ),
    );
    return $tabXlsFields;
}
?>