Extending the Basics
----
This module provides support for basic page types in SilverStripe, aka the default Page class in a raw installer. This documentation will help you get to the point were you have custom page types as well as support for more advanced extensions such as non-page data objects. It assumes you understand how to use the SilverStripe [extensions api](http://docs.silverstripe.org/en/developer_guides/extending/extensions/).

### Conversion History
If you are using a custom ``KapostObject`` extension and you want version history you will need to create your own history record see the [custom types documentation](custom-types.md#for-custom-objects) for more information.


### Available Extension Points
#### KapostService
 - ``newPost($blog_id, array $content, int $publish, bool $isPreview)`` Allows for overriding of the metaWeblog.newPost handling & response. What is returned to Kapost is the first non-empty result from extensions. This should return the post's id (for example KapostPage_10) on success, throw an error response or return null. See the [custom types documentation](custom-types.md) for more information. Note you should set the KapostObject->IsPreview based on the $isPreview parameter.
 - ``updateNewKapostPage(KapostObject $obj, $blog_id, array $content, int $publish, bool $isPreview)`` Allows for setting of custom page extension fields based on the data from Kapost. Note that *you must call write* on the KapostObject to save your changes.
 - ``editPost($content_id, array $content, int $publish, bool $isPreview)`` Allows for overriding of the metaWeblog.editPost handling & response. What is returned to Kapost is the first non-empty result from extensions. This should return true on success, throw an error response or return null. Note you should set the KapostObject->IsPreview based on the $isPreview parameter.
 - ``updateEditKapostPage(KapostObject $kapostObj, $content_id, array $content, int $publish, bool $isPreview)`` Allows for setting of custom page extension fields based on the data from Kapost. Note that *you must call write* on the KapostObject to save your changes.
 - ``getPost($content_id)`` Allows for overriding the metaWeblog.getPost handling & response to Kapost about the requested content. This can be used for sending information about non-page extension Kapost objects it should return an array similar to what is [defined here](https://gist.github.com/icebreaker/546f4223dc07a9e2e6e9#metawebloggetpost). What is returned to Kapost is the first non-empty result from extensions.
 - ``updatePageMeta(Page $page)`` Allows for modification of the page meta data to be added to the response to Kapost. This should return a map of field's to it's content.
 - ``updateObjectMeta(KapostObject $object)`` Allows for modification of the KapostObject's meta data to be added to the response to Kapost. This should return a map of field's to it's content.
 - ``getCategories($blog_id)`` Allows for adding in of additional categories to be sent to Kapost, this should return an array of categories similar to what is [defined here](https://gist.github.com/icebreaker/546f4223dc07a9e2e6e9#metawebloggetcategories).
 - ``updateNewMediaAsset($blog_id, array $content, File $mediaFile)`` Allows for modification of the File object that represents the media asset from Kapost.
 - ``getPreview($blog_id, $content, $content_id)`` Allows for overriding of the Kapost.getPreview handling & response. What is returned to Kapost is the first non-empty result from extensions. This should return an array containing the url to the preview and the id of the post (same as newPost) on success, throw an error response or return null.
 - ``updatePreviewDisplay(KapostObject $obj, ViewableData $controller)`` Allows you to update the display of the preview, this is useful if you want to add in your own requirements specific to the preview. It also allows you to add in any requirements required in the case of a Page's controller since Page_Controller::init() is not called. The controller argument is the controller that will be used for previewing the object this could be a ViewableData_Customised which is what it is when you are using KapostPage's default renderPreview().

#### KapostObject
 - ``updateCMSFields(FieldList $fields)`` Allows extensions to add cms fields to KapostObject and it's decedents.

#### KapostGridFieldDetailForm_ItemRequest
 - ``updateConvertObjectForm(Form $form, KapostObject $source)`` Allows extensions to adjust the form in the Convert Object lightbox.
 - ``doConvert{conversion_mode}(KapostObject $source, array $data, Form $form)`` Allows extensions to provide handing of conversions of custom KapostObject extensions. ``conversion_mode`` is replaced by the mode in the request, you must explicitly allow this mode in the KapostAdmin.extra_conversion_modes configuration option. This extension point should return the cms relative url to edit the final object (ex. admin/pages/edit/show/1) and it is handled on a first returned basis. When the lightbox closes the user will be directed to this url in the cms.
 - ``updateNewPageConversion(Page $destination, KapostObject $source, array $data, Form $form)`` Allows extensions to alter the destination page when creating a new page from the Kapost Object. Note that you *must call write on the page* to save your changes.
 - ``updateReplacePageConversion(Page $destination, KapostObject $source, array $data, Form $form)`` Allows extensions to alter the destination page when replacing a new page with the Kapost Object. Note that you *must call write on the page* to save your changes.

#### KapostPage
 - ``updatePreviewFieldMap`` Allows for adding fields to the customise call before rendering. You could use this to solve dependency issues where methods do not exist or are not available on your KapostPage instance. You should return the fields to be merged with the default fields
