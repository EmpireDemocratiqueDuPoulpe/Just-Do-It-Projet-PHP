<!DOCTYPE html>

<html lang="fr">
    <head>
        <title>ProjetPHP - Connexion</title>
        <?php include_once(ROOT."/views/models/head.php"); ?>
    </head>
    <body>
        <!-- Header -->
        <?php include_once(ROOT."/views/models/header.php"); ?>

        <!-- Todo list -->
        <div id="todoListContainer">
            <?= $todoListsHTML; ?>
        </div>
    </body>
</html>