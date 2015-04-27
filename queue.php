<?php
// Get the URL parameter containing the site to scan
$url = $_GET['url'];

// Validate the URL
if (filter_var($url, FILTER_VALIDATE_URL) === false) {
  // Not valid, redirect to homepage
  header('Location: /');
  exit;
}
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mattias Geniar">

    <!-- Redirect after 12 seconds -->
    <script>
      DPEngineHack_refresh = window.setTimeout (
        function() {
          window.location.href = "/check.php?url=<?= $url ?>";
        },
        12000
      );
    </script>
    <noscript>
      <!-- Fallback if JavaScript was disabled -->
      <meta http-equiv="refresh" content="12;URL=/check.php?url=<?= $url ?>">
    </noscript>

    <title>Drupal Check: The EngineHack - Scanning now</title>

    <!-- Bootstrap core CSS -->
    <link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">
      <div class="header">
        <h3 class="text-muted">Drupal Check: The EngineHack</h3>
      </div>

      <div class="jumbotron">
        <h1>Scanning website now ... </h1>
        <p class="lead">
          Our server horses are now connecting to the following website and are scanning for any evidence of the hack. Please hold on ... you should be redirected in about 10 seconds.
        </p>

        <h3><?= $url ?></h3>

        <p>
          You'll be redirected as soon as the check is finished.
        </p>

        <br /><br />

        <p>
          <img src="nucleus_logo.png" width="250px" class="pull-left img-rounded" style="margin-right: 15px;" /> In the meanwhile, go pay our kind <a href="https://www.nucleus.be/en" target="_blank">server sponsors a visit</a>. Without their server capacity, there would be no free Drupal check. If you'd like to say thanks, go ahead and mention <a href="https://twitter.com/nucleus_hosting" target="_blank">@nucleus_hosting</a>!<br />
          <br />
          This tool has been created by <a href="https://ma.ttias.be" target="_blank">Mattias Geniar</a>. You can follow his actions on Twitter at <a href="https://twitter.com/mattiasgeniar" target="_blank">@mattiasgeniar</a>.
        </p>


      </div>

      <br />

      <footer class="footer">
        <p>
          Drupal EngineHack - Created by <a href="https://ma.ttias.be">Mattias Geniar</a>. Twitter: <a href="https://twitter.com/mattiasgeniar">@mattiasgeniar</a>. Server capacity and bandwidth by <a href="https://www.nucleus.be/en">Nucleus, offering Uptime-as-a-Service</a>
        </p>
        <p>
          Spot an error? Would you like to contribute? This project is completely open source! More details here: <a href="https://ma.ttias.be/drupal-enginehack-detection-website" target="_blank">Announcing the Drupal EngineHack Detection Website</a>
        </p>
      </footer>

    </div> <!-- /container -->
  </body>
</html>