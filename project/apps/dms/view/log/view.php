<style type="text/css">
    .log-detail {
        margin: 15px;
    }
    .log-detail th, .log-detail td {
        border: 1px dotted #D7E3F2;
        padding: 5px;
        min-width: 50px;
    }
    .log-detail pre {
        padding: 5px;
        white-space:pre-wrap !important;
        word-wrap:break-word !important;
        word-break:break-all !important;
        width: 480px;
        height: 250px;
        overflow-x: hidden;
        overflow-y: auto;
    }
</style>
<div class="log-detail">
    <table cellpadding="0" cellspacing="0" width="100%">
        <tbody>
            <tr>
                <th>日志ID</th>
                <td><?=$logid?></td>
                <th>操作人</th>
                <td><?=htmlspecialchars($operator)?></td>
                <th>动作</th>
                <td colspan="3"><?=$action?></td>
            </tr>
            <tr>
                <th>操作应用</th>
                <td><?=$appname?></td>
                <th>操作模型</th>
                <td><?=$modelname?></td>
                <th>操作对象ID</th>
                <td><?=$target?></td>
            </tr>
            <tr>
                <th>数据</th>
                <td colspan="5">
                    <pre><?php echo $data ? htmlspecialchars($data) : ''; ?></pre>
                </td>
            </tr>
            <tr>
                <th>操作日期</th>
                <td><?=$timename?></td>
                <th>操作IP</th>
                <td colspan="3"><?=$ipname?></td>
            </tr>
        </tbody>
    </table>
</div>