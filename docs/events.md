# Events

## \bitboxde\minifier\services\View::EVENT_BEFORE_MINIFY_FILE

Fired before a file will be added.

    Event::on(
        \bitboxde\minifier\services\View::class,
        \bitboxde\minifier\services\View::EVENT_BEFORE_MINIFY_FILE,
        function(\bitboxde\minifier\events\ViewEvent $event) {
            $event->type;
            $event->filePath;
            $event->output;
    });
    
See **[Integrate a CSS preprocessor (Sass / Less)](integrate-a-css-preprocessor.md)** for an example.