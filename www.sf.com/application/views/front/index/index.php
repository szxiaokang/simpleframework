<?php
/**
 * @filename index.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-26 14:54:39
 * @version 1.0
 * @Description
 * 
 */
?>

<div class="row" style="margin-top:20px;">
    <div class="col-xs-1"></div>
    <div class="col-xs-6">
        <h4>最新新闻</h4>
        <table width="100%" class="table table-striped table-hover">
            <tbody>
                <?php
                if (count($rows)) {
                    foreach ($rows as $row) {
                        ?>
                        <tr>
                            <td>
                                <a href="/news/detail?id=<?php echo $row["id"] ?>"><?php echo!empty($row["images"]) ? "<span class=\"glyphicon glyphicon-picture\"></span>" : "" ?> <?php echo $row["title"] ?></a>
                            </td>
                            <td class="text-muted"><?php echo date('Y-m-d H:i', $row['addtime']) ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan=\"2\">空空如也...</td></tr>";
                }
                ?>

            </tbody>
        </table>
    </div>
    <div class="col-xs-4">
        <h4>最新用户</h4>
        <table width="100%" class="table table-striped table-hover">
            <tbody>
                <?php
                if (count($users)) {
                    foreach ($users as $row) {
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
                            <td style="vertical-align:middle" class="text-muted"><?php echo date('Y-m-d H:i', $row['addtime']) ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan=\"2\">空空如也...</td></tr>";
                }
                ?>

            </tbody>
        </table>
    </div>
</div>

