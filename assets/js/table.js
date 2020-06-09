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
                    return "<a href='" + data + "'><button class='btn btn-primary'>Link</button></a>";
                }},
            {data: 'name', render: function (data) {
                    return "<a href='<?= site_url()?>/Accueil/delete/" + data + "'><button class='btn btn-danger'>Delete</button></a>";
                }},
            {data: 'Order'}
        ]
    });

    var table = $('#example').DataTable();
    var data = table.rows().data();
    var count = 1;
    //fonction pour sélectionner les lignes choisies et récupérer les données 
    $('#example tbody').on('click', 'tr', function () {
        $(this).toggleClass('selected');
        // table.row( this ).data().Order =count;
        if (table.row(this).data().Order === "/") {
            table.row(this).cell(this, 7).data(count).draw();


            $('#data_name').val(table.row(this).data());
            count++;
        } else {
            table.row(this).cell(this, 7).data("/").draw();
        }
    });


    $('#button').click(function () {

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

    $('#Exec').click(function () {
        var stop = false;

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
        
        if (stop === true) {
         xhr.abort();
        }else{
            $.ajax({
                type: 'POST',
                url: '../Accueil/Task_Exec',
                data: {json: JSON.stringify(stop)},
                dataType: 'json'})
            var xhr =$.post("../Accueil/Task_Exec", function (data, status) {
               
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

});

