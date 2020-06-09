<?php

include 'vendor/autoload.php';

class Accueil extends CI_Controller {

    public function infos() {

        // Seul sont autorisés sur cette page les user enregistrés, redirection sur le loggin 
        $this->form_validation->set_rules('user_name', 'Name', 'htmlspecialchars|required');
        $this->form_validation->set_rules('user_pwd', 'Password', 'htmlspecialchars|required');
        // vérification des données postées
        if ($this->form_validation->run() == true) {
            $name = $this->input->post('user_name');
            $pwd = $this->input->post('user_pwd');
            //Si le user existe :
            if ($this->auth->login($name, $pwd, "user")) {

                // préparation des identifiants de connexion nécessaires pour se connecter à nextcloud
                $settings = array(
                    'baseUri' => 'http://192.168.159.132/remote.php/dav/files/admin/Documents',
                    'userName' => $_SESSION['user']['user_name'],
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
                $json = array();
                $i = 0;
                // Association des données récupérées dans un tableau
                foreach ($folder_content as $key => $value) {
                    /* $extension = substr($key,-4,4);
                      var_dump($extension);
                      if($extension === ".opi"){ */
                    $name = explode("/", $key);
                    $json[$i] = array(
                        "name" => ($name[6] === "") ? $name[5] : $name[6],
                        "id" => $value['{http://owncloud.org/ns}id'],
                        "fileId" => $value['{http://owncloud.org/ns}fileid'],
                        "fileTag" => $value['{DAV:}getetag'],
                        "LastModified" => $value['{DAV:}getlastmodified'],
                        "link" => '//'.$_SESSION['user']['user_name'].':'.$pwd.'@192.168.159.132/remote.php/dav/files/admin/Documents/' . $name[6],
                        "Order" => "/"
                    );

                    $i++;
                }
                // Encodage en json du tableau pour l'envoyer à la vue    
                $data['json'] = json_encode($json);
                $data['folder_content'] = $folder_content;
                $this->load->view('Test', $data);
            } else {
                $this->load->view('Connection');
            }
        } else {
            $this->load->view('Connection');
        }
        $this->auth->authorized(["user"], "Accueil/Loggin");
    }

    public function delete($id) {

        $settings = array(
            'baseUri' => 'http://192.168.159.132/remote.php/dav/files/admin/Documents/',
            'userName' => 'admin',
            'password' => 'takine90'
        );

        $client = new Sabre\DAV\Client($settings);
        // supression via l'id du document
        $delete_file = $client->request("DELETE", $id);
        $folder_content = $client->propFind('http://192.168.159.132/remote.php/dav/files/admin/Documents', array(
            '{DAV:}getlastmodified',
            '{DAV:}getcontenttype',
            '{http://owncloud.org/ns}fileid',
            '{http://owncloud.org/ns}id',
            '{DAV:}getetag',
                ), 1);
        $json = array();

        $i = 0;
        foreach ($folder_content as $key => $value) {
            /* $extension = substr($key,-4,4);
              var_dump($extension);
              if($extension === ".opi"){ */
            $name = explode("/", $key);
            $json[$i] = array(
                "name" => ($name[6] === "") ? $name[5] : $name[6],
                "id" => $value['{http://owncloud.org/ns}id'],
                "fileId" => $value['{http://owncloud.org/ns}fileid'],
                "fileTag" => $value['{DAV:}getetag'],
                "LastModified" => $value['{DAV:}getlastmodified'],
                "link" => 'http://192.168.159.132/remote.php/dav/files/admin/Documents/' . $name[6]
            );

            $i++;
        }
        $data['json'] = json_encode($json);
        $data['folder_content'] = $folder_content;
        $this->load->view('Test', $data);
    }

    public function Add_DataBase() {
        //Récupération via ajax des lignes sélectionnées dans le tableau
        $directions = json_decode($_POST['json'], true);
        $date = date("Y/m/d");
        // Association de ces données à un tableau, puis insertion pour chaque ligne
        foreach ($directions as $key => $value) {
            echo $value['name'];
            if ($value["name"] !== null) {
                $data = array(
                    'task_name' => $value["name"],
                    'task_id' => $value["id"],
                    'task_link' => $value["link"],
                    'task_lastrun' => $date,
                    'task_order' => $value["Order"],
                    'task_user_id' => $_SESSION['user']['user_name']
                );

                $this->db->insert('opi_task', $data);
            }
        }
    }

    public function Task_Exec() {
        if ($_POST) {
            $directions = json_decode($_POST['json'], true);
            var_dump($directions);
            if ($directions === true) {
                $data = array(
                    'alert_type' => 'Cancelled',
                    'alert_message' => 'L execution de la tache a été intérrompue',
                    'alert_user_id' => $_SESSION['user']['user_name'],
                    'alert_date' => date("Y/m/d")
                );
                mail($_SESSION['user']['user_email'], 'Opi Notif', 'L exécution de la tache vient d etre interrompue');
                $this->db->insert('opi_alert', $data);
            } else if ($directions === false) {
                $data = array(
                    'alert_type' => 'Done',
                    'alert_message' => 'L execution de la tache a été terminée',
                    'alert_user_id' => $_SESSION['user']['user_name'],
                    'alert_date' => date("Y/m/d")
                );
                mail($_SESSION['user']['user_email'], 'Opi Notif', 'L exécution de la tache vient de se terminer');
                $this->db->insert('opi_alert', $data);
                $this->db->delete('opi_task', array('task_user_id' => $_SESSION['user']['user_name']));
                $results = $this->db->query("SELECT * FROM opi_task WHERE task_user_id='" . $_SESSION['user']['user_name'] . "'");
            $data['tasks'] = $results->result();

            foreach ($data['tasks'] as $link) {
                $url = $link->task_link;
                $String = explode("//", $url);
                $url_log = $String[0] . "//admin:takine90@" . $String[1];
                $handle = fopen($url_log, "r");
                // var_dump($handle);

                $length = strlen(stream_get_contents($handle, -1, -1));

                fclose($handle);
//echo $length;
                $fact = 1;
                for ($i = 1; $i < $length; $i++) {
                    $fact = $fact * $i;
                }
            }
            // $this->load->view('TestTask', $data);
            echo json_encode($data['tasks']);
            }
        } 
    }

    public function Log() {
        $this->load->view('Register');
    }

    public function Loggin() {
        $this->form_validation->set_rules('user_name', 'Name', 'htmlspecialchars|required');
        $this->form_validation->set_rules('user_pwd', 'Password', 'htmlspecialchars|required');

        if ($this->form_validation->run() == true) {
            $name = $this->input->post('user_name');
            $pwd = password_hash($this->input->post('user_pwd'), PASSWORD_DEFAULT);

            if ($this->auth->login($name, $pwd, "user")) {
                redirect(site_url('Accueil/infos'));
            }
        }
        $this->load->view('Connection');
    }

    public function Register() {
        $this->form_validation->set_rules('user_email', 'Email', 'htmlspecialchars|required|valid_email');
        $this->form_validation->set_rules('user_name', 'Name', 'htmlspecialchars|required');
        $this->form_validation->set_rules('user_pwd', 'Password', 'htmlspecialchars|required');
        $this->form_validation->set_rules('confirm_pwd', 'Confirm', 'htmlspecialchars|required');
        if ($this->form_validation->run() == true) {
            if ($this->input->post('user_pwd') == $this->input->post('confirm_pwd')) {
                $a = array(
                    'user_email' => $this->input->post('user_email'),
                    'user_name' => $this->input->post('user_name'),
                    'user_pwd' => password_hash($this->input->post('user_pwd'), PASSWORD_DEFAULT)
                );
                $this->db->insert('opi_user', $a);
                redirect(site_url("Accueil/infos"));
            }
        }
    }

    public function logout() {
        $this->auth->logout();

        redirect(site_url("Accueil/Loggin"));
    }

}
