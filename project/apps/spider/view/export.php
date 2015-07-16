<pack>
	<?php foreach ($rules as $rule):?><rule>
		<name><![CDATA[<?=$rule['name']?>]]></name>
		<author><![CDATA[<?=$rule['author']?>]]></author>
		<guid><![CDATA[<?=$rule['guid']?>]]></guid>
		<version><?=$rule['version']?></version>
		<sitename><![CDATA[<?=$rule['sitename']?>]]></sitename>
		<description><![CDATA[<?=$rule['description']?>]]></description>
		<charset><![CDATA[<?=$rule['charset']?>]]></charset>
		<enter_rule><![CDATA[<?=$rule['enter_rule']?>]]></enter_rule>
		<list_rule>
			<listStart><![CDATA[<?=$rule['list_rule']['listStart']?>]]></listStart>
			<listEnd><![CDATA[<?=$rule['list_rule']['listEnd']?>]]></listEnd>
			<listType><![CDATA[<?=$rule['list_rule']['listType']?>]]></listType>
			<listUrl><![CDATA[<?=$rule['list_rule']['listUrl']?>]]></listUrl>
			<urlPattern><![CDATA[<?=$rule['list_rule']['urlPattern']?>]]></urlPattern>
			<listNextPage><![CDATA[<?=$rule['list_rule']['listNextPage']?>]]></listNextPage>
			<listLimitLength><![CDATA[<?=$rule['list_rule']['listLimitLength']?>]]></listLimitLength>
		</list_rule>
		<content_rule>
			<contentUrl><![CDATA[<?=$rule['content_rule']['contentUrl']?>]]></contentUrl>
			<rangeStart><![CDATA[<?=$rule['content_rule']['rangeStart']?>]]></rangeStart>
			<rangeEnd><![CDATA[<?=$rule['content_rule']['rangeEnd']?>]]></rangeEnd>
			<titleStart><![CDATA[<?=$rule['content_rule']['titleStart']?>]]></titleStart>
			<titleEnd><![CDATA[<?=$rule['content_rule']['titleEnd']?>]]></titleEnd>
			<contentStart><![CDATA[<?=$rule['content_rule']['contentStart']?>]]></contentStart>
			<contentEnd><![CDATA[<?=$rule['content_rule']['contentEnd']?>]]></contentEnd>
			<nextPage><![CDATA[<?=$rule['content_rule']['nextPage']?>]]></nextPage>
			<authorStart><![CDATA[<?=$rule['content_rule']['authorStart']?>]]></authorStart>
			<authorEnd><![CDATA[<?=$rule['content_rule']['authorEnd']?>]]></authorEnd>
			<sourceStart><![CDATA[<?=$rule['content_rule']['sourceStart']?>]]></sourceStart>
			<sourceEnd><![CDATA[<?=$rule['content_rule']['sourceEnd']?>]]></sourceEnd>
			<pubdateStart><![CDATA[<?=$rule['content_rule']['pubdateStart']?>]]></pubdateStart>
			<pubdateEnd><![CDATA[<?=$rule['content_rule']['pubdateEnd']?>]]></pubdateEnd>
			<allowTags><![CDATA[<?=$rule['content_rule']['allowTags']?>]]></allowTags>
			<saveRemoteImg><![CDATA[<?=$rule['content_rule']['saveRemoteImg']?>]]></saveRemoteImg>
			<?php foreach($rule['content_rule']['replacement'] as $r):?><replacement>
				<source><![CDATA[<?=$r['source']?>]]></source>
				<target><![CDATA[<?=$r['target']?>]]></target>
			</replacement>
			<?php endforeach;?>
		</content_rule>
		<?php foreach($rule['task'] as $t):?><task>
			<title><![CDATA[<?=$t['title']?>]]></title>
			<url><![CDATA[<?=$t['url']?>]]></url>
		</task>
		<?php endforeach;?>
	</rule>
	<?php endforeach;?>
</pack>