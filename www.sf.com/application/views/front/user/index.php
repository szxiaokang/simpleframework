<?php
/**
 * @filename index.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-30 13:35:15
 * @version 1.0
 * @Description
 * 
 */
?>
<div class="row" style="margin-top:20px;">
    <div class="col-xs-1"></div>
    <div class="col-xs-10">
        <h4>最新注册用户</h4>
        <table width="100%" class="table table-striped table-hover" style="margin-top:25px">
            <tbody>
                <?php
                if (count($rows)) {
                    foreach ($rows as $row) {
                        $src = empty($row['avatar']) ? '/assets/images/' . $row['sex'] . '.png' : $row['avatar'];
                        ?>
                        <tr>
                            <td>
                                <img src="<?php echo $src; ?>" width="60" height="60" />
                                <?php
                                $email = explode('@', $row["email"]);
                                if (strlen($email[0]) == 1) {
                                    $email = '*@' . $email[1];
                                } elseif (strlen($email[0]) > 1) {
                                    $email = $email[0][0] . '****@' . $email[1];
                                }
                                echo $email;
                                ?>
                                
                            </td>
                            <td style="vertical-align:middle" class="text-muted"><?php echo date('Y-m-d H:i', $row["addtime"]) ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    echo '<tr><td colspan="2">空空如也...</td></tr>';
                }
                ?>

            </tbody>
        </table>
        <div class="container-fluid">
            <div class="row">
                <?php echo $page ?>
            </div>
        </div>
    </div>

</div>

