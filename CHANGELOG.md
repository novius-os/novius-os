# Novius OS framework CHANGELOG

## July 30, 2014: version 5.0.1 (Elche)

* Bugfix: In require JS config paths, if use assets minified and a path not initialized in paths-min
* Bugfix: In ``Tools_Wysiwyg::parse_medias()``, if a img is wrapped in an anchor
* Bugfix: In template variation, buttons visualise and duplicate have disappeared
* Bugfix: In nosOnShow, sub element explicitly hidden are displayed
* Bugfix: Inspector model didn't work when a context key is defined in the common config of the model
* Bugfix: Regression for nosAjaxForm that wasn't trigger with custom layout (no fieldset)
* Bugfix: Fix for plural i18n
* Bugfix: Remove "create_from_id" from saved datas

### Form:
* Bugfix: In mail view, when several fields have the same label...

### Template Bootstrap:
* Bugfix: Correcting inline style & bootstrap structure
* Bugfix: Correcting css bug in the navbar

### Wijmo:
* Bugfix: Wijpager widjet don't work with the list grid widget : regression from Wijmo.

## July 08, 2014: version 5 (Elche)

### New features:

* New **Menu application** to manage website menu.
* New **Template variation application** to manage template variations by context.
* New **Bootstrap customisable template application** which use power of template variation. You can customize elements, layout, skin, menus.
* **UI**: Background image on body and sides always visible
* **UI**: New tray bar with new fullscreen feature
* **UI**: Appdesk and CRUD Toolbar at bottom
* **UI**: On Appstab, launchers now draggable on a grid, not only sortable
* **Renderer**: New renderer ``Renderer_Item_Picker`` based on native appdesk of model
* **Renderer**: New renderer ``Renderer_Select_Model`` to select item of a model. Manage contexable and twinnable behaviour.

### Developer:

* **ORM**: Add Providers feature. Possibility to add providers on model. Providers are accessors to relation's items by a key.
* **ORM**: Wysiwygs and Medias becomes native providers.
* **ORM**: New ``shared_wysiwygs_context`` and ``shared_medias_context`` providers.
* **Enhancer**: Config popup now run form validation if some fields have contraints.
* **Grid**: ``cellFormatters`` on columns work on all ``noslistgrid`` widget, not only on principal grid of appdesk

### Breaking changes:
* All shared Wysiwygs and Medias context must be access with providers shared_wysiwygs_context and shared_medias_context
* Variable shared_wysiwygs_context and shared_medias_context must not be declared in model

### Vendors update:

* jQuery 1.11.1
* jQuery UI 1.10.4
* Wijmo 2014v1.34
* require.js 2.1.11
* pNotify 2.0
* JQuery.cookie 1.4.1
* JQuery.form 3.50.0-2014.0.2.05
* JQuery.mousewheel 3.1.11
* JQuery.datetimepicker 1.4.4
* Webshims 1.13, in Form application,

### Deprecated:
* Methods Nos::parse_wysiwyg(), Nos::parse_enhancers() and Nos::get_enhrancer_content(), replace by Tools_Wysiwyg::parse(), Tools_Wysiwyg::parseEnhancers() and Tools_Enhancer::content()

### New features:

* Twinnable relations now allow to fix a context instead of using the one on the current model.

## July 08, 2014: version 4.3 (Dubrovka)

* Media: In Appdesk, add path to distinguish medias with same name in different folders
* Appdesk: New event for behaviours to alter the grid query configuration
* CSRF token with customisation
* Bugfix: Behaviour URLEnhancer. Method ``url()`` search good url if current url enhancer not allowed to display the item
* Bugfix: ``front.pageFound`` event is triggered twice on generating cache
* Bugfix: ``items`` method of appdesk controller uses ``model::query()`` to retrieve a query instance, and then use before_query mechanism
* Bugfix: Behaviour publishable. Adding alias ``published`` for ``orderby`` in ``before_query`` method
* Bugfix: In media permission, isMediaInRestrictedFolder(), check folder exist before access his property
* Bugfix: on folder delete, children and medias have a folder_id and are not delete in BD
* Bugfix: In CRUD delete popup confirmation button
* Bugfix: In last step of install wizard, wrong jquery url
* Bugfix: in wysiwyg, link to media become externe link
* Bugfix: wysiwyg link popup, the domain is display twice for media url
* Bugfix: Class 'FrontCache' not found in front controller when changing the page cache duration
* Bugfix: event ``front.pageFound`` is trigger twice in some cases with ``suffixHandlers``.
* Bugfix: wysiwyg enhancer preview display lost block container
* Bugfix: addJavascriptInline also check if inserted code begin with ``<script>``
* Bugfix: in front cache ``addSuffixHandler()``, use ``array_unique`` with ``SORT_REGULAR``
* Bugfix: The plural translation function n__() always returned the singular form
* Bugfix: Filter argument for the preview view set to false
* Bugfix: set local in rebuild cache
* Bugfix: Notice in relation twinnable manymany ``save``
* Bugfix: in behaviour virtualpath in case of uniqueness by context
* Bugfix: Front Controller ``getContext`` method don't work after cache
* Bugfix: Make a specific cache for 404 errors
* Bugfix: In front controller, move uniqueness checks in add methods for css and js, array_unique with SORT_REGULAR throw Fatal exception in some case

### Blog /News

* ``getUrlEnhanced()`` method return false if category is not in categories of the enhancer

### Form

* Select and radio fields don't have a default value checked anymore
* Text fields (single and multi-line) can now be pre-populated based on Request parameters

### App wizard

* Update the generated controller class in order to use the setItemDisplayed method

### Thank to:

@Foine, @Pierrinho, @shaoshiva, @FredDubois, @jay3

## April 16, 2014: version 4.2 (Dubrovka)

* Bugfix: remove a notice in enhancer popup controller
* Bugfix: on lazy load on ``Orm_Twinnable_HasMany`` relation
* Bugfix: on the twinnable many many relation save()
* Bugfix: no highlight cells in grid. Add ``nosCellsSelected`` option in ``noslistgrid``.
* Bugfix: on twinnable relations ``delete()``, deleting only after the delete of the last twin
* Bugfix: on twinnable relations ``save()``, create duplicate entries or deleting relations in some cases
* Bugfix: shared wysiwygs and medias are lost when deleting a twin
* Bugfix: remove a notice in 404 if the requested file have no extension
* Twinnable relations accept ``model_to`` not twinnable
* Improved methods of ``Tools_Enhancer``, they take into consideration the state of publication and transmit the parameters of the enhancer to the method ``getUrlEnhanced()``
* Adding event ``admin.launchers`` to add/remove launchers
* New constant ``PUBLIC_DIR`` that contains the name of the public directory. Initialized to ``public`` by default if not set.

### Blog /News

* ``getUrlEnhanced()`` method return false if post is not in categories of the enhancer

### Form

* Bugfix: when answer grid contains file and large label
* Bugfix: captcha calculated and displayed whitout cache
* Bugfix: jQuery and Modernizr are loaded only if they not already are
* Vendors update: Webshims 1.12.5

### App wizard

* Bugfix: force at least one category name

### Thank to:

@shaoshiva, @Foine, @jguyomard, @ounziw

## March 6, 2014: version 4.1 (Dubrovka)

### New features and improvements

* **Front Controller**:
    * New methods ``setItemDisplayed()`` and ``getItemDisplayed()``.
    * ``setItemDisplayed()`` set automatically ``title``, ``h1``, ``meta_description`` and ``meta_keywords``.
    * ``setItemDisplayed()`` triggers the event ``front.setItemDisplayed``.
    * New ``setH1()`` method.
    * ``setTitle()``, ``setH1()``, ``setMetaDescription()``, ``setMetaKeywords()`` methods take a template by second parameter (the default template can be set by config). The page's property is available in the template with a placeholder.
    * The method ``addJavascriptInline()`` detects the use of tag ``<script>``.
* **Appdesk**:
    * The search bar layout is improved
    * New possible config key ``multiContextHide`` for inspectors
    * Performance improved with a javascript refactoring: use of ``wijsplitter`` only if need.
    * Improving the resize process.
* **Relation Twinnable_ManyMany**: Improving of the ``join()`` method. Adding the ``main_context`` condition.
* **Behaviour Twinnable**: Improving performance of save operation by avoiding to save twins if not needed.
* **Behaviour sortable**: Add config key ``sort_twins``, default to true.

### Bugfixes:

* **Relation Twinnable_Manymany**: Wrong alias on fallback ``join_on``.
* **Relation Twinnable_Manymany**: Preserve relation when a deleted model still has twins.
* **Relation Twinnable_Hasmany**: In method get(), condition on ``key_to`` missing.
* **Appdesk**: Position of the close buttons of tags in search bar.
* **nosOnShow()**: Set display block only if element is display none. Bug in case of tag-it, input supposed hidden by css but made visible by ``nosOnShow``.
* **Tools_Url::encodePath()**: Use the ``rawurlencode()`` function, space must be encode with ``%20`` not ``+``.
* **Tools_File::send()**: Double call to ``fuel-shutdown`` event.
* **Tools_File::send()**: Forces the download, incompatible with its use to send media in the 404.
* **WYSIWYG**: No CSS in popup dialogs.
* **Page CRUD**: When switching the template, images in WYSIWYG are broken.
* **Share panel**: Checkboxes to enable fields don't work.
* **CRUD blankslate**: When clicking on a sub-menu of the button 'Translate from..', the two forms of the tab are submitted.
* **Blog and News**: The model post sets is own ``$_behaviours``, not uses that of blog/news. Fix makes possible the simultaneous use of blog and news.
* **Comments**: In Appdesk ``sorting_callback``, when sort by comments count.
* **Slideshow**: Always display the 'link to a page' in the admin slide.
* **FuelPHP**: ``html_tag()``, escape double quotes inside property value.

### Thank to:

@vrcAlbert, @Foine, @jay3


## February 10, 2014: version Dubrovka

### New features:
* Applications can extend multiple applications
* The extend application mechanism works also for views and lang files, not only for config files
* Russian translations
* Spanish translations
* Interlingue (Occidental) translations
* Japanese translations updated

### Breaking changes:
* FuelPHP 1.7.1
* Wijmo 2013v3.20
* End of support for config key migrations.enabled_types.metadata
* Event to extend metadata config must listen the file with a starting ``!``

### Vendors update:
* FuelPHP 1.7.1
* Wijmo 2013v3.20
* jQuery 1.10.2
* require.js 2.1.9
* TinyMCE 3.5.10
* JQuery.cookie 1.4
* JQuery.form 3.46.0-2013.11.21
* JQuery.validation 1.11.1
* JQuery.mousewheel 3.1.6
* JQuery.datetimepicker 1.4.3


### Improvements:
* Integration of the Novius OS new logo
* I18n : add plural mechanism and implement plural translation
* Messages in step 1 of the installation wizard
* WYSIWYG: Refactoring TinyMCE Novius OS specific features. Explode all features in plugins, much more modular.
* Behaviour: All aliases in ``where`` and ``order_by`` options of ``find()`` work whatever the level where the alias is used and even in chaining methods.
  Concern: ``context`` in ``Contextable``, ``published`` in ``Publishable``, ``default_sort`` in ``Sortable``, ``parent`` in ``Tree`` and ``context_main`` in ``Twinnable``.
* Migration: Add a incremental ID and a execution date in migration table
* App manager: disabled buttons after click. Can't call same action two times
* Add a generic Renderer::renderer() method for all renderers that extended Renderer
* Blog/News: Add a specific title for author posts list
* Blog/News: Change page_title and meta title for category, tag and author posts list
* Comments: The comment context can be passed by parameter in API
* Bugfix: when switching tab before end of loading, crud not well initialize

### Deprecated:
* Some i18n keys of CRUD config for plural forms.
* ``Nos::hmvc()`` API is simplified, second argument can be just an array, not an array with an ``args`` key containing an array.
* The method ``\Config::loadConfiguration()``. Use ``\Config::load()``.
* The scope ``public`` of the method ``\Nos\Application::applicationRequiredFromMetadata()`` is deprecated (will become protected)
* In metadata files, the ``extends`` key containing an array with an ``application`` key
* Config files extended by application extending mechanism must be defined in a subdirectory apps/application_name/

### Thank to:
@ounziw, Vadim Oparin, @vrcAlbert, @jay3, @jguyomard

## February 10, 2014: version Chiba 2.4.1

* Exception messages will no longer be replace by a common message in PRODUCTION environment for fieldset complete process
* Adding a "check all" on the CRUD delete popup when twinnable
* Bugfix: In Twinnable::findMainOrContext(), $context parameter can be an array
* Bugfix: Renderer_Time_Picker, js_init() is a static function
* Bugfix: in 404.php, remove a warning log if REDIRECT_QUERY_STRING not setted in $_SERVER
* Bugfix: Cascade save of Attachment relation fails on new model item
* Bugfix: Increase maximum execution time for install migrations to prevent PHP timeout
* Bugfix: confirmationDialog will now be centered verticaly
* Bugfix: Update ui.css of the bootstrap theme of tinyMCE for custom styles readability
* Bugfix: Relation twinnable many many cleans with id, not with common ids

### Thanks to:
@Pierrinho

## January 20, 2014: version Chiba 2.4

* Considers cache dirty if cache duration changes
* Add a lock mechanism on images resized generation
* Init all relation types to empty in models
* Bugfix: Relation Orm_Twinnable_ManyMany, preserve relation when a deleted model still has twins
* Bugfix: in ostabs.dispatchEvent(), case of tab opened not at the end. Index is in a data, not that of panels array. Comparaison with selected fails
* Bugfix: patch for WYSIWYG's IE11 compatibility
* Bugfix: if a metadata value contains two consecutive spaces, this metadata always marked as dirty
* Bugfix: Config_Php save when apc enabled
* Bugfix: App manager refresh all metadata save metadatas with labels translated
* Bugfix: Model_User->checkPermission() don't work due to a typo

### Form:

* Bugfix: blank lines in the submit emails field are excluded before sending email. Blank lines can be added by a pre-process event.
* Bugfix: if long label in answer fields, grid is truncated. Add auto scroll mode.

### Thank to:
@jay3, @vrcAlbert, @Unibozu

## December 20, 2013: version Chiba 2.3.3

* Page's Renderer_Selector now accepts checkboxes, not only radiobuttons
* Improving of 404, allowing to use novius_ftplite app to add custom robots.txt (favicon or humans.txt)
* Bugfix: regression, the event front.start takes into account modifications of cache_path
* Bugfix: in popups enhancer that use fields config, opening and closing the form with fieldset methods (calling of build_append and build_js_validation)
* Bugfix: updating url_enhanced file when changing the virtual_path of one ancestor of an url_enhanced page
* Bugfix: case of a checkbox in common fields, when is disabled, CRUD always saves it like unchecked
* Bugfix: adding WYSIWYG's IE11 compatibility
* Bugfix: In Appdesk, aborting previous search request before send the new one
* Bugfix : Renderer_Date_Picker and Renderer_Datetime_Picker ignore value attribute in standalone case ::renderer()
* Bugfix : inverting cache duration (600s in developpement, 3600s in production)

### Slideshow:

* Allowing to add link or script tags in image_fields

### Form:

* Bugfix : the event noviusos_form::data_validation makes a warning if it returns nothing

### Thank to:
@vrcAlbert, @jay3, @jguyomard, @Foine

## December 3, 2013: version Chiba 2.3.2

*  Fix XSS in profiler


## December 2, 2013: version Chiba 2.3.1

* UI: Remove javascript ref to css class ui-default-state for textbox enabling by checkbox (title menu on page, virtual name on media)
* When inserting during a pick process, picks automatically the new item (media, page)
* Fix a regression due to temporary removal of sensible config items in profiler display (images resized fails if profiler displayed)

## November 28, 2013: version Chiba 2.3

* Bugfix: Fixed task execution on refine with local and nos namespaces
* Bugfix: Back-office fails when a tab with a numeric label is opened
* Bugfix: wysiwyg, skin bootstrap, background color for active button
* Bugfix: Fixed virtualname, there was a notice when using setups inside setups
* Bugfix: Don't create cache for POST request
* Bugfix: unobtrusive javascript error when click a context menu of a tab
* Bugfix: Firefox 24 remove HTMLCommandElement implementation, update test for menu context support (https://developer.mozilla.org/en-US/docs/Site_Compatibility_for_Firefox_24)
* Bugfix: Now logging errors when occuring during an HMVC request
* Bugfix: add scrollbar on wijmenu displaying contexts, in case of many contexts
* Bugfix: in layout_standard view, for fields in subtitle zone, use placeholder if is set
* Bugfix: (FuelPHP core) on _resize when using BC Math and on some locales (as french), _resize always tried to generate images with width = 0 and height = 0
* Bugfix: In Tools_url::encodePath(), if url not have a path part
* Bugfix: in layout_standard, in subtitle, don't had a <td> if field begin by a td
* Bugfix: inspector tree radio and chackbox, reloadEvent don't listen context if contextChange is false
* Bugfix: an uninitialized variable cause a notice in method url() of behaviour URLEnhancer
* Bugfix: permission, nos::permissions displayed twice, generate duplicate insert when save or note remove permission
* Bugfix: renderer datetimepicker can not be set to null
* Bugfix: Install fails in PHP 5.5. Extend Config_Php, invalidate opcode cache when save
* Renderer datetimepicker : add option null_allowed, default false
* Remove PHP Notice if publication_start_property not set in behaviour publishable
* Improvement: Improve Toolkit_Image->sizes(), not loading image
* Exclude some items from config in Profiler
* Wysiwyg image popup: add fields border, align, vspace and hspace to easily update style
* Improve javascript for context common fields, now none support input can implement their own process
* Improve javascript for context common fields, work also on not input fields
* Implement context common fields on virtual name renderer
* Profiler: new methods markDeltaStart() and markDeltaStop() to study time durations

### ORM:
* Add through_where parameter in many_many relation configuration

### Form:
* Bugfix: Answer export fails when too many answer.
* Bugfix: form name is html encoded in answer email subject
* Adding a "replyto" field to sent emails if an email is present in the answer. Depends on the add_replyto_to_first_email config key of noviusos_form.config.php file. Default true.
* Form CRUD: move submit email field on the top of the page

### AppWizard:
* Bugfix: now column can contain numbers
* Bugfix: added missing relations declarations
* Improvement: added check on local/applications folder permission

### BlogNews:
* Bugfix: pagination didn't take into account categories

### Slideshow:
* Bugfix: Change \Config::load() to \Config::loadConfiguration in order to easily extend the application

### Thank to:
@jay3, @Foine, @vrcAlbert, @shaoshiva

## October 14, 2013: version Chiba 2.2

* Bugfix: In RequireJs settings, re-introduce alias for wijmo widgets dropdown, radio, checkbox, textbox.
* Bugfix: In the compilation of the friendly slug configuration for javascript execution, the lowercase rule is removed.
* Bugfix: Install, move install in shared-hosting case to install.php
* Bugfix: Install, in shared-hosting case, if on Windows, not write backslashes in .htaccess
* Bugfix: Now the virtual_path behaviour take into account the maximum size of the virtual_name field when checking for its unicity.
* Bugfix: Fieldset->isExpert() throw an error if user is not expert and a field added to fieldset after build
* Bugfix: $args decode in get_enhancer_content
* Bugfix: In pages, when saving deep arrays on enhancers parameters
* Bugfix: nosAction window.open fails on IE9 and less if url is relative
* Bugfix: Patch for the profiler when displaying  EXPLAIN queries containing accent
* Bugfix: Regression in media renderer, standalone case, after refactor with Nos\Renderer
* Bugfix: The sharing panel don't open
* Bugfix: WYSIWYG, add automatic scrollbar on ButtonMenu in bootstrap theme
* Bugfix: WYSIWYG, when there was a media link on a media, the media url was partly replaced
* Bugfix: WYSIWYG, bad textbox style in media popup when fields are enabled
* Bugfix: WYSIWYG, when changing an image, sizes are changed but not the url
* Bugfix: UI support for more than 10 contexts
* Bugfix: If call model::relations(null), must return false not all relations
* Remove unused and obsolete method Fieldset->readonly_context()
* Improvement: Class Nos\Renderer_Date_Picker was factorized into Nos\Renderer_Datetime_Picker
* Improvement: Media and folders deletions are manage by models, not by CRUD controller
* Improvement: In the i18n class, adding addPriorityDictionary and addPriorityMessages methods
* Improvement:  FuelPHP tasks have been adapted to Novius OS. Tasks namespace now depends on application namespaces allowing two tasks with similar names in many applications.
                A related application, novius_taskmanager, has been implemented in order to allow tasks execution from an url.

### Form:
* Bugfix: When the user switched from a unique choice field to a multiple choice field, the mandatory checkbox were hidden but still checked, causing issues on front.
* Bugfix: Bad tab format of the awnsers appdesk (no label and 32 pixels icon size)
* Bugfix: Bad parameters for event noviusos_form::after_submission
* Improve layout of the answer email

### Blog/News:
Bugfix: Regression due to ORM when saving categories relation of a post

### Thank to:
@jay3

## September 11, 2013: version Chiba 2.1

* Bugfix : Toolkit_image->parse($url) checks url match in absolute but it's passed in relative. Image's media transformed return a 403 if you're not connected to back-office.
* Bugfix : Fix on media permission; when updating a user, his writing rights on medias were disabled.
* Bugfix : My account tab parameters are replaced by user CRUD when saving
* Improvement: The configuration of button 'save' is no more required in CRUD fields settings.
* Improvement: In Models, when use cache_model_properties, new possibility to set a callback (check_property_callback, see local/config/config.php.sample) to check if property is a potential unknow column, and avoid a "show field" SQL request.
* Improvement: Factorization of code for user password strength validation.
* Improvement: Factorization of code between inspector model and modeltree for process config input.query
* Improvement: New class Nos\Renderer for factorizing code between all renderers.
    * Deprecated: Method set_renderer_options of Renderer_Selector is deprecated, use setRendererOptions instead.

### Media:

* Include mass upload feature from https://github.com/novius/novius_mediamassupload

### Templates basic:

* Refactoring templates_basic, better code factorization between top and left menu template

### Slideshow:

* Refactoring configuration and organization. Widgets for displaying slideshow in front are manage by a formats config for better extendable.
    * Deprecated: The struture of slideshow config file have changed
    * Deprecated: The use of view.index in Controller_Slideshow config file is deprecated, use view key of your format in slideshow config file instead.
    * Deprecated: The config file noviusos_slideshow::flexslider is deprecated, please use noviusos_slideshow::formats/flexslider instead
    * Deprecated: The view noviusos_slideshow::slideshow_js is deprecated, please use noviusos_slideshow::flexslider/javascript instead
    * Deprecated: The view noviusos_slideshow::slideshow is deprecated, please use noviusos_slideshow::flexslider/slideshow instead

### Blog/News and Comments:

* Better clean-up of front-cache when a post or a comment is inserted, updated or deleted.

## August 29, 2013: version Chiba 2

### New features:

* Windows support (Vista and upper).
* Better install wizard (UI, more tests, choose of languages)
* Advanced permissions for all natives applications.
* Comments application:
    * Administration interface
    * Emails are sent when new comments are posted, to post author and others commenters.

### Breaking changes:

* Model: If a column of a 'dataset' contains HTML, you must also set 'isSafeHtml' => true if you don't want that it to be encoded (for security reasons).
* CRUD: For item updating, the callback function 'success' is called after 'save' (not before), like for creating.
* Attachment: Methods ->url() and ->urlResized() return absolute URLs. They accept an optional parameter for the return of relative URLs.
* Comments: Comments are now contextable. Migration tries to guess context of existing comments, but if you've implement comments on a not contextable model, migration can't do nothing: set yourself context if you want to see those comments in new administration interface.
* Blog/News: Thumbnail is now configurable (size & link).
    * Default thumbnail size changed from 200 everywhere to 120 in the listing and 200 on the item.
    * Thumbnail can now be clicked to go on the item page (set thumbnail.front.list.link_to_item = false to restore old behaviour).

### Vendors update:

* FuelPHP 1.6
* jQuery 1.9.1
* jQuery UI 1.10.3
* Wijmo 2013v1.4
* require.js 2.1.6

### Improvements:

* i18n: Default dictionary 'app::default' is used if no dictionary is set with Nos\I18n::current_dictionary().
* DB: Change interclassement on all columns containing a slug.
* ORM: Improvement of the model properties' cache mechanism, just one query of 'columns' from DB by request.
* ORM: 4 new relation types, twinnable_belongs_to, twinnable_has_one, twinnable_has_many, twinnable_many_many.
* ORM: Model class, new addRelation(), configModel(), getApplication() methods.
* Behaviour: New behaviour author, used by Page, Media, Blog/News, Slideshow, Form.
* Behaviour: Refactoring behaviour implementation (behaviours can intercept model events).
* Behaviour Twinnable: Models now can have fields, medias and WYSIWYGs common to all contexts.
* Behaviour Twinnable: new findMainOrContext(), hasCommonFields(), isCommonField() methods.
* Behaviour URLEnhancer: New methods deleteCacheEnhancer() and deleteCacheItem().
* Behaviour URLEnhancer: Delete front's cache of the item on deleting and updating.
* Enhancer: In the configuration popup, new ability to define a 'layout' and 'fields' configuration instead of a view, much like the CRUD.
* Enhancer: In enhancer configuration, new possible key 'valid_container', which is callable. Can restrict the enhancer availability depending on container.
* Enhancer: The HTML output generated for the front-office is wrapped in a div with classes 'noviusos_enhancer' and the enhancer name ('noviusos_blog', 'noviusos_news', 'noviusos_slideshow', 'noviusos_form')
* Renderer: New datetime picker renderer to manage both date and time in the same input.
* WYSIWYG: New WYSIWYG configuration mechanism, with a 'wysiwygOptions' event registrable by behaviour (and used by twinnable), and 'wysiwyg' config sample file.
* WYSIWYG: In Nos::parse_wysiwyg(), replacing anchors by URL#anchor only in front.
* SEO: New friendly slug configuration mechanism, with a 'friendlySlug' event registrable by behaviour (and used by twinnable), and 'friendly_slug' config sample file.
* OsTabs: New reload method in API.
* OsTabs: Change in tabs opening position. Tab added without index now is added at 'selected + 1', excepted on the desktop, which always adds the new tab at the end.
* Appdesk: Two new keys, 'css' and 'notify' in appdesk configuration.
* Appdesk: Ability to ignore a cellFormatter based on a column value.
* Appdesk: Now custom cellFormatters are allowed in appdesks.
* Grid: New 'align' key on actions configuration.
* Grid: New option for the initial opening depth on tree grid.
* UI: Using '.ui-priority-primary' instead '.primary' on button and '.title' on textbox inputs.
* UI: Use browser native select, checkbox and radio, no more use of Wijmo widgets for those inputs.
* Page: Setting the home page is not allowed in multi-context view.
* Page: Deleting or unpublishing the home page is not allowed.
* Page: Increased title and url columns characters length.
* Media: New field 'filesize'. Display 'filesize' and dimensions in appdesk preview and CRUD form.
* Media: Refactoring get_img_tag() and get_img_tag_resized() methods of Model_Media, uses HTML::img() for returning a tag with attributes.
* Media: You can now transform (crop, rotate, rounded, watermark, resize, shrink, grayscale, border) Media and Attachment images with Toolkit_Image API.
* Media: New "Renew media's cache" action in Media appdesk toolbar, visible for expert users.
* Media: Increased title and url columns characters length.
* Comments: New API for use of noviusos_comments application.
* Form: New 'message' view for the confirmation.
* Misc: New events '404.mediaFound', '404.attachmentFound', 'admin.loginFail' and 'nos.deprecated'.
* Misc: All URLs are now urlencoded when use in a href or in a redirection.
* Misc: New 'temp' directory in local/data, assign to 'novius-os.temp_dir' config key by default.
* Front: 'is_preview' is true only when you are logged in.

### Deprecated:

* Enhancer: get_url_model($item, $params) in enhancer front controller is deprecated, please use getURLEnhanced($params) and $item in a key 'item' of $params.
* Media: Change Model_Media API, deprecating all snake_case methods.
* Media: Deprecating delete_from_disk() and delete_public_cache() methods of Model_Folder. Use deleteFromDisk() and deleteCache() instead.
* Page: Model_Page->link() is deprecated, please use Model_Page->htmlAnchor() instead.
* Misc: Event 'user_login' is deprecated, please use 'admin.loginSuccess' instead.

### Thank to:
@ounziw, @jguyomard

## ?: version Chiba 1.?

- Bugfix : In behaviour sortable, in event before_query, order_by can be string...
- Bugfix : Attachment->url() trims the end of the filename
- Bugfix : Fieldset::defaultComplete when error option is callable
- Bugfix : bad rule in .htaccess for shared hosting. Popups with a htm extension in WYSIWYG are redirected to front.
- Bugfix : Use the sort property of the Sortable behaviors despite of a static property
- Bugfix : URL Enhancer don't use context when item has behaviour contextable
- Bugfix : Behaviour publishable when not using publication_start and publication_end
- Bugfix : Prevent from caching OS tab content
- Bugfix : In wysiwyg, IE9 set simple quote for tag attribute, not well parse when detecting enhancer
- Bugfix : Bugfix: Response::json() set content-type to text/plain for no-ajax request. For browser receive pseudo ajax request for file transport using iframe.
- Bugfix : Wysiwyg for img[usemap] and linked deleted when editing a media.
- Bugfix : In wijdialog. Case of double modal dialog, the browser stop javascript with an ERROR on close click. Must preventDefault before all other code execution
- Bugfix : In appdesk, pageIndex is not reinitialized when the search is changed
- Bugfix : In appdesk, i18n key was processed, triggering javascript bugs when "columns" key was present.
- Bugfix : In attachment, when set() without the filename parameter, use the basename (filename + extension) of the file
- Bugfix : Bad option in convert command
- Bugfix : in media popup for pick an image, extension inspector not hide
- Add a defense for Clickjacking attack in admin

### Slideshow :
- Bugfix : remove link on a slide doesn't works
- Bugfix : in slideshow administration, generate a PHP Notice if has restricted field

### Form:
- Bugfix : form open tag not auto-closed anymore

### Thank to:
@romunovius, @vrcAlbert, @Foine, @ounziw, @jguyomard

## May 17, 2013: version Chiba 1.0.1

- Improvement : Class Pagination, every page number surrounded by the same class
- Bugfix : Selecting custom dates in inspector dates do nothing
- Bugfix : Remove List columns cache for model properties cache conditioned by configuration (default false)
- Bugfix : On wysiwyg in enhancers
- Bugfix : i18n class don't trigger event on load file

### Form:
Minors bugfix and improvement

### Thank to:
@ounziw, @vrcAlbert, @Pierrinho

## April 25, 2013: version Chiba 1

- New feature: Behaviour_Publishable now allows to choose publication start & end dates
- DB Config : installation now sets the active configuration depending on the current Fuel::$env value
- Migrations are now dispatched per application
- New metadata key 'requires' which allows to define that an application requires another one
- The extends key, in metadata, now allows to disable automatic configuration recursive merge (only bootstrap loading remains in this case)
- It is now possible to use href="##..." in enhancers or templates; occurences will be replaced by href="#..." without prepending the base_url.
- CRUD: When returning a string in the disabled key, it is displayed as a title. Disabled and visible key can now be simple values, callbacks or array of callbacks.
- Moved install.php and migrate.php.sample from public/ to public/htdocs/. No URL impact.
- Resized images are now secured: you can't generate a lot of thumbnails and flood the server anymore
- Bugfix: form/expander and form/fields views can now deal with restricted fields
- Crud->save() now returns dispatch event in the array form
- Renamed configuration directive 'use_xsendfile' to 'novius-os.use_xsendfile'

### Deprecated:
- Moved Nos\Renderer_Media to Nos\Media\Renderer_Media.
- Launchers configuration: the 'url' key is deprecated. Use 'action' instead.
- The 'widget' key is deprecated in renderer configuration. Use the 'renderer' key and update the class name.
- The 'widget_options' key is deprecated in renderer configuration. Use the 'renderer_options' key.
- \Config::extendable_load() is deprecated. Renamed to \Config::loadConfiguration().
- Orm_Behaviour_Publishable configuration: the 'publication_bool_property' key is deprecated. Use 'publication_state_property' instead.

### Permissions:
- Ability to define per-application permissions with a configuration file
- New API to check permissions for a user, or a specific role
- Ability to enable multi-roles on the users with the novius-os.users.enable_roles configuration

### Front improvements:
- Profiling is activated by default on front in the DEVELOPMENT environment
- Setting config novius-os.cache by default always at true
- Setting configs novius-os.cache_duration_page and novius-os.cache_duration_function at 600 by default, except in PRODUCTION at 3600
- New events: 'front.pageFound' and 'front.response'
- New methods in Controller_Front : getContext, disableCaching, setCacheDuration, setStatus, setHeader, getCustomData, setCustomData, sendContent, addCacheSuffixHandler
- Status and headers are now save in cache
- Mechanism to adapt the cache path with suffixes, depending GET parameters or what you want
- Mechanism to execute code when using the cache
- Bugfix: if set, use page_cache_duration of the page


### Models properties:
- Add a config for fuelphp cache, add local/cache/fuelphp directory, add check for it in install process
- All models (core and native apps) now defines the $_properties
- Implement a cache mechanism on models properties, using fuelphp cache. Attempt to auto-refresh when an unknown properties is called.

### Vendors:
- jQuery 1.8.3
- jQuery UI 1.9.2
- Wijmo 2.3.7
- tinyMCE 3.5.8
- require.js 2.1.4
- FuelPHP and packages 1.5

### Slideshow:
- add HTML5 progress bar

### Blog / News:
- Display related posts of authors

### Thank to:
@ounziw, @jay3, @vrcAlbert, @Pierrinho


## ?: version 0.2.0.3

- Bugfix : front controller, getUrl() returns a bad URL for secondary contexts
- Bugfix : Model::set() generate a NOTICE in PHP 5.4 if property name is an array
- Bugfix : Inspector tree model checkbox/radio removes sub item checked not display
- Bugfix : Refresh all metadata don't alter app_dependencies anymore
- Bugfix : Wysiwyg retreive nosContext when it is needed, not at init
- Bugfix : parse wysiwyg, looking for medias. ie : Fail if media ID 2 and ID 21 in same wysiwyg.
- Bugfix : Wysiwyg in IE, loosing selection when popup link or image opening
- Bugfix : disconnect don't work in IE9, IE9 don't use base href in javascript
- Bugfix : IE, bad background color in inspector grid tr hover
- Bugfix : Template, mediaprint.css replace by print.css

## March 21, 2013: version 0.2.0.2

- Adding 4 events in 404 entry
- Standalone build of the wysiwyg renderer.
- now inspectors key also allow class to be written
- Refactor OS Tabs : if not a touch device, close icon now in tab with a context menu
- Refactor : generate base_url now use server variable http_host
- Adding a config novius-os.cache for use of cache on front, by default true except in development
- Setting configs novius-os.cache_duration_page and novius-os.cache_duration_function at 60 by default
- In front controller, adding js inline accept content with <script...
- Bugfix : controller front, POST and _preview use cache
- Bugfix : front controller, getUrl now returns an absolute URL
- Bugfix : front controller, context_url was set after parse wysiwyg
- Bugfix : popup for saving form answer attachment in media centre
- Bugfix : media managing when not exist on disk
- Bugfix : front, infinite loop in 404 and URL with accents
- Bugfix : SEO data not used in front
- Bugfix : crud popup delete, js not require wijgrid
- Bugfix : crud delete, bad i18n message
- Bugfix : enhancer in wysiwyg saved with IE have quote around attributs
- Bugfix : wysiwyg, set document base uri
- Bugfix : wysiwyg in IE, enhancer popup don't closing
- Bugfix : Update crud.ctrl.php to hide translate button if just one context
- Bugfix : Update links to documentation in blank slate front
- Bugfix : In appdesk, grid not trigger selectionChanged when is reloading and has a selected item

### Blog / News:
- Bugfix : &nbsp; form i18n not decode for RSS title and description

### Slideshow:
- Bugfix: front of slideshow javascript
- Bugfix: front in IE8, in onreadystatechange event, readyState is not 'complete' but 'loaded'

### Thank to:
@jguyomard, @Pierrinho and @Foine


## February 26, 2013: version 0.2.0.1

- Front: if access to a page which is external link, redirect
- $.nosFormUI: Add notransform filter on checkbox
- Install: improve symlink creation
- Refactor routing: use REQUEST_URI instead REDIRECT_URL
- Front: improve cache of the current page
- Add constants for entry points of Novius OS
- Bugfix: enhancer popup in wysiwyg for updating
- Bugfix: front locale
- Bugfix: event register on files that don't exist
- Bugfix: appmanager, uninstalling one app uninstall all apps
- Bugfix: Tools_URL::page() (missing .html)
- Bugfix: A user can edit its own account
- Bugfix: account editing, restored JS validation
- Bugfix: no scroll in Novius OS tabs
- Bugfix: Crud form, enter in a field close the tab
- And many others minor fixes

### AppWizard:
- Add the type 'image'
- Add URL enhancer
- Translations

### Form:
- Bugfix: layout becomes all messed up when putting 2 4-width fields on the same line at the bottom
- Bugfix: cannot move a date field just after adding it
- Bugfix: captcha always checked
- Bugfix: front with pagination fail if no captcha

### Blog / News:
- Bugfix: post save fails when root category is selected
- Bugfix: categories inspector won't load when just one context
- Bugfix: bad icon URLs when not installed on root domain

### Slideshow:
- Bugfix: bad icon URLs when not installed on root domain

### Thank to:
@vrcAlbert, @Pierrinho, @jay3, @FredDubois, @Foine, @mvy


## January 31, 2013: version 0.2

- Rename lang, lang_common_id, lan_is_main columns. Replace lang by context. Resize lang columns. And all variables
- Rename behabiour Translatable by Twinnable and all variables
- In config.php, change locales by contexts
- CRUD : rename context by environment, context_relation by environment_relation, item_context by item_environment
- Update vendors jQuery, jQuery UI, Wijmo, tinyMCE
- Model_Page->get_link() become Model_Page->link()
- Model_Page->get_href() become Model_Page->url()
- Model_Page::get_url() become Tools_Url::page(), remove Model_Page::get_url_absolute()
- New class helper Tools_Url
- All functions and methods returning url use absolute url by default (Model_Page->url, Model_Page::find_url, Model_Page->link, Url_Enhancer->url, Tools_Enhancer::url_item)
- Add Simple Google+ sharer
- Click middle on Os Tabs close it, New button Close all other tabs
- Appdesk : possibility to format column by php config
- CRUD function from_item rename in init_item and call just if item is new.
- Fuel PHP (and packages) 1.4 (update submodules)
- Attachment
- Refactoring : Rename all Widget by renderer. Can impact classes names, views paths
- Slideshow
- Form
- App wizard
- Overload any views and config in local
- Simplest enhancer popup declaration
- Config : upload.disabled_extensions move in novius-os.upload.disabled_extensions
- Back-office available in english and french
- PHP Event front.start have new param cache_path
- Time picker renderer can be use outside a fieldset
- Many UI improvements and bugfixes

### Thank to:
@jay3, @Shaoshiva,  @vrcAlbert, @jguyomard

## December 20, 2012: version 0.1.5

### Core
* Bugfix: Front - remove href when internal page no longer exists (thanks @vrcAlbert)
* Bugfix: Fixed recursive deletion for pages and Tree behaviour
* Bugfix: nosTabsOpen if not 3 tabs in tray (due to permissions)
* Bugfix: '&' in virtualname prevents from reaching the page
* Bugfix: Front - Multiple js_inline and css_inline (thanks @jguyomard)

### Blog & News
* Bugfix: check if author of post, exist before call fullname method
* Bugfix: remove load of comments in blognews bootstrap

## November 26, 2012: version 0.1.4

### Core
* Bugfix: Front post-processing has to run before the cache is written
* Bugfix: Widget_Media with mode=all (server side)
* Bugfix: Bahaviour sortable not completely effective
* Bugfix: locked page can be delete via toolbar crud
* Bugfix: regression 0.1.3, can't insert enhancer in wysiwyg


### Install
* Bugfix: Added test on public/media (is writeable)

### Blog & News
* Bugfix: Use nosLang passed by wysiwyg for widget category in popup ehancer
* Bugfix: Front View : don't filter object, use htmlentities on each echo
* Bugfix: get_all method for Model_Post for front, case multiples categories query and fetch all related
* Bugfix: front controller not use the category selected in the enhancer popup
* Bugfix: enhancer popup not reselected category selected


## November 7, 2012: version 0.1.3

### Core
* Bugfix: Inspector tree in crud form don't use lang

### Blog & News
* Bugfix: CRUD function form_item rename in from_item.


## October 22, 2012: version 0.1.2

### Core
* Refactoring: CRUD function form_item rename in from_item.
* Refactoring: Novius OS can now be install in a sub-directory of a shared hosting, even if public is not the DOCUMENT_ROOT.
* Refactoring: Install process improvement, doesn't need to create directories anymore. Only changing files permissions.
* Bugfix: Conflict between sortable and click events for launchers in the AppTab.
* Bugfix: In wysiwyg, hide labels of Enhancer's actions links
* Bugfix: Appdesk now listen all langs events, in case user switch the selected lang
* Bugfix: BD request for Appdesk json test if LIMIT is not negative
* Bugfix: CRUD's toolbar buttons not reloaded when event is delete the current item

### Blog & News
* Bugfix: Blog and News stories, bad namespace and url for inspector's date.


## October 3, 2012: version 0.1.1

### Core
* Bugfix: non-image media now displays properly in front-office
* Page: the boxes 'URL' and 'SEO' are now hidden when editing an external link
* RSS: removed data catchers, replaced with a Tools_RSS class (Blog and News applications were updated accordingly)
* CRUD: actions buttons are now refreshed upon change
* Behaviour: the url() method now only generates URL for the authorised enhancers' item
* Updated the front-office blank slate to remove 404 links
* Installation: now checks the directive short_open_tag is active
* DB cleanup: removed unused columns

### Comments
* Better spam checking management (in addition to RE-Captcha)

### Blog & News
* Re-added the visualisation count stat for post items
* Bugfix for main list pagination


## September 20, 2012: version 0.1

* Initial release
