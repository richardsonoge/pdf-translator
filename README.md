PdfTranslator PHP
====================

Free PHP package for translating any PDF document with the shell command in collaboration with the [Stichoza library using the Google Translate API](https://github.com/Stichoza/google-translate-php). 

---

 - **[Features](#Features)**
 - **[Requirements](#Requirements)**
 - **[Installing](#Installing)**
 - **[Basic Usage](#basic-usage)**
 - **[Errors and Exception Handling](#errors-and-exception-handling)**
 - **[Known Limitations](#known-limitations)**
 - **[Disclaimer](#disclaimer)**
 - **[Donation](#donation)**

## Features

*pdf-translator* brings the full power of `pdftk`, `wkhtmltopdf` , `pdf2htmlEX`,  `qpdf`,  `xvfb` to PHP - and more.

* Consider PDF files as password-protected or not.
* Remove encryption protocols and passwords from translated PDFs.
* Set the number of pages for PDF translation. The default value is 100 pages.
* Specify the desired number of pages to divide your PDF file, to avoid excessive consumption of server resources. Default value: 20 pages.
* Define a pause in the translation process (optional). Default value is "1" second. 
* The result is a **translated HTML file** and a **translated PDF file**.

## Requirements

* `pdftk`: Manipulate PDF files, such as merge, split, etc. Necessary for certain operations in the PdfTranslator library.

* wkhtmltopdf`: Converts HTML and CSS to PDF. Essential for translating HTML content into PDF, a key feature of PdfTranslator.

* `pdf2htmlEX`: Converts PDF files to HTML. Essential for processing PDF content and translating it into different languages using PdfTranslator.

* `qpdf`: Provides tools for transforming and inspecting PDF files. Required for specific PDF operations in PdfTranslator.

* `xvfb`: X Virtual Framebuffer, used to run graphics applications without display. Required for screenless operation of wkhtmltopdf in PdfTranslator.

* `extension=dom`: The DOM extension for PHP, which supports the document object model. Essential for PdfTranslator's ability to process HTML and XML documents.

## Installing

1. Install the required PHP extensions by adding the following lines to your php.ini file:

    ```ini
    extension=dom
    extension=mbstring
    ```

2. Install additional dependencies for PDF translation:

    ```bash
    sudo apt-get update
    sudo apt-get install -y pdftk wkhtmltopdf pdf2htmlEX qpdf xvfb
    sudo apt-get upgrade -y
    ```

3. Require the package using [Composer](https://getcomposer.org):

    ```bash
    composer require richardsonoge/pdf-translator
    ```

> **Note**
> **PHP 8.0 or later** is required. Use following versions of this package for older PHP versions:

| Package version | PHP Version |
|-----------------|-------------|
| `^5.1`          | PHP >= 8.0  |
| `^4.1`          | PHP >= 7.1  |
| `^3.2`          | PHP < 7.1   |

## Basic Usage

Create GoogleTranslate object

```php
use Richardson\PdfTranslator\PdfTranslator;

$translationResult = new PdfTranslator();
$translationResult->setPdfFilePath('documents/freq.pdf')      // Set the path to the PDF file for translation  
    ->setTranslationLanguages('en', 'fr')                     // Set the translation languages (from, to) or (to) 
    ->fileExists()                                            // Check if the PDF file exists.
    ->ensureValidTranslationConditions()                      // Set the pages for PDF document translation. 
                                                              // Default is 100. You can set it yourself.
    ->splitPdfIntoPages()                                     // Split the PDF file into several smaller files. Default is "20".
    ->convertPdfToHtml()                                      // Convert each page from PDF to HTML.
    ->pauseTranslationProcess()                               // Pause the translation process (optional). Default is "1" second.
    ->getSortedHtmlFileList()                                 // Get a list of HTML files sorted in a Array.
    ->convertHtmlToText()                                     // Array to convert HTML files to text files.
    ->translateAllTextFiles()                                 // Translate all text files
    ->convertTextFileToArray()                                // Convert the Original TXT files and translation files into Array. 
    ->setOutputPath('translate/freq-fr.pdf')                  // Set the output path for the translated PDF
    ->translatePdfFile();                                     // Translate the PDF file

// Check if the translation was successful
if ($translationResult->getTranslationSuccess() === true) {

    // Get the paths to the translated files
    $paths = $translationResult->getTranslationPaths();
    
    // Capture the PDF and HTML output during translation.
    $htmlOutput = $translationResult->getHtmlOutput();

    // Display the links to the translated files
    echo $htmlOutput;

    $translationResult->cleanupFilesByPattern();                // Clean up files by pattern
    $translationResult->deleteOldTranslatedFiles();             // The translated PDF file will be deleted after one hour. 
                                                                // Default value: 3600 seconds. 
                                                                // Set your timeout for deletion of the translated file.
}
```
You can also use this second method to translate your PDF document.
```php
$translationResult = new PdfTranslator('documents/freq.pdf');  // Create a new instance of PdfTranslator 
                                                               // with the PDF file path as a constructor parameter
$translationResult->setTranslationLanguages('en', 'fr')        // Set the translation languages (from, to) or (to) 
    ->fileExists()                                             // Check if the PDF file exists.
    ->ensureValidTranslationConditions()                       // Set the pages for PDF document translation. 
                                                               // Default is 100. You can set it yourself.
    ->splitPdfIntoPages()                                      // Split the PDF file into several smaller files. Default is "20".
    ->convertPdfToHtml()                                       // Convert each page from PDF to HTML.
    ->pauseTranslationProcess()                                // Pause the translation process (optional). By default is "1" second.
    ->getSortedHtmlFileList()                                  // Get a list of HTML files sorted in an Array.
    ->convertHtmlToText()                                      // Array to convert HTML files to text files.
    ->translateAllTextFiles()                                  // Translate all text files
    ->convertTextFileToArray()                                 // Convert the Original TXT files and translation files into Array. 
    ->setOutputPath('translate/freq.pdf')                      // Set the output path for the translated PDF
    ->translatePdfFile();                                      // Translate the PDF file

// Check if the translation was successful
if ($translationResult->getTranslationSuccess() === true) {

    // Get the paths to the translated files
    $paths = $translationResult->getTranslationPaths();
    
    // Capture the PDF and HTML output during translation.
    $htmlOutput = $translationResult->->getHtmlOutput();

    // Display the links to the translated files
    echo $htmlOutput;

    $translationResult->cleanupFilesByPattern();                // Clean up files by pattern
    $translationResult->deleteOldTranslatedFiles();             // The translated PDF file will be deleted after one hour. 
                                                                // Default value: 3600 seconds. 
                                                                // Set your timeout for deletion of the translated file.
}
```
You can also use this `setTranslationLanguages()` function without adding the PDF document language:
```php
$translationResult->setTranslationLanguages('en')  
```
We can add a value in the `ensureValidTranslationConditions()` function to accept a larger PDF file:
```php
->ensureValidTranslationConditions(200) 
```
We can add a value to the `splitPdfIntoPages()` function to divide the PDF file into smaller pieces. 
For example, if we enter a value of `5`, the PDF will be divided into smaller 
pieces only if the number of pages is greater than `5`.
```php
->splitPdfIntoPages(5)  
```
You also need to know if you're not sending any number of pages. **By default**, it will be divided into `20` pages.

You can give a value to the `pauseTranslationProcess()` function to pause the translation process so that it runs smoothly and doesn't overload the server. But this is optional; you can remove it if you don't want to use it.
To change the value, you need to do this:
```php
->pauseTranslationProcess(3)  
```
You also need to know if you don't want to send seconds. **By default**, the number of seconds is 1.

We need to add the path of our new translated PDF file to the `setOutputPath()` function.
```php
->setOutputPath('translate/test.pdf')  
```
## Errors and Exception Handling
 
 - `Invalid output PDF file response`: This error tells us we need to add the output path for the translated PDF document. It must be in `string` and it must have the extension `.pdf` at the end of its output.
 - `Only "xxx" parameter is allowed for the "xxx" function` response: This error tells us that we need to add exactly the number of parameters that the "xxx" function requires.
 - `The language "target" must be a string.` response: This error indicates that we simply need to insert text.
 - `The language "source" must be a string.` response: This error indicates that we simply need to insert text.
 - `"xxx" function does not accept any arguments.` response: This error tells us that you shouldn't pass any arguments to this function.
 - `The PDF file "xxx" does not exist`: This error indicates that the path to the PDF file you specified for translation doesn't exist. Please ensure that the file path for the translation is correct.
 - `The maximum number of pages allowed must be an integer` : This error indicates that there is an error in the parameter of the function `ensureValidTranslationConditions()`. You must pass an integer value to indicate the maximum number of pages you can currently translate. 
 - `The PDF file has "xxx" pages, which exceeds the maximum allowed page count.` : This error indicates that the PDF document exceeds the number of pages we accept for translation. You should know that the default is `100`. If you want to increase the number of PDF pages accepted for translation, please pass an `int` value to this function `->ensureValidTranslationConditions()`.
 - `"xxx" function accepts at most "xxx" argument.` : This error indicates that the "xxx" function accepts only one argument and you shouldn't add any other parameters yet.
 - `Invalid argument in the "xxx" function. An array of PDF files is expected.` : This error indicates that the "xxx" function expects only an array of PDF files and nothing else to be returned in this function.
 - `The maximum number of seconds before the HTML file is translated must be an integer.` : This error indicates that you should only add an integer to the "xxx" function parameter. You should also know that the number you will add will be considered as the number of `Pause` you wish to take in the translation of your PDF document.
 - `The parameter to the "xxx" function must be an array.` : This error indicates that the argument in the parameter must be an Array only.
 - `The output path of your translated PDF file must be a string.` : This error indicates that the argument in the parameter must be an Array only.
 - `The path provided for the translated PDF file must have a valid path with the .pdf extension.` : This error indicates that the output path you provided does not have the .pdf extension. PLEASE ADD IT! Please add it!
 - `Invalid output PDF file. Please provide a valid path to save the translated HTML and PDF files.` : This error indicates that the argument you passed to the `setOutputPath()` function is indeed a `string`, but it's empty.
 - `The code "xxx" provided for the language of your PDF document is invalid. Please use the language code "xxx"` : If your language code does not exist among all Google Translate codes. It will automatically detect the correct language code for you in this error.
 - `The "xxx" language code you provided for your PDF document is invalid.` : This error indicates that this language code in Google Translate does not exist and that it could not give you the language code. PLEASE! You need to check the language code you provided for the source language of your document.
 - `The language code "xxx" you provided for the PDF translation is invalid. Please use this language code "xxx".` : If the language code "xxx" you provided for PDF translation does not exist among all Google Translate codes. In this case, Google Translate will automatically detect the correct language code. For example, it will automatically detect your language code if you only know the name of the language you want your PDF document translated into.
 - `The language code "xxx" provided for the translation of the PDF document is invalid.` : This error indicates that the language code for the PDF document translation in Google Translate does not exist and that it could not give you the language code. You need to check the language code you provided for the target language of your document.

## Known Limitations

 - **Translation limitations:** The `richardsonoge/pdf-translator` package addresses the limitations found in [Stichoza/google-translate-php](https://github.com/Stichoza/google-translate-php). By integrating and configuring a proxy, this package eliminates the translation limits imposed on your PDF documents and prevents Google from reporting your IP address. Although it doesn't guarantee complete anonymity, it does considerably improve translation capabilities over the original package.
 
## Disclaimer

**richardsonoge/pdf-translator** is provided "as is" without warranty of any kind. Use it at your discretion as it may fail at any time. The author is not responsible for any direct or indirect damage caused by this library. It is your responsibility to examine and respect the licenses of the libraries and tools used in this package. Always ensure that you have the necessary permissions and comply with applicable laws and terms of service when using external tools such as Google Translate and the [Stichoza](https://github.com/Stichoza/google-translate-php) package.

## Donation

If you find **richardsonoge/pdf-translator** helpful and want to support its development, consider donating. Your contribution helps ensure the maintenance and improvement of this open-source project. Every donation, no matter the size, is greatly appreciated. :)

 - [PayPal](https://www.paypal.me/richardsonoge)
 - [Patreon](https://www.patreon.com/richardsonoge)