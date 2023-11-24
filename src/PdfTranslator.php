<?php
namespace Richardson\PdfTranslator;
use Richardson\PdfTranslator\Includes\Constants;
use Richardson\PdfTranslator\Exceptions\Exceptions;
use Richardson\PdfTranslator\Processors\SettingsProcessor;

/**
 * PdfTranslator Class
 *
 * This class, authored by [Your Name], facilitates the translation process for PDF documents. It provides a set of methods to handle tasks such as setting the PDF file path, validating file existence and page count, translating content, and managing output paths.

 * @package Richardson\PdfTranslator
 * @author Richardson Oge
 * @version 1.0.0
 * @link https://github.com/richardsonoge/PdfTranslator
 *
 * Properties:
 *
 * - `private $absolutePath`: The absolute path of the current script's directory.
 * - `private $pdfFilePath`: Path to the PDF file.
 * - `private $source`: Source language.
 * - `private $target`: Target language.
 * - `private $htmlFiles`: Array of HTML files.
 * - `private $dataFilesTextOriginalToArray`: Array containing paths to original text files.
 * - `private $dataFilesTextTranslateToArray`: Array containing paths to translated text files.
 * - `private $saveAs`: Output path for translated PDF file.
 *
 * Methods:
 *
 * - `__construct($pdfFilePath = null, $baseDir = __DIR__)`: Initializes the PdfTranslator class.
 * - `setPdfFilePath($pdfFilePath): PdfTranslator`: Sets the PDF file path.
 * - `setTranslationLanguages(?string $source = null, ?string $target = null): PdfTranslator`: Sets source and target languages.
 * - `fileExists(): bool`: Checks if the file exists.
 * - `validatePageCount(?int $maxPagesDocTrans = null): bool`: Validates the number of pages in the PDF.
 * - `getDecryptedPdfFilePath(): string`: Decrypts the PDF file if encrypted.
 * - `splitPdfIntoPages(?int $maxPagesPerFile = null): array`: Splits the PDF file into smaller files.
 * - `convertPdfToHtml($pdfFiles): array`: Converts PDF files to HTML.
 * - `deleteTempOriginalPdfFile(): bool`: Deletes the temporary decrypted PDF file.
 * - `pauseTranslationProcess(?int $seconds = null): mixed`: Delays the translation process.
 * - `getSortedHtmlFileList(): array`: Gets sorted HTML files associated with a PDF file.
 * - `convertHtmlToText(?array $htmlFiles = null): array`: Creates text files from HTML files.
 * - `translateAllTextFiles(?array $arrayListFilesTextOriginal = null)`: Translates all text files.
 * - `convertTextFileToArray(?array $arrayListFilesTextTranslate = null): PdfTranslator`: Transforms text files into arrays.
 * - `translatePdfFile(): array`: Translates the content of a PDF file.
 * - `setOutputPath($saveAs): PdfTranslator`: Sets the output path for the translated PDF file.
 * - `cleanupFilesByPattern(): void`: Deletes unnecessary files and folders.
 * - `deleteOldTranslatedFiles(bool $shouldUnlink = true): void`: Unlinks old translated files.
 */

class PdfTranslator
{
    /**
     * Get the absolute path of the current script's directory.
     *
     * @var string $absolutePath The absolute path of the current script's directory.
     */
    private $absolutePath;

    /**
     * Path to the PDF file.
     *
     * @var string|null $pdfFilePath The path to the PDF file.
     */
    private $pdfFilePath;

    private $source;

    private $target;

    private $htmlFiles;

    private $createHtmlToTextResult;

    private $dataFilesTextOriginalToArray;

    private $dataFilesTextTranslateToArray;

    private $saveAs;

    private $decryptedPdfFilePath;

    private $resultPdfSplitInArray;

    private $arrayListFilesTextTranslate;

    private $success;

    private $translatedPdfPaths;

    /**
     * Initialize the Translate class.
     *
     * @param string|null $pdfFilePath The path to the PDF file (optional).
     */
    public function __construct($pdfFilePath = null, $baseDir = null)
    {
        // Use the provided $baseDir or default to dirname(__DIR__)
        $this->absolutePath = $baseDir ?? dirname(__DIR__);
        if ($pdfFilePath !== null) {
            $this->setPdfFilePath($pdfFilePath); // Validate PDF file path
        }
        $this->pdfFilePath = $pdfFilePath;
    }

    private function getSource(): string
    {
        return $this->source;
    }

    private function getTarget(): string
    {
        return $this->target;
    }

    private function getHtmlFiles(): array
    {
        return $this->htmlFiles;
    }

    private function getCreateHtmlToTextResult(): array
    {
        return $this->createHtmlToTextResult;
    }

    private function getDataFilesTextOriginalToArray(): array
    {
        return $this->dataFilesTextOriginalToArray;
    }

    private function getDataFilesTextTranslateToArray(): array
    {
        return $this->dataFilesTextTranslateToArray;
    }

    private function getSetOutputPath(): string
    {
        return $this->saveAs;
    }

    /**
     * Get the full path to the PDF file.
     *
     * @return string The full path to the PDF file.
     */
    private function getFullPdfPath(): string
    {
        return $this->absolutePath . '/' . $this->pdfFilePath;
    }
    
    /**
     * Decrypt the PDF file if encrypted.
     *
     * @return string The path to the decrypted PDF file.
     * @throws Exception If any argument is provided.
     */
    private function getDecryptedPdfFilePath(): string
    {
        return $this->decryptedPdfFilePath;
    }

    private function getResultPdfSplitInArray(): array
    {
        return $this->resultPdfSplitInArray;
    }
    
    private function getArrayListFilesTextTranslate(): array
    {
        return $this->arrayListFilesTextTranslate;
    }

    /**
     * Check if the translation was successful.
     *
     * @return bool Whether the translation was successful or not.
     */
    public function getSuccess(): bool
    {
        // Assuming that success is a property in your class
        return $this->success;
    }

    /**
     * Get the paths associated with the translation.
     *
     * @return array The paths associated with the translation.
     */
    public function getPaths(): array
    {
        // Assuming that paths is a property in your class
        return $this->translatedPdfPaths;
    }

    /**
     * Set the PDF file path.
     *
     * @param string $pdfFilePath The path to the PDF file.
     * @return $this
     */
    public function setPdfFilePath($pdfFilePath): PdfTranslator
    {

        Exceptions::validateCountArgument(1, func_get_args(), 'Only one parameter is allowed for the setPdfFilePath function.');

        // Check if the file has a PDF extension
        if (!is_string($pdfFilePath) || strtolower(pathinfo($pdfFilePath, PATHINFO_EXTENSION)) !== 'pdf') {
            throw new \InvalidArgumentException('Invalid PDF file. Please provide a valid PDF file path.');
        }

        $this->pdfFilePath = $pdfFilePath;
        return $this;
    }

    /**
     * Translates the source content to the target language.
     *
     * @param string|null $source The source language.
     * @param string|null $target The target language.
     *
     * @return $this The current Translate instance for method chaining.
     */
    public function setTranslationLanguages(?string $source = null, ?string $target = null): PdfTranslator
    {
        // Check if only one parameter is provided
        if (func_num_args() === 1) {
            // Assume the single parameter as $target and set $source to an empty string
            $target = $source ?? $target;
            $source = '';
        }

        // Validate target language parameter
        Exceptions::validateStringArgument($target, 'The language "target" must be a string.');

        // Validate source language parameter
        Exceptions::validateStringArgument($source, 'The language "source" must be a string.');

        // Check if $source is a language code
        Exceptions::handleSourceLanguageException($source);

        // Similar check for $target if it is provided
        Exceptions::handleTargetLanguageException($target);

        // Set the source and target properties of the Translate instance
        $this->source = $source;
        $this->target = $target;

        // Return the current instance for method chaining
        return $this;
    }

    /**
     * Check if the file exists.
     *
     * @return bool
     */
    public function fileExists()
    {
        // Check if any argument is provided
        Exceptions::validateCountArgument(0, func_get_args(), 'fileExists function does not accept any arguments.');

        // Check if the file exists
        $result = file_exists($this->getFullPdfPath());

        if (!$result) {
            throw new Exception("The PDF file '{$this->getFullPdfPath()}' does not exist.");
        }

        return $this;
    }

    /**
     * Ensures that the PDF file meets the criteria for translation and decryption based on the specified page count.
     *
     * @param int|null $maxAllowedPages - The maximum number of pages allowed for translation and decryption.
     *
     * @return PdfTranslator - The current PdfTranslator instance for method chaining.
     * @throws Exception - If the number of pages exceeds the specified threshold.
     */
    public function ensureValidTranslationConditions(?int $maxAllowedPages = null): self
    {
        // Ensure only one parameter is provided
        Exceptions::validateCountArgument(1, func_get_args(), 'Only one parameter is allowed for the ensureValidTranslationConditions function.');

        $this->fileExists(); // Check if the file exists

        // Validate that the provided page count is an integer
        Exceptions::validateIntArgument($maxAllowedPages, 'The maximum number of pages allowed must be an integer.');

        // Get the path to the decrypted PDF file
        $decryptedPdfFilePath = SettingsProcessor::decryptedFilePdf($this->getFullPdfPath());
        $this->decryptedPdfFilePath = $decryptedPdfFilePath;

        // Get the number of pages in the PDF file
        $numberOfPages = $this->getNumberOfPages($decryptedPdfFilePath);

        // Check if a maximum page count is set
        if (!is_null($maxAllowedPages)) {
            // Check if the number of pages exceeds the specified threshold
            if ($numberOfPages > $maxAllowedPages) {
                throw new \Exception("The PDF file has $numberOfPages pages, which exceeds the maximum allowed page count.\n");
            }
        } else {
            // Check if the number of pages exceeds the default threshold
            if ($numberOfPages > Constants::DEFAULT_MAX_PAGES) {
                throw new \Exception("The PDF file has $numberOfPages pages, which exceeds the default maximum page count.\n");
            }
        }

        return $this;
    }

    /**
     * Get the number of pages in the PDF file.
     *
     * @param string $pdfFilePath The path to the PDF file.
     * @return int The number of pages in the PDF file.
     */
    private function getNumberOfPages($pdfFilePath)
    {
        // Check if more than one parameter is provided
        Exceptions::validateCountArgument(1, func_get_args(), 'Only one parameter is allowed for the getNumberOfPages function.');

        return SettingsProcessor::getNumberOfPages($pdfFilePath);
    }

    /**
     * Split the PDF file into smaller files.
     *
     * @param string $pdfFilePath The path to the PDF file.
     * @return array The result of splitting the PDF file.
     */
    public function splitPdfIntoPages(?int $maxPagesPerFile = null)
    {
        // Check if any argument is provided
        Exceptions::validateCountArgument(1, func_get_args(), 'splitPdfIntoPages function accepts at most one argument.');

        Exceptions::validateIntArgument($maxPagesPerFile, 'The maximum number of pages allowed in the splitPdfIntoPages function must be an integer.');


        // Set $split based on the provided or default value
        $splitOfPages = !is_null($maxPagesPerFile) ? $maxPagesPerFile : Constants::SPLIT_OF_PAGES;
        
        $absoluteSplitPath = Constants::PATH_LIBRARY.Constants::FOLDER_SPLIT;
        $resultPdfSplitInArray = SettingsProcessor::splitPdfs($this->getDecryptedPdfFilePath(), $splitOfPages, $absoluteSplitPath);
    
        $this->resultPdfSplitInArray = $resultPdfSplitInArray;

        return $this;
    }

    /**
     * Convert PDF files to HTML using pdf2htmlEX.
     *
     * @param array $pdfFiles The array of PDF files to convert to HTML.
     * @return array The array of HTML files generated from the PDF files.
     * @throws Exception If an error occurs during the PDF to HTML conversion.
     */
    public function convertPdfToHtml()
    {

        // Validate the number of arguments
        Exceptions::validateCountArgument(0, func_get_args(), 'convertPdfToHtml function does not accept any arguments.');

        $pdfFiles = $this->getResultPdfSplitInArray();

        // Check if the provided argument is an array
        Exceptions::checkIsArray($pdfFiles, 'Invalid argument in the getResultPdfSplitInArray function. An array of PDF files is expected.');

        // Convert the PDF files to HTML
        SettingsProcessor::pdfToHtml($pdfFiles, Constants::PATH_LIBRARY);

        return $this;
    }

    /**
     * Delay the translation process for a specified number of seconds.
     *
     * This method introduces a delay in the translation process, allowing for time-sensitive
     * tasks to be completed before initiating the translation. It accepts an optional argument
     * specifying the number of seconds to delay, with a default value defined in the Constants class.
     * It leverages the SettingsProcessor class to handle the actual delay logic.
     *
     * @param int|null $seconds The number of seconds to delay translation (optional).
     *                          If not provided, the default delay from Constants will be used.
     * @return mixed The result of the delay operation, typically true if successful.
     * @throws Exception If more than one argument is provided.
     *                   If the specified number of seconds is not an integer.
     */
    public function pauseTranslationProcess(?int $seconds = null)
    {
        // Check if any argument is provided
        Exceptions::validateCountArgument(1, func_get_args(), 'pauseTranslationProcess function accepts at most one argument.');

        // Validate that the specified number of seconds is an integer
        Exceptions::validateIntArgument($seconds, 'The maximum number of seconds before the HTML file is translated must be an integer.');

        // Set $seconds based on the provided or default value
        $seconds = !is_null($seconds) ? $seconds : Constants::SLEEP_BEFORE_START_TRANSLATION;

        // Use SettingsProcessor to handle the delay logic
       SettingsProcessor::delayTranslation($seconds);

       return $this;
    }

    /**
     * Get sorted HTML files associated with a PDF file.
     *
     * This method retrieves an array of HTML files sorted in a specific order.
     * It relies on the SettingsProcessor class to perform the sorting based on the provided PDF file path
     * and the absolute path of the current script's directory.
     *
     * @param string $pdfFilePath The path to the PDF file for which sorted HTML files are requested.
     * @return array The array of sorted HTML files.
     */
    public function getSortedHtmlFileList()
    {
        // Check if any argument is provided
        Exceptions::validateCountArgument(0, func_get_args(), 'getSortedHtmlFileList function does not accept any arguments.');

        // Use SettingsProcessor to obtain sorted HTML files based on the PDF file path and script's directory
        $result = SettingsProcessor::getSortedHtmlFiles($this->getFullPdfPath(), $this->absolutePath);

        $this->htmlFiles = $result;

        return $this;
    }

    /**
     * Create text files from HTML files.
     *
     * This method takes an array of HTML files and generates corresponding text files.
     * It ensures that only one parameter is provided and that the parameter is an array.
     * The process is carried out using the SettingsProcessor class with predefined constants for paths and commands.
     *
     * @param array|null $htmlFiles The array of HTML files for which text files will be generated.
     * @return array The result of the text file creation process.
     * @throws Exception If more than one parameter is provided.
     *                   If the provided parameter is not an array.
     */
    public function convertHtmlToText()
    {
        // Validate that only one parameter is provided
        Exceptions::validateCountArgument(0, func_get_args(), 'convertHtmlToText function does not accept any arguments.');

        // Validate that the provided parameter is an array
        Exceptions::validateArrayArgument($this->getHtmlFiles(), 'The parameter to the convertHtmlToText function must be an array.');

        // Use SettingsProcessor to create text files from HTML files with predefined constants
        $pathTextOriginal = Constants::PATH_LIBRARY.'/'.Constants::PATH_TEXT_FILES_ORIGINAL;
        $result = SettingsProcessor::createHtmltotext($this->getHtmlFiles(), $pathTextOriginal, Constants::COMMAND_ORIGINAL);

        // Store the result in the class property
        $this->createHtmlToTextResult = $result;

        return $this;
    }

    /**
     * Translates all text files in the provided array to the target language.
     *
     * @param array|null $arrayListFilesTextOriginal An array containing paths to original text files.
     *
     * @return mixed The result of the translation process.
     */
    public function translateAllTextFiles()
    {
        $getCreateHtmlToTextResult = $this->getCreateHtmlToTextResult();

        // Validate that only one parameter is provided
        Exceptions::validateCountArgument(0, func_get_args(), 'translateAllTextFiles function does not accept any arguments.');

        // Validate that the provided parameter is an array
        Exceptions::validateArrayArgument($getCreateHtmlToTextResult, 'The parameter to the translateAllTextFiles function must be an array.');

        // Define the output path for translated text files
        $outputTranslatePath = Constants::PATH_LIBRARY.'/'.Constants::PATH_TEXT_FILES_TRANSLATE; 

        // Check if the translation output directory exists; if not, create it with full permissions
        if (!file_exists($outputTranslatePath)) {
            mkdir($outputTranslatePath, 0777, true);
        }

        // Set the source and target languages for translation
        $from = $this->getSource(); // Source language
        $to = $this->getTarget();   // Target language

        // Use SettingsProcessor to create text files from HTML files with predefined constants
        $results = SettingsProcessor::translateAllFilesTxt(
            $getCreateHtmlToTextResult,   // Original text files array
            $outputTranslatePath,          // Output path for translated text files
            $to,                           // Target language
            $from                          // Source language
        );
        $this->arrayListFilesTextTranslate = $results;

        return $this;
    }

    /**
     * Transform original and translated text files into arrays.
     *
     * @param array|null $arrayListFilesTextTranslate An array containing paths to translated text files (optional).
     *
     * @return $this The current Translate instance for method chaining.
     * @throws Exception If more than one parameter is provided.
     *                   If the provided parameter is not an array.
     */
    public function convertTextFileToArray()
    {
        // Validate that only one parameter is provided
        Exceptions::validateCountArgument(0, func_get_args(), 'convertTextFileToArray function does not accept any arguments.');

        // Validate that the provided parameter is an array
        $arrayListFilesTextTranslate = $this->getArrayListFilesTextTranslate();
        Exceptions::validateArrayArgument($arrayListFilesTextTranslate, 'The parameter of the convertTextFileToArray function must be an array.');

        // Get the original text files array
        $arrayListFilesTextOriginal = $this->getCreateHtmlToTextResult();

        // Transform original text files into an array
        $dataFilesTextOriginalToArray = SettingsProcessor::transformContentFileTxtToArray($arrayListFilesTextOriginal);

        // Transform translated text files into an array
        $dataFilesTextTranslateToArray = SettingsProcessor::transformContentFileTxtToArray($arrayListFilesTextTranslate);

        // Store the arrays in class properties for potential future use
        $this->dataFilesTextOriginalToArray = $dataFilesTextOriginalToArray;
        $this->dataFilesTextTranslateToArray = $dataFilesTextTranslateToArray;

        // Return the current instance for method chaining
        return $this;
    }

    /**
     * Set the output path for the translated PDF file.
     *
     * This method allows specifying the path where the translated PDF file should be saved.
     *
     * @param string $saveAs The path to save the translated PDF file.
     * @return $this The current Translate instance for method chaining.
     */
    public function setOutputPath(?string $saveAs = null)
    {
        Exceptions::validateCountArgument(1, func_get_args(), 'Only one parameter is allowed for the setOutputPath function.');

        Exceptions::validateStringArgument($saveAs, 'The output path of your translated PDF file must be a string.');

        Exceptions::validatePdfPathArgument($saveAs, 'The path provided for the translated PDF file must have a valid path with the .pdf extension.');

        if (!empty($saveAs)) {
            $translateDir = dirname($saveAs);

            if (!file_exists($translateDir)) {
                mkdir($translateDir, 0777, true);
            }
            
        } else {
            throw new \InvalidArgumentException('Invalid output PDF file. Please provide a valid path to save the translated HTML and PDF files.');
        }

        // Set the specified output path in the class property
        $this->saveAs = $saveAs;

        // Return the current instance for method chaining
        return $this;
    }

    /**
     * Translate the content of a PDF file using preprocessed HTML and text files.
     *
     * This method leverages the SettingsProcessor class to perform the translation
     * of a PDF file based on preprocessed HTML and text files. It also allows
     * for specifying the output path for the translated PDF file.
     *
     * @return array An associative array indicating the success of the translation and the paths to translated files.
     */
    public function translatePdfFile()
    {
        // Check if any argument is provided
        Exceptions::validateCountArgument(0, func_get_args(), 'translatePdfFile function does not accept any arguments.');

        // Retrieve necessary data from class properties
        $htmlFiles = $this->htmlFiles;
        $dataFilesTextOriginalToArray = $this->getDataFilesTextOriginalToArray();
        $dataFilesTextTranslateToArray = $this->getDataFilesTextTranslateToArray();

        // Determine the output path for the translated PDF file
        $pathSaveOutputPdf = !empty($this->getSetOutputPath()) ? $this->getSetOutputPath() : '';

        // Get the full path of the PDF file
        $pdfFilePath = $this->getFullPdfPath();

        // Use SettingsProcessor to handle the translation of the PDF file
        $pathUrlTranslateHtml = SettingsProcessor::translateHtmlPdf(
            $htmlFiles,
            $dataFilesTextOriginalToArray,
            $dataFilesTextTranslateToArray,
            $pathSaveOutputPdf,
            $pdfFilePath
        );

        // Check if both HTML and PDF files exist
        $returnBooleanHtmlPdfExists = false;

        // Ensure that $pathUrlTranslateHtml is not empty and contains exactly two files
        if (!empty($pathUrlTranslateHtml) && is_countable($pathUrlTranslateHtml) && count($pathUrlTranslateHtml) === 2) {
            $returnBooleanHtmlPdfExists = true;
        }

        // Store the result in the class properties
        $this->success = $returnBooleanHtmlPdfExists;
        $this->translatedPdfPaths = $pathUrlTranslateHtml;

         // Return an associative array indicating the success of the translation and the paths to translated files
        return ['success' => $returnBooleanHtmlPdfExists, 'paths' => $pathUrlTranslateHtml];
    }

    /**
     * Delete unnecessary files and folders based on a specific pattern.
     *
     * This method deletes files and folders matching a specified pattern
     * in the folders specified in Constants::FOLDER_NAMES. It uses the path
     * information from the PDF file to create the pattern.
     *
     * @throws Exception If more than one parameter is provided.
     *                   If the provided parameter is not a boolean.
     */
    public function cleanupFilesByPattern(): void
    {
        // Check if any argument is provided
        Exceptions::validateCountArgument(0, func_get_args(), 'cleanupFilesByPattern function does not accept any arguments.');

        // Get the base filename of the PDF file without the extension
        $textPattern = pathinfo($this->getFullPdfPath(), PATHINFO_FILENAME);

        // Call the static function from SettingsProcessor to delete files by pattern in specified folders
        SettingsProcessor::deleteFilesByPatternInFolders(Constants::FOLDER_NAMES, $textPattern);
    }

    /**
     * Unlink old files based on the specified expiration time in the specified folder.
     *
     * @param bool $shouldUnlink Whether to perform the unlink operation. Default is true.
     * @throws Exception If more than one parameter is provided.
     *                   If the provided parameter is not a boolean.
     */
    public function deleteOldTranslatedFiles(bool $shouldUnlink = true): void
    {
        Exceptions::validateCountArgument(1, func_get_args(), 'Only one parameter is allowed for the deleteOldTranslatedFiles function.');
        Exceptions::validateBooleanArgument($shouldUnlink, 'The parameter to the deleteOldTranslatedFiles function must be a boolean.');

        if ($shouldUnlink) {
            // Call the static function from SettingsProcessor
            SettingsProcessor::unlinkOldFiles(Constants::ARRAY_FOLDER_TRANSLATE, Constants::EXPIRATION_TIME);
        }
    }

}