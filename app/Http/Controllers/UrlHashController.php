<?php

namespace App\Http\Controllers;

use App\Models\UrlHashings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 *
 * APIs for managing URL's hashing
 */
class UrlHashController extends Controller
{
    /**
     *  Create tiny URL for the long URL
     *  @param request Request from the client contains the long URL
     *  @return json
     */
    public function createTinyURL(Request $request)
    {
        $requestData = $request->all();
        if (empty($requestData)) {
            return json_encode(
                [
                    "status" => false,
                    "error" => "Empty data provided."
                ]
            );
        }
        $url = trim($requestData['url']);
        $urlHashModel = new UrlHashings();
        $validation = $urlHashModel->isURLValid($url);
        
        if (!$validation['status']) {
            return json_encode($validation);
        }

        $isTinyURLExist = $urlHashModel->isURLAlreadyHashed($url);

        if ($isTinyURLExist) {
            return json_encode(
                [
                    "status" => false,
                    "error" => "Tiny URL already exist for this URL."
                ]
            );
        }

        $tinyURL = $urlHashModel->getTinyURL($url);

        if (!$tinyURL['status']) {
            return json_encode(
                [
                    "status" => false,
                    "error" => "Failed to create the tiny URL."
                ]
            );
        }

        return json_encode($tinyURL, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Get the original(long) URL for the tiny URL
     * @param request Request from the client contains the long URL
     * @return json
     */
    public function getOriginalURL(Request $request)
    {
        $requestData = $request->all();
        if (empty($requestData)) {
            return json_encode(
                [
                    "status" => false,
                    "error" => "Empty data provided."
                ]
            );
        }
        $tinyURL = trim($requestData['url']);
        $urlHashModel = new UrlHashings();
        $isTinyURLValid = $urlHashModel->isTinyURLValid($tinyURL);

        if ($isTinyURLValid['status'] == false) {
            return json_encode($isTinyURLValid);
        }

        $originalURL = $urlHashModel->getOriginalURL($tinyURL);

        if (empty($originalURL)) {
            return json_encode(
                [
                    "status" => false,
                    "error" => "Original URL not exist for this tiny URL."
                ]
            );
        }
        
        return json_encode(
            [
                "status" => true,
                "url" => $originalURL
            ],
            JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Update the url click count when user clicks the url
     * @param request Request from the client contains the long URL
     * @return json
     */
    public function updateClickCount(Request $request)
    {
        $requestData = $request->all();
        if (empty($requestData)) {
            return json_encode(
                [
                    "status" => false,
                    "error" => "Empty data provided."
                ]
            );
        }
        $url = $requestData['url'];
        $url = trim($url);
        $urlHashModel = new UrlHashings();
        $validationResult = $urlHashModel->isURLValid($url);
        
        if (!$validationResult['status']) {
            return json_encode($validationResult);
        }

        $updateStatus = $urlHashModel->updateClickCount($url);

        if ($updateStatus) {
            return json_encode(
                [
                    "status" => true,
                    "message" => "Successfully updated the click count."
                ]
            );
        }

        return json_encode(
            [
                "status" => true,
                "message" => "Failed to updated the click count."
            ]
        );
    }
    
    /**
     * Redirect to the original website
     * @param String $url
     * 
     */
    public function redirectOriginalUrl(Request $request)
    {
        $url = $request->query('name');
        $url = trim($url);
        if (empty($url)) {
            Log::error("Empty URL provided.");
        }
        // validate the tiny url
        $urlHashModel = new urlHashings();
        $validationStatus = $urlHashModel->isTinyURLValid($url);
        if ($validationStatus['status'] === false) {
            Log::error("Invalid URL provided. Error : " . $validationStatus['error']);

            return;
        }
        // fetch the original url
        $originalURL = $urlHashModel->getOriginalURL($url);

        if (empty($originalURL)) {
            Log::error("Original URL not found for the tiny url. Tiny URL : " . $url);
            return;
        }

        // redirect to the original url
        return redirect($originalURL);
    }
}
