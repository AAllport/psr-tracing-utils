PSR Tracing
=======

This repository holds utilities related to
[PSR-22](https://github.com/php-fig/fig-standards/blob/master/proposed/tracing.md).

Installation
------------

```bash
composer require psr/tracing-utils
```

Usage
-----

Before tracing-utils

```php
function imgResize($size=100) {
    $span = $this->tracer->startSpan('image.resize')
        ->setAttribute('size',$size)
        ->activate();    

    try{
    
      //Resize the image
      return $resizedImage;
    
    } catch (Exception $e) {
        // Ideally, you would attach the exception to the span here
        $span->setStatus(SpanInterface::STATUS_ERROR)
             ->addException($e);
    } finally {
        $span->finish();
    }    
}

```

After tracing-utils

```php
function imgResize($size=100) {
    return $this->tracingUtils->wrap('imgResize', function() use ($size) {
        // do something expensive
        return img_resize($size);
    });
}
```

Look at all the removed boilerplate!