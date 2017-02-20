<?php require_once "_header.php"; ?>
<style>
    .gameContainer {
        padding:10px;
        border:solid 1px #ddd;
    }
    .gameContainer h3 {
        margin-top:0;
    }
    .gameContainer table {
        border-collapse: collapse;
        width:100%;
    }
    .gameContainer th,
    .gameContainer td {
        border:solid 1px #ddd;
        padding:5px 10px;
    }
    .gameContainer th {
        text-align: left;
        background:#eee;
    }
    @media all and (max-width: 800px){
        .gameContainer {
            padding:0;
            border:none;
        }
    }
</style>
<div class="contBox">
	<h1>Games</h1>
        <div class="gameContainer">
            <h3>Servers</h3>
            <p>
                Here is a list of game servers that is currently hosted here.
            </p>
            <table cellspacing="0" border="0">
                <tr>
                    <th width="30%">Game</th>
                    <th width="70%">URL</th>
                </tr>
                <tr>
                    <td>Counter Strike 1.6</td>
                    <td>cs16.dev-nook.de:27015</td>
                </tr>
            </table>
        </div>
</div>
<?php require_once "_footer.php"; ?>