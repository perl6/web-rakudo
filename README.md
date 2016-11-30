# web-rakudo
Miscellaneous stuff for rakudo.org website



### download-page.*

Relevant files:

```
	download-page.css
	download-page.htaccess-sliver
	download-page.php
```

Controls download page display for `rakudo.org/downloads/[star|rakudo|nqp]/`.

The `download-page.htaccess-sliver` is the portion of `.htaccess` file that
needs to be inserted into the `.htaccess` file on the actual website. These
rewrites and redirects provide sane URLs that map to functions of the PHP
script.

Accepted query parameters:

* `asset`. Valid values: `star`, `rakudo`, `nqp`. Controls whether to display
star, rakudo, or nqp downloads.
* `latest`. When set, requests the script to redirect to the latest release
of `asset` deliverable in `latest` format. Valid values are determined by
`vars` key of the `$valid_pages` array hash for the particular `asset` and
whether a deliverable with that extension exists. Typical values would be
`.tar.gz`, `.dmg`, or `.msi`. If such an asset isn't found, the script will
proceed as if `latest` was not specified
