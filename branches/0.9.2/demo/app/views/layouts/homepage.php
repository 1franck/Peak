<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Demo App</title>
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="stylesheet" type="text/css" href="<?php $this->baseUrl('css/bootstrap.min.css'); ?>" media="screen" />
    <link rel="stylesheet" type="text/css" href="<?php $this->baseUrl('css/demo.css'); ?>" media="screen" />
  </head>
<body>

    <div class="topbar2">
      <div class="fill">
        <div class="container">
          <a class="brand" href="#"><strong>Pe</strong>ak Fr<strong>a</strong>mewor<strong>k</strong></a>

          <ul class="nav">
          </ul>
        </div>
      </div>
    </div>

    <div class="container">

      <div class="content">
        <div class="page-header">
          <h1>It's Demo App <small>Yeah!</small></h1>
        </div>
        <div class="row">
          <div class="span10">
            <?php $this->layoutContent(); ?>
          </div>
          <div class="span4">
            <h3>What's next?</h3>
            <ul>
             <li><a href="#">Coming soon...</a></li>
             <!--
             <li><a href="#">How to start</a></li>
             <li><a href="#">Requirements</a></li>
             <li><a href="#">Documentation</a></li>
             <li><a href="#">Framework Autodoc</a></li>
            -->
            </ul>
          </div>
        </div>
        <div class="coffee_cup"></div>
      </div>

      <footer>
        <!--<p>&copy; Company 2011</p>-->
      </footer>

    </div> <!-- /container -->

  </body>
</html>
