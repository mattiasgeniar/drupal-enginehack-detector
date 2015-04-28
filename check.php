<?php

// Get the URL parameter containing the site to scan
$url = $_GET['url'];

// Validate the URL
if (filter_var($url, FILTER_VALIDATE_URL) === false) {
  // Not valid, redirect to homepage
  header('Location: /');
  exit;
}

// We've got a valid URL, build a curl request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);

// I want the body of the site as well as the headers
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);

// If the domain redirects, follow it, we want the 200 HTTP response
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

// In case the domain uses a self-signed SSL certificate, ignore it entirely
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

// Consider 404's an error?
curl_setopt($ch, CURLOPT_FAILONERROR, true);

// Fake the GoogleBot User-Agent, otherwise the hack won't show
curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'));

// Get the headers and the body
$response    = curl_exec($ch);

// Did the response succeed?
if (curl_errno($ch)) {
  // an error number is present, the request failed
  $request_succeeded = false;
  $request_error     = curl_error($ch);
} else {
  $request_succeeded = true;
  $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
  $headers     = substr($response, 0, $header_size);
  $body        = substr($response, $header_size);
}

// close curl resource to free up system resources
curl_close($ch);

// Parse the results
require 'functions.php';

if (is_hacked_drupal($headers)) {
  $site_hacked = true;
} else {
  $site_hacked = false;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mattias Geniar">

    <title>Drupal Check: The EngineHack - Result</title>

    <!-- Bootstrap core CSS -->
    <link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

    <!-- Custom styles for this template -->
    <link href="jumbotron-narrow.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">
      <div class="header">
        <nav>
          <ul class="nav nav-pills pull-right">
            <li role="presentation" class="active">
              <a href="/">Scan another site</a>
            </li>
          </ul>
        </nav>
        <h3 class="text-muted">EngineHack Check: <?= htmlspecialchars($url) ?></h3>
      </div>

      <div class="jumbotron">
        <h1>EngineHack Check Results</h1>
        <h3>Site: <?= htmlspecialchars($url) ?></h3>
        <p class="lead">
          The scan on the website finished. Here's the result.
        </p>

        <?php
          if ($request_succeeded) {
            // Curl request worked, check the status
            if ($site_hacked == true) {
              // time to tell the bad news ...
            ?>
            <div class="alert alert-danger" role="alert">
              <h3>Damn, looks like your site has been hacked!</h3>

              <p>
                I hate to break it to you, but it looks like your Drupal site has been hacked.<br />
                Check below for more details explaining how our detection works.
                <img src="/check_pixel.png?url=<?= $url ?>&compromised=true" width="1px" height="1px" />
              </p>
            </div>

            <div class="alert alert-warning" role="alert">
              <p>
                Our scanner found indications that your website has been compromised. Specifically, the <code>engine_ssl_</code> and <code>engine_ssid_</code> cookies are being set to visitors who browse your website.<br />
                <br />
                You can find some pointers to fix this and the full HTTP headers we received below.
              </p>
            </div>

            <?php
            } else {
              // seems safe, but we should be careful how we phrase this
            ?>
            <div class="alert alert-success" role="alert">
              <h3>Good news!<h3>
              <p>
                Pfew, we couldn't find any evidence of the EngineHack Drupal hack.<sup>(*)</sup>
                <img src="/check_pixel.png?url=<?= $url ?>&compromised=false" width="1px" height="1px" />
              </p>
            </div>

            <p>
              <sup>
                (*) remember: just because we couldn't find it, doesn't mean your Drupal is 100% safe. Check out <a href="#prevention">the prevention measures</a> listed below!
                </sup>
            </p>

            <?php
            }
          } else {
            // Request failed, no idea about the status
            ?>
            <div class="alert alert-warning" role="alert">
              <h3>Sorry, the request failed</h3>
              <p>
                I'm sorry, the check didn't work on the website you entered.<br />
                <br />
                Here's the error message we received when connecting to your site:<br />
                <strong><?= $request_error ?></strong><br />
                <br />
                For more details to help you debug, you can find the HTTP headers we retrieved below.<br />
                Maybe you mistyped the URL? You can try <a href="/">entering a new one, if you'd like</a>.
              </p>
            </div>

            <?php
          }
        ?>
      </div>

      <div class="row marketing">
        <div class="col-lg-12">
          <h2>HTTP headers</h2>
          <p class="lead">
            We managed to retrieve the following headers from the remote site.<br />
            <pre><?= strlen($headers) > 5 ? htmlspecialchars($headers) : "No HTTP headers received."; ?></pre>
          </p>
          <p class="lead">
            The detection happens by checking for <code>Set-Cookie</code> headers that try to set <code>engine_ssl_</code> or <code>engine_ssid_</code> cookies.
          </p>
        </div>

        <div class="col-lg-12">
          <h2><a name="prevention"></a>What should I do if I've been hacked?</h2>
          <p class="lead">
            Talk to your webdevelopers or whoever you paid to setup your Drupal installation. Ideally, you can let them fix the problem for you.<br />
            <br />
            If you're the one responsible for the installation and the fix, here are some pointers to repair the hack and prevent a future compromised site:<br />
            <ol>
                <li><a href="https://ma.ttias.be/drupal-engine_ssid_-and-engine_ssl_-cookies-youve-been-hacked/" target="_blank">Read this blogpost for more details on the EngineHack</a></li>
                <li>Take a back-up of your files and database</li>
                <li>Do a fresh install of Drupal, import your <code>sites/*</code> data and your database</li>
                <li>Check your installation for unwanted Admin accounts and remove them</li>
                <li>Check your pages, remove any page you did not add yourself</li>
                <li>Make sure you're up-to-date: core, themes, plugins, server, php version, ...</li>
                <li>Check crontasks on the server: malware likes to reinstall itself via cron</li>
                <li><a href="https://ma.ttias.be/presentation-code-obfuscation-php-shells-more-what-hackers-do-once-they-get-passed-your-code/">Learn more about securing your PHP and server</a></li>
            </ol>
          </p>
        </div>

        <div class="col-lg-12">
          <h2>This site says I've been hacked, but I don't see it?</h2>
          <p class="lead">
            This is a tricky part about this hack, you <em>probably</em> won't see it.<br />
            <br />
            The attack only shows itself when someone browses the site with the <code>GoogleBot</code> User-Agent.
          </p>

          <p class="lead">
            If you're adventurous, you can try the following Browser addons to fake your User-Agent.<br />
            <ul>
              <li>Mozilla Firefox: <a href="https://addons.mozilla.org/en-Us/firefox/addon/user-agent-switcher/">User Agent Switcher</a></li>
              <li>Google Chrome: <a href="https://chrome.google.com/webstore/detail/user-agent-switcher-for-c/djflhoibgkdhkhhcedjiklpkjnoahfmg">User-Agent Switcher for Chrome</a></li>
            </ul>

            Once you've installed the plugin, add the following User-Agent in your browser and navigate to the website.<br />
            <br />

            <pre>Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)</pre>

            <br />
            <em>(make sure to reset your User-Agent after your testing, the web can look kind of weird with a GoogleBot User-Agent)</em><br />
            <br />

            If you're handy at the Command Line, you can use the following simple <code>curl</code> request to check your website.<br />
            <br />

            <pre>curl <?= htmlspecialchars($url) ?> -A "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)"</pre>

            At the bottom of the page you see in your terminal, you should see includes for typical drugs and medicines.
          </p>
        </div>

        <div class="col-lg-12">
          <div class="alert alert-info" role="alert">
            <h3>Need more help?</h3>
            <p>
              Drupal has a very active community you can rely on. Make sure to check out their <a href="https://www.drupal.org/node/2365547">"Your Drupal site got hacked. Now what?"</a> pages.<br />
              <br />
              Need more help? Feel free to poke me <a href="https://twitter.com/mattiasgeniar">on Twitter</a>, I'll see what I can do!
            </p>
          </div>
        </div>
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

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-4635324-12', 'auto');
  ga('send', 'pageview');

</script>
  </body>
</html>