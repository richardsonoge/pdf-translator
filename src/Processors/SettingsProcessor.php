<?php
namespace Richardson\PdfTranslator\Processors;
use DOMDocument;
use Richardson\PdfTranslator\Includes\Constants;
use Richardson\PdfTranslator\Exceptions\Exceptions;
use Richardson\PdfTranslator\Stichoza\Translator;

/**
 * SettingsProcessor Class
 *
 * This class, authored by Richardson Oge, serves as a comprehensive toolset for handling the translation and processing of PDF documents and HTML content. It encapsulates various methods to streamline tasks related to translating text within HTML files, converting HTML to PDF, decrypting PDF files, splitting PDFs, and managing translated content.

 * @package Richardson\PdfTranslator
 * @author Richardson Oge
 * @version 1.0.0
 * @link https://github.com/richardsonoge/PdfTranslator
 *
 * Methods:
 *
 * - `deleteDecryptedAndHtmlFiles($decryptedFilePdf, $outputHtmlPath)`: Deletes decrypted PDF and HTML files.
 * - `convertHtmlToPdf($outputHtmlSavePath, $outputPdfSavePath)`: Converts translated HTML to PDF.
 * - `saveTranslatedHtml($dom, $outputHtmlSavePath)`: Saves translated HTML to a file.
 * - `translateTextInHtml($dom, $htmlFiles, $dataFilesTextOriginalToArray, $dataFilesTextTranslateToArray)`: Translates text in an HTML file.
 * - `translateHtmlPdf($htmlFiles, $dataFilesTextOriginalToArray, $dataFilesTextTranslateToArray, $outputTranslateHTMLPath, $pdfFilePath, $to)`: Orchestrates the translation process.
 * - `getNumberOfPages($pdfFilePath)`: Gets the number of pages in the PDF file.
 * - `unlinkOldFiles($folderNames, $expirationTime)`: Deletes old files based on expiration time in specified folders.
 * - `deleteFilesByPatternInFolders($pdfFilePath, $folderNames)`: Deletes unnecessary files and folders based on a pattern.
 * - `delayTranslation($seconds)`: Delays execution for a specified number of seconds.
 * - `transformContentFileTxtToArray($arrayListFilesTextOriginal)`: Transforms content of text files to a multidimensional array.
 * - `translateAllFilesTxt($arrayListFilesTextOriginal, $outputTranslatePath, $commandTranslate, $to, $from)`: Translates content of text files and saves the results to new text files.
 * - `getSortedHtmlFiles($pdfFilePath, $absolutePath)`: Retrieves all HTML files starting with "unlocked_pdf_NAME" in the "split" folder.
 * - `pdfToHtml($decryptedFilePdf)`: Converts decrypted PDF to HTML.
 * - `decryptedFilePdf($pdfFilePath)`: Decrypts the PDF file.
 * - `splitPdf($pdfFilePath)`: Splits the PDF file into smaller files.
 * - `htmlToPdf($outputHtmlSavePath, $outputPdfSavePath, $additionalOptions = '')`: Converts HTML to PDF.
 * - `extractTextFromHTMLToArray($html)`: Extracts only text content from HTML, excluding JavaScript and CSS code.
 * - `htmltotext($htmlFile, $txtFilePath)`: Converts HTML to text using an external tool or library.
 * - `createHtmltotext($htmlFiles, $outPath, $command)`: Converts HTML files to plain text and saves the results to text files.
 */
class SettingsProcessor
{
    /**
     * Deletes decrypted PDF and HTML files.
     *
     * @param string $decryptedFilePdf  Path to the decrypted PDF file.
     * @param string $outputHtmlPath    Path to the HTML file.
     */
    public static function deleteDecryptedAndHtmlFiles($decryptedFilePdf, $outputHtmlPath)
    {
        if (file_exists($decryptedFilePdf) && file_exists($outputHtmlPath)) {
            unlink($decryptedFilePdf);
            unlink($outputHtmlPath);
        }
    }

    /**
     * Converts translated HTML to PDF.
     *
     * @param string $outputHtmlSavePath   Path to the translated HTML file.
     * @param string $outputPdfSavePath    Path to save the translated PDF file.
     *
     * @return string Path to the translated PDF file.
     */
    public static function convertHtmlToPdf($outputHtmlSavePath, $outputPdfSavePath)
    {
        // Add your htmlToPdf logic here and return the path to the translated PDF
        return self::htmlToPdf($outputHtmlSavePath, $outputPdfSavePath);
    }

    /**
     * Saves translated HTML to a file.
     *
     * @param DOMDocument $dom The DOMDocument object representing the HTML.
     * @param string $outputHtmlSavePath Path to save the translated HTML file.
     */
    public static function saveTranslatedHtml($dom, $outputHtmlSavePath)
    {
        $dom->saveHTMLFile($outputHtmlSavePath);
    }

    /**
     * Translates text in an HTML file.
     *
     * This function iterates through text nodes in the HTML file
     * and replaces original text with translated text if a match
     * is found in the provided translation arrays.
     *
     * @param DOMDocument $dom                       The DOMDocument object representing the HTML.
     * @param array       $htmlFiles                 Array of HTML filenames.
     * @param array       $dataFilesTextOriginalToArray Array of original text data.
     * @param array       $dataFilesTextTranslateToArray Array of translated text data.
     */
    public static function translateTextInHtml($dom, $htmlFiles, $dataFilesTextOriginalToArray, $dataFilesTextTranslateToArray)
    {
        $xpath = new \DOMXPath($dom);
        $textNodes = $xpath->query('//text()');

        foreach ($textNodes as $textNode) {
            $originalText = $textNode->nodeValue;

            foreach ($htmlFiles as $key => $htmlFile) {
                
                $translatePartOriginal = isset($dataFilesTextOriginalToArray[$key]) ? $dataFilesTextOriginalToArray[$key] : [];
                $translatePartTranslate = isset($dataFilesTextTranslateToArray[$key]) ? $dataFilesTextTranslateToArray[$key] : [];

                if (is_array($translatePartOriginal) && in_array($originalText, $translatePartOriginal)) {
                    $index = array_search($originalText, $translatePartOriginal);

                    if ($index !== false && array_key_exists($index, $translatePartTranslate)) {
                        $translatedText = $translatePartTranslate[$index];
                        $textNode->nodeValue = $translatedText;
                        break;
                    }

                }
            }
        }
    }

    /**
     * Orchestrates the translation process.
     *
     * @param array  $htmlFiles                    Array of HTML filenames.
     * @param array  $dataFilesTextOriginalToArray  Array of original text data.
     * @param array  $dataFilesTextTranslateToArray Array of translated text data.
     * @param string $outputTranslateHTMLPath      Output path for translated HTML files.
     * @param string $pdfFilePath                  Path to the PDF file.
     * @param string $to                            Language code for translation.
     *
     * @return array Array containing paths to the translated HTML and PDF files.
     */
    public static function translateHtmlPdf($htmlFiles, $dataFilesTextOriginalToArray, $dataFilesTextTranslateToArray, $pathSaveOutputPdf, $pdfFilePath)
    {
        // Array to store paths to translated HTML files
        $pathUrlTranslateHtml = [];

        $pathSaveOutputHtml = str_replace('.pdf', '.html', $pathSaveOutputPdf);

        // Decrypt the PDF file and convert it to HTML
        $decryptedFilePdf = self::decryptedFilePdf($pdfFilePath);
        $outputHtmlPath = self::pdfToHtml($decryptedFilePdf);

        // Load the original HTML content
        $dom = new DOMDocument;
        $dom->loadHTMLFile($outputHtmlPath);

        // Translate text in the HTML file
        self::translateTextInHtml($dom, $htmlFiles, $dataFilesTextOriginalToArray, $dataFilesTextTranslateToArray);

        // Extract the filename without extension
        $filenameWithoutExtension = pathinfo($pdfFilePath, PATHINFO_FILENAME);
        
        // Save the translated HTML file
        $pathUrlTranslateHtml[] = $pathSaveOutputHtml;
        self::saveTranslatedHtml($dom, $pathSaveOutputHtml);

        // Array to store paths to converted HTML files
        $convertHtmlToPdfArray = [];

        // Check if there are paths in $pathUrlTranslateHtml
        if (!empty($pathUrlTranslateHtml)) {
            
            // Convert the translated HTML to PDF
            $pathUrlTranslateHtml[] = self::convertHtmlToPdf($pathSaveOutputHtml, $pathSaveOutputPdf);
        }

        // Delete decrypted PDF and HTML files
        self::deleteDecryptedAndHtmlFiles($decryptedFilePdf, $outputHtmlPath);

        return $pathUrlTranslateHtml;
    }

    /**
     * Get the number of pages in the PDF file.
     *
     * @param string $pdfFilePath The path to the PDF file.
     * @return int The number of pages in the PDF file.
     */
    public static function getNumberOfPages($pdfFilePath)
    {
        // Use pdftk to get the number of pages in the PDF file
        $pageCount = exec("pdftk $pdfFilePath dump_data | grep NumberOfPages | cut -d ' ' -f2");

        return (int)$pageCount;
    }

    /**
     * Unlink (delete) files in specified folders that are older than a specified expiration time.
     *
     * This static method iterates through each folder specified in $folderNames,
     * retrieves the list of files in each folder, and deletes files that are older
     * than the specified $expirationTime. It returns true if any files were deleted,
     * and false otherwise.
     *
     * @param array|string $folderNames An array of folder names or a single folder name.
     * @param int $expirationTime The expiration time in seconds.
     * @return bool True if any files were deleted, false otherwise.
     */
    public static function unlinkOldFiles($folderNames, $expirationTime)
    {
        // Flag to track if any files were deleted
        $filesDeleted = false;

        // Iterate through each folder
        foreach ((array)$folderNames as $folderName) {
            // Get the list of files in the folder
            $files = scandir($folderName);

            if ($files !== false) {
                // Iterate through each file and check if it's older than the expiration time
                foreach ($files as $file) {
                    // Check if the file is a regular file and not a directory
                    $filePath = $folderName . DIRECTORY_SEPARATOR . $file;
                    if (is_file($filePath) && filemtime($filePath) < time() - $expirationTime) {
                        // Delete the file
                        unlink($filePath);
                        $filesDeleted = true;
                    }
                }
            }
        }

        // Return true if any files were deleted, false otherwise
        return $filesDeleted;
    }

    /**
     * Delete files in specified folders that match a specified text pattern.
     *
     * This static method iterates through each folder specified in $folderNames,
     * retrieves the list of files in each folder, and deletes files that match the
     * specified text pattern. It returns true if any files were deleted, and false otherwise.
     *
     * @param array|string $folderNames An array of folder names or a single folder name.
     * @param string $textPattern The text pattern to match filenames against.
     * @return bool True if any files were deleted, false otherwise.
     */
    public static function deleteFilesByPatternInFolders($folderNames, $textPattern)
    {
        // Flag to track if any files were deleted
        $filesDeleted = false;

        // Iterate through each folder
        foreach ((array)$folderNames as $folderName) {
            // Get the list of files in the folder
            $files = scandir($folderName);

            if ($files !== false) {
                // Iterate through each file
                foreach ($files as $file) {
                    // Check if the file is a regular file and not a directory
                    $filePath = $folderName . DIRECTORY_SEPARATOR . $file;
                    if (is_file($filePath)) {
                        // Use a regular expression to match filenames containing the specified text pattern
                        if (preg_match("/$textPattern/iu", $file)) {
                            // Delete the file
                            unlink($filePath);
                            $filesDeleted = true;
                        }
                    }
                }
            }
        }

        // Return true if any files were deleted, false otherwise
        return $filesDeleted;
    }

    /**
     * Delays the execution of code for a specified number of seconds.
     *
     * This function uses the sleep function to introduce a delay in the execution
     * of PHP code, allowing for a pause in the script's execution.
     *
     * @param int $seconds The number of seconds to delay the execution.
     *
     * @return void
     */
    public static function delayTranslation($seconds) {
        // Use the sleep function to introduce a delay.
        sleep($seconds);
    }

    /**
     * Transform content of text files to a multidimensional array.
     *
     * @param array $arrayListFilesTextOriginal An array of file paths containing text content.
     *
     * @return array The transformed multidimensional array.
     */
    public static function transformContentFileTxtToArray($arrayListFilesTextOriginal) {
        // Initialize the array to store the transformed data
        $dataContentTxtOriginalArray = [];

        // Iterate through each file path in the input array
        foreach ($arrayListFilesTextOriginal as $file) {

            // Read the content of the file
            $txtContent = file_get_contents($file);

            // Initialize an array to store the original text lines
            $originalTxtArray = [];

            // Split the text into an array by line breaks
            $lines = explode("\n", $txtContent);

            // Iterate through each line and add it to the originalTxtArray
            foreach ($lines as $row) {
                $originalTxtArray[] = $row;
            }

            // Remove empty values from the array
            $newOriginalTxtArray = array_filter($originalTxtArray);

            // Re-index the array if needed
            $newOriginalTxtArray = array_values($newOriginalTxtArray);

            // Add the transformed array for the current file to the main result array
            $dataContentTxtOriginalArray[] = $newOriginalTxtArray;
        }

        // Return the final multidimensional array
        return $dataContentTxtOriginalArray;
    }

    /**
     * Translate content of text files and save the results to new text files.
     *
     * @param array  $arrayListFilesTextOriginal An array of paths to original text files.
     * @param string $outputTranslatePath        The output path for the translated text files.
     * @param string $commandTranslate           A prefix for the generated translated text file names.
     * @param string $to                         The target language code for translation.
     * @param string $from                       The source language code for translation.
     *
     * @return array An array of paths to the created translated text files.
     */
    public static function translateAllFilesTxt($arrayListFilesTextOriginal, $outputTranslatePath, $commandTranslate, $to, $from) {

        $txtFilesTranslatePath = []; // Initialize an array to store paths of translated text files

        foreach ($arrayListFilesTextOriginal as $txtFile) {
            // Read the content of the original text file
            $txtContent = file_get_contents($txtFile);

            // Split the text into chunks
            $stringSplit = str_split($txtContent, Constants::NUMBER_CHARACTERS_GOOGLE);
            $countStringSplit = count($stringSplit);

            $translatedTextChunks = [];

            for ($i = 0; $i < $countStringSplit; $i++) {
                // Translate each chunk of text
                $translateText = Translator::translate($stringSplit[$i], $from, $to);

                // Check if the translation is successful, and use the translated text or the original
                $translatedTextChunks[] = $stringSplit[$i] != $translateText ? $translateText : $stringSplit[$i];
            }

            // Concatenate the translated chunks into a single string
            $translatedText = implode('', $translatedTextChunks);

            // Extract the filename without extension
            $filenameWithoutExtension = pathinfo($txtFile, PATHINFO_FILENAME);
            $filenameOriginal = $commandTranslate . $filenameWithoutExtension . '.txt';
            $outputTranslateFilePath = $outputTranslatePath . $filenameOriginal;

            // Create the translation output file if it doesn't exist
            if (!file_exists($outputTranslateFilePath)) {
                fopen($outputTranslateFilePath, "w");
            }

            // Write the translated content to the translation output file
            if (file_put_contents($outputTranslateFilePath, $translatedText)) {
                $txtFilesTranslatePath[] = $outputTranslateFilePath; // Add the path to the translated text file to the array
            }
        }

        return $txtFilesTranslatePath; // Return an array of paths to created translated text files
    }

    /**
     * Retrieve all HTML files starting with "unlocked_pdf_NAME" in the "split" folder.
     * 
     * @param string $pdfFilePath   The path to the PDF file.
     * @param string $absolutePath  The absolute path to the root folder.
     * 
     * @return array An array of sorted HTML file paths.
     */
    public static function getSortedHtmlFiles($pdfFilePath, $absolutePath) {
        $folderPath = $absolutePath.Constants::FOLDER_SPLIT;

        // Extract the filename without extension
        $filenameWithoutExtension = pathinfo($pdfFilePath, PATHINFO_FILENAME);

        $pattern = $folderPath . 'unlocked_pdf_'.$filenameWithoutExtension.'*.html';
        
        // Get all HTML files matching the pattern
        $htmlFiles = glob($pattern);
        
        // Sort the HTML files in ascending order
        sort($htmlFiles);

        return $htmlFiles;
    }

    /**
     * Converts decrypted PDF to HTML.
     *
     * @param string $decryptedFilePdf Path to the decrypted PDF file.
     *
     * @return string Path to the generated HTML file.
     */
    public static function pdfToHtml($decryptedFilePdf)
    {
        if (is_array($decryptedFilePdf)) {
            foreach ($decryptedFilePdf as $value) {
                // Extract the filename without extension
                $filenameWithoutExtension = pathinfo($value, PATHINFO_FILENAME);

                // Define the output HTML path
                $outputHtmlPath = Constants::FOLDER_SPLIT.$filenameWithoutExtension.'.html'; // Replace with your desired HTML output path

                // Use pdf2htmlEX to convert the unlocked PDF to HTML
                $command = "pdf2htmlEX --process-outline 0 --fit-width 1024 --space-as-offset 1 {$value} {$outputHtmlPath}";
                
                // Execute the command
                exec($command);
            }
        } else {
            // Extract the filename without extension
            $filenameWithoutExtension = pathinfo($decryptedFilePdf, PATHINFO_FILENAME);

            if (!file_exists(Constants::FOLDER_TRANSLATE)) {
                mkdir(Constants::FOLDER_TRANSLATE, 0777, true);
            }

            // Define the output HTML path
            $outputHtmlPath = Constants::FOLDER_TRANSLATE.'/'.$filenameWithoutExtension.'.html'; // Replace with your desired HTML output path

            // Use pdf2htmlEX to convert the unlocked PDF to HTML
            $command = "pdf2htmlEX --process-outline 0 --fit-width 1024 --space-as-offset 1 {$decryptedFilePdf} {$outputHtmlPath}";
            
            // Execute the command
            exec($command);

            return $outputHtmlPath;
        }
    }

    /**
     * Decrypts the PDF file.
     *
     * @param string $pdfFilePath Path to the PDF file.
     *
     * @return string Path to the decrypted PDF file.
     */
    public static function decryptedFilePdf($pdfFilePath)
    {
        // Check if more than one parameter is provided
        Exceptions::validateCountArgument(1, func_get_args(), 'Only one parameter is allowed for decryptedFilePdf function.');

        // Extract the filename without extension
        $filenameWithoutExtension = pathinfo($pdfFilePath, PATHINFO_FILENAME);

        // Define the HTML output path based on the extracted filename
        $outputHtmlPath = $filenameWithoutExtension.'.html'; // Replace with the desired HTML output path

        // Create a temporary file for the unlocked PDF
        $decryptedFilePath = tempnam(sys_get_temp_dir(), 'unlocked_pdf_'.$filenameWithoutExtension);

        // Use qpdf to remove password protection and encryption
        $qpdfDecryptCommand = "qpdf --decrypt {$pdfFilePath} {$decryptedFilePath}";
        exec($qpdfDecryptCommand, $output, $returnCode);

        // Check if the decryption was successful
        if ($returnCode === 0) {
            // Decryption successful, return true
            return $decryptedFilePath;
        } else {
            // Decryption failed, throw an exception with an error message
            throw new Exception("Failed to decrypt the PDF file '{$this->pdfFilePath}'.");
        }

    }

    /**
     * Split the PDF file into smaller files.
     *
     * @param string $pdfFilePath The path to the PDF file.
     * @return array The result of splitting the PDF file.
     */
    public static function splitPdfs($pdfFilePath, $splitOfPages, $absolutePath)
    {
        // Check if any argument is provided
        Exceptions::validateCountArgument(3, func_get_args(), 'splitPdfs function accepts at most three argument.');
        
        // Check if the argument is an int.
        Exceptions::validateIntArgument($splitOfPages, 'The maximum allowed number of pages must be an integer.');

        // Get information about the original PDF file
        $path_info = pathinfo($pdfFilePath);

        // Create a temporary file for the unlocked PDF
        $unlockedPdf02 = tempnam(sys_get_temp_dir(), 'unlocked_pdf_');

        // Use qpdf to remove password protection and encryption
        $qpdfDecryptCommand = "qpdf --decrypt {$pdfFilePath} {$unlockedPdf02}";
        exec($qpdfDecryptCommand);

        // Check the number of pages using pdftk
        $pageCount = exec("pdftk $unlockedPdf02 dump_data | grep NumberOfPages | cut -d ' ' -f2");

        // Initialize the result array
        $result = [];

        if ($pageCount > $splitOfPages) {
            // Split into parts if more than $splitOfPages pages
            $parts = ceil($pageCount / $splitOfPages);

            for ($part = 1; $part <= $parts; $part++) {
                // Calculate the start and end page for each part
                $startPage = 1 + (($part - 1) * $splitOfPages);
                $endPage = min($part * $splitOfPages, $pageCount);

                // Use pdftk to extract specific pages
                $new_filename = $absolutePath . '/files/split/' . $path_info['filename'] . '_part' . $part . ".pdf";
                exec("pdftk $unlockedPdf02 cat $startPage-$endPage output $new_filename");

                // Add the new filename to the result array
                $result[] = $new_filename;
            }
        } else {
            // Less than or equal to $splitOfPages pages, return the original PDF
            $result[] = $pdfFilePath;
        }

        // Clean up temporary files
        unlink($unlockedPdf02);

        // Return the result array
        return $result;
    }

    /**
     * Converts HTML to PDF.
     *
     * @param string $outputHtmlSavePath Path to the HTML file.
     * @param string $outputPdfSavePath  Path to save the PDF file.
     *
     * @return string Path to the generated PDF file.
     */
    private static function htmlToPdf($outputHtmlSavePath, $outputPdfSavePath, $additionalOptions = '')
    {
        // Logic HTML to PDF conversion and return the path to the PDF file

        // Set font options
        $fontOptions = "--no-images --quiet --dpi 300";

        // Use wkhtmltopdf to convert HTML to PDF with font and path options
        $command = "xvfb-run -a wkhtmltopdf {$fontOptions} {$additionalOptions} {$outputHtmlSavePath} {$outputPdfSavePath}";
        exec($command);

        // Check if the conversion was successful
        if (file_exists($outputPdfSavePath)) {
            return $outputPdfSavePath;
        } else {
            return '';
        }

    }

    /**
     * Extracts only text content from HTML, excluding JavaScript and CSS code.
     *
     * This function takes an HTML string as input and performs the following steps:
     * 1. Removes all script and style tags, along with their content, to exclude JavaScript and CSS code.
     * 2. Extracts only visible text content from the HTML.
     * 3. Trims whitespace from each text fragment.
     *
     * @param string|null $html The input HTML string from which to extract text.
     *
     * @return array An array containing visible text fragments extracted from the HTML.
     */
    public static function extractTextFromHTMLToArray($html)
    {
        // Check if $html is null
        if ($html === null) {
            return [];
        }

        // Create a DOMDocument
        $dom = new DOMDocument;

        // Load HTML content, ignoring errors
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        // Remove script and style elements
        $scriptNodes = $dom->getElementsByTagName('script');
        foreach ($scriptNodes as $scriptNode) {
            $scriptNode->parentNode->removeChild($scriptNode);
        }

        $styleNodes = $dom->getElementsByTagName('style');
        foreach ($styleNodes as $styleNode) {
            $styleNode->parentNode->removeChild($styleNode);
        }

        // Extract only visible text content
        $texts = [];
        $body = $dom->getElementsByTagName('body')->item(0);

        if ($body) {
            foreach ($body->childNodes as $childNode) {
                if ($childNode->nodeType === XML_TEXT_NODE) {
                    $texts[] = $childNode->nodeValue;
                } elseif ($childNode->nodeType === XML_ELEMENT_NODE) {
                    // Extract text content from nested elements
                    $texts = array_merge($texts, self::extractTextFromElement($childNode));
                }
            }
        }

        // Trim whitespace from each text fragment
        $texts = array_map('trim', $texts);

        // Remove empty fragments
        $texts = array_filter($texts);

        return array_values($texts);
    }

    /**
     * Extracts text content from an XML element.
     *
     * @param DOMElement $element The XML element.
     *
     * @return array An array containing text fragments extracted from the element.
     */
    private static function extractTextFromElement($element)
    {
        $texts = [];

        foreach ($element->childNodes as $childNode) {
            if ($childNode->nodeType === XML_TEXT_NODE) {
                $texts[] = $childNode->nodeValue;
            } elseif ($childNode->nodeType === XML_ELEMENT_NODE) {
                // Recursively extract text content from nested elements
                $texts = array_merge($texts, self::extractTextFromElement($childNode));
            }
        }

        return $texts;
    }

    /**
     * Convert HTML to text using an external tool or library.
     *
     * @param string $htmlFile   Path to the HTML file.
     * @param string $txtFilePath Path to save the text file.
     *
     * @return bool True on success, false on failure.
     */
    private static function htmltotext($htmlFile, $txtFilePath)
    {
        // Read the HTML content from the file
        $htmlContent = file_get_contents($htmlFile);

        // Extract text from HTML and store in an array
        $texts = self::extractTextFromHTMLToArray($htmlContent);

        // Remove empty values from the array
        $arrayTxt = array_filter($texts);

        // Re-index the array if needed
        $arrayTxts = array_values($arrayTxt);

        // Concatenate the extracted text with new lines
        $extractedText = '';
        foreach ($arrayTxts as $text) {
            $extractedText .= $text . PHP_EOL;
        }

        if (!file_exists(dirname($txtFilePath))) {
            mkdir(dirname($txtFilePath), 0777, true);
        }

        // Save the extracted text to a TXT file
        if (file_put_contents($txtFilePath, $extractedText)) {
            // Return true on successful file write
            return true;
        } else {
            // Return false if file write fails
            return false;
        }
        // The following line is unreachable and can be removed
        // return true;
    }

    /**
     * Convert HTML files to plain text using htmltotext and save the results to text files.
     *
     * @param array  $htmlFiles An array of HTML file paths.
     * @param string $outPath   The output path for the text files.
     * @param string $command   A prefix for the generated text file names.
     *
     * @return array|bool An array of paths to the created text files on success, or false on failure.
     */
    public static function createHtmltotext($htmlFiles, $outPath, $command)
    {
        $txtFilesPath = []; // Initialize an array to store paths of created text files

        foreach ($htmlFiles as $htmlFile) {
            // Extract the filename without extension
            $filenameWithoutExtension = pathinfo($htmlFile, PATHINFO_FILENAME);
            $filenameOriginal = $command . $filenameWithoutExtension . '.txt';
            $txtFilePath = $outPath . $filenameOriginal;

            // Convert HTML to text using the htmltotext function
            $htmltotext = self::htmltotext($htmlFile, $txtFilePath);

            // Check if the conversion was successful
            if ($htmltotext === true) {
                $txtFilesPath[] = $txtFilePath; // Add the path to the created text file to the array
            }
        }

        return $txtFilesPath; // Return an array of paths to created text files or false if none were created
    }

}

