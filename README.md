# uBillboard

uBillboard used to be very popular image slider plugin for WordPress back in the day. It has since been superseeded by new and (probably) better slider plugins and I didn't have to maintain and support it so I just let it be. Now the time has come to opensource it. For the sake of community and the people who would like to contribute updates.

## Preview

You can see the demo here [http://code.udesignstudios.net/uBillboard/](http://code.udesignstudios.net/uBillboard/)

## Installation

Should be fairly straightforward, you can either copy the whole directory into `wp-content/plugins` and activate, or you can use the build system to obtain a codecanyon.net style plugin package with the development files stripped and resources minified. To do that run:

1. Install closure compiler:

```bash
brew install closure-compiler
```

2. Install YUI Compressor

```bash
brew install yuicompressor
```

3. Package uBillboard

```bash
ant package
```

## Contributions

While I no longer actively develop this project, contributions are welcome. Just send pull requests
