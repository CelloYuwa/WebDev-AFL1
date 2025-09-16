<?php
include_once 'model.php';

$action = $_REQUEST['action'] ?? '';

if ($action == 'add_genre' && !empty($_POST['genre_name'])) {
    addGenre(trim($_POST['genre_name']));
    header('Location: index.php?page=genres');
    exit;
}

if ($action == 'add_artist' && !empty($_POST['name'])) {
    addArtist(trim($_POST['name']), (int)$_POST['albums'], (int)$_POST['tracks'], trim($_POST['country']));
    header('Location: index.php?page=artists');
    exit;
}

if ($action == 'add_artist_genre' && !empty($_POST['artists']) && !empty($_POST['genres'])) {
    addArtistGenre($_POST['artists'], $_POST['genres']);
    header('Location: index.php?page=artist_genre');
    exit;
}

if ($action == 'edit_artist' && isset($_POST['id'])) {
    editArtist((int)$_POST['id'], trim($_POST['name']), (int)$_POST['albums'], (int)$_POST['tracks'], trim($_POST['country']));
    header('Location: index.php?page=artists');
    exit;
}

if ($action == 'delete_artist' && isset($_POST['id'])) {
    deleteArtist((int)$_POST['id']);
    header('Location: index.php?page=artists');
    exit;
}

if ($action == 'delete_genre' && isset($_POST['id'])) {
    deleteGenre((int)$_POST['id']);
    header('Location: index.php?page=genres');
    exit;
}

?>
