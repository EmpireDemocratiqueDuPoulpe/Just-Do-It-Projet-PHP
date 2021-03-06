<?php
require_once "../../init.php";

############################
# Get data
############################

$data = json_decode(file_get_contents("php://input"));

// List ID and user ID
$list_id = $data[0] ?? null;
$user_id = $data[1] ?? null;

############################
# Add the user to the list
############################

$SharesManager = new Shares($db);
$message = "";

if ($SharesManager->add($list_id, $user_id)) $message = "Partage ajouté !";
else $message = "Erreur pendant la création du partage";

echo json_encode($message);