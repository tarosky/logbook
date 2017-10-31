# talog

[![Build Status](https://travis-ci.org/tarosky/talog.svg?branch=master)](https://travis-ci.org/tarosky/talog)

A logging plugin for WordPress.

Download: https://github.com/tarosky/talog/releases

## Customizing

### Add your custom log events

1. Create a class that extends the `Talog\Logger` class.
2. Load the class by `Talog\init_log()` like following.

```
add_action( 'plugins_loaded', function() {
	Talog\init_log( 'Your_Class' );
} );
```

[The example class is here.](https://github.com/tarosky/talog/blob/master/example/Example.php)

## Screenshot

### List of logs

![](https://www.evernote.com/l/ABWwkNLfbklOh5YYM6k0boOjBenoOwM6GBYB/image.png)

### Detail screen of the log

Updated post:

![](https://www.evernote.com/l/ABVgRrfpi_5MAar-zDO_Q9V18F3hkhspV18B/image.png)

Last error of PHP:

![](https://www.evernote.com/l/ABUj6csA8ElG3q5hwXiSHrYTRFtQ0lGyX0MB/image.png)
