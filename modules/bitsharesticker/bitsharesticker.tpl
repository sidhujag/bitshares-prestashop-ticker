{*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="bitsharesticker_left" class="block bitsharesticker_left">

	<h4>
		{$title}
	</h4>	
	<div id="accordion">	
	{foreach from=$rates item='rateObj'}
		<h3>{$rateObj.heading}</h3>
		<div class="bitshares_rates">
		{foreach from=$rateObj.rates item='rate'}
			<div class="ticker-rates">	
				<div class="ticker-row">
					 <span class="key">{$rateObj.lineItem}</span>=<span class="value">{$rate}</span>
				</div>
			</div>
		{/foreach}
		</div>
	{/foreach}
	</div>
	<div class="ticker-footer">
			<strong><span class="rates_from">Rates updated every 5 minutes</span></strong>
	</div>
</div>