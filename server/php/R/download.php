<?php
/**
 * To download card attachment
 *
 * PHP version 5
 *
 * @category   PHP
 * @package    Restyaboard
 * @subpackage Core
 * @author     Restya <info@restya.com>
 * @copyright  2014 Restya
 * @license    http://www.restya.com/ Restya Licence
 * @link       http://www.restya.com
 */
require_once ('config.inc.php');
if (!empty($_GET['id']) && !empty($_GET['hash'])) {
    $md5_hash = md5(SecuritySalt . 'download' . $_GET['id']);
    if ($md5_hash == $_GET['hash']) {
        if ($db_lnk) {
            $result = pg_query_params($db_lnk, 'SELECT * FROM card_attachments WHERE id = $1', array(
                $_GET['id']
            ));
            $attachment = pg_fetch_assoc($result);
            $mediadir = APP_PATH . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'Card' . DIRECTORY_SEPARATOR . $attachment['card_id'];
            $file = $mediadir . DIRECTORY_SEPARATOR . $attachment['name'];
            if (file_exists($file)) {
                $quoted = sprintf('"%s"', addcslashes(basename($file) , '"\\'));
                $size = filesize($file);
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . $quoted);
                header('Content-Transfer-Encoding: binary');
                header('Connection: Keep-Alive');
                header('Content-length: ' . $size);
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                readfile($file);
                exit;
            }
        }
    } else {
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
    }
} else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 400 Not Found', true, 400);
}
