<?php
/*
* 2007-2015 PrestaShop
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
*/

if (!defined('_PS_VERSION_'))
	exit;

class BitsharesTicker extends Module
{
	public function __construct()
	{
		$this->name = 'bitsharesticker';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'bitshares.org';
		$this->need_instance = 0;
		
		$config = Configuration::getMultiple(array('PS_BITSHARES_TICKER_POSITION', 'PS_BITSHARES_TICKER_CURRENCY'));

		if (isset($config['PS_BITSHARES_TICKER_POSITION']))
			$this->ticker_position = $config['PS_BITSHARES_TICKER_POSITION'];
			
		if (isset($config['PS_BITSHARES_TICKER_CURRENCY']))
			$this->ticker_currency = $config['PS_BITSHARES_TICKER_CURRENCY'];

		parent::__construct();

		$this->displayName = $this->l('Bitshares Gold/Silver ticker');
		$this->description = $this->l('Displays latest Bitshares asset exchange rates in relation to Gold/Silver.');
	}

	public function install()
	{
		Configuration::updateValue('PS_BITSHARES_TICKER_POSITION', '');
		Configuration::updateValue('PS_BITSHARES_TICKER_CURRENCY', '');
		if (!parent::install())
			return false;
		if (!$this->registerHook('leftColumn') || !$this->registerHook('rightColumn') || !$this->registerHook('footer') || !$this->registerHook('header') || !$this->registerHook('displayBanner'))
			return false;
		return true;
	}

	public function uninstall()
	{
		Configuration::deleteByName('PS_BITSHARES_TICKER_POSITION');
		Configuration::deleteByName('PS_BITSHARES_TICKER_CURRENCY');
		return parent::uninstall();
	}

	public function getContent()
	{
		$html = '
		<h2>'.$this->l('Bitshares Gold/Silver ticker').'</h2>
		<br/>
		<img src="../modules/bitsharesticker/logo.png" style="float:left; margin-right:15px;">
		<b>Developed by: bitshares.org<br/>
		This module allows you to displays the latest Bitshares asset exchange rates in relation to Gold/Silver. If you require help please contact bitshares.org</b><br /><br />
		';

		if (Tools::isSubmit('submitConfiguration'))
		{
			$positions = implode(',', Tools::getValue('ticker_position'));
			$currencies = implode(',', Tools::getValue('ticker_currency'));
			
			Configuration::updateValue('PS_BITSHARES_TICKER_POSITION', $positions);
			Configuration::updateValue('PS_BITSHARES_TICKER_CURRENCY', $currencies);
			$html .= $this->displayConfirmation($this->l('The settings have been updated.'));
		}
		
		$currencies = Tools::getValue('ticker_currency', explode(',', $this->ticker_currency));
		$positions = Tools::getValue('ticker_position', explode(',', $this->ticker_position));


		$html .= '
			<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
				<fieldset>
					<legend><img src="'.$this->_path.'/logo.png" alt="" /> '.$this->l('Configure').'</legend>
					<table border="0" width="500" cellpadding="0" cellspacing="0" id="form">
						<tr><td width="150">'.$this->l('Position to show Ticker').'</td>
							<td>
							<input type="checkbox" name="ticker_position[]" value="top" '.(in_array('top', $positions)? 'checked':'').'/>&nbsp;Top<br/>	
							<input type="checkbox" name="ticker_position[]" value="left" '.(in_array('left', $positions)? 'checked':'').'/>&nbsp;Left<br/>
							<input type="checkbox" name="ticker_position[]" value="right" '.(in_array('right', $positions)? 'checked':'').'/>&nbsp;Right<br/>
							<input type="checkbox" name="ticker_position[]" value="footer" '.(in_array('footer', $positions)? 'checked':'').'/>&nbsp;Footer<br/>
							</td>
						</tr>
						<tr><td colspan="2"><br/></td></tr>
						
						<tr><td>'.$this->l('Bitshares Assets (Created in Currency section)').'</td>
							<td>';
							$currencieslist = Currency::getCurrencies(true, false, true);
							foreach ($currencieslist as $currency)
							{
								if(stripos($currency->name, 'bit') !== FALSE)
								{ 							
									$html .= '<input type="checkbox" name="ticker_currency[]" value="'.$currency->iso_code.'" '.(in_array($currency->iso_code, $currencies)? 'checked':'').'/>&nbsp;'.$currency->name.'<br/>';	
								}
							}		
							$html .= '</td>
						</tr>
						
						<tr><td colspan="2" align="center"><input class="button" name="submitConfiguration" value="'.$this->l('Update settings').'" type="submit" /></td></tr>
						
					</table>
				</fieldset>
			</form>
			';

		return $html;
	}

	/**
	* Returns module content
	*
	* @param array $params Parameters
	* @return string Content
	*/
	public function hookLeftColumn($params)
	{
		$currencies = explode(',', $this->ticker_currency);
		$positions = explode(',', $this->ticker_position);
		if(!in_array('left', $positions)) return '';
		$currencieslist = Currency::getCurrencies(true, false, true);
		$goldCurrency = '';
		$silverCurrency = '';
		$gldrates = '';
		$slvrates = '';
		foreach ($currencieslist as $currency)
		{
			if(stripos($currency->name, 'bit') !== FALSE)
			{ 
				if($currency->iso_code === 'XAU')
				{
					$goldCurrency = $currency;
				}
				else if($currency->iso_code === 'XAG')
				{
					$silverCurrency = $currency;
				}
			}	
		}
		if($goldCurrency === '' ||  $silverCurrency === '')
		{
			return 'Please tell the administrator to create bitSILVER(XAG) and bitGOLD(XAU) currencies from the Admin -> Currency section';
		}
		foreach ($currencieslist as $currency)
		{
			if(stripos($currency->name, 'bit') !== FALSE)
			{ 	
				if(in_array($currency->iso_code, $currencies))
				{
					if($goldCurrency->iso_code !== $currency->iso_code)
					{
						$convertedGoldPrice = Tools::convertPriceFull(1,$goldCurrency, $currency); 
						$gldrates[$currency->iso_code] = Tools::displayPrice($convertedGoldPrice, $currency);
					}
					if($silverCurrency->iso_code !== $currency->iso_code)
					{
						$convertedSilverPrice = Tools::convertPriceFull(1,$silverCurrency, $currency); 
						$slvrates[$currency->iso_code] = Tools::displayPrice($convertedSilverPrice, $currency);					
					}
				}
			}	
				
		}
		
		$rates = array(array('heading'=>$this->l('Gold Rates'), 'lineItem'=>$this->l('1 Gold oz'), 'rates'=>$gldrates), array('heading'=>$this->l('Silver Rates'), 'lineItem'=>$this->l('1 Silver oz'), 'rates'=>$slvrates));
		$this->smarty->assign('rates', $rates);
		$this->smarty->assign('title', $this->l('Gold/Silver Rates'));		
		$this->smarty->assign('this_path', $this->_path);	
		return $this->display(__FILE__, 'bitsharesticker.tpl');
	}
	public function hookDisplayBanner($params)
	{
		$positions = explode(',', $this->ticker_position);
		if(!in_array('top', $positions)) return '';				
		$currencyPrimary = 'GOLD,SILVER';
		$source = Context::getContext()->shop->getBaseURL() . 'bitshares/checkout/callbacks/callback_getfeedprices.php';
		$currencyTemplateSource = Context::getContext()->shop->getBaseURL() . 'bitshares/checkout/Common-Currency.json';
		$this->smarty->assign('title', $this->l('Live Ticker'));	
		$this->smarty->assign('source', $source);
		$this->smarty->assign('currencyTemplateSource', $currencyTemplateSource);	
		$this->smarty->assign('currencyPrimary', $currencyPrimary);
		$this->smarty->assign('currencySecondary', $this->ticker_currency);	
		return $this->display(__FILE__, 'bitsharesticker-top.tpl');
	}
	public function hookRightColumn($params)
	{
		$currencies = explode(',', $this->ticker_currency);
		$positions = explode(',', $this->ticker_position);
		if(!in_array('right', $positions)) return '';
		$currencieslist = Currency::getCurrencies(true, false, true);
		$goldCurrency = '';
		$silverCurrency = '';
		$gldrates = '';
		$slvrates = '';	
		foreach ($currencieslist as $currency)
		{
			if(stripos($currency->name, 'bit') !== FALSE)
			{ 
				if($currency->iso_code === 'XAU')
				{
					$goldCurrency = $currency;
				}
				else if($currency->iso_code === 'XAG')
				{
					$silverCurrency = $currency;
				}
			}	
		}
		if($goldCurrency === '' ||  $silverCurrency === '')
		{
			return 'Please tell the administrator to create bitSILVER(XAG) and bitGOLD(XAU) currencies from the Admin -> Currency section';
		}			
		foreach ($currencieslist as $currency)
		{
			if(stripos($currency->name, 'bit') !== FALSE)
			{ 	
				if(in_array($currency->iso_code, $currencies))
				{
					if($goldCurrency->iso_code !== $currency->iso_code)
					{
						$convertedGoldPrice = Tools::convertPriceFull(1,$goldCurrency, $currency); 
						$gldrates[$currency->iso_code] = Tools::displayPrice($convertedGoldPrice, $currency);
					}
					if($silverCurrency->iso_code !== $currency->iso_code)
					{
						$convertedSilverPrice = Tools::convertPriceFull(1,$silverCurrency, $currency); 
						$slvrates[$currency->iso_code] = Tools::displayPrice($convertedSilverPrice, $currency);					
					}
				}
			}	
				
		}		
		$rates = array(array('heading'=>$this->l('Gold Rates'), 'lineItem'=>$this->l('1 Gold oz'), 'rates'=>$gldrates), array('heading'=>$this->l('Silver Rates'), 'lineItem'=>$this->l('1 Silver oz'), 'rates'=>$slvrates));
		$this->smarty->assign('rates', $rates);
		$this->smarty->assign('title', $this->l('Gold/Silver Rates'));		
		$this->smarty->assign('this_path', $this->_path);
		return $this->display(__FILE__, 'bitsharesticker-right.tpl');
	}

	public function hookFooter($params)
	{
		$positions = explode(',', $this->ticker_position);
		if(!in_array('footer', $positions)) return '';
		return hookDisplayBanner($params);
	}
	public function hookHeader($params)
	{
		$this->context->controller->addJqueryUI('ui.accordion');
		$this->context->controller->addJS(($this->_path).'bitsharesticker.js');
		$this->context->controller->addJS(Context::getContext()->shop->getBaseURL() . 'bitshares/checkout/'.'bitsharescheckout.min.js');
		$this->context->controller->addCSS(_PS_JS_DIR_.'jquery/ui/themes/ui-lightness/jquery-ui.css');	
		$this->context->controller->addCSS(($this->_path).'bitsharesticker.css', 'all');
		$this->context->controller->addCSS(Context::getContext()->shop->getBaseURL() . 'bitshares/checkout/'.'bitsharescheckout.min.css', 'all');
	}

}