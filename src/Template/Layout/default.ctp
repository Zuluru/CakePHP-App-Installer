<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __('Cake App Installer');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>
        <?= $title_for_layout ?> |
        <?= $cakeDescription ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <?php
    echo $this->Html->meta('icon');
    echo $this->Html->css('Installer.bootstrap.min.css');
    echo $this->fetch('meta');
    echo $this->fetch('css');
?>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
        <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>

<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only"><?= __('Toggle navigation') ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><?= $cakeDescription ?></a>
        </div>

    </div><!-- /.container-fluid -->
</nav>

<div class="container">

    <?= $this->Flash->render() ?>
    <!-- nocache -->

    <?= $this->fetch('content') ?>

    <hr>
    <footer>
        <p class="pull-right">
            <?= $this->Html->link(__('GitHub Repository'), 'https://github.com/anuj9196/CakePHP-App-Installer') ?> &copy; <?= date('Y') ?><br />
            <?= __('Developer <span class="glyphicon glyphicon-heart text-danger"></span> {0}', $this->Html->link('Anuj Sharma', 'http://profplus.in/anujsharma')) ?>
        </p>
        <p>
            <?= __('Developed with the {0}.', $this->Html->link('CakePHP Framework', 'https://cakephp.org')) ?><br/>
            <?= __('Designed with {0}.', $this->Html->link('Twitter Bootstrap', 'https://twitter.github.com/bootstrap/')) ?>
        </p>
    </footer>
</div><!-- /container -->

</body>
</html>
