<?php 
/**
 * Ultimate_ModuleCreator extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category   	Ultimate
 * @package		Ultimate_ModuleCreator
 * @copyright  	Copyright (c) 2012
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */ 
/**
 * entity model
 * 
 * @category	Ultimate
 * @package		Ultimate_ModuleCreator
 * @author 		Marius Strajeru <marius.strajeru@gmail.com>
 */ 
class Ultimate_ModuleCreator_Model_Entity extends Ultimate_ModuleCreator_Model_Abstract{
	/**
	 * entity attributes
	 * @var array
	 */
	protected $_attribtues 			= array();
	/**
	 * entity module
	 * @var Ultimate_ModuleCreator_Model_Module
	 */
	protected $_module 				= null;
	/**
	 * attribute that behaves as name
	 * @var Ultimate_ModuleCreator_Model_Attribute
	 */
	protected $_nameAttribute 		= null;
	/**
	 * remember if attributes were prepared
	 * @var bool
	 */
	protected $_preparedAttributes 	= null;
	/**
	 * set the entity module
	 * @access public
	 * @param Ultimate_ModuleCreator_Model_Module $module
	 * @return Ultimate_ModuleCreator_Model_Entity
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function setModule(Ultimate_ModuleCreator_Model_Module $module){
		$this->_module = $module;
		return $this;
	}
	/**
	 * get the entity module
	 * @access public
	 * @return mixed (Ultimate_ModuleCreator_Model_Module|null)
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getModule(){
		return $this->_module;
	}
	/**
	 * add new attribute
	 * @access public
	 * @param Ultimate_ModuleCreator_Model_Attribute $attribute
	 * @return Ultimate_ModuleCreator_Model_Entity
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function addAttribute(Ultimate_ModuleCreator_Model_Attribute $attribute){
		$attribute->setEntity($this);
		if (isset($this->_attribtues[$attribute->getCode()])){
			throw new Ultimate_ModuleCreator_Exception(Mage::helper('modulecreator')->__('An attribute with the code "%s" already exists for entity "%s"', $attribute->getCode(), $this->getNameSingular()));
		}
		$this->_preparedAttributes = false;
		$this->_attribtues[$attribute->getCode()] = $attribute;
		if ($attribute->getIsName()){
			if ($attribute->getType() != 'text'){
				throw new Ultimate_ModuleCreator_Exception(Mage::helper('modulecreator')->__('An attribute that acts as name must have the type "Text".'));
			}
			$this->_nameAttribute = $attribute;
		}
		return $this;
	}
	/**
	 * prepare attributes 
	 * @access protected
	 * @return Ultimate_ModuleCreator_Model_Entity
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	protected function _prepareAttributes(){
		if ($this->_preparedAttributes){
			return $this;
		}
		$attributesByPosition = array();
		foreach ($this->_attribtues as $key=>$attribute){
			$attributesByPosition[$attribute->getPosition()][] = $attribute;
		}
		ksort($attributesByPosition);
		$attributes = array();
		foreach ($attributesByPosition as $position=>$attributeList){
			foreach ($attributeList as $attribute){
				$attributes[$attribute->getCode()] = $attribute;
			}
		}
		$this->_attribtues = $attributes;
		$this->_preparedAttributes = true;
		return $this;
	}
	/**
	 * ge the entity attribtues
	 * @access public
	 * @return array()
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getAttributes(){
		if (!$this->_preparedAttributes){
			$this->_prepareAttributes();
		}
		return $this->_attribtues;
	}
	/**
	 * entity to xml
	 * @access protected
	 * @param array $arrAttributes
	 * @param string $rootName
	 * @param bool $addOpenTag
	 * @param bool $addCdata
	 * @return string
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	protected function __toXml(array $arrAttributes = array(), $rootName = 'entity', $addOpenTag=false, $addCdata=false){
		$xml = '';
		if ($addOpenTag) {
			$xml.= '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		}
		if (!empty($rootName)) {
			$xml.= '<'.$rootName.'>'."\n";
		}
		$start = '';
		$end = '';
		if ($addCdata){
			$start = '<![CDATA[';
			$end = ']]>';
		}
		$xml .= parent::__toXml($this->getXmlAttributes(), '', false, $addCdata);
		$xml .= '<attributes>';
		foreach ($this->getAttributes() as $attribute){
			$xml .= $attribute->toXml(array(), 'attribute', false, $addCdata);
		}
		$xml .= '</attributes>';
		if (!empty($rootName)) {
			$xml.= '</'.$rootName.'>'."\n";
		}
		return $xml;
	}
	/**
	 * get the attributes saved in the xml
	 * @access public
	 * @return array();
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getXmlAttributes(){
		return array('label_singular', 'label_plural', 'name_singular', 'name_plural', 'created_to_grid', 
					'updated_to_grid', 'add_status', 'use_frontend', 'frontend_list', 
					'frontend_list_template', 'frontend_view', 'frontend_view_template', 'frontend_add_seo',
					'rss', 'widget', 'link_product', 'show_on_product', 'show_products'
		);
	}
	/**
	 * get the placeholders for an entity
	 * @access public
	 * @return array
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getPlaceholders(){
		$placeholders = array();
		$placeholders['{{EntityLabel}}'] 		= ucfirst($this->getLabelSingular());
		$placeholders['{{entityLabel}}'] 		= strtolower($this->getLabelSingular());
		$placeholders['{{EntitiesLabel}}'] 		= ucfirst($this->getLabelPlural());
		$placeholders['{{entitiesLabel}}'] 		= strtolower($this->getLabelPlural());
		$placeholders['{{entity}}'] 			= strtolower($this->getNameSingular());
		$placeholders['{{Entity}}'] 			= ucfirst($this->getNameSingular());
		$placeholders['{{ENTITY}}'] 			= strtoupper($this->getNameSingular());
		$placeholders['{{Entities}}'] 			= ucfirst($this->getNamePlural());
		$placeholders['{{entities}}'] 			= $this->getNamePlural();
		$placeholders['{{listLayout}}'] 		= $this->getFrontendListTemplate();
		$placeholders['{{viewLayout}}'] 		= $this->getFrontendViewTemplate();
		$nameAttribute 							= $this->getNameAttribute();
		$placeholders['{{EntityNameMagicCode}}']= $this->getNameAttributeMagicCode();
		$placeholders['{{nameAttribute}}'] 		= $nameAttribute->getCode();
		$placeholders['{{nameAttributeLabel}}'] = $nameAttribute->getLabel();
		$placeholders['{{firstImageField}}']	= $this->getFirstImageField();
		$placeholders['{{attributeSql}}']		= $this->getAttributesSql();
		$placeholders['{{menu_sort}}']			= $this->getPosition();
		$placeholders['{{defaults}}']			= $this->getConfigDefaults();
		$placeholders['{{systemAttributes}}']	= $this->getSystemAttributes();
		$placeholders['{{EntityListItem}}']		= $this->getListItemHtml();
		$placeholders['{{EntityViewAttributes}}']= $this->getViewAttributesHtml();
		$placeholders['{{EntityViewWidgetAttributes}}'] = $this->getViewWidgetAttributesHtml();
		$placeholders['{{EntityViewRelationLayout}}'] = $this->getRelationLayoutXml();
		return $placeholders;
		
	}
	/**
	 * get magic function code for the name attribute
	 * @access public
	 * @return string
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getNameAttributeMagicCode(){
		$nameAttribute = $this->getNameAttribute();
		if ($nameAttribute){
			$entityNameMagicCode = $nameAttribute->getMagicMethodCode();
		}
		else{
			$entityNameMagicCode = 'Name';
		}
		return $entityNameMagicCode;
	}
	/**
	 * get the name attribute
	 * @access public
	 * @return mixed(null|Ultimate_ModuleCreator_Model_Attribute)
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getNameAttribute(){
		return $this->_nameAttribute;
	}
	/**
	 * check if the entity has file attributes
	 * @access public
	 * @return bool
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getHasFile(){
		foreach ($this->getAttributes() as $attribute){
			if ($attribute->getType() == 'file'){
				return true;
			}
		}
		return false;
	}
	/**
	 * check if the entity has image attributes
	 * @access public
	 * @return bool
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getHasImage(){
		foreach ($this->getAttributes() as $attribute){
			if ($attribute->getType() == 'image'){
				return true;
			}
		}
		return false;
	}
	/**
	 * check if the entity has upload attributes
	 * @access public
	 * @return bool
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getHasUpload(){
		return $this->getHasFile() || $this->getHasImage();
	}
	/**
	 * get the first image attribute code
	 * @access public
	 * @return string
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getFirstImageField(){
		foreach ($this->getAttributes() as $attribute){
			if ($attribute->getType() == 'image'){
				return $attribute->getCode();
			}
		}
		return '';
	}
	/**
	 * get the sql for attributes
	 * @access public
	 * @return string
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getAttributesSql(){
		$padding = "\t\t";
		$content = '';
		foreach ($this->getAttributes() as $attribute){
			$content .= $padding.$attribute->getSqlColumn()."\n";
		}
		if($this->getAddStatus()){
			$attr = Mage::getModel('modulecreator/attribute');
			$attr->setCode('status');
			$attr->setLabel('Status');
			$attr->setType('yesno');
			$content .= $padding.$attr->getSqlColumn()."\n";
		}
		if($this->getRss()){
			$attr = Mage::getModel('modulecreator/attribute');
			$attr->setCode('in_rss');
			$attr->setLabel('In RSS');
			$attr->setType('yesno');
			$content .= $padding.$attr->getSqlColumn()."\n";
		}
		if ($this->getFrontendAddSeo()){
			$attr = Mage::getModel('modulecreator/attribute');
			$attr->setCode('meta_title');
			$attr->setLabel('Meta title');
			$attr->setType('text');
			$content .= $padding.$attr->getSqlColumn()."\n";
			
			$attr = Mage::getModel('modulecreator/attribute');
			$attr->setCode('meta_keywords');
			$attr->setLabel('Meta keywords');
			$attr->setType('textarea');
			$content .= $padding.$attr->getSqlColumn()."\n";
			
			$attr = Mage::getModel('modulecreator/attribute');
			$attr->setCode('meta_description');
			$attr->setLabel('Meta description');
			$attr->setType('textarea');
			$content .= $padding.$attr->getSqlColumn()."\n";
		}
		return substr($content,0, strlen($content) - strlen("\n"));
	}
	/**
	 * get the default settings for config
	 * @access public
	 * @return string
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getConfigDefaults(){
		$content = '';
		$padding = str_repeat("\t", 4);
		if ($this->getRss()){
			$content .= $padding.'<rss>1</rss>'."\n"; 
		}
		if ($this->getFrontendAddSeo() && $this->getFrontendList()){
			$content .= $padding.'<meta_title>'.ucfirst($this->getLabelPlural()).'</meta_title>'."\n";
		}
		return substr($content,0, strlen($content) - strlen("\n"));
	}
	/**
	 * get the system attributes
	 * @access public
	 * @return string
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getSystemAttributes(){
		$position = 20;
		$content = '';
		$tab = "\t";
		$padding = str_repeat($tab, 6);
		if ($this->getRss()){
			$content .= $padding.'<rss translate="label" module="'.strtolower($this->getModule()->getModuleName()).'">'."\n";
			$content .= $padding.$tab.'<label>Enable rss</label>'."\n";
			$content .= $padding.$tab.'<frontend_type>select</frontend_type>'."\n";
			$content .= $padding.$tab.'<source_model>adminhtml/system_config_source_yesno</source_model>'."\n";
			$content .= $padding.$tab.'<sort_order>'.$position.'</sort_order>'."\n";
			$content .= $padding.$tab.'<show_in_default>1</show_in_default>'."\n";
			$content .= $padding.$tab.'<show_in_website>1</show_in_website>'."\n";
			$content .= $padding.$tab.'<show_in_store>1</show_in_store>'."\n";
			$content .= $padding.'</rss>'."\n";
			$position += 10;
		}
		if ($this->getFrontendAddSeo() && $this->getFrontendList()){
			$content .= $padding.'<meta_title translate="label" module="'.strtolower($this->getModule()->getModuleName()).'">'."\n";
			$content .= $padding.$tab.'<label>Meta title for '.strtolower($this->getLabelPlural()).' list page</label>'."\n";
			$content .= $padding.$tab.'<frontend_type>text</frontend_type>'."\n";
			$content .= $padding.$tab.'<sort_order>'.$position.'</sort_order>'."\n";
			$content .= $padding.$tab.'<show_in_default>1</show_in_default>'."\n";
			$content .= $padding.$tab.'<show_in_website>1</show_in_website>'."\n";
			$content .= $padding.$tab.'<show_in_store>1</show_in_store>'."\n";
			$content .= $padding.'</meta_title>'."\n";
			$position += 10;
			
			$content .= $padding.'<meta_description translate="label" module="'.strtolower($this->getModule()->getModuleName()).'">'."\n";
			$content .= $padding.$tab.'<label>Meta description for '.strtolower($this->getLabelPlural()).' list page</label>'."\n";
			$content .= $padding.$tab.'<frontend_type>textarea</frontend_type>'."\n";
			$content .= $padding.$tab.'<sort_order>'.$position.'</sort_order>'."\n";
			$content .= $padding.$tab.'<show_in_default>1</show_in_default>'."\n";
			$content .= $padding.$tab.'<show_in_website>1</show_in_website>'."\n";
			$content .= $padding.$tab.'<show_in_store>1</show_in_store>'."\n";
			$content .= $padding.'</meta_description>'."\n";
			$position += 10;
			
			$content .= $padding.'<meta_keywords translate="label" module="'.strtolower($this->getModule()->getModuleName()).'">'."\n";
			$content .= $padding.$tab.'<label>Meta keywords for '.strtolower($this->getLabelPlural()).' list page</label>'."\n";
			$content .= $padding.$tab.'<frontend_type>textarea</frontend_type>'."\n";
			$content .= $padding.$tab.'<sort_order>'.$position.'</sort_order>'."\n";
			$content .= $padding.$tab.'<show_in_default>1</show_in_default>'."\n";
			$content .= $padding.$tab.'<show_in_website>1</show_in_website>'."\n";
			$content .= $padding.$tab.'<show_in_store>1</show_in_store>'."\n";
			$content .= $padding.'</meta_keywords>'."\n";
		}
		return substr($content,0, strlen($content) - strlen("\n"));
	}
	/**
	 * get the html for list view
	 * @access public
	 * @return string
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getListItemHtml(){
		$tab = "\t";
		$padding = str_repeat($tab, 3);
		$content = '';
		$start = '';
		if ($this->getFrontendView()){
			$content.= $padding.'<a href="<?php echo $'.'_'.$this->getNameSingular().'->get'.ucfirst($this->getNameSingular()).'Url();?>" title="<?php echo $this->htmlEscape($_'.$this->getNameSingular().'->get'.$this->getNameAttributeMagicCode().'()) ?>">'."\n";
			$start = $tab;
		}
		$content .= $padding.$start.'<?php echo $_'.$this->getNameSingular().'->get'.$this->getNameAttributeMagicCode().'(); ?>'. "\n";
		if ($this->getFrontendView()){
			$content.= $padding.'</a>'."\n";
		}
		return $content;
	}
	/**
	 * get the html for attributes in view page
	 * @access public
	 * @return string
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getViewAttributesHtml(){
		$content = '';
		$padding = "\t";
		$tab = "\t";
		foreach ($this->getAttributes() as $attribute){
			if ($attribute->getFrontend()){
				$content .= $padding.$attribute->getFrontendHtml();
			}
		}
		return $content;
	}
	/**
	 * get the html for attributes for the view widget
	 * @access public
	 * @return string
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getViewWidgetAttributesHtml(){
		$content = '';
		$padding = "\t\t\t";
		$tab = "\t";
		foreach ($this->getAttributes() as $attribute){
			if ($attribute->getWidget()){
				$content .= $padding.$attribute->getFrontendHtml();
			}
		}
		return $content;
	}
	/**
	 * get the attribute name for plural
	 * @access public
	 * @return string
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getNamePlural(){
		$plural = $this->getData('name_plural');
		if ($plural == $this->getNameSingular()){
			if ($plural == ""){
				return "";
			}
			$plural = $this->getNameSingular().'s';
		}
		return $plural;
	}
	/**
	 * check if frontend list files must be created
	 * @access public
	 * @return bool
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getFrontendList(){
		return $this->getUseFrontend() && $this->getData('frontend_list');
	}
	/**
	 * check if frontend view files must be created
	 * @access public
	 * @return bool
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getFrontendView(){
		return $this->getUseFrontend() && $this->getData('frontend_view');
	}
	/**
	 * check if widget list files must be created
	 * @access public
	 * @return bool
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getWidget(){
		return $this->getUseFrontend() && $this->getData('widget');
	}
	/**
	 * check if SEO attributes should be added
	 * @access public
	 * @return bool
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getFrontendAddSeo(){
		return $this->getUseFrontend() && $this->getData('frontend_add_seo');
	}
	/**
	 * check if SEO attributes should be added
	 * @access public
	 * @return bool
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getRss(){
		return $this->getUseFrontend() && $this->getData('rss');
	}
	/**
	 * check if products are listed in the entity view page
	 * @access public
	 * @return bool
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getShowProducts(){
		return $this->getLinkProduct() && $this->getData('show_products');
	}
	/**
	 * get layout xml for relation to product
	 * @access public
	 * @return string
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getRelationLayoutXml(){
		if ($this->getShowProducts()){
			return "\t\t\t".'<block type="'.strtolower($this->getModule()->getModuleName()).'/'.strtolower($this->getNameSingular()).'_catalog_product_list" name="'.strtolower($this->getNameSingular()).'.info.products" as="'.strtolower($this->getNameSingular()).'_products" template="'.strtolower($this->getModule()->getNamespace()).'_'.strtolower($this->getModule()->getModuleName()).'/'.strtolower($this->getNameSingular()).'/catalog/product/list.phtml" />'."\n\t\t";
		}
		else{
			return "\t\t";
		}
	}
	/**
	 * check if entity list is shown on product page
	 * @access public
	 * @return bool
	 * @author Marius Strajeru <marius.strajeru@gmail.com>
	 */
	public function getShowOnProduct(){
		return $this->getLinkProduct() && $this->getData('show_on_product');
	}
}