<?php
if ($user_class->jail > 0 || $user_class->hospital > 0) {
    echo "
            <div class='alert alert-danger'>
                <strong>Fail!</strong> You are currently in jail or hospital and cannot complete this quest.
            </div>
        ";
    exit;
}
?>
<h1>Follow Salvatore</h1><hr />

<canvas id="gameCanvas" width="800" height="600"></canvas>


<script type="text/javascript">
    const canvas = document.getElementById('gameCanvas');
    const ctx = canvas.getContext('2d');

    const player = {
        x: 50,
        y: 50,
        width: 50,
        height: 50,
        color: 'blue',
        speed: 5
    };

    const salvatore = {
        x: 200,
        y: 200,
        width: 50,
        height: 50,
        color: 'red'
    };

    function drawCharacter(character) {
        ctx.fillStyle = character.color;
        ctx.fillRect(character.x, character.y, character.width, character.height);
    }

    function clearCanvas() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    function update() {
        clearCanvas();
        drawCharacter(player);
        drawCharacter(salvatore);
        requestAnimationFrame(update);
    }

    function movePlayer(event) {
        switch (event.key) {
            case 'ArrowUp':
                player.y -= player.speed;
                break;
            case 'ArrowDown':
                player.y += player.speed;
                break;
            case 'ArrowLeft':
                player.x -= player.speed;
                break;
            case 'ArrowRight':
                player.x += player.speed;
                break;
        }
    }

    document.addEventListener('keydown', movePlayer);
    update();
</script>
