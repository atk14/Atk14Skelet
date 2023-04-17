<div class="iobject--contact contact--shortcode{if $class} {$class}{/if}">
  {if $image}
  <div class="iobject__image">
    <img src="{$image}" alt="{$name}">
  </div>
  {/if}
  <div class="iobject__body">
    <h4 class="iobject__title">
      {$name} <span>{$position}</span>
    </h4>
    <div class="iobject__description">
      {!$content}
      <ul class="list--icons">
        {if $email}
        <li>
          <span class="list--icons__icon">{!"envelope"|icon}</span>
          <span class="list--icons__value"><a href="mailto:{$email}">{$email}</a></span>
        </li>
        {/if}
        {if $phone}
        <li>
          <span class="list--icons__icon">{!"mobile-alt"|icon}</span>
          <span class="list--icons__value">{!$phone|display_phone:true}</span>
        </li>
        {/if}
        {if $phone2}
        <li>
          <span class="list--icons__icon">{!"mobile-alt"|icon}</span>
          <span class="list--icons__value">{!$phone2|display_phone:true}</span>
        </li>
        {/if}
        {if $web}
        <li>
          <span class="list--icons__icon">{!"globe"|icon}</span>
          <span class="list--icons__value"><a href="{$web}">{$web}</a></span>
        </li>
        {/if}
        {if $facebook}
        <li>
          <span class="list--icons__icon">{!"facebook"|icon:"brands"}</span>
          <span class="list--icons__value"><a href="{$facebook}">{$facebook}</a></span>
        </li>
        {/if}
        {if $twitter}
        <li>
          <span class="list--icons__icon">{!"twitter"|icon:"brands"}</span>
          <span class="list--icons__value"><a href="{$twitter}">{$twitter}</a></span>
        </li>
        {/if}
        {if $instagram}
        <li>
          <span class="list--icons__icon">{!"instagram"|icon:"brands"}</span>
          <span class="list--icons__value"><a href="{$instagram}">{$instagram}</a></span>
        </li>
        {/if}
        {* TODO: more optional stuff like social networks etc.*}
      </ul>
      
    </div>
    {* TODO: QR code generation*}
    {*<div class="iobject__body-bottom">
      <div>
        <a href="#person_qr_{$uniqid}" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="person_qr_5f57a6054b046" class="qr-code-link">
          <ul class="list--icons mb-0">
            <li>
              <span class="list--icons__icon"><span class="fas fa-qrcode"></span></span>
              <span class="list--icons__value">QR code <span class="icon-down"><i class="fas fa-chevron-down"></i></span></span>
            </li>
          </ul>
        </a>
        <div class="collapse" id="person_qr_{$uniqid}">
          <img src="http://api.qrserver.com/v1/create-qr-code/?color=000000&amp;bgcolor=FFFFFF&amp;data=https%3A%2F%2Fsnapps.eu&amp;qzone=1&amp;margin=0&amp;size=400x400&amp;ecc=L" alt="QR kód" class="iobject__qr-code">
        </div>
      </div>
      <div class="text-right align-self-start">
        <a href="#" class="btn btn-sm btn-outline-primary">Více… <i class="fas fa-chevron-right"></i></a>
      </div>
    </div>*}
  </div>
</div>

