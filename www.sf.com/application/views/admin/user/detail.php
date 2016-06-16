<?php
/**
 * @filename index.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-6-2 9:40:46
 * @version 1.0
 * @Description
 * 
 */
?>

<aside class="right-side">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h3>
            用户详情 <small><a class="btn btn-info btn-sm" onclick="history.back()">返回</a> </small>
        </h3>
    </section>
    <!-- Main content -->
    <section class="content">
        <table width="100%" class="table table-striped table-hover">
            <tbody>
                <tr>
                    <td height="50" style="vertical-align: middle" width="10%">头像:  </td>
                    <td style="vertical-align: middle"><img width="45" height="45" src="<?php echo empty($row['avatar']) ? "/assets/images/{$row['sex']}.png" : $row['avatar'] ?>" /></td>
                </tr>
                <tr>
                    <td height="50" style="vertical-align: middle">UID:  </td>
                    <td style="vertical-align: middle"><?php echo $row['uid'] ?></td>
                </tr>
                <tr>
                    <td height="50" style="vertical-align: middle">Email:  </td>
                    <td style="vertical-align: middle"><?php echo $row['email'] ?></td>
                </tr>
                <tr>
                    <td height="50" style="vertical-align: middle">注册时间:  </td>
                    <td style="vertical-align: middle"><?php echo date('Y-m-d H:i:s', $row['addtime']) ?></td>
                </tr>
                <tr>
                    <td height="50" style="vertical-align: middle">性别:  </td>
                    <td style="vertical-align: middle"><?php echo $row['sex'] == 1 ? '男' : '女' ?></td>
                </tr>
                <tr>
                    <td height="50" style="vertical-align: middle">上次更新时间:  </td>
                    <td style="vertical-align: middle"><?php echo $row['edittime'] ? date('Y-m-d H:i:s', $row['edittime']) : '-' ?></td>
                </tr>
                <tr>
                    <td height="50" style="vertical-align: middle">登录次数:  </td>
                    <td style="vertical-align: middle"><?php echo $row['login_num'] ?></td>
                </tr>
            </tbody>
        </table>
    </section>
</aside>



