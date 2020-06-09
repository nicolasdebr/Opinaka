<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="<?= base_url('assets/css/accueil.css') ?>">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>  
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">






        <script >
            var json = <?= $json ?>;
        </script>

    </head>
    <body>

        <div class="container-fluid">
            <h1>Liste des documents :</h1>
        <table id="example" class="display">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>id</th>
                    <th>fileId</th>
                    <th>fileTag</th>
                    <th>LastModified</th>
                    <th>Link</th>
                    <th>Delete</th>
                    <th>Order</th>
                </tr>
            </thead>
        </table>
        <button type="submit" id="button" class="btn btn-primary">Add tasks</button>
        <button id ="Exec" class="btn btn-primary">Execute task</button>
        <a href="<?= site_url('Accueil/Logout') ?>"><button class="btn btn-danger">Déconnexion</button></a>
        <div id="dialog-message2" title="Task ">
            <p>
                <span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
                Your files just finished to be executed
            </p>
            <p>

            </p>
        </div>
        <div id="dialog-message" title="Task Running ">
            <p>
                <span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
                Please wait until End of execution or click leave to stop it.
                <img src="<?= base_url('assets/images/ajax-loader.gif') ?>">
            </p>
            <p>

            </p>
        </div>
        </div>
    </body>
    <script src="<?= base_url('assets/js/table.js') ?>"></script>  
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</html>
