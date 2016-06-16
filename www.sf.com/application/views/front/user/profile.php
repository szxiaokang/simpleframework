<?php
/**
 * @filename index.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-31 13:35:15
 * @version 1.0
 * @Description
 * 
 */
?>
<div class="row" style="margin-top:20px;">
    <div class="col-xs-1"></div>
    <div class="col-xs-10">
        <h3>个人信息 <button type="submit" class="btn btn-default btn-sm" onclick="location.href='/user/edit'">编辑</button></h3>
        <table width="100%" class="table table-striped table-hover" style="margin-top:25px">
            <tbody>
                <tr>
                    <td height="50" style="vertical-align: middle" width="10%">头像:  </td>
                    <td style="vertical-align: middle"><img width="45" height="45" src="<?php echo empty($row['avatar']) ? "/assets/images/{$row['sex']}.png" : $row['avatar'] ?>" /></td>
                </tr>
                <tr>
                    <td height="50" style="vertical-align: middle">UID:  </td>
                    <td style="vertical-align: middle"><?php echo $row['uid']?></td>
                </tr>
                <tr>
                    <td height="50" style="vertical-align: middle">Email:  </td>
                    <td style="vertical-align: middle"><?php echo $row['email']?></td>
                </tr>
                <tr>
                    <td height="50" style="vertical-align: middle">注册时间:  </td>
                    <td style="vertical-align: middle"><?php echo date('Y-m-d H:i:s', $row['addtime'])?></td>
                </tr>
                <tr>
                    <td height="50" style="vertical-align: middle">性别:  </td>
                    <td style="vertical-align: middle"><?php echo $row['sex'] == 1 ? '男' : '女'?></td>
                </tr>
                <tr>
                    <td height="50" style="vertical-align: middle">上次更新时间:  </td>
                    <td style="vertical-align: middle"><?php echo $row['edittime'] ? date('Y-m-d H:i:s', $row['edittime']) : '-'?></td>
                </tr>
                <tr>
                    <td height="50" style="vertical-align: middle">登录次数:  </td>
                    <td style="vertical-align: middle"><?php echo $row['login_num']?></td>
                </tr>

            </tbody>
        </table>
    </div>
</div>
