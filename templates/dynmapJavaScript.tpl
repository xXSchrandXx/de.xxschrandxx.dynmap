{if !$__dynmapInit|isset}
	{assign var=__dynmapInit value=1}

	<link rel="stylesheet" type="text/css" href="{$__wcf->getPath('wcf')}js/3rdParty/dynmap/dynmap_style.css" media="screen" />

	<script data-relocate="true">
		{jsphrase name='wcf.global.leaflet.copy.openstreetmap'}
		{jsphrase name='wcf.global.leaflet.copy.topplus_open'}
		{jsphrase name='wcf.global.leaflet.copy.topplus_open_grau'}
		{jsphrase name='wcf.global.leaflet.copy.topplus_open_light'}
		{jsphrase name='wcf.global.leaflet.copy.topplus_open_light_grau'}
		{jsphrase name='wcf.global.leaflet.copy.custom'}

		require.config({
			paths: {
				'leaflet': '3rdParty/leaflet/leaflet',
				'dynmap': '3rdParty/dynmap/map'
			},
			shim: {
				'leaflet': {
					exports: 'L'
				},
				'dynmap': {
					exports: 'D'
				}
			}
		});

		require(['3rdParty/leaflet/leaflet'], function(L) {
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
