<?php
session_start();
echo "<link rel='stylesheet' type='text/css' href='style.css'>";

if (!isset($_SESSION['artists'])) $_SESSION['artists'] = [];
if (!isset($_SESSION['genres'])) $_SESSION['genres'] = [];
if (!isset($_SESSION['artist_genre'])) $_SESSION['artist_genre'] = [];

function nextId($arr) {
    if (!$arr) return 1;
    return max(array_keys($arr)) + 1;
}

function getArtists() {
    return $_SESSION['artists'];
}

function getGenres() {
    return $_SESSION['genres'];
}

function getArtistGenre() {
    return $_SESSION['artist_genre'];
}

function addArtist($name, $albums, $tracks, $country) {
    $id = nextId($_SESSION['artists']);
    $_SESSION['artists'][$id] = ['name' => $name, 'albums' => $albums, 'tracks' => $tracks, 'country' => $country];
    return $id;
}

function addGenre($name) {
    $id = nextId($_SESSION['genres']);
    $_SESSION['genres'][$id] = ['name' => $name];
    return $id;
}

function addArtistGenre($artistIds, $genreIds) {
    foreach ($artistIds as $aid) {

        $_SESSION['artist_genre'] = array_values(array_filter($_SESSION['artist_genre'], function($ag) use ($aid) {
            return $ag['artist_id'] != $aid;
        }));

        foreach ($genreIds as $gid) {
            $_SESSION['artist_genre'][] = ['artist_id' => $aid, 'genre_id' => $gid];
        }
    }
}


function deleteArtist($id) {
    if (isset($_SESSION['artists'][$id])) unset($_SESSION['artists'][$id]);
    $_SESSION['artist_genre'] = array_values(array_filter($_SESSION['artist_genre'], function($ag) use ($id) {
        return $ag['artist_id'] != $id;
    }));
}

function deleteGenre($id) {
    if (isset($_SESSION['genres'][$id])) unset($_SESSION['genres'][$id]);
    $_SESSION['artist_genre'] = array_values(array_filter($_SESSION['artist_genre'], function($ag) use ($id) {
        return $ag['genre_id'] != $id;
    }));
}

function editArtist($id, $name, $albums, $tracks, $country) {
    if (isset($_SESSION['artists'][$id])) {
        $_SESSION['artists'][$id] = ['name' => $name, 'albums' => $albums, 'tracks' => $tracks, 'country' => $country];
    }
}
?>
