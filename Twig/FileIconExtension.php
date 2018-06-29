<?php

namespace Bkstg\ResourceBundle\Twig;

use Bkstg\MediaBundle\Entity\Media;

class FileIconExtension extends \Twig_Extension
{
    /**
     * Return set of twig functions.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_Function('file_icon', [$this, 'getFileIcon']),
        ];
    }

    /**
     * Return a file extension icon.
     *
     * @param  Media $media The media to get the icon for.
     * @return string
     */
    public function getFileIcon(Media $media)
    {
        switch ($media->getContentType()) {
            case 'audio/basic':
            case 'audio/midi':
            case 'audio/mp4':
            case 'audio/mpeg':
            case 'audio/ogg':
            case 'audio/prs.sid':
            case 'audio/webm':
            case 'audio/x-aiff':
            case 'audio/x-gsm':
            case 'audio/x-matroska':
            case 'audio/x-mpegurl':
            case 'audio/x-ms-wax':
            case 'audio/x-ms-wma':
            case 'audio/x-pn-realaudio':
            case 'audio/x-realaudio':
            case 'audio/x-scpls':
            case 'audio/x-sd2':
            case 'audio/x-wav':
                return 'file-audio-o';

            case 'image/gif':
            case 'image/ief':
            case 'image/jpeg':
            case 'image/pcx':
            case 'image/png':
            case 'image/svg+xml':
            case 'image/tiff':
            case 'image/vnd.djvu':
            case 'image/vnd.microsoft.icon':
            case 'image/vnd.wap.wbmp':
            case 'image/webp':
            case 'image/x-cmu-raster':
            case 'image/x-coreldraw':
            case 'image/x-coreldrawpattern':
            case 'image/x-coreldrawtemplate':
            case 'image/x-corelphotopaint':
            case 'image/x-jg':
            case 'image/x-jng':
            case 'image/x-ms-bmp':
            case 'image/x-photoshop':
            case 'image/x-portable-anymap':
            case 'image/x-portable-bitmap':
            case 'image/x-portable-graymap':
            case 'image/x-portable-pixmap':
            case 'image/x-rgb':
            case 'image/x-xbitmap':
            case 'image/x-xpixmap':
            case 'image/x-xwindowdump':
                return 'file-image-o';

            case 'video/3gpp':
            case 'video/dl':
            case 'video/dv':
            case 'video/fli':
            case 'video/gl':
            case 'video/mp4':
            case 'video/mpeg':
            case 'video/ogg':
            case 'video/quicktime':
            case 'video/vnd.mpegurl':
            case 'video/webm':
            case 'video/x-flv':
            case 'video/x-la-asf':
            case 'video/x-m4v':
            case 'video/x-matroska':
            case 'video/x-mng':
            case 'video/x-ms-asf':
            case 'video/x-ms-wm':
            case 'video/x-ms-wmv':
            case 'video/x-ms-wmx':
            case 'video/x-ms-wvx':
            case 'video/x-msvideo':
            case 'video/x-sgi-movie':
                return 'file-video-o';

            case 'application/pdf':
                return 'file-pdf-o';

            case 'application/msword':
            case 'application/vnd.ms-word.document.macroEnabled.12':
            case 'application/vnd.oasis.opendocument.text':
            case 'application/vnd.oasis.opendocument.text-template':
            case 'application/vnd.oasis.opendocument.text-master':
            case 'application/vnd.oasis.opendocument.text-web':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
            case 'application/vnd.stardivision.writer':
            case 'application/vnd.sun.xml.writer':
            case 'application/vnd.sun.xml.writer.template':
            case 'application/vnd.sun.xml.writer.global':
            case 'application/vnd.wordperfect':
            case 'application/x-abiword':
            case 'application/x-applix-word':
            case 'application/x-kword':
            case 'application/x-kword-crypt':
                return 'file-word-o';

            case 'application/vnd.ms-excel':
            case 'application/vnd.ms-excel.sheet.macroEnabled.12':
            case 'application/vnd.oasis.opendocument.spreadsheet':
            case 'application/vnd.oasis.opendocument.spreadsheet-template':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
            case 'application/vnd.stardivision.calc':
            case 'application/vnd.sun.xml.calc':
            case 'application/vnd.sun.xml.calc.template':
            case 'application/vnd.lotus-1-2-3':
            case 'application/x-applix-spreadsheet':
            case 'application/x-gnumeric':
            case 'application/x-kspread':
            case 'application/x-kspread-crypt':
                return 'file-excel-o';

            case 'application/vnd.ms-powerpoint':
            case 'application/vnd.ms-powerpoint.presentation.macroEnabled.12':
            case 'application/vnd.oasis.opendocument.presentation':
            case 'application/vnd.oasis.opendocument.presentation-template':
            case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
            case 'application/vnd.stardivision.impress':
            case 'application/vnd.sun.xml.impress':
            case 'application/vnd.sun.xml.impress.template':
            case 'application/x-kpresenter':
                return 'file-powerpoint-o';

            case 'application/zip':
            case 'application/x-zip':
            case 'application/stuffit':
            case 'application/x-stuffit':
            case 'application/x-7z-compressed':
            case 'application/x-ace':
            case 'application/x-arj':
            case 'application/x-bzip':
            case 'application/x-bzip-compressed-tar':
            case 'application/x-compress':
            case 'application/x-compressed-tar':
            case 'application/x-cpio-compressed':
            case 'application/x-deb':
            case 'application/x-gzip':
            case 'application/x-java-archive':
            case 'application/x-lha':
            case 'application/x-lhz':
            case 'application/x-lzop':
            case 'application/x-rar':
            case 'application/x-rpm':
            case 'application/x-tzo':
            case 'application/x-tar':
            case 'application/x-tarz':
            case 'application/x-tgz':
                return 'file-archive-o';

            case 'application/ecmascript':
            case 'application/javascript':
            case 'application/mathematica':
            case 'application/vnd.mozilla.xul+xml':
            case 'application/x-asp':
            case 'application/x-awk':
            case 'application/x-cgi':
            case 'application/x-csh':
            case 'application/x-m4':
            case 'application/x-perl':
            case 'application/x-php':
            case 'application/x-ruby':
            case 'application/x-shellscript':
            case 'text/vnd.wap.wmlscript':
            case 'text/x-emacs-lisp':
            case 'text/x-haskell':
            case 'text/x-literate-haskell':
            case 'text/x-lua':
            case 'text/x-makefile':
            case 'text/x-matlab':
            case 'text/x-python':
            case 'text/x-sql':
            case 'text/x-tcl':
            case 'application/xhtml+xml':
            case 'application/x-macbinary':
            case 'application/x-ms-dos-executable':
            case 'application/x-pef-executable':
                return 'file-code-o';

            default:
                return 'file-o';
        }
    }
}
