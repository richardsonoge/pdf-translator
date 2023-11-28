<?php
namespace Richardson\PdfTranslator\Exceptions;
use Richardson\PdfTranslator\Includes\Constants;

/**
 * Exceptions Class
 *
 * This class, designed for handling validation exceptions, provides static methods to ensure the validity of various types of arguments. It includes methods to validate integers, array counts, arrays, strings, and booleans. If a validation check fails, an exception is thrown with a specified error message.
 *
 * @package Richardson\PdfTranslator
 * @version 1.0.0
 *
 * Methods:
 *
 * - `validateIntArgument(int|null $argument, string $message)`: Validates that the provided argument is an integer.
 * - `validateCountArgument(int $countArgument, array $arguments, string $message)`: Validates that the provided count argument matches the expected number of arguments.
 * - `validateArrayArgument(array|null $argument, string $message)`: Validates that the provided argument is an array.
 * - `checkIsArray(mixed $array, string $message)`: Validates whether the given variable is an array.
 * - `validateStringArgument(string|null $argument, string $message)`: Validates that the provided argument is a string.
 * - `validateBooleanArgument(?bool $argument, string $message)`: Validates that the provided argument is a boolean.
 * - `validatePdfPathArgument(?string $argument, string $message)`: Validate that the given argument represents a valid PDF path with the .pdf extension.
 * - `handleSourceLanguageException(string $source)`: Handles exceptions related to the source language.
 * - `handleTargetLanguageException(string $target)`: Handles exceptions related to the target language.
 */

class Exceptions
{
	/**
     * Validate that the provided argument is an integer.
     *
     * This function checks if the provided argument is not null and is of integer type.
     * If the argument is not an integer, it throws an exception with the specified error message.
     *
     * @param mixed $argument The argument to be validated.
     * @param string $message The error message to be thrown if validation fails.
     * @throws Exception If the provided argument is not null and not an integer.
     */
    public static function validateIntArgument(?int $argument, string $message): void
    {
        // Check if the argument is not null and is not an integer
        if (!is_null($argument) && !is_int($argument)) {
            // Throw an exception with the specified error message
            throw new \Exception($message);
        }
    }

    /**
	 * Validate that the provided count argument matches the expected number of arguments.
	 *
	 * This function checks if the number of arguments passed matches the expected count.
	 * If the count does not match, it throws an exception with the specified error message.
	 *
	 * @param int $countArgument The count of arguments to be validated.
	 * @param array $arguments The array of arguments passed to the function.
	 * @param string $message The error message to be thrown if validation fails.
	 * @throws Exception If the number of arguments does not match the expected count.
	 */
	public static function validateCountArgument(int $countArgument, array $arguments, string $message): void
    {
	    // Check if the number of arguments passed is not equal to the expected count
	    if (count($arguments) > $countArgument) {
	        // Throw an exception with the specified error message
	        throw new \Exception($message);
	    }
	}

	/**
	 * Validate that the provided argument is an array.
	 *
	 * This static method checks if the provided argument is not null and is an array.
	 * If the argument fails the validation, it throws an exception with the specified error message.
	 *
	 * @param mixed $argument The argument to be validated.
	 * @param string $message The error message to be used in the exception if validation fails.
	 * @throws Exception If the argument is not null and is not an array.
	 */
	public static function validateArrayArgument(?array $argument, string $message): void
    {
	    // Check if the argument is not null and is not an array
	    if (!is_null($argument) && !is_array($argument)) {
	        // Throw an exception with the specified error message
	        throw new \Exception($message);
	    }
	}

	/**
	 * Validate whether the given variable is an array.
	 *
	 * This function checks if the provided variable is of type array.
	 * If not, it throws an exception with the specified error message.
	 *
	 * @param mixed $array The variable to be validated.
	 * @param string $message The error message to be thrown if the validation fails.
	 * @throws Exception If the variable is not an array.
	 */
	public static function checkIsArray(mixed $array, string $message): void
    {
	    if (!is_array($array)) {
	        throw new \Exception($message);
	    }
	}

	/**
	 * Validate that the provided argument is a string.
	 *
	 * This static method checks if the provided argument is not null and is a string.
	 * If the argument fails the validation, it throws an exception with the specified error message.
	 *
	 * @param mixed $argument The argument to be validated.
	 * @param string $message The error message to be used in the exception if validation fails.
	 * @throws Exception If the argument is not null and is not a string.
	 */
	public static function validateStringArgument(?string $argument, string $message): void
    {
	    // Check if the argument is not null and is not a string
	    if (!is_null($argument) && !is_string($argument)) {
	        // Throw an exception with the specified error message
	        throw new \Exception($message);
	    }
	}

	/**
	 * Validate that the provided argument is a boolean.
	 *
	 * @param bool|null $argument The argument to validate.
	 * @param string $message The error message to throw if validation fails.
	 * @throws Exception If the argument is not a boolean.
	 */
	public static function validateBooleanArgument(?bool $argument, string $message): void
    {
	    // Check if the argument is not null and is not a boolean
	    if (!is_null($argument) && !is_bool($argument)) {
	        // Throw an exception with the specified error message
	        throw new \Exception($message);
	    }
	}

	/**
	 * Validate that the given argument represents a valid PDF path with the .pdf extension.
	 *
	 * This static function checks if the provided argument has the .pdf extension at the end
	 * of the text. If not, it throws an exception with the specified error message.
	 *
	 * @param string|null $argument The path to be validated.
	 * @param string $message The error message to be thrown if validation fails.
	 *
	 * @throws Exception If the provided argument does not have the .pdf extension.
	 */
	public static function validatePdfPathArgument(?string $argument, string $message): void
	{
	    // Ensure the path has the .pdf extension at the end
	    if (pathinfo($argument, PATHINFO_EXTENSION) !== 'pdf') {
	        // Throw an exception with the specified error message
	        throw new \Exception($message);
	    }
	}

	public static function handleSourceLanguageException(string $source): void
    {
		$lowercaseLang = strtolower($source);
        if (!empty($lowercaseLang)) {
            if (!array_key_exists($lowercaseLang, array_map('strtolower', Constants::LANG_ACCEPT_TRANSLATEFILE))) {
                // Check if $lowercaseLang is a language name and find the corresponding code
                $foundCode = array_search($lowercaseLang, array_map('strtolower', Constants::LANG_ACCEPT_TRANSLATEFILE));
                
                if ($foundCode) {
                    throw new \Exception("The code '$lowercaseLang' provided for the language of your PDF document is invalid. Please use the language code '$foundCode'.");
                } else {
                    throw new \Exception("The '$lowercaseLang' language code you provided for your PDF document is invalid.");
                }
            }
        }
	}

	public static function handleTargetLanguageException(string $target): void
    {
		$lowercaseLang = strtolower($target);
        if ($lowercaseLang && !array_key_exists($lowercaseLang, array_map('strtolower', Constants::LANG_ACCEPT_TRANSLATEFILE))) {
            $foundCode = array_search($lowercaseLang, array_map('strtolower', Constants::LANG_ACCEPT_TRANSLATEFILE));
            
            if ($foundCode) {
                throw new \Exception("The language code '$lowercaseLang' you provided for the PDF translation is invalid. Please use this language code '$foundCode'.");
            } else {
                throw new \Exception("The language code '$lowercaseLang' provided for the translation of the PDF document is invalid.");
            }
        }
	}

}