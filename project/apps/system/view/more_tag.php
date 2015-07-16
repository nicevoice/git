<?php if(defined('BIZ_NAME')){?>
<tr>
    <th><?php echo BIZ_NAME;?>：</th>
    <td><?=element::shop_seletor('shopid','shopid', $shopid)?></td>
</tr>
<?php }?>