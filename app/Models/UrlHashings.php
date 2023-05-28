<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\support\Facades\Log;

class UrlHashings extends Model
{
    use HasFactory;
    const ZEROCLICKS = '0';
    const START = 0;
    const END = 12;
    const TINYURL = "https:://newsbytes.com/rd=";
    const MAX_URL_LENGTH = 2048;
    const ACTIVE = 1;

    protected $fillable = ["url", "hashed_url", "clicks_count", "active"];
    /**
     * Method to validate the URL
     * @param string $url
     * @return array
     */
    public function isURLValid($url): array
    {
        if (empty($url)) {
            return [
                'status' => false,
                'error' => "URL cannot be empty."
            ];
        }

        if (strlen($url) > self::MAX_URL_LENGTH) {
            return [
                'status' => false,
                'error' => "URL length is too long."
            ];
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return [
                'status' => false,
                'error' => "Invalid URL format."
            ];
        }

        return [
            'status' => true
        ];
    }

    /**
     * Method to check is tiny URL is valid or not
     * @param string $tinyURL
     * @return array
     */
    public function isTinyURLValid($tinyURL):array
    {
        if (empty($tinyURL)) {
            return [
                "status" => false,
                "error" => "Tiny URL cannot be empty."
            ];
        }
        
        if (strpos($tinyURL, self::TINYURL) === false
            || strlen(substr($tinyURL, strlen(self::TINYURL))) != self::END
        ) {
            return [
                "status" => false,
                "error" => "Invalid Tiny URL."
            ];
        }

        return [
            'status' => true
        ];
    }

    /**
     * Method to create the tiny URL
     * @param string $url
     * @return array
     */
    public function getTinyURL($url): array
    {
        try {
            $shortURL = '';
            while (true) {
                $shortURL = substr(md5($url . mt_rand()), self::START, self::END);
                $isShortURLUnique = urlHashings::where('hashed_url', '=', $shortURL)
                    ->get()
                    ->count();
                
                if (!$isShortURLUnique) {
                    break;
                }
            }

            urlHashings::create(
                [
                   'url' => $url,
                   'hashed_url' => $shortURL,
                   'clicks_count' => self::ZEROCLICKS,
                   'active' => self::ACTIVE
                ]
            );
        } catch (\Error $t) {
            Log::error(
                "Error occured during hashing process for the url : " . $url . ", error :" . $t->getMessage()
            );

            return [
                'status' => false,
            ];
        }

        return [
            'status' => true,
            'tinyURL' => self::TINYURL . $shortURL
        ];
    }

    /**
     * Method to get the original URL
     * @param string $tirnyURL
     * @return string original URL
     */
    public function getOriginalURL($tinyURL):string
    {
        $tinyURL = substr($tinyURL, strlen(self::TINYURL));

        $originalURL = urlHashings::where('hashed_url', '=', $tinyURL)
            ->select('url')
            ->first();

        if (empty($originalURL)) {
            return "";
        }
        
        return $originalURL['url'];
    }

    /**
     * Method to check is URL already hashed
     * @param string $url
     * @return int
     */
    public function isURLAlreadyHashed($url):int
    {
        return urlHashings::where('url', '=', $url)
            ->get()
            ->count();
    }

    /**
     * Method to update click count of a URL
     * @param string $url
     * @return bool
     */
    public function updateClickCount($url):bool
    {
        $urlClickCount = urlHashings::where('url', '=', $url)
            ->select('clicks_count')
            ->first();
        
        if (empty($urlClickCount)) {
            Log::debug(
                "Failed to update the click count for the url : " . $url . " . Error : URL not found in the database"
            );

            return false;
        }
        try {
            urlHashings::where('url', $url)
                ->update(['clicks_count' => $urlClickCount['clicks_count'] + 1]);
        } catch (\Error $t) {
            Log::error("Failed during click count update, for the url : " . $url . " and error : " . $t->getMessage());

            return false;
        }

        return true;
    }
}
