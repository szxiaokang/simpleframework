<?php
/**
 * @filename Pager.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-4 11:14:30
 * @version 1.0
 * @Description
 * 分页程序
 */
class Pager {

    //当前页
    public $pageNo = 1;
    //页总数
    public $pageCount = 1;
    //页大小
    public $pageSize = 10;
    //记录数
    public $recordCount = 1;
    //分页变量
    public $pageStr = 'page';
    //分页连接字符串
    public $firstPage = '';
    public $nextPage = '';
    public $prevPage = '';
    public $lastPage = '';
    //分页显示, 初始化
    public $firstHref = '<li><span>首页</span></li>';
    public $nextHref = '<li><span>上一页</span></li>';
    public $prevHref = '<li><span>下一页</span></li>';
    public $lastHref = '<li><span>最后一页</span></li>';

    //当前文件名
    public $thisFile;
    //当前查询参数
    public $thisParams;
    //错误信息
    public $err = '';

    //初始化
    public function setParams($myRecordCount = 1, $myPageSize = 10, $myPageStr = 'page') {
        if (!is_numeric($myRecordCount) || (!is_numeric($myPageSize))) {
            $this->err = 'params error.';
            $this->halt();
        }
        $this->recordCount = $myRecordCount;
        $this->pageSize = $myPageSize;
        $this->pageCount = @ceil($myRecordCount / $myPageSize);
        $this->pageStr = $myPageStr;
        $this->setPageUrl();
    }

    //处理url
    public function setPageUrl() {
        $pageStr = $this->pageStr;
        $myPageNo = $this->getPageNo();
        $nextPageNo = $myPageNo + 1;
        $prevPageNo = $myPageNo - 1;
        $replace = $this->pageStr . '=' . $myPageNo;
        $myFile = $_SERVER['REQUEST_URI'];
        //$myUrl = $_SERVER['QUERY_STRING'];
        $myUrl = $_SERVER['QUERY_STRING'];
        if (empty($myUrl)) {
            $myFile = '?' . $replace;
        } elseif (!preg_match("/$replace/", $myUrl)) {
            $myFile = '?' . $myUrl . '&' . $replace;
        }
        $this->thisFile = $myFile;
        $this->thisParams = $myFile;
        $this->firstPage = str_replace($replace, $pageStr . '=1', $myFile);
        $this->nextPage = str_replace($replace, $pageStr . '=' . $nextPageNo, $myFile);
        $this->prevPage = str_replace($replace, $pageStr . '=' . $prevPageNo, $myFile);
        $this->lastPage = str_replace($replace, $pageStr . '=' . $this->pageCount, $myFile);
    }

    //获取当前页码
    public function getPageNo() {
        $myPageNo = @intval($_GET[$this->pageStr]);
        if ($myPageNo >= 1 && $myPageNo <= $this->pageCount) {
            $this->pageNo = $myPageNo;
        }
        return $this->pageNo;
    }

    //连接处理
    public function getLink($linkUrl, $linkName, $linkCss = '') {
        return '<li><a class="' . $linkCss . '" href="' . $linkUrl . '"><span>' . $linkName . '</span></a></li>';
    }

    //下拉列表跳转页 ，适合于后台
    public function getSelectPage($divCss = '') {
        $myPageCount = @$this->pageCount;
        $myFile = $this->thisFile;
        $myParams = $this->thisParams;
        $myPageStr = $this->pageStr;
        $myPageNo = $this->pageNo;
        $myReplace = $myPageStr . '=' . $myPageNo;
        $str = '';

        if ($myPageNo > 1) {
            $this->firstHref = $this->getLink($this->firstPage, '首页');
            $this->prevHref = $this->getLink($this->prevPage, '上一页');
        }
        if ($myPageNo < $myPageCount) {
            $this->nextHref = $this->getLink($this->nextPage, '下一页');
            $this->lastHref = $this->getLink($this->lastPage, '最后一页');
        }
        $str = $this->firstHref . $this->prevHref . $this->nextHref . $this->lastHref;
        $str .= '<li> 共' . $this->recordCount . '条记录, ' . $myPageCount . '页';
        if ($myPageCount > 0) {
            $str .= ' 转到 ';
            $str .= '<select name = "__selectPage' . $myPageCount . '" onChange = "javascript:location.href = this.value">';
            for ($i = 1; $i <= $myPageCount; $i++) {
                $str .= '<option';
                $str .= $myPageNo == $i ? ' value = "' . $i . '" selected' : ' value = "' . str_replace($myReplace, $myPageStr . '=' . $i, $myParams) . '"';
                $str .= '>';
                $str .= $i . '</option>';
            }
            $str .= '</select>';
        }
        $str .= '</li>';
        return '<ul class="pager">' . $str . '</ul>';
    }

    //数字式分页
    public function getNumberPage($divCss = '') {
        $myPageCount = @$this->pageCount;
        $myFile = $this->thisFile;
        $myParams = $this->thisParams;
        $myPageStr = $this->pageStr;
        $myPageNo = $this->pageNo;
        $back = '上一页 ';
        $next = '下一页';
        //$myPageCount = 10;
        $myReplace = $myPageStr . '=' . $myPageNo;
        $myPrevPage = $myPageNo > 1 ? $myPageNo - 1 : 1;
        $myNextPage = $myPageNo < $myPageCount && $myPageNo != $myPageCount ? $myPageNo + 1 : $myPageCount;
        $str = '<div class="' . $divCss . '">';
        $str .= $myPageNo == 1 ? '<span class="disabled">' . $back . '</span>' : '';
        $str .= $myPageNo > 1 ? '<a href="' . $this->prevPage . '">' . $back . '</a>' : '';
        if ($myPageCount > 10 && $myPageNo >= 9 && ($myPageNo + 4) < $myPageCount) {

            $str .= '<a href="' . str_replace($myReplace, $myPageStr . '=1', $myParams) . '">1</a> .  .  . ';
            $endI = $myPageNo + 3;
            $endI = $endI >= $myPageCount ? $myPageCount : $endI;
            $startI = $endI - 6;
            for ($i = $startI; $i <= $endI; $i++) {
                $str .= $myPageNo == $i ?
                        '<span class="current">' . $i . '</span>' :
                        '<a href="' . str_replace($myReplace, $myPageStr . '=' . $i, $myParams) . '">' . $i . '</a>';
            }
            $str .= '. . . <a href="' . str_replace($myReplace, $myPageStr . '=' . $myPageCount, $myParams) . '">' . $myPageCount . '</a>';
        } elseif ($myPageCount > 10 && $myPageNo < 9) {
            for ($i = 1; $i <= 9; $i++) {
                $str .= $myPageNo == $i ?
                        '<span class="current">' . $i . '</span>' :
                        '<a href="' . str_replace($myReplace, $myPageStr . '=' . $i, $myParams) . '">' . $i . '</a>';
            }
            $str .= '. . . <a href="' . str_replace($myReplace, $myPageStr . '=' . $myPageCount, $myParams) . '">' . $myPageCount . '</a>';
        } elseif ($myPageCount > 10 && ($myPageNo + 4) >= $myPageCount) {
            $str .= '<a href="' . str_replace($myReplace, $myPageStr . '=1', $myParams) . '">1</a> .  .  . ';
            for ($i = $myPageCount - 7; $i <= $myPageCount; $i++) {
                $str .= $myPageNo == $i ?
                        '<span class="current">' . $i . '</span>' :
                        '<a href="' . str_replace($myReplace, $myPageStr . '=' . $i, $myParams) . '">' . $i . '</a>';
            }
        } elseif ($myPageCount <= 10) {
            for ($i = 1; $i <= $myPageCount; $i++) {
                $str .= $myPageNo == $i ?
                        '<span class="current">' . $i . '</span>' :
                        '<a href="' . str_replace($myReplace, $myPageStr . '=' . $i, $myParams) . '">' . $i . '</a>';
            }
        }

        $str .= $myPageCount == $myPageNo ? '<span class="disabled">' . $next . '</span>' : '';
        $str .= $myPageCount > $myPageNo ? '<a href="' . $this->nextPage . '">' . $next . '</a>' : '';
        $str .= '</div>';
        return $str;
    }

    //显示
    public function getHtml($model = 1, $divCss = '') {
        $str = '';
        //第一种样式
        if ($model == 1) {
            $str = $this->getSelectPage('general');
        }

        //第二种样式
        if ($model == 2) {
            $divCss = $divCss == '' ? 'badoo' : $divCss;
            $str = $this->getNumberPage($divCss);
        }
        return $str;
    }

    //错误处理
    public function halt() {
        die($this->err);
    }

}
