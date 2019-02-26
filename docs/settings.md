# Settings

## Debugging / Disable Minifier
There are two settings **Disable for "Admin"** and **Enable for devMode** for disable Minifier.
If one of this rules will match, the original file will be integrates as you know it from Craft.

    {% do view.registerCssFile(url, options = []}) %}
    {% do view.registerJsFile(url, options = []) %}

### Disable for "Admin"

Activate this to disable Minifier only for logged-in admins. This rule has a higher priority then **Enable for devMode**.

### Enable for devMode

Minifier is checking the [devMode](https://github.com/craftcms/cms/blob/develop/docs/config/config-settings.md#devmode) from the Craft Settings.

devMode | Enable for devMode | Minifier
------- | ------------------ | --------
enabled | enabled | enabled
enabled | disabled | disabled
disabled | enabled | enabled
disabled | disabled | enabled

## Path and URL Settings

These are the default settings for a general use of the minifier. Have a look at 

### CSS Base URL

The base URL to the CSS-files.

### CSS File System Path

The path to the CSS files on the file system, where the minified CSS files will be stored.

### JS Base URL

The base URL to the JS files.

### JS File System Path

The path to the JS files on the file system, where the minified JS files will be stored.

## Multi-Site Settings

Have a look at **[Multi-Site usage](multi-site-usage.md)**.

## Screenshot

![Screenshot](../resources/img/screenshot-settings.png)

## Advanced

Some specific settings can only made in the general config.

Config | Default | Description
------ | ------- | -----------
minifierCssMinDir | (string) 'min' | The name of the min dir for css files.
minifierJsMinDir | (string) 'min' | The name of the min dir for js files.
minifierCssCompiledDir | (string) 'compiled' | The name of the compiled dir for compiled css preprocessor files.
minifierCssClass | (string) '\MatthiasMullie\Minify\CSS' | The class to minify css.
minifierJsClass | (string) '\MatthiasMullie\Minify\JS' | The class to minify js.