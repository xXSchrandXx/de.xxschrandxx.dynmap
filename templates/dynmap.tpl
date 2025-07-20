{include file='header' pageTitle='wcf.page.dynmapMap.title'|phrase contentTitle='wcf.page.dynmapMap.title'|phrase}

{hascontent}
	<div class="paginationTop">
		{content}
			{pages print=true assign=pagesLinks controller="Dynmap" link="pageNo=%d"}
		{/content}
	</div>
{/hascontent}

{if $objects|count}
	<div class="section">
		<div class="contentItemList">
			{foreach from=$objects item=object}
				<div class="contentItem contentItemMultiColumn">
					<div class="contentItemLink">
						<div class="contentItemImage contentItemImageLarge">
							{icon name=$object->getIcon() size=64}
							<div class="contentItemContent">
								<h2 class="contentItemTitle"><a href="{link controller='DynmapMap' id=$object->getObjectID()}{/link}" class="contentItemTitleLink">{$object->getTitle()}</a></h2>
								<div class="contentItemDescription">
									{@$object->getDescription()}
								</div>
							</div>
						</div>
					</div>
				</div>
			{/foreach}
		</div>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
