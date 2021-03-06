<!DOCTYPE html>

<html>

    <head>

        <link href="/css/bootstrap.min.css" rel="stylesheet"/>
        <link href="/css/bootstrap-theme.min.css" rel="stylesheet"/>
<link href='http://fonts.googleapis.com/css?family=Muli' rel='stylesheet' type='text/css'>
        <link href="/css/styles.css" rel="stylesheet"/>
        <?php if (isset($title)): ?>
            <title>MergeBox: <?= htmlspecialchars($title) ?></title>
        <?php else: ?>
            <title>MergeBox</title>
        <?php endif ?>
	<link rel="shortcut icon" href="/favicon.ico"/>
        <script src="/js/jquery-1.11.1.min.js"></script>
        <script src="/js/bootstrap.min.js"></script>
        <script src="/js/scripts.js"></script>

    </head>

    <body>

        <div class="container">

            <div id="top">
                <a href="/"><img id="logo" alt="MERGEBOX" src="/img/logo3.png"/></a>
            </div>

            <div id="middle">
