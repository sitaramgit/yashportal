<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/include/utils/EditViewUtils.php,v 1.188 2005/04/29 05:5 * 4:39 rank Exp
 * Description:  Includes generic helper functions used throughout the application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('include/database/PearDatabase.php');
require_once('include/ComboUtil.php'); //new
require_once('include/utils/CommonUtils.php'); //new
require_once 'modules/PickList/DependentPickListUtils.php';

function getAngularAssociatedProducts($module, $focus, $seid = '', $refModuleName = false)
{
	global $log;
	$log->debug("Entering getAssociatedProducts(".$module.",".get_class($focus).",".$seid."='') method ...");
	global $adb;
	$output = '';
	global $theme,$current_user;

	$no_of_decimal_places = getCurrencyDecimalPlaces();
	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	$product_Detail = Array();
	$product_final = Array();
	$inventoryModules = getInventoryModules();
	if (in_array($module, $inventoryModules)) {
		$taxtype = getInventoryTaxType($module, $focus->id);
	}

	$additionalProductFieldsString = $additionalServiceFieldsString = '';
	$lineItemSupportedModules = array('Accounts', 'Contacts', 'Leads', 'Potentials');

	// DG 15 Aug 2006
	// Add "ORDER BY sequence_no" to retain add order on all inventoryproductrel items

	if (in_array($module, $inventoryModules))
	{
		$query="SELECT
					case when vtiger_products.productid != '' then vtiger_products.productname else vtiger_service.servicename end as productname,
					case when vtiger_products.productid != '' then vtiger_products.product_no else vtiger_service.service_no end as productcode,
					case when vtiger_products.productid != '' then vtiger_products.unit_price else vtiger_service.unit_price end as unit_price,
					case when vtiger_products.productid != '' then vtiger_products.qtyinstock else 'NA' end as qtyinstock,
					case when vtiger_products.productid != '' then 'Products' else 'Services' end as entitytype,
					vtiger_inventoryproductrel.listprice, vtiger_products.is_subproducts_viewable, 
					vtiger_inventoryproductrel.description AS product_description, vtiger_inventoryproductrel.*,
					vtiger_crmentity.deleted FROM vtiger_inventoryproductrel
					LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_inventoryproductrel.productid
					LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_inventoryproductrel.productid
					LEFT JOIN vtiger_service ON vtiger_service.serviceid=vtiger_inventoryproductrel.productid
					WHERE id=? ORDER BY sequence_no";
			$params = array($focus->id);
	}
	elseif(in_array($module, $lineItemSupportedModules))
	{
		$query = '(SELECT vtiger_products.productid, vtiger_products.productname, vtiger_products.product_no AS productcode, vtiger_products.purchase_cost,
					vtiger_products.unit_price, vtiger_products.qtyinstock, vtiger_crmentity.deleted, "Products" AS entitytype,
					vtiger_products.is_subproducts_viewable, vtiger_crmentity.description '.$additionalProductFieldsString.' FROM vtiger_products
					INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_products.productid
					INNER JOIN vtiger_seproductsrel ON vtiger_seproductsrel.productid=vtiger_products.productid
					INNER JOIN vtiger_productcf ON vtiger_products.productid = vtiger_productcf.productid
					WHERE vtiger_seproductsrel.crmid=? AND vtiger_crmentity.deleted=0 AND vtiger_products.discontinued=1)
					UNION
					(SELECT vtiger_service.serviceid AS productid, vtiger_service.servicename AS productname, vtiger_service.service_no AS productcode,
					vtiger_service.purchase_cost, vtiger_service.unit_price, "NA" as qtyinstock, vtiger_crmentity.deleted,
					"Services" AS entitytype, 1 AS is_subproducts_viewable, vtiger_crmentity.description '.$additionalServiceFieldsString.' FROM vtiger_service
					INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_service.serviceid
					INNER JOIN vtiger_crmentityrel ON vtiger_crmentityrel.relcrmid = vtiger_service.serviceid
					INNER JOIN vtiger_servicecf ON vtiger_service.serviceid = vtiger_servicecf.serviceid
					WHERE vtiger_crmentityrel.crmid=? AND vtiger_crmentity.deleted=0 AND vtiger_service.discontinued=1)';
			$params = array($seid, $seid);
	}
	elseif ($module == 'Vendors') {
		$query = 'SELECT vtiger_products.productid, vtiger_products.productname, vtiger_products.product_no AS productcode, vtiger_products.purchase_cost,
					vtiger_products.unit_price, vtiger_products.qtyinstock, vtiger_crmentity.deleted, "Products" AS entitytype,
					vtiger_products.is_subproducts_viewable, vtiger_crmentity.description '.$additionalServiceFieldsString.' FROM vtiger_products
					INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_products.productid
					INNER JOIN vtiger_vendor ON vtiger_vendor.vendorid = vtiger_products.vendor_id
					INNER JOIN vtiger_productcf ON vtiger_products.productid = vtiger_productcf.productid
					WHERE vtiger_vendor.vendorid=? AND vtiger_crmentity.deleted=0 AND vtiger_products.discontinued=1';
			$params = array($seid);
	}
	elseif ($module == 'HelpDesk') {
		$query = 'SELECT vtiger_service.serviceid AS productid, vtiger_service.servicename AS productname, vtiger_service.service_no AS productcode,
					vtiger_service.purchase_cost, vtiger_service.unit_price, "NA" as qtyinstock, vtiger_crmentity.deleted,
					"Services" AS entitytype, 1 AS is_subproducts_viewable, vtiger_crmentity.description '.$additionalServiceFieldsString.' FROM vtiger_service
					INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_service.serviceid
					INNER JOIN vtiger_crmentityrel ON vtiger_crmentityrel.relcrmid = vtiger_service.serviceid
					INNER JOIN vtiger_servicecf ON vtiger_service.serviceid = vtiger_servicecf.serviceid
					WHERE vtiger_crmentityrel.crmid=? AND vtiger_crmentity.deleted=0 AND vtiger_service.discontinued=1';
			$params = array($seid);
	}
	elseif($module == 'Products')
	{
		$query = 'SELECT vtiger_products.productid, vtiger_products.productname, vtiger_products.product_no AS productcode, vtiger_products.purchase_cost,
					vtiger_products.unit_price, vtiger_products.qtyinstock, vtiger_crmentity.deleted, "Products" AS entitytype,
					vtiger_products.is_subproducts_viewable, vtiger_crmentity.description '.$additionalProductFieldsString.' FROM vtiger_products
					INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_products.productid
					INNER JOIN vtiger_productcf ON vtiger_products.productid = vtiger_productcf.productid
					WHERE vtiger_crmentity.deleted=0 AND vtiger_products.productid=?';
			$params = array($seid);
	}
	elseif($module == 'Services')
	{
		$query='SELECT vtiger_service.serviceid AS productid, vtiger_service.servicename AS productname, vtiger_service.service_no AS productcode,
					vtiger_service.purchase_cost, vtiger_service.unit_price AS unit_price, "NA" AS qtyinstock, vtiger_crmentity.deleted,
					"Services" AS entitytype, 1 AS is_subproducts_viewable, vtiger_crmentity.description '.$additionalServiceFieldsString.' FROM vtiger_service
					INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_service.serviceid
					INNER JOIN vtiger_servicecf ON vtiger_service.serviceid = vtiger_servicecf.serviceid
					WHERE vtiger_crmentity.deleted=0 AND vtiger_service.serviceid=?';
			$params = array($seid);
	}

	$result = $adb->pquery($query, $params);
	$num_rows=$adb->num_rows($result);
	for($i=1;$i<=$num_rows;$i++)
	{
		$deleted = $adb->query_result($result,$i-1,'deleted');
		$hdnProductId = $adb->query_result($result,$i-1,'productid');
		$hdnProductcode = $adb->query_result($result,$i-1,'productcode');
		$productname=$adb->query_result($result,$i-1,'productname');
		$productdescription=$adb->query_result($result,$i-1,'description');
		$comment=$adb->query_result($result,$i-1,'comment');
		$qtyinstock=$adb->query_result($result,$i-1,'qtyinstock');
		$qty=$adb->query_result($result,$i-1,'quantity');
		$unitprice=$adb->query_result($result,$i-1,'unit_price');
		$listprice=$adb->query_result($result,$i-1,'listprice');
		$entitytype=$adb->query_result($result,$i-1,'entitytype');
		$purchaseCost = $adb->query_result($result,$i-1,'purchase_cost');
		$margin = $adb->query_result($result,$i-1,'margin');
		$isSubProductsViewable = $adb->query_result($result, $i-1, 'is_subproducts_viewable');

		if ($purchaseCost) {
			$product_Detail['purchaseCost'] = number_format($purchaseCost, $no_of_decimal_places, '.', '');
		}

		if ($margin) {
			$product_Detail['margin'] = number_format($margin, $no_of_decimal_places, '.', '');
		}

		if(($deleted) || (!isset($deleted))){
			$product_Detail['productDeleted'] = true;
		}elseif(!$deleted){
			$product_Detail['productDeleted'] = false;
		}

		if (!empty($entitytype)) {
			$product_Detail['entityType']=$entitytype;
		}

		if($listprice == '')
			$listprice = $unitprice;
		if($qty =='')
			$qty = 1;

		//calculate productTotal
		$productTotal = $qty*$listprice;

		//Delete link in First column
		if($i != 1)
		{
			$product_Detail['delRow']="Del";
		}

		if (in_array($module, $lineItemSupportedModules) || $module === 'Vendors' || (!$focus->mode && $seid)) {
			$subProductsQuery = 'SELECT vtiger_seproductsrel.crmid AS prod_id, quantity FROM vtiger_seproductsrel
								 INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_seproductsrel.crmid
								 INNER JOIN vtiger_products ON vtiger_products.productid = vtiger_seproductsrel.crmid
								 WHERE vtiger_seproductsrel.productid=? AND vtiger_seproductsrel.setype=? AND vtiger_products.discontinued=1';

			$subParams = array($seid);
			if (in_array($module, $lineItemSupportedModules) || $module === 'Vendors') {
				$subParams = array($hdnProductId);
			}
			array_push($subParams, 'Products');
		} else {
			$subProductsQuery = 'SELECT productid AS prod_id, quantity FROM vtiger_inventorysubproductrel WHERE id=? AND sequence_no=?';
			$subParams = array($focus->id, $i);
		}
		$subProductsResult = $adb->pquery($subProductsQuery, $subParams);
		$subProductsCount = $adb->num_rows($subProductsResult);

		$subprodid_str='';
		$subprodname_str='';

		$subProductQtyList = array();
		for($j=0; $j<$subProductsCount; $j++){
			$sprod_id = $adb->query_result($subProductsResult, $j, 'prod_id');
			$sprod_name = getProductName($sprod_id);
			if(isset($sprod_name)) {
				$subQty = $adb->query_result($subProductsResult, $j, 'quantity');
				$subProductQtyList[$sprod_id] = array('name' => $sprod_name, 'qty' => $subQty);
				if(isRecordExists($sprod_id) && $_REQUEST['view'] === 'Detail') {
					$subprodname_str .= "<a href='index.php?module=Products&view=Detail&record=$sprod_id' target='_blank'> <em> - $sprod_name ($subQty)</em><br></a>";
				} else {
					$subprodname_str .= "<em> - $sprod_name ($subQty)</em><br>";
				}
				$subprodid_str .= "$sprod_id:$subQty,";
			}
		}
		$subprodid_str = rtrim($subprodid_str, ',');

		$product_Detail['hdnProductId'] = $hdnProductId;
		$product_Detail['productName'] = from_html($productname);
		/* Added to fix the issue Product Pop-up name display*/
		if($_REQUEST['action'] == 'CreateSOPDF' || $_REQUEST['action'] == 'CreatePDF' || $_REQUEST['action'] == 'SendPDFMail')
			$product_Detail['productName']= htmlspecialchars($product_Detail['productName']);
		$product_Detail['hdnProductcode'] = $hdnProductcode;
		$product_Detail['productDescription']= from_html($productdescription);
		if($module == 'Vendors' || $module == 'Products' || $module == 'Services' || in_array($module, $lineItemSupportedModules)) {
			$product_Detail['comment']= $productdescription;
		}else {
            $product_Detail['comment']= $comment;
		}

		if($module != 'PurchaseOrder' && $focus->object_name != 'Order') {
			$product_Detail['qtyInStock']=decimalFormat($qtyinstock);
		}
		$listprice = number_format($listprice, $no_of_decimal_places,'.','');
		$product_Detail['qty']=decimalFormat($qty);
		$product_Detail['listPrice']=$listprice;
		$product_Detail['unitPrice']=number_format($unitprice, $no_of_decimal_places,'.','');
		$product_Detail['productTotal'] = number_format($productTotal, $no_of_decimal_places, '.', '');
		$product_Detail['subproduct_ids']=$subprodid_str;
		if ($isSubProductsViewable) {
			$product_Detail['subprod_qty_list'] = $subProductQtyList;
			$product_Detail['subprod_names']=$subprodname_str;
		}
		$discount_percent = decimalFormat($adb->query_result($result,$i-1,'discount_percent'));
		$discount_amount = $adb->query_result($result,$i-1,'discount_amount');
		$discount_amount = decimalFormat(number_format($discount_amount, $no_of_decimal_places,'.',''));
		$discountTotal = 0;
		//Based on the discount percent or amount we will show the discount details

		//To avoid NaN javascript error, here we assign 0 initially to' %of price' and 'Direct Price reduction'(for Each Product)
		$product_Detail['discount_percent'] = 0;
		$product_Detail['discount_amount'] = 0;

		if(!empty($discount_percent)) {
			$product_Detail['discount_type'] = "percentage";
			$product_Detail['discount_percent'] = $discount_percent;
			$product_Detail['checked_discount_percent'] = ' checked';
			$product_Detail['style_discount_percent'] = ' style="visibility:visible"';
			$product_Detail['style_discount_amount'] = ' style="visibility:hidden"';
			$discountTotal = $productTotal*$discount_percent/100;
		} elseif(!empty($discount_amount)) {
			$product_Detail['discount_type'] = "amount";
			$product_Detail['discount_amount'] = $discount_amount;
			$product_Detail['checked_discount_amount'] = ' checked';
			$product_Detail['style_discount_amount'] = ' style="visibility:visible"';
			$product_Detail['style_discount_percent'] = ' style="visibility:hidden"';
			$discountTotal = $discount_amount;
		}
		else
		{
			$product_Detail['checked_discount_zero'] = ' checked';
		}
		$totalAfterDiscount = $productTotal-$discountTotal;
		$totalAfterDiscount = number_format($totalAfterDiscount, $no_of_decimal_places,'.','');
		$discountTotal = number_format($discountTotal, $no_of_decimal_places,'.','');
		$product_Detail['discountTotal'] = $discountTotal;
		$product_Detail['totalAfterDiscount'] = $totalAfterDiscount;

		$taxTotal = 0;
		$taxTotal = number_format($taxTotal, $no_of_decimal_places,'.','');
		$product_Detail['taxTotal'] = $taxTotal;

		//Calculate netprice
		$netPrice = $totalAfterDiscount+$taxTotal;
		//if condition is added to call this function when we create PO/SO/Quotes/Invoice from Product module
		if (in_array($module, $inventoryModules)) {
			if($taxtype == 'individual')
			{
				//Add the tax with product total and assign to netprice
				$netPrice = $netPrice+$taxTotal;
			}
		}
		$product_Detail['netPrice'] = number_format($netPrice, getCurrencyDecimalPlaces(), '.', '');

		//First we will get all associated taxes as array
		$tax_details = getTaxDetailsForProduct($hdnProductId,'all');
		$regionsList = array();
		foreach ($tax_details as $taxInfo) {
			$regionsInfo = array('default' => $taxInfo['percentage']);
			foreach ($taxInfo['productregions'] as $list) {
				if (is_array($list['list'])) {
					foreach(array_fill_keys($list['list'], $list['value']) as $key => $value) {
						$regionsInfo[$key] = $value;
					}
				}
			}
			$regionsList[$taxInfo['taxid']] = $regionsInfo;
		}
		//Now retrieve the tax values from the current query with the name
		for($tax_count=0;$tax_count<count($tax_details);$tax_count++)
		{
			$tax_name = $tax_details[$tax_count]['taxname'];
			$tax_label = $tax_details[$tax_count]['taxlabel'];
			$tax_value = 0;

			$tax_value = $tax_details[$tax_count]['percentage'];
			if($focus->id != '' && $taxtype == 'individual') {
				$lineItemId = $adb->query_result($result, $i-1, 'lineitem_id');
				$tax_value = getInventoryProductTaxValue($focus->id, $hdnProductId, $tax_name, $lineItemId);
				$selectedRegionId = $focus->column_fields['region_id'];
				if ($selectedRegionId) {
					$regionsList[$tax_details[$tax_count]['taxid']][$selectedRegionId] = $tax_value;
				} else {
					$regionsList[$tax_details[$tax_count]['taxid']]['default'] = $tax_value;
				}
			}

			$product_Detail['taxes'][$tax_count]['taxname']		= $tax_name;
			$product_Detail['taxes'][$tax_count]['taxlabel']	= $tax_label;
			$product_Detail['taxes'][$tax_count]['percentage']	= $tax_value;
			$product_Detail['taxes'][$tax_count]['deleted']		= $tax_details[$tax_count]['deleted'];
			$product_Detail['taxes'][$tax_count]['taxid']		= $tax_details[$tax_count]['taxid'];
			$product_Detail['taxes'][$tax_count]['type']		= $tax_details[$tax_count]['type'];
			$product_Detail['taxes'][$tax_count]['method']		= $tax_details[$tax_count]['method'];
			$product_Detail['taxes'][$tax_count]['regions']		= $tax_details[$tax_count]['regions'];
			$product_Detail['taxes'][$tax_count]['compoundon']	= $tax_details[$tax_count]['compoundon'];
			$product_Detail['taxes'][$tax_count]['regionsList']	= $regionsList[$tax_details[$tax_count]['taxid']];
		}
		
		$product_final["pro"][] = $product_Detail;
	}
	$finalDiscount = 0;
	

	$subTotal = ($focus->column_fields['hdnSubTotal'] != '')?$focus->column_fields['hdnSubTotal']:0;
	$subTotal = number_format($subTotal, $no_of_decimal_places,'.','');

	
	$discountPercent = ($focus->column_fields['hdnDiscountPercent'] != '')?$focus->column_fields['hdnDiscountPercent']:0;
	$discountAmount = ($focus->column_fields['hdnDiscountAmount'] != '')?$focus->column_fields['hdnDiscountAmount']:0;
    if($discountPercent != '0'){
       
    }

	//To avoid NaN javascript error, here we assign 0 initially to' %of price' and 'Direct Price reduction'(For Final Discount)
	$discount_amount_final = 0;
	$discount_amount_final = number_format($discount_amount_final, $no_of_decimal_places,'.','');
  

	$hdnDiscountPercent = (float) $focus->column_fields['hdnDiscountPercent'];
	$hdnDiscountAmount	= (float) $focus->column_fields['hdnDiscountAmount'];

	if(!empty($hdnDiscountPercent)) {
		$finalDiscount = ($subTotal*$discountPercent/100);
		
	} elseif(!empty($hdnDiscountAmount)) {
		$finalDiscount = $focus->column_fields['hdnDiscountAmount'];
		
	}
	$finalDiscount = number_format($finalDiscount, $no_of_decimal_places,'.','');

	//To set the Final Tax values
	//we will get all taxes. if individual then show the product related taxes only else show all taxes
	//suppose user want to change individual to group or vice versa in edit time the we have to show all taxes. so that here we will store all the taxes and based on need we will show the corresponding taxes

	//First we should get all available taxes and then retrieve the corresponding tax values
	$tax_details = getAllTaxes('available','','edit',$focus->id);
	$taxDetails = array();

	for($tax_count=0;$tax_count<count($tax_details);$tax_count++)
	{
		if ($tax_details[$tax_count]['method'] === 'Deducted') {
			continue;
		}

		$tax_name = $tax_details[$tax_count]['taxname'];
		$tax_label = $tax_details[$tax_count]['taxlabel'];

		//if taxtype is individual and want to change to group during edit time then we have to show the all available taxes and their default values
		//Also taxtype is group and want to change to individual during edit time then we have to provide the asspciated taxes and their default tax values for individual products
		if($taxtype == 'group')
			$tax_percent = $adb->query_result($result,0,$tax_name);
		else
			$tax_percent = $tax_details[$tax_count]['percentage'];//$adb->query_result($result,0,$tax_name);

		if($tax_percent == '' || $tax_percent == 'NULL')
			$tax_percent = 0;
		$taxamount = ($subTotal-$finalDiscount)*$tax_percent/100;
        list($before_dot, $after_dot) = explode('.', $taxamount);
        if($after_dot[$no_of_decimal_places] == 5) {
            $taxamount = round($taxamount, $no_of_decimal_places, PHP_ROUND_HALF_DOWN); 
        } else {
            $taxamount = number_format($taxamount, $no_of_decimal_places,'.','');
        }

		$taxId = $tax_details[$tax_count]['taxid'];
		$taxDetails[$taxId]['taxname']		= $tax_name;
		$taxDetails[$taxId]['taxlabel']		= $tax_label;
		$taxDetails[$taxId]['percentage']	= $tax_percent;
		$taxDetails[$taxId]['amount']		= $taxamount;
		$taxDetails[$taxId]['taxid']		= $taxId;
		$taxDetails[$taxId]['type']			= $tax_details[$tax_count]['type'];
		$taxDetails[$taxId]['method']		= $tax_details[$tax_count]['method'];
		$taxDetails[$taxId]['regions']		= Zend_Json::decode(html_entity_decode($tax_details[$tax_count]['regions']));
		$taxDetails[$taxId]['compoundon']	= Zend_Json::decode(html_entity_decode($tax_details[$tax_count]['compoundon']));
	}

	$compoundTaxesInfo = getCompoundTaxesInfoForInventoryRecord($focus->id, $module);
	//Calculating compound info
	$taxTotal = 0;
	foreach ($taxDetails as $taxId => $taxInfo) {
		$compoundOn = $taxInfo['compoundon'];
		if ($compoundOn) {
			$existingCompounds = $compoundTaxesInfo[$taxId];
			if (!is_array($existingCompounds)) {
				$existingCompounds = array();
			}
			$compoundOn = array_unique(array_merge($existingCompounds, $compoundOn));
			$taxDetails[$taxId]['compoundon'] = $compoundOn;

			$amount = $subTotal-$finalDiscount;
			foreach ($compoundOn as $id) {
				$amount = (float)$amount + (float)$taxDetails[$id]['amount'];
			}
			$taxAmount = ((float)$amount * (float)$taxInfo['percentage']) / 100;
			list($beforeDot, $afterDot) = explode('.', $taxAmount);

			if ($afterDot[$no_of_decimal_places] == 5) {
				$taxAmount = round($taxAmount, $no_of_decimal_places, PHP_ROUND_HALF_DOWN);
			} else {
				$taxAmount = number_format($taxAmount, $no_of_decimal_places, '.', '');
			}

			$taxDetails[$taxId]['amount'] = $taxAmount;
		}
		$taxTotal = $taxTotal + $taxDetails[$taxId]['amount'];
	}
	

	//To set the Shipping & Handling charge
	$shCharge = ($focus->column_fields['hdnS_H_Amount'] != '')?$focus->column_fields['hdnS_H_Amount']:0;
	$shCharge = number_format($shCharge, $no_of_decimal_places,'.','');


	//To set the Shipping & Handling tax values
	//calculate S&H tax
	$shtaxtotal = 0;
	//First we should get all available taxes and then retrieve the corresponding tax values
	$shtax_details = getAllTaxes('available','sh','edit',$focus->id);

	//if taxtype is group then the tax should be same for all products in vtiger_inventoryproductrel table
	for($shtax_count=0;$shtax_count<count($shtax_details);$shtax_count++)
	{
		$shtax_name = $shtax_details[$shtax_count]['taxname'];
		$shtax_label = $shtax_details[$shtax_count]['taxlabel'];
		$shtax_percent = 0;
		//if condition is added to call this function when we create PO/SO/Quotes/Invoice from Product module
		if (in_array($module, $inventoryModules)) {
			$shtax_percent = getInventorySHTaxPercent($focus->id,$shtax_name);
		}
		$shtaxamount = $shCharge*$shtax_percent/100;
		$shtaxtotal = $shtaxtotal + $shtaxamount;
		
	}
	$shtaxtotal = number_format($shtaxtotal, $no_of_decimal_places,'.','');
	
	//To set the Adjustment value
	$adjustment = ($focus->column_fields['txtAdjustment'] != '')?$focus->column_fields['txtAdjustment']:0;
	$adjustment = number_format($adjustment, $no_of_decimal_places,'.','');


	//To set the grand total
	$grandTotal = ($focus->column_fields['hdnGrandTotal'] != '')?$focus->column_fields['hdnGrandTotal']:0;
	$grandTotal = number_format($grandTotal, $no_of_decimal_places,'.','');
	if ($productIdsList) {
			$imageDetailsList = Products_Record_Model::getProductsImageDetails($productIdsList);

			foreach($productsCount as $p=>$info) {
				$productId = $info['hdnProductId'];
				$imageDetails = $imageDetailsList[$productId];
				if ($imageDetails) {
					//public.php?fid=22&key=kutch.jpg
					$product_Detail['productImage'] = 'public.php?fid='.$imageDetails[0]['path'].'&key='.$imageDetails[0]['orgname'];
				}
			}
		}
	$log->debug("Exiting getAssociatedProducts method ...");

	return $product_final;

}



?>