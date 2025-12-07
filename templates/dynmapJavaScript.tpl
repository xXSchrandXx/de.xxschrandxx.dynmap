{if !$__dynmapInit|isset}
	{assign var=__dynmapInit value=1}

	<script data-relocate="true">
		{jsphrase name='wcf.global.leaflet.copy.openstreetmap'}
		{jsphrase name='wcf.global.leaflet.copy.topplus_open'}
		{jsphrase name='wcf.global.leaflet.copy.topplus_open_grau'}
		{jsphrase name='wcf.global.leaflet.copy.topplus_open_light'}
		{jsphrase name='wcf.global.leaflet.copy.topplus_open_light_grau'}
		{jsphrase name='wcf.global.leaflet.copy.custom'}

		require(['3rdParty/leaflet/leaflet', 'WoltLabSuite/Core/Core'], function(L, Core) {
			require([
				'3rdParty/dynmap/custommarker',
				'3rdParty/dynmap/dynmaputils',
				'3rdParty/dynmap/sidebarutils',
				'3rdParty/dynmap/minecraft',
				'3rdParty/dynmap/map',
			], function(
				custommarker,
				dynmaputils,
				sidebarutils,
				minecraft,
				map
			) {
				require(['3rdParty/dynmap/hdmap', 'xXSchrandXx/Core/Component/minecraft-map']);
			});
		});
	</script>
{/if}
