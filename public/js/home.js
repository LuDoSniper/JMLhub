document.addEventListener('DOMContentLoaded', function() {
    // Gestion des playlists
    function hidePlaylistsDivs() {
        var playlistsDivs = document.querySelectorAll(".playlists");
        playlistsDivs.forEach((playlistsDiv) => {
            playlistsDiv.style.display = 'none';
        });
    }

    function hideOverlay() {
        document.getElementById("overlay").style.display = 'none';
    }

    function listToString(list) {
        var str = "";
        list.forEach((e) => {
            str += e + ",";
        });

        return str.slice(0, -1);
    }

    function formatURL(url) {
        let parts = url.split("/");
        parts = parts.slice(0, -2);
        return parts.join("/");
    }

    const playlistsButtons = document.querySelectorAll(".playlistsButton");
    playlistsButtons.forEach((playlistsButton) => {
        playlistsButton.addEventListener('click', function() {
            const overlay = document.getElementById("overlay");
            const playlistsDivs = document.querySelectorAll(".playlists");

            overlay.style.display = 'block';

            const id = this.id.split('-')[1];
            const playlistsDiv = document.getElementById("playlists-" + id);
            playlistsDiv.style.display = 'block';
        });
    })

    document.getElementById("overlay").addEventListener('click', function() {
        hideOverlay();
        hidePlaylistsDivs();
    });

    const cancelButtons = document.querySelectorAll(".cancel");
    cancelButtons.forEach((button) => {
        button.addEventListener('click', function() {
            hideOverlay();
            hidePlaylistsDivs();
        });
    });

    const confirmButtons = document.querySelectorAll(".confirm");
    confirmButtons.forEach((button) => {
        button.addEventListener('click', function() {
            const playlistsDivId = button.parentElement.parentElement.id;
            const checkboxes = document.querySelectorAll("#" + playlistsDivId + " input[type='checkbox']");
            const playlistsChecked = [];
            checkboxes.forEach((checkbox) => {
                if (checkbox.checked) {
                    playlistsChecked.push(checkbox.name);
                }
            });

            const movieId = document.getElementById("movieId-" + playlistsDivId.split('-')[1]).value;
            const url = document.getElementById("url-" + playlistsDivId.split('-')[1]).value;
            var test = formatURL(url);
            var test2 = listToString(playlistsChecked);
            window.location.href = formatURL(url) + "/" + movieId + "/" + listToString(playlistsChecked);
        });
    });
});