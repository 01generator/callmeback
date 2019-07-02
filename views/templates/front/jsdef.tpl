{*
*  @ LICENSE
*    MIT License
*  @ LICENSE
*}
<script type="text/javascript">
	var callmeback_ajax = '{$callmeback_ajax|escape:'javascript':'UTF-8'}';
	var current_country_iso_code = "{$current_country}";
    var prefered_countries = [];
    {foreach from=$prefered_countries item=country_code}
        prefered_countries.push("{$country_code}");
    {/foreach}
    var dir = '{$dir}';
</script>