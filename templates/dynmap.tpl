{include file='header'}

{hascontent}
	<div class="paginationTop">
		{content}
			{pages print=true assign=pagesLinks controller="Dynmap" link="pageNo=%d"}
		{/content}
	</div>
{/hascontent}

{if $objects|count}
	<div class="section tabularBox">
		<table class="table">
			<thead>
				<tr>
					<th>{lang}wcf.global.objectID{/lang}</th>
					<th>{lang}wcf.global.title{/lang}</th>
				</tr>
			</thead>
			<tbody class="jsReloadPageWhenEmpty">
				{foreach from=$objects item=object}
					<a href="{link controller='DynmapMap' id=$object->getObjectID()}{/link}">
						<tr>
							<td class="columnID">{#$object->getObjectID()}</td>
							<td class="columnTitle">{$object->getTitle()}</td>
						</tr>
					</a>
				{/foreach}
			</tbody>
		</table>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
