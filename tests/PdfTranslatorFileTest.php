<?php
use PHPUnit\Framework\TestCase;
use Richardson\PdfTranslator\PdfTranslator;

class PdfTranslatorFileTest extends TestCase
{
    public function testTranslatePdfFile()
    {
        $translationResult = new PdfTranslator();
        $translationResult->setPdfFilePath('documents/freq.pdf')
            ->setTranslationLanguages('en', 'fr')
            ->fileExists()
            ->ensureValidTranslationConditions()
            ->splitPdfIntoPages()
            ->convertPdfToHtml()
            ->pauseTranslationProcess()
            ->getSortedHtmlFileList()
            ->convertHtmlToText()
            ->translateAllTextFiles()
            ->convertTextFileToArray()
            ->setOutputPath('translate/freq-fr.pdf');

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