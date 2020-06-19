$(document).ready(function () {
    var stop = false;
    // chargement des données json dans le tableau
    $('#example').DataTable({
        data: json,
        columns: [

            {data: 'name'},
            {data: 'id'},
            {data: 'fileId'},
            {data: 'fileTag'},
            {data: 'LastModified'},
            //utilisation de la fonction render pour afficher le boutton link 
            {data: 'link', render: function (data) {
                    return "<a href='" + data + "'><button class='btn btn-primary'>Preview</button></a>";
                }},
            // affichage des input checkbox
            {data: 'name', render: function (data) {
                    return "<input type='checkbox' id='select'>";
                }},
            {data: 'Order', className: 'Order'}
        ]
    });
    //Création d'une variable représentant le tableau
    var table = $('#example').DataTable();
    //Création d'une variable contenant les données par ligne de tableau
    var data = table.rows().data();
    var count = 1;
    //fonction pour sélectionner les lignes choisies et récupérer les données 
    $('#example tbody').on('click', 'input[type="checkbox"]', function () {
        var $row = $(this).closest('tr');
        // Ajout d'une classe selected à la ligne cliquée
        $($row).toggleClass('selected');
        // Si l'ordre n'a pas encore été modifié, la case concernée prends la valeur du count, qui prends +1
        if (table.row($row).data().Order === "/") {
            table.row($row).cell($row, 7).data(count).draw();
            $('#data_name').val(table.row($row).data());
            count++;
            // Si l'ordre etait déja défini ça veut dire que la personne veut déselectionner la ligne, donc l'ordre redevient /
        } else {
            count = table.row($row).cell($row, 7).data();
            table.row($row).cell($row, 7).data("/").draw();

        }
    });
    // Au moment du click sur le boutton add tasks
    $('#button').click(function () {
        // Envoie d'une requête ajax contenant les lignes sélectionnées
        $.ajax({
            type: 'POST',
            url: '../Accueil/Add_DataBase',
            data: {json: JSON.stringify(table.rows('.selected').data())},
            dataType: 'json'

        })
                .done(function (data) {
                    console.log('done');
                    console.log(data);
                    alert('La task list a été enregistrée et est prête à être éxécutée');
                })
                .fail(function () {
                    alert('La task list a été enregistrée et est prête à être éxécutée');
                });
    });
    // Clique du boutton execute task
    $('#Exec').click(function () {
        var stop = false;
        //ouverture d'un dialog prévenant que l'éxécution de la tache est en cours
        $("#dialog-message").dialog({
            modal: true,
            buttons: {
                Leave: function () {
                    $(this).dialog("close");
                    var stop = true;
                    $.ajax({
                        type: 'POST',
                        url: '../Accueil/Task_Exec',
                        data: {json: JSON.stringify(stop)},
                        dataType: 'json'
                    })
                    return stop;
                }
            }
        });
        // Si la personne clique sur leave, arrête de l'éxécution de la tache en théorie, ne fonctionne pas
        if (stop === true) {
            xhr.abort();
            // Si les taches n'ont pas été annulées : Envoie d'une requête ajax pour remplir la table task et prévenir de la réussite de l'éxécution
        } else {
            $.ajax({
                type: 'POST',
                url: '../Accueil/Task_Exec',
                data: {json: JSON.stringify(stop)},
                dataType: 'json'})
            var xhr = $.post("../Accueil/Task_Exec", function (data, status) {
                // Si reussite de la requête ajax :
                if (status) {
                    $("#dialog-message").dialog("close");

                    $(function () {
                        $("#dialog-message2").dialog({
                            modal: true,
                            buttons: {
                                Ok: function () {
                                    $(this).dialog("close");
                                }
                            }
                        });

                    });
                }
            });
        }
    });
    // Click du boutton delete, requête ajax afin de déclencher la suppression des lignes supprimées
    $('#delete').click(function () {
        $.ajax({
            type: 'POST',
            url: '../Accueil/delete',
            data: {json: JSON.stringify(table.rows('.selected').data())},
            dataType: 'json'

        })
                .done(function (data) {
                    console.log('done');
                    console.log(data);
                    alert('Les taches sélectionnées ont bien été supprimées');
                })
                .fail(function () {
                    location.reload();

                });
    });
    // Click du boutton reset
    $('#reset').click(function () {
        // On enlève la class selected de la ligne
        $('#example tbody tr').removeClass('selected');

        var cellData = table.cells('.Order');
        //On réinitialise la valeur de la case draw concernée
        cellData.every(function () {
            if (this.data() !== '/') {
                this.data('/').draw();
            }
        })
// réinitialisation du count à 
        count = 1;
    });

});
