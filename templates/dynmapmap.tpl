{include file='header' pageTitle=$object->getTitle() contentTitle=$object->getTitle()}

<div class="section">
    <minecraft-map class="googleMap" id="{@$object->getObjectID()}"></minecraft-map>
</div>

{include file='dynmapJavaScript'}

{include file='footer'}
