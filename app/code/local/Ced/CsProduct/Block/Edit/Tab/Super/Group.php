<?php 

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced
 * @package     Ced_CsProduct
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product in category grid
 *
 * @category   Ced
 * @package    Ced_CsProduct
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */

class Ced_CsProduct_Block_Edit_Tab_Super_Group extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
   
	public function __construct()
	{
		parent::__construct();
		$this->setId('super_product_grid');
		$this->setTemplate('csmarketplace/widget/grid.phtml');
		$this->setDefaultSort('entity_id');
		$this->setSkipGenerateContent(true);
		$this->setUseAjax(true);
		if ($this->_getProduct()->getId()) {
			$this->setDefaultFilter(array('in_products'=>1));
		}
	}
	
	public function getTabUrl()
	{
		return $this->getUrl('*/*/superGroup', array('_current'=>true));
	}
	
	public function getTabClass()
	{
		return 'ajax';
	}
	
	/**
	 * Retrieve currently edited product model
	 *
	 * @return Mage_Catalog_Model_Product
	 */
	protected function _getProduct()
	{
		return Mage::registry('current_product');
	}
	
	protected function _addColumnFilterToCollection($column)
	{
		// Set custom filter for in product flag
		if ($column->getId() == 'in_products') {
			$productIds = $this->_getSelectedProducts();
			if (empty($productIds)) {
				$productIds = 0;
			}
			if ($column->getFilter()->getValue()) {
				$this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
			}
			else {
				$this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
			}
		}
		else {
			parent::_addColumnFilterToCollection($column);
		}
	
		return $this;
	}
	
	protected function _prepareCollection()
	{
		$allowProductTypes = array();
		foreach (Mage::getConfig()->getNode('global/catalog/product/type/grouped/allow_product_types')->children() as $type) {
			$allowProductTypes[] = $type->getName();
		}
		
		$vproducts=Mage::getModel('csmarketplace/vproducts')->getVendorProductIds();
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		$collection = Mage::getModel('catalog/product_link')->useGroupedLinks()
		->getProductCollection()
		->setProduct($this->_getProduct())
		->addAttributeToSelect('*')
		->addFilterByRequiredOptions()
		->addAttributeToFilter('type_id', $allowProductTypes)
		->addAttributeToFilter('entity_id',array('in'=>$vproducts));
	
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	protected function _prepareColumns()
	{
		$this->addColumn('in_products', array(
				'header_css_class' => 'a-center',
				'type'      => 'checkbox',
				'name'      => 'in_products',
				'values'    => $this->_getSelectedProducts(),
				'align'     => 'center',
				'index'     => 'entity_id'
		));
	
		$this->addColumn('entity_id', array(
				'header'    => Mage::helper('catalog')->__('ID'),
				'sortable'  => true,
				'width'     => '60px',
				'index'     => 'entity_id'
		));
		$this->addColumn('name', array(
				'header'    => Mage::helper('catalog')->__('Name'),
				'index'     => 'name'
		));
		$this->addColumn('sku', array(
				'header'    => Mage::helper('catalog')->__('SKU'),
				'width'     => '80px',
				'index'     => 'sku'
		));
		$this->addColumn('price', array(
				'header'    => Mage::helper('catalog')->__('Price'),
				'type'      => 'currency',
				'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
				'index'     => 'price'
		));
	
		$this->addColumn('qty', array(
				'header'    => Mage::helper('catalog')->__('Default Qty'),
				'name'      => 'qty',
				'type'      => 'number',
				'validate_class' => 'validate-number',
				'index'     => 'qty',
				'width'     => '1',
				'editable'  => true
		));
	
		$this->addColumn('position', array(
				'header'    => Mage::helper('catalog')->__('Position'),
				'name'      => 'position',
				'type'      => 'number',
				'validate_class' => 'validate-number',
				'index'     => 'position',
				'width'     => '1',
				'editable'  => true,
				'edit_only' => !$this->_getProduct()->getId()
		));
	
		return parent::_prepareColumns();
	}
	
	public function getGridUrl()
	{
		return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/superGroupGridOnly', array('_current'=>true));
	}
	
	/**
	 * Retrieve selected grouped products
	 *
	 * @return array
	 */
	protected function _getSelectedProducts()
	{
		$products = $this->getProductsGrouped();
		if (!is_array($products)) {
			$products = array_keys($this->getSelectedGroupedProducts());
		}
		return $products;
	}
	
	/**
	 * Retrieve grouped products
	 *
	 * @return array
	 */
	public function getSelectedGroupedProducts()
	{
		$associatedProducts = Mage::registry('current_product')->getTypeInstance(true)
		->getAssociatedProducts(Mage::registry('current_product'));
		$products = array();
		foreach ($associatedProducts as $product) {
			$products[$product->getId()] = array(
					'qty'       => $product->getQty(),
					'position'  => $product->getPosition()
			);
		}
		return $products;
	}
	
	public function getTabLabel()
	{
		return Mage::helper('catalog')->__('Associated Products');
	}
	public function getTabTitle()
	{
		return Mage::helper('catalog')->__('Associated Products');
	}
	public function canShowTab()
	{
		return true;
	}
	public function isHidden()
	{
		return false;
	}
   
}