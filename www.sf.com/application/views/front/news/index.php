<?php
/**
 * @filename index.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-27 14:07:12
 * @version 1.0
 * @Description
 * @category SimpleFramework
 * 
 */
?>

<div class="row" style="margin-top:20px;">
    <div class="col-xs-1"></div>
    <div class="col-xs-8">
        <h4>新闻</h4>
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
        <div class="container-fluid">
            <div class="row">

                <?php echo $page ?>
            </div>
        </div>
    </div>
    <div class="col-xs-2">
        <h4>新闻分类</h4>
        <ul class="nav nav-pills nav-stacked">
            <li role="presentation"<?php echo empty($type) ? " class=\"active\"" : "" ?>><a href="/">所有</a>
                <?php
                foreach ($types as $type_id => $name) {
                    echo "<li role=\"presentation\"";
                    echo $type == $type_id ? " class=\"active\"" : '';
                    echo "><a href=\"?type={$type_id}\">{$name}</a>";
                }
                ?>
        </ul>
    </div>
</div>
