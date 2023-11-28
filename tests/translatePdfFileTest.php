<?php
use PHPUnit\Framework\TestCase;
use Richardson\PdfTranslator\PdfTranslator;

class PdfTranslatorTest extends TestCase
{
    public function translatePdfFileTest()
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

        $this->assertTrue($result['success']);
        $this->assertIsArray($result['paths']);
        $this->assertNotEmpty($result['paths']);

        // Clean up and delete old translated files
        $translationResult->cleanupFilesByPattern();
        $translationResult->deleteOldTranslatedFiles(false);
    }
}