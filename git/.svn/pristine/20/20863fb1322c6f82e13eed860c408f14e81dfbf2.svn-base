<?php
$db = & factory::db();
$today = strtotime(date('Y-m-d 00:00:00'));

$r = $db->get("select count(*) as posts from #table_content where status=6");
$posts = $r['posts'];

$r = $db->get("select count(*) as posts from #table_content where status=3");
$posts_check = $r['posts'];

$r = $db->get("select count(*) as posts from #table_content where status=6 and published>=$today");
$posts_today = $r['posts'];

$r = $db->get("select count(*) as comments from #table_comment_topic where disabled=0");
$comments = $r['comments'];

$r = $db->get("select count(*) as comments from #table_comment_topic where disabled=1");
$comments_check = $r['comments'];

$r = $db->get("select count(*) as comments from #table_comment_topic where disabled=0 and created>=$today");
$comments_today = $r['comments'];

$r = $db->get("select count(*) as members from #table_member where groupid>=6 or groupid=1");
$members = $r['members'];

$r = $db->get("select count(*) as members from #table_member where groupid=4");
$members_check = $r['members'];

$r = $db->get("select count(*) as members from #table_member where groupid>=6 or groupid=1 and regtime>=$today");
$members_today = $r['members'];

$r = $db->get("select count(*) as count from #table_category");
$categorys = $r['count'];

$r = $db->get("select count(*) as count from #table_admin");
$admins = $r['count'];

$r = $db->get("select count(*) as count from #table_department");
$departments = $r['count'];
?> 
<div class="box_10 mar_t_10 " id="stat">
        <h3 class="layout" style="cursor:move;"><span class="f_l b">综合统计</span><span class="f_r mar_t_2" ><span><img src="images/down_1.gif" height="14" width="14" class="hand" onclick="module.down(this)" /></span>&nbsp;<span><img src="images/up_1.gif" height="14" width="14" class="hand" onclick="module.up(this)"/></span>&nbsp;<img src="images/close_1.gif" alt="关闭" title="关闭" height="14" width="14"  class="hand" onclick="module.del(this)" /></span></h3>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_info">
          <tbody>
            <tr>
              <th width="40">栏目</th>
              <td><?=$categorys?></td>
              <th width="60">管理员</th>
              <td><?=$admins?></td>
              <th width="60">部门</th>
              <td><?=$departments?></td>
            </tr>
            <tr>
              <th>内容</th>
              <td><?=$posts?></td>
              <th>待审内容</th>
              <td><?=$posts_check?></td>
              <th>今日发布</th>
              <td><?=$posts_today?></td>
            </tr>
            <tr>
              <th>评论</th>
              <td><?=$comments?></td>
              <th>待审评论</th>
              <td><?=$comments_check?></td>
              <th>今日发表</th>
              <td><?=$comments_today?></td>
            </tr>
            <tr>
              <th>会员</th>
              <td><?=$members?></td>
              <th>待审会员</th>
              <td><?=$members_check?></td>
              <th>今日注册</th>
              <td><?=$members_today?></td>
            </tr>
          </tbody>
        </table>
</div>