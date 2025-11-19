<?php
$twig = $GLOBALS['twig'];

echo $twig->render('pages/example.twig', [
    'test' => 'Exemple test'
]);
