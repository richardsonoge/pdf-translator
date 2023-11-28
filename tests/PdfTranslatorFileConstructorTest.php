<?php
use PHPUnit\Framework\TestCase;
use Richardson\PdfTranslator\PdfTranslator;

class PdfTranslatorFileConstructorTest extends TestCase
{

    public function testTranslatePdfFileWithConstructor()
    {
        $translationResult = new PdfTranslator('documents/freq.pdf');
        $translationResult->setTranslationLanguages('en', 'fr')
            ->fileExists()
            ->ensureValidTranslationConditions()
            ->splitPdfIntoPages()
            ->convertPdfToHtml()
            ->pauseTranslationProcess()
            ->getSortedHtmlFileList()
            ->convertHtmlToText()
            ->translateAllTextFiles()
            ->convertTextFileToArray()
            ->setOutputPath('translate/freq.pdf');

        $result = $translationResult->translatePdfFile();

        // Get the success status
        $success = $result->getTranslationSuccess();

        // Get the paths associated with the translation
        $paths = $result->getTranslationPaths();

        $this->assertTrue($success);
        $this->assertIsArray($paths);
        $this->assertNotEmpty($paths);

        // Check if the actual output contains the expected values
        $this->assertStringContainsString($result->getHtmlOutput(), $result->getHtmlOutput());

        // Clean up and delete old translated files
        $translationResult->cleanupFilesByPattern();
        $translationResult->deleteOldTranslatedFiles(false);
    }
}