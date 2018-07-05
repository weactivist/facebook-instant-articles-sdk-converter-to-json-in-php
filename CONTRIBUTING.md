## Running Tests

``` bash
$ ./vendor/bin/phpunit
```

When doing a pull request, consider if this diff has a testcase that was covered in a wrong way or if it needs a new test case.


## Running PHP Code Sniffer

Run Code Sniffer against the `src/` and `tests/` directories.

``` bash
$ vendor/bin/phpcs --standard=phpcs.xml -p
```

Give a try for the autofixer for code style

``` bash
$ vendor/bin/phpcbf --standard-phpcs.xml -p
```
**Happy coding**!
