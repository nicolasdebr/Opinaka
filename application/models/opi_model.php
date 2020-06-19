<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class opi_model extends CI_Model {

    public function api($user, $pwd) {
        $settings = array(
            'baseUri' => 'http://192.168.159.132/remote.php/dav/files/admin/Documents',
            'userName' => $user,
            'password' => $pwd
        );
        // utilisation du framework sabre
        $client = new Sabre\DAV\Client($settings);
        // Requête via l'api de nextcloud pour récupérer les différentes infos des fichiers
        $folder_content = $client->propFind('http://192.168.159.132/remote.php/dav/files/admin/Documents', array(
            '{DAV:}getlastmodified',
            '{DAV:}getcontenttype',
            '{http://owncloud.org/ns}fileid',
            '{http://owncloud.org/ns}id',
            '{DAV:}getetag',
                ), 1);
        return $folder_content;
    }

}
