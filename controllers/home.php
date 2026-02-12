<?php
$twig = $GLOBALS['twig'];

echo $twig->render('pages/home.twig', [
    'test' => 'Exemple test'
]);
