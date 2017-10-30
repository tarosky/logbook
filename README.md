# talog

[![Build Status](https://travis-ci.org/tarosky/talog.svg?branch=master)](https://travis-ci.org/tarosky/talog)

A logging plugin for WordPress.

Download: https://github.com/tarosky/talog/releases

## Customizing

### Add your custom log events

Use `Talog\watch()` to add your custom event.

```
/**
 * Registers the logger to the specific hooks.
 *
 * @param string|array $hooks         An array of hooks to save the log.
 * @param callable     $log           The callback function to return log message.
 * @param callable     $message       The callback function to return long message of the log.
 * @param string       $log_level     The Log level like `Talog\Log_Level::INFO`. See `Talog\Log_Level`.
 * @param int          $priority      An int value passed to `add_action()`.
 * @param int          $accepted_args An int value passed to `add_action()`.
 */
Talog\watch( $hooks, $log, $message = null, $log_level = null, $priority = 10, $accepted_args = 1 );
```

## Screenshot

### List of logs

![](https://www.evernote.com/l/ABWwkNLfbklOh5YYM6k0boOjBenoOwM6GBYB/image.png)

### Detail screen of the log

Updated post:

![](https://www.evernote.com/l/ABVgRrfpi_5MAar-zDO_Q9V18F3hkhspV18B/image.png)

Last error of PHP:

![](https://www.evernote.com/l/ABUj6csA8ElG3q5hwXiSHrYTRFtQ0lGyX0MB/image.png)
