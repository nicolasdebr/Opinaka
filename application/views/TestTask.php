<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>jQuery UI Dialog - Modal message</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

</head>

<script>var i = 1;</script>
<?php
foreach ($tasks as $link) {
    $url = $link->task_link;
    // var_dump($url);
    $String = explode("//", $url);
    //var_dump($bite);
    $url_log = $String[0] . "//admin:takine90@" . $String[1];
    //var_dump($url_log);
    $handle = fopen($url_log, "r");
    // var_dump($handle);

    $length = strlen(stream_get_contents($handle, -1, -1));

    fclose($handle);
//echo $length;
    $fact = 1;
    for ($i = 1; $i < $length; $i++) {
        $fact = $fact * $i;
    }
    echo $fact;
    ?>
    <script>
        $(document).ready(function () {
            $("#dialog-message").dialog({
                modal: true,
                buttons: {
                    Leave: function () {
                        $(this).dialog("close");
                    }
                }
            });
        });

        i++;
    </script>

<?php } ?>

<script>
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
</script>
</head>
<body>
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

