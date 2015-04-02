<?php
class KapostObject extends DataObject {
    private static $db=array(
                            'Title'=>'Varchar(255)',
                            'Content'=>'HTMLText',
                            'KapostChangeType'=>"Enum(array('new', 'edit'), 'new')",
                            'KapostRefID'=>'Varchar(255)',
                            'ToPublish'=>'Boolean'
                         );
    
    private static $default_sort='Created';
    
    private static $summary_fields=array(
                                        'Title',
                                        'Created',
                                        'ClassName',
                                        'KapostChangeType',
                                        'ToPublish'
                                    );
    
    /**
     * Prevent creation of the KapostObjects, they are delivered from Kapost
     * @param {int|Member} $member Member ID or member instance
     * @return {bool} Returns boolean false
     */
    final public function canCreate($member=null) {
        return false;
    }
    
    /**
     * Prevent editing of the KapostObjects, they are delivered from Kapost
     * @param {int|Member} $member Member ID or member instance
     * @return {bool} Returns boolean false
     */
    final public function canEdit($member=null) {
        return false;
    }
    
    
    /**
     * Gets fields used in the cms
     * @return {FieldList} Fields to be used
     */
    public function getCMSFields() {
        $fields=new FieldList(
                            new TabSet('Root',
                                    new Tab('Main', _t('KapostObject.MAIN', '_Main'),
                                        new ReadonlyField('Created', $this->fieldLabel('Created')),
                                        new ReadonlyField('KapostChangeTypeNice', $this->fieldLabel('KapostChangeType')),
                                        new ReadonlyField('ToPublishNice', $this->fieldLabel('ToPublish')),
                                        new ReadonlyField('ClassNameNice', $this->fieldLabel('ClassName')),
                                        new ReadonlyField('Title', $this->fieldLabel('Title')),
                                        HtmlEditorField_Readonly::create('ContentNice', $this->fieldLabel('Content'), $this->sanitizeHTML($this->Content))
                                    )
                                )
                        );
        
        
        //Allow extensions to adjust the fields
        $this->extend('updateCMSFields', $fields);
        
        return $fields;
    }
    
    /**
     * Gets the change type's friendly label
     * @return {string} Returns new or edit
     */
    public function getKapostChangeTypeNice() {
        switch($this->KapostChangeType) {
            case 'new': return _t('KapostFieldCaster.CHANGE_TYPE_NEW', '_New');
            case 'edit': return _t('KapostFieldCaster.CHANGE_TYPE_EDIT', '_Edit');
        }
    
        return $this->KapostChangeType;
    }
    
    /**
     * Gets the publish type's friendly label
     * @return {string} Returns live or draft
     */
    public function getToPublishNice() {
        if($this->ToPublish==true) {
            return _t('KapostFieldCaster.PUBLISH_TYPE_LIVE', '_Live');
        }
    
        return _t('KapostFieldCaster.PUBLISH_TYPE_DRAFT', '_Draft');
    }
    
    /**
     * Wrapper for the object's i18n_singular_name()
     * @return {string} Non-XML ready result of i18n_singular_name or the raw value
     */
    public function getClassNameNice() {
        return $this->i18n_singular_name();
    }
    
    /**
     * Gets the destination class when converting to the final object, by default this simply removes Kapost form the class name
     * @return {string} Class to convert to
     */
    public function getDestinationClass() {
        return preg_replace('/^Kapost/', '', $this->ClassName);
    }
    
    /**
     * Strips out not allowed tags, mainly this is to remove the kapost beacon script so it doesn't conflict with the cms
     * @param {string} $str String to be sanitized
     * @return {string} HTML to be used
     */
    private function sanitizeHTML($str) {
        $htmlValue=Injector::inst()->create('HTMLValue', $str);
        $santiser=Injector::inst()->create('HtmlEditorSanitiser', HtmlEditorConfig::get_active());
        $santiser->sanitise($htmlValue);
        
        return $htmlValue->getContent();
    }
}
?>