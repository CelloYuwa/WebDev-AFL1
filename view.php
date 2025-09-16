<?php
include_once 'model.php';

$page = $_GET['page'] ?? 'artists';
$artists = getArtists();
$genres = getGenres();
$artistGenre = getArtistGenre();
?>

<link rel="stylesheet" type="text/css" href="style.css">

<nav>
    <a href="index.php?page=artists">Artists</a> | 
    <a href="index.php?page=genres">Genres</a> | 
    <a href="index.php?page=artist_genre">Artists-Genre</a>
</nav>
<hr>

<?php if ($page == 'artists'): ?>
    <h2>Artists</h2>
    <table border="1">
        <tr>
            <th>Name</th>
            <th>Albums</th>
            <th>Tracks</th>
            <th>Country</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        <?php foreach ($artists as $id => $a):
            $name = htmlspecialchars($a['name']);
            $albums = (int)$a['albums'];
            $tracks = (int)$a['tracks'];
            $country = htmlspecialchars($a['country']);
            $jsonName = json_encode($a['name']);
            $jsonCountry = json_encode($a['country']);
        ?>
        <tr>
            <td><?= $name ?></td>
            <td><?= $albums ?></td>
            <td><?= $tracks ?></td>
            <td><?= $country ?></td>
            <td>
                <button class="primary" onclick='openEdit(<?= $id ?>, <?= $jsonName ?>, <?= $albums ?>, <?= $tracks ?>, <?= $jsonCountry ?>)'>Edit</button>
            </td>
            <td>
                <form method="post" action="controller.php?action=delete_artist" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <button type="submit" class="delete" onclick="return confirm('Delete artist: <?= $name ?>?')">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Add Artist</h3>
    <form method="post" action="controller.php?action=add_artist">
        <input type="text" name="name" placeholder="Name" required><br>
        <input type="number" name="albums" placeholder="Albums" required><br>
        <input type="number" name="tracks" placeholder="Tracks" required><br>
        <input type="text" name="country" placeholder="Country" required><br>
        <button class="primary" type="submit">Add Artist</button>
    </form>

    <div id="editPopup" style="display:none; position:fixed; top:20%; left:35%; background:#fff; padding:20px; border:1px solid #000;">
        <h3>Edit Artist</h3>
        <form method="post" action="controller.php?action=edit_artist">
            <input type="hidden" name="id" id="edit_id">
            <input type="text" name="name" id="edit_name" required><br>
            <input type="number" name="albums" id="edit_albums" required><br>
            <input type="number" name="tracks" id="edit_tracks" required><br>
            <input type="text" name="country" id="edit_country" required><br>
            <button type="submit" class="primary">Save</button>
            <button type="button" class="cancel" onclick="closeEdit()">Cancel</button>
        </form>
    </div>

    <script>
    function openEdit(id, name, albums, tracks, country) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_albums').value = albums;
        document.getElementById('edit_tracks').value = tracks;
        document.getElementById('edit_country').value = country;
        document.getElementById('editPopup').style.display = 'block';
    }
    function closeEdit() {
        document.getElementById('editPopup').style.display = 'none';
    }
    </script>
<?php endif; ?>

<?php if ($page == 'genres'): ?>
    <h2>Genres</h2>
    <table border="1">
        <tr>
            <th>Name</th>
            <th>Artists</th>
            <th>Delete</th>
        </tr>
        <?php foreach ($genres as $id => $g):
            $name = htmlspecialchars($g['name']);
            $count = count(array_filter($artistGenre, fn($ag) => $ag['genre_id'] == $id));
        ?>
        <tr>
            <td><?= $name ?></td>
            <td><?= $count ?></td>
            <td>
                <form method="post" action="controller.php?action=delete_genre" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <button type="submit" class="delete" onclick="return confirm('Delete genre: <?= $name ?>?')">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <form method="post" action="controller.php?action=add_genre">
        <input type="text" name="genre_name" placeholder="New Genre" required>
        <button type="submit" class="primary">Add Genre</button>
    </form>
<?php endif; ?>

<?php if ($page == 'artist_genre'): ?>
    <h2>Artists-Genre</h2>
    <table border="1">
        <tr>
            <th>Artist</th>
            <th>Genres</th>
        </tr>
        <?php
        $map = [];
        foreach ($artistGenre as $ag) {
            $aid = $ag['artist_id'];
            $gid = $ag['genre_id'];
            if (isset($artists[$aid]) && isset($genres[$gid])) {
                $map[$aid][] = $genres[$gid]['name'];
            }
        }

        foreach ($artists as $aid => $a):
            $glist = isset($map[$aid]) ? implode(', ', $map[$aid]) : '-';
            $name = htmlspecialchars($a['name']);
        ?>
        <tr>
            <td><?= $name ?></td>
            <td><?= $glist ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Assign Artist to Genres</h3>
    <button type="button" class="primary" onclick="openArtistPopup()">Assign Artist</button>

    <div id="artistPopup" style="display:none; position:fixed; top:20%; left:45%; background:#fff; padding:20px; border:1px solid #000;">
        <h3>Select Artist</h3>
        <form>
            <select name="artist" id="artistSelect" required>
                <option value="">-- Choose an Artist --</option>
                <?php foreach ($artists as $id => $a): ?>
                    <option value="<?= $id ?>"><?= htmlspecialchars($a['name']) ?></option>
                <?php endforeach; ?>
            </select><br><br>
            <button type="button" class="primary" onclick="nextToGenres()">Next</button>
            <button type="button" class="cancel" onclick="closeArtistPopup()">Cancel</button>
        </form>
    </div>

    <div id="genrePopup" style="display:none; position:fixed; top:20%; left:45%; background:#fff; padding:20px; border:1px solid #000;">
        <h3>Select Genres</h3>
        <form method="post" action="controller.php?action=add_artist_genre">
            <input type="hidden" name="artists[]" id="chosenArtist">
            <?php foreach ($genres as $id => $g): ?>
                <input type="checkbox" name="genres[]" value="<?= $id ?>"> <?= htmlspecialchars($g['name']) ?><br>
            <?php endforeach; ?>
            <br>
            <button type="submit" class="primary">Save</button>
            <button type="button" class="cancel" onclick="closeGenrePopup()">Cancel</button>
        </form>
    </div>

    <script>
    function openArtistPopup() {
        document.getElementById('artistPopup').style.display = 'block';
    }
    function closeArtistPopup() {
        document.getElementById('artistPopup').style.display = 'none';
    }
    function nextToGenres() {
        let artistId = document.getElementById('artistSelect').value;
        if (!artistId) {
            alert('Please select an artist first');
            return;
        }
        document.getElementById('chosenArtist').value = artistId;
        closeArtistPopup();
        document.getElementById('genrePopup').style.display = 'block';
    }
    function closeGenrePopup() {
        document.getElementById('genrePopup').style.display = 'none';
    }
    </script>
<?php endif; ?>
