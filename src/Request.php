<?php

namespace App;

class Request
{
    private const METHOD_GET = "GET";
    private const METHOD_POST = "POST";

    /**
     * Get the request method
     * @return string 
     */
    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Check whether the request method is `POST` or not
     * @return bool
     */
    public static function isPost()
    {
        return self::method() === self::METHOD_POST;
    }

    /**
     * Check whether the request method is `GET` or not
     * @return bool
     */
    public static function isGet()
    {
        return self::method() === self::METHOD_GET;
    }

    /**
     * Get a request field data
     * @param string $fieldName
     * @return string 
     */
    public static function get($fieldName)
    {
        $input = null;

        if (self::isGet()) {
            $input = $_GET[$fieldName];
        } elseif (self::isPost()) {
            $input = $_POST[$fieldName];
        }

        return self::sanitize($input);
    }

    /**
     * Get all request data
     * @return array 
     */
    public static function all()
    {
        $postOrGetArray = [];
        $dataArray = [];

        if (self::isGet()) {
            $postOrGetArray = $_GET;
        } elseif (self::isPost()) {
            $postOrGetArray = $_POST;
        }

        foreach (array_keys($postOrGetArray) as $fieldName) {
            $dataArray[$fieldName] = self::get($fieldName);
        }

        return $dataArray;
    }

    /**
     * Get specific request data only
     * @param array $fieldNames
     */
    public static function only($fieldNames = [])
    {
        $onlyArray = [];

        foreach ($fieldNames as $fieldName) {
            $onlyArray[$fieldName] = self::get($fieldName);
        }

        return $onlyArray;
    }

    /**
     * Get all request data except some fields
     * @param array $fieldNames
     * @return array
     */
    public static function except($fieldNames = [])
    {
        $all = self::all();

        foreach ($fieldNames as $fieldName) {
            unset($all[$fieldName]);
        }

        return $all;
    }

    /**
     * Check whether the request has a field
     * @param array $fieldNames
     * @return bool
     */
    public static function has($fieldNames)
    {
        if (is_string($fieldNames)) {
            if (array_key_exists($fieldNames, self::all())) {
                return true;
            }
        }

        if (is_array($fieldNames)) {
            $fieldsExist = [];

            foreach ($fieldNames as $fieldName) {
                if (array_key_exists($fieldName, self::all())) {
                    $fieldsExist[] = true;
                }
            }

            if (count($fieldsExist) === count($fieldNames)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sanitize the request input
     * @param string $input
     * @return string 
     */
    private static function sanitize($input)
    {
        return filter_var($input, FILTER_SANITIZE_STRING);
    }
}
