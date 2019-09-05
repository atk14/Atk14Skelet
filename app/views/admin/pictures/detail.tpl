<h1>{$page_title}</h1>

<p class="lead">{$picture->getDescription()}</p>

{!$image|pupiq_img:"800"}<br>
{t}velikost originálu:{/t} {$image->getOriginalWidth()}&times;{$image->getOriginalHeight()}
