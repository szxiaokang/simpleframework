<?php
/**
 * @filename error_50x.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-6 13:30:53
 * @version 1.0
 * @Description
 * error page
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>500</title>
        <style type="text/css">
            <!--
            body,td,th {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 16px;
                color: #161616;
            }
            .font { font-size:14px; color:#969696}
            -->
        </style>
    </head>
    <body>
        <table width="70%" border="0" align="center" cellpadding="2" cellspacing="2">
            <tr>
                <td width="44%" height="516" style="line-height:30px"><img src="/assets/images/logo.gif" />
                    <div style="margin-bottom:20px"></div>
                    <div style="padding-left:10px;">
                        <div class="font"><?php echo isset($code) ? $code : ''; ?> That's an error. </div>
                        <div><b>Error: </b><?php echo isset($text) ? $text : 'Unknown Error.'; ?></div>
                        <div class="font">That's all we know.</div>
                    </div></td>
                <td width="56%" >
                    <div><img src="/assets/images/jugg.gif" height="500" width="370" /></div>
                </td>
            </tr>
        </table>
    </body>
</html>

