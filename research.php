<?php
include 'header.php';


$levels = 5;
$levelRows = array();

$i = 1;
while ($i <= 5) {
    $db->query("SELECT * FROM research_type WHERE `level` = " . $i);
    $db->execute();
    $levelRows[$i] = $db->fetch_row();

    $i++;
}

?>

<div class='box_top'>Research</div>
<div class='box_middle'>
    <!-- Combat Research -->
    <div class="row">
        <div class="col-md-12">
            <h2>Research</h2>

            <div class="table-container">
                <table class="new_table" id="newtables" style="width:100%;">
                        <tr>
                            <?php
                            $i = 1;
                            while ($i <= 5):
                            ?>
                                <td>
                                    <?php foreach ($levelRows[$i] as $levelRow): ?>
                                        <div class="card text-white bg-info mb-3">
                                            <div class="card-header">
                                                <?php echo $levelRow['name'] ?>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text">
                                                    <?php echo $levelRow['description'] ?>
                                                </p>
                                            </div>
                                            <div class="card-footer">
                                                <a href="#" class="btn btn-primary">Research</a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <?php $i++; ?>
                            <?php endwhile; ?>
                        </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php'
?>