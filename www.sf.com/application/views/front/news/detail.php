<?php
/**
 * @filename detail.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-27 14:57:26
 * @version 1.0
 * @Description
 * 
 */
?>

<div class="row" style="margin-top:20px;">
    <div class="col-xs-1"></div>
    <div class="col-xs-7">
        <h4 class="text-center"><?php echo $row['title']; ?> <small><?php echo date('Y-m-d H:i', $row['addtime']); ?></small>
        </h4>
        <div class="_content" style="border:solid 1px #efefef; padding:20px; line-height:24px; margin-bottom:20px">
            <?php
            echo!empty($row['images']) ? '<p style="padding:5px;"><img src="' . $row['images'] . '" /></p>' : '';
            echo $row['content'];
            ?>
        </div>
    </div>
    <div class="col-xs-3">
        <h4>相关新闻</h4>
        <table width="100%" class="table table-striped table-hover">
            <tbody>
                <?php
                if (count($rows)) {
                    foreach ($rows as $item) {
                        ?>
                        <tr>
                            <td><a href="/news/detail?id=<?php echo $item['id']; ?>">
                                    <?php
                                    echo!empty($item["images"]) ? "<span class=\"glyphicon glyphicon-picture\"></span>" : "";
                                    echo $item['title'];
                                    ?> 
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan = \"2\">空空如也...</td></tr>";
                }
                ?>

            </tbody>
        </table>
    </div>
</div>
<style type="text/css">
    ._content img {max-width: 100%; height: auto; display: block;}
</style>