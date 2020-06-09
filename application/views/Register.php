<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    </head>
    <body>
        <div class="container">
        <?php  echo form_open('Accueil/Register'); ?> 
        <label for="user_name">Name</label>
        <input type="text" class="form-control" name="user_name" id="user_name"  placeholder="Enter name">
        <label for="user_email">Email address</label>
        <input type="email" class="form-control"name="user_email" id="user_email"  placeholder="Enter email">
        <label for="user_pwd">Password</label>
        <input type="password" class="form-control" name="user_pwd" id="user_pwd"  placeholder="Enter password">
        <label for="confirm_pwd">Confirm Password</label>
        <input type="password" class="form-control" name="confirm_pwd" id="confirm_pwd"  placeholder="Enter password">
        <br>
        <button type="submit" class="btn btn-primary">Inscription</button>
        </div>
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    </body>
