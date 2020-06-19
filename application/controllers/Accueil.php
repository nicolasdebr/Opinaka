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
                $results = $this->db->query("SELECT * FROM opi_data");
                $data = $results->result();
                //var_dump($data);
                //var_dump(empty($data));
                if (empty($data)) {
                    $folder_content = $this->opi_model->api($name, $pwd);
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
                            "link" => '//' . $_SESSION['user']['user_name'] . ':' . $pwd . '@192.168.159.132/remote.php/dav/files/admin/Documents/' . $name[6],
                            "Order" => "/"
                        );
                        $this->db->insert('opi_data', $json[$i]);
                        $i++;
                    }

                    // Encodage en json du tableau pour l'envoyer à la vue    
//                $data['json'] = json_encode($json);
//                $data['folder_content'] = $folder_content;
                    $results = $this->db->query("SELECT * FROM opi_data");
                    $data['json'] = json_encode($results->result());
                    $this->load->view('Test', $data);
                } else {
                    $results = $this->db->query("SELECT * FROM opi_data");
                    $data['json'] = json_encode($results->result());
                    $this->load->view('Test', $data);
                }
                //$this->load->view('Test', $data);
            } else {
                $this->load->view('Connection');
            }
        } else {
            $this->load->view('Connection');
        }
        $this->auth->authorized(["user"], "Accueil/Loggin");
    }

// suppression des lignes sélectionnées 
    public function delete() {
        $directions = json_decode($_POST['json'], true);
        foreach ($directions as $key => $value) {
            if (isset($value["name"])) {
                $del = $this->db->delete('opi_data', array('name' => $value["name"]));
            }
        }
    }

// Ajout des taches dans la table opi_task, déclenché par une requête ajax
    public function Add_DataBase() {
        //Récupération via ajax des lignes sélectionnées dans le tableau
        $directions = json_decode($_POST['json'], true);
        $date = date("Y/m/d");
        // Association de ces données à un tableau, puis insertion pour chaque ligne
        foreach ($directions as $key => $value) {

            if (isset($value["name"])) {
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

// Execution des taches contenues dans la bdd task_exec
    public function Task_Exec() {
        // Au moment du clic sur tache exec, on vérifie si des données ont bien été transmises par la requête ajax
        if ($_POST) {
            //Décode du booleen récupéré
            $directions = json_decode($_POST['json'], true);
            var_dump($directions);
            // Si directions est vrai
            if ($directions === true) {
                //Préparation des données à ajouter dans la table opi_alert
                $data = array(
                    'alert_type' => 'Cancelled',
                    'alert_message' => 'L execution de la tache a été intérrompue',
                    'alert_user_id' => $_SESSION['user']['user_name'],
                    'alert_date' => date("Y/m/d")
                );
                //Envoie d'un email à l'utilisateur le prévenant de l'annulation
                mail($_SESSION['user']['user_email'], 'Opi Notif', 'L exécution de la tache vient d etre interrompue');
                //insertion de data dans opi_alert
                $this->db->insert('opi_alert', $data);
                //Si il n'y a pas eu d'annulation dans l'éxécution des taches                
            } else if ($directions === false) {
                $data = array(
                    'alert_type' => 'Done',
                    'alert_message' => 'L execution de la tache a été terminée',
                    'alert_user_id' => $_SESSION['user']['user_name'],
                    'alert_date' => date("Y/m/d")
                );
                //envoi mail de réussite
                mail($_SESSION['user']['user_email'], 'Opi Notif', 'L exécution de la tache vient de se terminer');
                //insertion dans la table opi_alert de la réussite
                $this->db->insert('opi_alert', $data);
                //Suppression des taches ayant été éxécutées dans opi_tasj
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

// Identification de l'utilisateur
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

// Enregistrement d'un nouvel utilisateur dans la bdd
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

// Déconnexion de l'utilisateur
    public function logout() {
        $this->auth->logout();

        redirect(site_url("Accueil/Loggin"));
    }

}
