<?php
use PHPUnit\Framework\TestCase;
use Richardson\PdfTranslator\PdfTranslator;

class PdfTranslatorTest extends TestCase
{
    public function translatePdfFileWithConstructorTest()
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

        $this->assertTrue($result['success']);
        $this->assertIsArray($result['paths']);
        $this->assertNotEmpty($result['paths']);

        // Display the links to the translated files
        foreach ($result['paths'] as $key => $value) {
            $number = $key + 1;
            $this->assertStringContainsString($value, $translationResult->getHtmlOutput());
        }

        // Clean up and delete old translated files
        $translationResult->cleanupFilesByPattern();
        $translationResult->deleteOldTranslatedFiles(false);
    }
}