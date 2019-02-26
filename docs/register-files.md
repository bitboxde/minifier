# Register files

The examples are based on the default settings.

See **[Settings](settings.md)** for more informations.

## CSS-File

:information_source: The file path for the following **registerCssFile**-method is based on the
**CSS File System Path** set in **[Plugin Settings](settings.md)**.

### Single file

    {% do minifier.view.registerCssFile('/file.css') %}

The complete file path would be `@webroot/css/file.css`

Output

    <link href="/css/min/3f48a421fe28e0958090cc0061dec077.css?c=1548336831" rel="stylesheet">

#### With options

    {% do minifier.view.registerCssFile('/file.css', {media: 'print'}) %}
        
Output

    <link media="print" href="/css/min/3f48a421fe28e0958090cc0061dec077.css?c=1548336831" rel="stylesheet">
        
#### With Options and Target File

    {% do minifier.view.registerCssFile('/file.css', {media: 'print'}, 'print') %}
        
Output

    <link media="print" href="/css/min/print.css?c=1548336831" rel="stylesheet">
        

### Multiple files

Both files will be combined.

    {% do minifier.view.registerCssFile('/file.css') %}
    {% do minifier.view.registerCssFile('/file2.css') %}

Output

    <link href="/css/min/3f48a421fe28e0958090cc0061dec077.css?c=1548336831" rel="stylesheet">

#### With same options
Both files will be combined.
        
    {% do minifier.view.registerCssFile('/file.css', {media: 'print'}) %}
    {% do minifier.view.registerCssFile('/file2.css', {media: 'print'}) %}

Output

    <link media="print" href="/css/min/3f48a421fe28e0958090cc0061dec077.css?c=1548336831" rel="stylesheet">

#### With differenct options
Both files will be minified in a separate file.
        
    {% do minifier.view.registerCssFile('/file.css', {media: 'print'}) %}
    {% do minifier.view.registerCssFile('/file2.css') %}

Output

    <link media="print" href="/css/min/3f48a421fe28e0958090cc0061dec077.css?c=1548336831" rel="stylesheet">
    <link href="/css/min/e4cf6efeb8b84ecd5eab28cea274c696.css?c=1548336831" rel="stylesheet">

#### With differenct options and Target File
Both files will be combined and the options will be merged together.
        
    {% do minifier.view.registerCssFile('/file.css', {media: 'print'}, 'all') %}
    {% do minifier.view.registerCssFile('/file2.css', {'data-test': 'test'}, 'all') %}

Output

    <link data-test="test" media="print" href="/css/min/all.css?c=1548336831" rel="stylesheet">

## JS-File

It's the same way like the CSS-File, just with an other method call.

    {% do minifier.view.registerJsFile('/file.js', options = [], targetfile = null) %}

Output
    
    <script src="/js/min/3f48a421fe28e0958090cc0061dec077.js?c=1548336831"></script>
    
## Advanced

For override the default **[Plugin Settings](settings.md)** for some files you can do the following

    {% do minifier.view.registerCssFile('/bootstrap.css', {
        basePath: '@webroot/assets',
        baseUrl: '@web/assets'
    }, 'bootstrap') %}

The target file would be `@webroot/css/min/bootstrap.css`. For a different target path you can set the following
options:

    {% do minifier.view.registerCssFile('/bootstrap.css', {
        basePath: '@webroot/assets',
        baseUrl: '@web/assets',
        targetPath: '@webroot/assets',
        targetUrl: '@web/assets'
    }, 'bootstrap') %}
    
Then the target file would be `@webroot/assets/bootstrap.css`. Please note, if you set the `targetPath` it's the final path.
The subdirectory `min` won't created.
