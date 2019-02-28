# Integrate a CSS preprocessor

To integrate a preprocessor you can use the **\bitboxde\minifier\services\View::EVENT_BEFORE_MINIFY_FILE** event in your Plugin.
This event will only fired if a file has changed. We used for this example the simple repository
of **[leafo/lessphp](https://github.com/leafo/lessphp)**, but you can use any preprocessor. Just compile the file and set the
result in the output property of the ViewEvent.

    Event::on(
        \bitboxde\minifier\services\View::class,
        \bitboxde\minifier\services\View::EVENT_BEFORE_MINIFY_FILE,
        function(\bitboxde\minifier\events\ViewEvent $event) {
            $pathinfo = pathinfo($event->filePath);
            $ext = $pathinfo['extension'];

            if($ext === 'less') {
                $parser = new \lessc();
                $event->output = $parser->compileFile($event->filePath);
            }
        }
    );