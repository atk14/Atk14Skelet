{if $DEVELOPMENT}
{*
	Bootstrap 4 breakpoints info in right bottom corner. Visible in development mode.
*}
<div class="hidden-print" id="js-devcssinfo">
	<span class="badge badge-danger d-block  d-sm-none">XS</span>
	<span class="badge badge-warning d-none d-sm-block d-md-none">SM</span>
	<span class="badge badge-success d-none d-md-block d-lg-none">MD</span>
	<span class="badge badge-info d-none d-lg-block d-xl-none">LG</span>
	<span class="badge badge-secondary d-none d-xl-block">XL</span>
	<div class="text-center" style="font-size: 12px; font-weight: bold;" id="js-devcssinfo_text">&hellip;</div>
	<script>
		window.onresize = devinfocss_update;
		function devinfocss_update( event ){
			document.getElementById( 'js-devcssinfo_text' ).innerHTML = ( window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth ) + ' px';
		}
		devinfocss_update( null );		
	</script>
	<style scoped="true">
		#js-devcssinfo {
			position: fixed;
			bottom: 10px;
			right: 10px;
			width: 60px;
			border-radius: 6px;
			background-color: rgba(0,0,0,0.85);
			padding: 2px;
			color: white;
			transition: transform 0.5s;
			transform-origin: bottom right;
		}
		#js-devcssinfo:hover {
			transform: scale(2);
			transition: transform 0.05s;
		}
	</style>
</div>
{/if}
