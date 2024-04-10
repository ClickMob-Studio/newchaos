<?php

require "header.php";

if($user_class->admin < 1){
    exit();
}

function newitemPop($text, $id, $color = ''){
    $id = intval($id);
    if(isset($id)){
        $query = mysql_query("SELECT * FROM items WHERE `id` = ".$id);
        if(mysql_num_rows($query)){
            $result = mysql_fetch_assoc($query);
        }
?>
    <!-- Button trigger modal -->
    <button type="button" class="dcPrimaryButton my-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
      <?php echo $text;?>
    </button>
    <style>
        .modal-content {
    position: relative;
    display: flex;
    flex-direction: column;
    width: 100%;
    pointer-events: auto;
    background-color: #000;
    background-clip: padding-box;
    outline: 0;
}
</style>
    <!-- Modal -->
    <div class="modal fade" data-bs-theme="dark" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Information about <?= $result['itemname'];?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row text-center">
                <div class="col-6">
                    <img src="<?= $result['image'];?>" style="width: 100px; height: 100px;" alt="<?= $result['itemname'];?>" class="vacant-throne">
                </div>
                <div class="col-6">
                Description: <?= $result['description'];?>
              </div>
                <div class="col-12">Details:</div>
                <div class="col-6">Sell Value: $<?php echo prettynum($result['cost'] * .6) ?></div>
                <div class="col-6">Shop Value: $<?php echo prettynum($result['cost']) ?></div>
                <div class="col-6">Attack Bonus: <?php echo prettynum($result['offense']) ?>%</div>
                <div class="col-6">Defense Bonus: <?php echo prettynum($result['defense']) ?>%</div>
                <div class="col-6">Speed Bonus: <?php echo prettynum($result['speed']) ?>%</div>
                <div class="col-6">Required Level: <?php echo prettynum($result['level']) ?>%</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <?php
    }
}

echo newitemPop('Knife' ,1);